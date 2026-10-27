<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiLegalService
{
    protected ?string $apiKey;
    protected string $ollamaBaseUrl;
    protected string $ollamaModel;
    protected array $models = [
        'gemini-2.5-flash',
        'gemini-flash-latest',
        'gemini-3.8-flash',
        'gemini-3.5-flash-lite',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->ollamaBaseUrl = rtrim(config('services.ollama.base_url', 'http://127.0.0.1:11434'), '/');
        $this->ollamaModel = config('services.ollama.model', 'llama3.2:latest');
    }

    /**
     * Kirim pertanyaan hukum ke AI (Gemini atau Ollama Lokal) dengan grounding data direktori putusan MA RI
     */
    public function askLegalAssistant(string $userPrompt, array $conversationHistory = [], array $relevantPutusan = [], string $provider = 'ollama'): array
    {
        $groundingContext = "";
        if (!empty($relevantPutusan)) {
            $groundingContext .= "\n\nBERIKUT ADALAH DATA PUTUSAN RESMI DARI DIREKTORI MAHKAMAH AGUNG RI (putusan3.mahkamahagung.go.id) SEBAGAI REFERENSI UTAMA WAJIB:\n";
            foreach (array_slice($relevantPutusan, 0, 5) as $idx => $item) {
                $num = $idx + 1;
                $groundingContext .= "--- Dokumen Putusan #{$num} ---\n";
                $groundingContext .= "Judul: " . ($item['judul'] ?? 'Putusan MA') . "\n";
                $groundingContext .= "Pengadilan: " . ($item['pengadilan'] ?? 'Mahkamah Agung RI') . "\n";
                $groundingContext .= "Klasifikasi: " . ($item['klasifikasi'] ?? '-') . "\n";
                $groundingContext .= "Tgl Putus: " . ($item['tgl_putus'] ?? '-') . "\n";
                $groundingContext .= "Amar / Ringkasan: " . ($item['ringkasan'] ?? '-') . "\n";
                
                // Tambahkan data teks lengkap dari berkas dokumen PDF jika tersedia
                if (!empty($item['identitas_para_pihak'])) {
                    $groundingContext .= "Identitas Lengkap Para Pihak (dari Dokumen Putusan Asli):\n" . $item['identitas_para_pihak'] . "\n";
                }
                if (!empty($item['amar_putusan'])) {
                    $groundingContext .= "Amar Putusan Lengkap:\n" . $item['amar_putusan'] . "\n";
                }
                if (!empty($item['pdf_excerpt'])) {
                    $groundingContext .= "Kutipan Dokumen Tambahan:\n" . $item['pdf_excerpt'] . "\n";
                }

                $groundingContext .= "Tautan Resmi: " . ($item['url'] ?? '') . "\n\n";
            }
        }

        $isSingleFocus = !empty($relevantPutusan[0]['is_single_focus']);
        $focusTitle = $isSingleFocus ? ($relevantPutusan[0]['judul'] ?? 'Perkara yang Sedang Dibuka') : '';

        $systemInstruction = <<<EOT
Anda adalah "TanyaPutusan AI Legal Intelligence", asisten pakar hukum Indonesia berbasis data resmi Direktori Putusan Mahkamah Agung RI (putusan3.mahkamahagung.go.id).

ATURAN KETAT ANTI-HALUSINASI & FOKUS PERKARA (GROUNDING PROTOCOL):
1. ANDA WAJIB HANYA MEMBAHAS DOKUMEN PERKARA YANG DILAMPIRKAN.
EOT;

        if ($isSingleFocus) {
            $systemInstruction .= "\nPERINGATAN KHUSUS: Pengguna saat ini sedang membuka dan menelaah secara khusus perkara: '{$focusTitle}'. Anda DILARANG KERAS membahas perkara lain atau mengalihkan jawaban ke nomor putusan lain. Semua analisis, kaidah hukum, pihak, dan pertimbangan wajib 100% difokuskan hanya pada perkara '{$focusTitle}'.\n";
        }

        $systemInstruction .= <<<EOT
2. JANGAN PERNAH mengarang nomor perkara, tanggal putusan, nama hakim, atau amar putusan yang tidak ada datanya.
3. Sebutkan secara eksplisit nomor putusan, klasifikasi, pihak, dan amar perkara yang sedang ditelaah.
4. Susun jawaban secara sistematis, terstruktur, dan elegan:
   - **Kaidah Hukum / Analisis Doktrin**: Penjelasan hukum atas perkara yang ditanyakan.
   - **Dasar Putusan Terkait**: Nomor putusan, klasifikasi, para pihak, tanggal putus.
   - **Konstruksi Pertimbangan & Amar**: Uraian pertimbangan hakim dan amar putusan.
   - **Kesimpulan**: Ringkasan jawaban atas pertanyaan pengguna.
5. Format menggunakan Markdown yang rapi dan profesional.
EOT;

        if (!empty($groundingContext)) {
            $systemInstruction .= $groundingContext;
        }

        // ==========================================
        // 1. JIKA PILIHAN OLLAMA (LOKAL LLM ON-DEMAND)
        // ==========================================
        if ($provider === 'ollama') {
            // Ringkas system instruction agar Ollama lokal di CPU/GPU cepat memproses prompt
            $ollamaPrompt = $this->buildOllamaSystemInstruction($relevantPutusan, $isSingleFocus, $focusTitle);
            return $this->callOllama($ollamaPrompt, $conversationHistory, $userPrompt, $relevantPutusan);
        }

        // ==========================================
        // 2. JIKA PILIHAN GOOGLE GEMINI (CLOUD API)
        // ==========================================
        return $this->callGemini($systemInstruction, $conversationHistory, $userPrompt, $relevantPutusan);
    }

    /**
     * Susun instruksi ringkas & terarah khusus untuk Ollama Llama 3.2 lokal agar komputasi ringan & cepat
     */
    protected function buildOllamaSystemInstruction(array $relevantPutusan, bool $isSingleFocus, string $focusTitle): string
    {
        $context = "";
        if (!empty($relevantPutusan)) {
            $item = $relevantPutusan[0];
            $context .= "DOKUMEN PERKARA:\n";
            $context .= "Judul: " . ($item['judul'] ?? 'Putusan MA') . "\n";
            $context .= "Klasifikasi: " . ($item['klasifikasi'] ?? '-') . "\n";
            $context .= "Amar Ringkas: " . mb_substr($item['ringkasan'] ?? '-', 0, 500) . "\n";

            if (!empty($item['identitas_para_pihak'])) {
                $context .= "Pihak: " . mb_substr($item['identitas_para_pihak'], 0, 400) . "\n";
            }
            if (!empty($item['amar_putusan'])) {
                $context .= "Amar Lengkap: " . mb_substr($item['amar_putusan'], 0, 1000) . "\n";
            }
        }

        return "Anda adalah Asisten Hukum AI Indonesia. Jawab secara ringkas, lugas, dan terstruktur dalam Bahasa Indonesia berdasarkan data perkara berikut:\n\n{$context}\nAturan: Fokus hanya pada perkara di atas, sebutkan pertimbangan hukum dan amarnya secara jelas.";
    }

    /**
     * Panggil Ollama Lokal (Llama 3.2)
     */
    protected function callOllama(string $systemInstruction, array $conversationHistory, string $userPrompt, array $relevantPutusan): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemInstruction]
        ];

        foreach (array_slice($conversationHistory, -4) as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['text'] ?? ''
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userPrompt
        ];

        try {
            $response = Http::timeout(60)
                ->post("{$this->ollamaBaseUrl}/api/chat", [
                    'model' => $this->ollamaModel,
                    'messages' => $messages,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.3,
                        'top_p' => 0.9,
                        'num_ctx' => 2048,   // Batasi context window agar inferensi CPU lokal sangat gesit
                        'num_predict' => 512, // Output ringkas padat agar selesai dalam hitungan detik
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['message']['content'] ?? null;
                if (!empty($reply)) {
                    return [
                        'success' => true,
                        'reply' => $reply,
                        'model_used' => "Ollama Local ({$this->ollamaModel})",
                        'provider' => 'ollama',
                    ];
                }
            }

            Log::warning("Ollama API error: " . $response->body());
        } catch (\Throwable $e) {
            Log::warning("Ollama connection exception: " . $e->getMessage());
        }

        // Fallback jika Ollama offline: Coba jalankan Gemini jika ada API key, atau extractor
        if (!empty($this->apiKey)) {
            $geminiRes = $this->callGemini($systemInstruction, $conversationHistory, $userPrompt, $relevantPutusan);
            if (!empty($geminiRes['success'])) {
                $geminiRes['reply'] = "> *Catatan: Server Ollama Lokal tidak merespons, dialihkan otomatis ke Google Gemini.* \n\n" . $geminiRes['reply'];
                return $geminiRes;
            }
        }

        return $this->buildDirectExtractorFallback($relevantPutusan, "Koneksi ke Ollama lokal ({$this->ollamaModel}) gagal. Pastikan aplikasi Ollama sedang berjalan di perangkat Anda.");
    }

    /**
     * Panggil Google Gemini API
     */
    protected function callGemini(string $systemInstruction, array $conversationHistory, string $userPrompt, array $relevantPutusan): array
    {
        if (empty($this->apiKey)) {
            // Jika API Key kosong, coba langsung fallback ke Ollama jika Ollama hidup
            $ollamaRes = $this->callOllama($systemInstruction, $conversationHistory, $userPrompt, $relevantPutusan);
            if (!empty($ollamaRes['success'])) {
                $ollamaRes['reply'] = "> *Catatan: API Key Gemini belum disetel, dialihkan otomatis ke Ollama Lokal ({$this->ollamaModel}).* \n\n" . $ollamaRes['reply'];
                return $ollamaRes;
            }

            return [
                'success' => false,
                'message' => 'API Key Google Gemini belum dikonfigurasi di file .env.'
            ];
        }

        // Susun payload riwayat chat untuk Gemini
        $contents = [];
        foreach ($conversationHistory as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['text']]]
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userPrompt]]
        ];

        $lastError = 'Terjadi kendala saat menghubungi server AI.';

        foreach ($this->models as $modelName) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$this->apiKey}";

            try {
                $response = Http::withoutVerifying()
                    ->timeout(18)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post($endpoint, [
                        'system_instruction' => [
                            'parts' => [
                                ['text' => $systemInstruction]
                            ]
                        ],
                        'contents' => $contents,
                        'generationConfig' => [
                            'temperature' => 0.3,
                            'topP' => 0.95,
                            'maxOutputTokens' => 2048,
                        ]
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if (!empty($reply)) {
                        return [
                            'success' => true,
                            'reply' => $reply,
                            'model_used' => $modelName,
                            'provider' => 'gemini',
                        ];
                    }
                }

                $errJson = $response->json();
                $lastError = $errJson['error']['message'] ?? $response->body();
                Log::warning("Gemini model {$modelName} gagal: {$lastError}, mencoba model berikutnya...");

            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning("Gemini exception pada {$modelName}: " . $e->getMessage());
            }
        }

        // FALLBACK 1: Jika kuota limit Gemini habis, otomatis oper ke Ollama Lokal Llama 3.2
        try {
            $ollamaFallback = $this->callOllama($systemInstruction, $conversationHistory, $userPrompt, $relevantPutusan);
            if (!empty($ollamaFallback['success'])) {
                $ollamaFallback['reply'] = "> *Catatan: Limit kuota Google Gemini tercapai. Menjawab menggunakan Ollama Lokal ({$this->ollamaModel}).* \n\n" . $ollamaFallback['reply'];
                return $ollamaFallback;
            }
        } catch (\Throwable $e) {
            Log::warning("Ollama fallback failed: " . $e->getMessage());
        }

        // FALLBACK 2: Ekstraksi Langsung Dokumen Putusan MA
        return $this->buildDirectExtractorFallback($relevantPutusan, $lastError);
    }

    /**
     * Fallback ekstraksi berkas asli direktori putusan MA RI
     */
    protected function buildDirectExtractorFallback(array $relevantPutusan, string $reason): array
    {
        if (!empty($relevantPutusan)) {
            $primary = $relevantPutusan[0];
            $title = $primary['judul'] ?? 'Dokumen Putusan MA';
            $ringkasan = $primary['ringkasan'] ?? '';
            $identitasRaw = $primary['identitas_para_pihak'] ?? '';
            $amarRaw = $primary['amar_putusan'] ?? '';

            $cleanText = function($txt) {
                if (empty($txt)) return '';
                $txt = preg_replace('/(Mahkamah Agung Republik Indonesia\s*)+/i', '', $txt);
                $txt = preg_replace('/(Direktori Putusan Mahkamah Agung Republik Indonesia\s*)+/i', '', $txt);
                $txt = preg_replace('/putusan\.mahkamahagung\.go\.id/i', '', $txt);
                $txt = preg_replace('/Disclaimer[\s\S]*?(?:Telp\s*:\s*[0-9\-\s\(\)\.ext]+|Halaman\s+[0-9]+)/i', '', $txt);
                $txt = preg_replace('/Halaman\s+\d+\s+dari\s+\d+\s+halaman.*?(\r?\n|$)/i', '', $txt);
                $txt = preg_replace("/[\r\n]{3,}/", "\n\n", trim($txt));
                return trim($txt);
            };

            $identitasClean = $cleanText($identitasRaw);
            $amarClean = $cleanText($amarRaw);

            $fallbackReply = "### Telaah Dokumen Putusan Mahkamah Agung RI\n\n";
            $fallbackReply .= "**Perkara yang Ditelaah:**\n* **{$title}**\n\n";

            if (!empty($ringkasan)) {
                $fallbackReply .= "**Ringkasan Perkara:**\n{$ringkasan}\n\n";
            }

            if (!empty($identitasClean)) {
                $fallbackReply .= "---\n### Identitas Lengkap Para Pihak (Dokumen Resmi MA):\n```\n{$identitasClean}\n```\n\n";
            }

            if (!empty($amarClean)) {
                $fallbackReply .= "---\n### Amar Putusan Resmi:\n```\n{$amarClean}\n```\n\n";
            }

            $fallbackReply .= "\n> *Catatan Sistem: Mode Ekstraksi Dokumen Asli Mahkamah Agung RI aktif ({$reason}).*";

            return [
                'success' => true,
                'reply' => $fallbackReply,
                'model_used' => 'ma-document-direct-extractor',
                'provider' => 'extractor',
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal memproses AI: ' . $reason
        ];
    }
}

