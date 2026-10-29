<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected ?string $projectId = null;
    protected ?array $credentials = null;
    protected ?string $cachedToken = null;
    protected int $tokenExpiresAt = 0;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id', 'tanyaputusan');
        $credPath = base_path(config('services.firebase.credentials', 'storage/app/firebase/firebase_credentials.json'));
        
        if (file_exists($credPath)) {
            $this->credentials = json_decode(file_get_contents($credPath), true);
        } elseif (env('FIREBASE_CREDENTIALS_JSON')) {
            $raw = env('FIREBASE_CREDENTIALS_JSON');
            $this->credentials = json_decode($raw, true) ?: json_decode(base64_decode($raw), true);
        }
    }

    /**
     * Dapatkan Google OAuth2 Access Token untuk Service Account menggunakan pure OpenSSL
     */
    protected function getAccessToken(): ?string
    {
        if ($this->cachedToken && time() < ($this->tokenExpiresAt - 60)) {
            return $this->cachedToken;
        }

        if (!$this->credentials) {
            Log::warning('Firebase service account credentials tidak ditemukan.');
            return null;
        }

        $now = time();
        $jwtHeader = ['alg' => 'RS256', 'typ' => 'JWT'];
        $jwtClaim = [
            'iss' => $this->credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore https://www.googleapis.com/auth/cloud-platform',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $encodedHeader = rtrim(strtr(base64_encode(json_encode($jwtHeader)), '+/', '-_'), '=');
        $encodedClaim = rtrim(strtr(base64_encode(json_encode($jwtClaim)), '+/', '-_'), '=');
        $dataToSign = $encodedHeader . '.' . $encodedClaim;

        $privateKey = openssl_pkey_get_private($this->credentials['private_key']);
        if (!$privateKey) {
            Log::error('Firebase: Gagal membaca private key OpenSSL.');
            return null;
        }

        $signature = '';
        openssl_sign($dataToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $encodedSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        $jwt = $dataToSign . '.' . $encodedSignature;

        // Tukar JWT dengan Google Access Token
        $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $this->cachedToken = $data['access_token'] ?? null;
            $this->tokenExpiresAt = $now + ($data['expires_in'] ?? 3600);
            return $this->cachedToken;
        }

        Log::error('Firebase OAuth Token Error: ' . $response->body());
        return null;
    }

    /**
     * Konversi associative PHP array ke format Firestore typed fields
     */
    protected function formatFirestoreFields(array $data): array
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $fields[$key] = ['nullValue' => null];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_int($value)) {
                $fields[$key] = ['integerValue' => (string)$value];
            } elseif (is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } elseif (is_array($value)) {
                // Sederhanakan objek/array sebagai map
                $fields[$key] = ['mapValue' => ['fields' => $this->formatFirestoreFields($value)]];
            } else {
                $fields[$key] = ['stringValue' => (string)$value];
            }
        }
        return $fields;
    }

    /**
     * Simpan atau perbarui dokumen user di Cloud Firestore
     * Collection: users, Document ID: email hash / sanitized email
     */
    public function syncUserToFirestore(array $userData): bool
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                Log::warning('Firebase: Tidak ada token akses untuk sinkronisasi.');
                return false;
            }

            $email = $userData['email'] ?? null;
            if (!$email) return false;

            // Gunakan email terenkode sebagai ID dokumen unik di Firestore
            $docId = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $email);

            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/users/{$docId}";

            $fields = $this->formatFirestoreFields(array_merge($userData, [
                'updated_at' => now()->toIso8601String(),
            ]));

            // Patch dokumen (insert or update)
            $response = Http::withoutVerifying()
                ->withToken($token)
                ->patch($url, [
                    'fields' => $fields
                ]);

            if ($response->successful()) {
                Log::info("Firebase: Data pengguna {$email} berhasil disimpan ke Cloud Firestore.");
                return true;
            } else {
                Log::error("Firebase Error saat menyimpan {$email}: " . $response->body());
                return false;
            }
        } catch (\Throwable $e) {
            Log::error('Firebase Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil data user dari Firestore berdasarkan email
     */
    public function getUserFromFirestore(string $email): ?array
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) return null;

            $docId = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $email);
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/users/{$docId}";

            $response = Http::withoutVerifying()->withToken($token)->get($url);
            if ($response->successful()) {
                return $response->json();
            }
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Simpan hasil ekstraksi teks dokumen PDF putusan ke Cloud Firestore (Collection: putusan_documents)
     */
    public function savePutusanDocument(string $identifier, array $docData): bool
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) return false;

            // Bersihkan nama identifier untuk dijadikan ID dokumen unik di Firestore
            $docId = preg_replace('/[^a-zA-Z0-9_\-]/', '_', trim($identifier));
            $docId = trim($docId, '_');
            if (empty($docId)) return false;

            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/putusan_documents/{$docId}";

            $fields = $this->formatFirestoreFields(array_merge($docData, [
                'updated_at' => now()->toIso8601String(),
            ]));

            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withToken($token)
                ->patch($url, [
                    'fields' => $fields
                ]);

            if ($response->successful()) {
                Log::info("Firebase: Dokumen putusan {$docId} berhasil disimpan ke Firestore.");
                return true;
            } else {
                Log::warning("Firebase: Gagal menyimpan putusan {$docId}: " . $response->body());
                return false;
            }
        } catch (\Throwable $e) {
            Log::error("Firebase savePutusanDocument exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil data dokumen putusan yang sudah diekstrak dari Cloud Firestore
     */
    public function getPutusanDocument(string $identifier): ?array
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) return null;

            $docId = preg_replace('/[^a-zA-Z0-9_\-]/', '_', trim($identifier));
            $docId = trim($docId, '_');
            if (empty($docId)) return null;

            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/putusan_documents/{$docId}";

            $response = Http::withoutVerifying()
                ->timeout(10)
                ->withToken($token)
                ->get($url);

            if ($response->successful()) {
                $raw = $response->json();
                $fields = $raw['fields'] ?? [];
                $result = [];
                foreach ($fields as $key => $val) {
                    if (isset($val['stringValue'])) {
                        $result[$key] = $val['stringValue'];
                    } elseif (isset($val['integerValue'])) {
                        $result[$key] = (int)$val['integerValue'];
                    } elseif (isset($val['booleanValue'])) {
                        $result[$key] = (bool)$val['booleanValue'];
                    }
                }
                return $result;
            }
            return null;
        } catch (\Throwable $e) {
            Log::warning("Firebase getPutusanDocument exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Catat histori unduhan dokumen PDF putusan ke Cloud Firestore
     * Collection: putusan_downloads
     */
    public function recordPdfDownload(string $url, ?string $title = null, ?string $userName = null, ?string $userEmail = null): bool
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) return false;

            $hash = md5($url);
            $docId = $hash . '_' . time();

            $endpoint = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/putusan_downloads/{$docId}";

            $data = [
                'doc_hash' => $hash,
                'url' => $url,
                'title' => $title ?: 'Putusan Mahkamah Agung',
                'user_name' => $userName ?: 'Guest User',
                'user_email' => $userEmail ?: 'guest@tanyaputusan.local',
                'downloaded_at' => now()->toIso8601String(),
                'ip_address' => request()->ip() ?: '127.0.0.1',
            ];

            $fields = $this->formatFirestoreFields($data);

            $res = Http::withoutVerifying()
                ->timeout(10)
                ->withToken($token)
                ->patch($endpoint, [
                    'fields' => $fields
                ]);

            return $res->successful();
        } catch (\Throwable $e) {
            Log::warning('Firebase recordPdfDownload error: ' . $e->getMessage());
            return false;
        }
    }
}
