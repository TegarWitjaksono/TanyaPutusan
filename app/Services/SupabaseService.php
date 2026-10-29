<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected string $url;
    protected string $key;
    protected string $bucket;

    public function __construct()
    {
        $this->url = rtrim(config('services.supabase.url') ?: env('SUPABASE_URL', ''), '/');
        $this->key = config('services.supabase.key') ?: env('SUPABASE_KEY', '');
        $this->bucket = config('services.supabase.bucket') ?: env('SUPABASE_BUCKET', 'putusan-pdf');
    }

    /**
     * Cek apakah file sudah ada di Supabase Storage
     */
    public function fileExists(string $filePath): bool
    {
        if (empty($this->url) || empty($this->key)) {
            return false;
        }

        try {
            $endpoint = "{$this->url}/storage/v1/object/info/public/{$this->bucket}/" . ltrim($filePath, '/');
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'apikey' => $this->key,
                    'Authorization' => "Bearer {$this->key}"
                ])
                ->get($endpoint);

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Dapatkan Public URL file dari Supabase Storage
     */
    public function getPublicUrl(string $filePath): string
    {
        return "{$this->url}/storage/v1/object/public/{$this->bucket}/" . ltrim($filePath, '/');
    }

    /**
     * Upload berkas PDF lokal ke Supabase Storage
     *
     * @param string $localPath Path file di storage lokal
     * @param string $remoteFileName Nama file tujuan di bucket Supabase
     * @return string|null Mengembalikan Public URL jika sukses, null jika gagal
     */
    public function uploadPdf(string $localPath, string $remoteFileName): ?string
    {
        if (empty($this->url) || empty($this->key)) {
            Log::warning('Supabase: URL atau API Key belum diatur di .env');
            return null;
        }

        if (!file_exists($localPath) || filesize($localPath) < 100) {
            Log::warning("Supabase: File lokal tidak valid atau kosong: {$localPath}");
            return null;
        }

        try {
            $cleanName = ltrim($remoteFileName, '/');
            $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}/{$cleanName}";

            $fileContents = file_get_contents($localPath);

            // Upload menggunakan multipart atau raw binary dengan header Content-Type application/pdf
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'apikey' => $this->key,
                    'Authorization' => "Bearer {$this->key}",
                    'Content-Type' => 'application/pdf',
                    'x-upsert' => 'true' // overwrite jika sudah ada
                ])
                ->withBody($fileContents, 'application/pdf')
                ->post($endpoint);

            if ($response->successful()) {
                $publicUrl = $this->getPublicUrl($cleanName);
                Log::info("Supabase: Sukses upload {$cleanName} ke bucket {$this->bucket}. Public URL: {$publicUrl}");
                return $publicUrl;
            } else {
                Log::warning("Supabase: Gagal upload {$cleanName}. Status: {$response->status()}, Body: " . $response->body());
                return null;
            }
        } catch (\Throwable $e) {
            Log::error("Supabase upload exception: " . $e->getMessage());
            return null;
        }
    }
}
