<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiService
{
    private ?string $primaryKey;
    private ?string $fallbackKey;
    private string $model;

    public function __construct()
    {
        $this->primaryKey = config('services.gemini.primary_key');
        $this->fallbackKey = config('services.gemini.fallback_key');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash-lite');
    }

    /**
     * Analyze and rewrite an article using Google Gemini with dual-key failover.
     *
     * @param string $rawTitle The original title from RSS feed
     * @param string $description The snippet description from RSS feed
     * @param array<int, string> $existingTitles Titles of recently saved articles to detect duplicates
     * @return array|null Returns structured analysis or null if AI is unavailable
     */
    public function analyzeArticle(string $rawTitle, string $description, array $existingTitles = []): ?array
    {
        if (empty($this->primaryKey) && empty($this->fallbackKey)) {
            return null;
        }

        $prompt = $this->buildAnalysisPrompt($rawTitle, $description, $existingTitles);

        // 1. Try Primary Key
        if (!empty($this->primaryKey)) {
            $result = $this->callGeminiApi($this->primaryKey, $prompt);
            if ($result !== null) {
                $result['key_used'] = 'primary';
                return $result;
            }
            Log::warning('Gemini Primary API key failed or rate-limited. Failing over to Fallback key...');
        }

        // 2. Failover to Secondary Key
        if (!empty($this->fallbackKey)) {
            $result = $this->callGeminiApi($this->fallbackKey, $prompt);
            if ($result !== null) {
                $result['key_used'] = 'fallback';
                return $result;
            }
            Log::warning('Gemini Fallback API key also failed or rate-limited.');
        }

        return null;
    }

    /**
     * Send HTTP request to Gemini API and parse JSON response.
     */
    protected function callGeminiApi(string $apiKey, string $prompt): ?array
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$apiKey}";

        try {
            $response = Http::timeout(12)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                        'temperature' => 0.2,
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning("Gemini API HTTP Error {$response->status()}: " . substr($response->body(), 0, 250));
                return null;
            }

            $json = $response->json();
            $rawText = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($rawText)) {
                return null;
            }

            $parsed = json_decode($rawText, true);
            if (!is_array($parsed)) {
                return null;
            }

            return $this->sanitizeAiOutput($parsed, $rawTitle ?? '');
        } catch (Throwable $e) {
            Log::warning('Gemini API call exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build the structured prompt for deduplication, relevance, categorization, and educational rewriting.
     */
    private function buildAnalysisPrompt(string $rawTitle, string $description, array $existingTitles): string
    {
        $existingList = empty($existingTitles)
            ? 'Belum ada berita sebelumnya.'
            : implode("\n", array_map(fn($t, $i) => ($i + 1) . '. ' . $t, $existingTitles, array_keys($existingTitles)));

        return <<<PROMPT
Anda adalah Asisten Redaksi & Analis Aksesibilitas Sleman Akses (Sistem Informasi Ramah Disabilitas Fisik di Kabupaten Sleman & DIY).

Tugas Anda:
1. DETEKSI DUPLIKASI SEMANTIK (is_duplicate):
   Bandingkan berita baru ini dengan daftar judul berita yang SUDAH ADA di database:
   --- DAFTAR BERITA YANG SUDAH ADA DI DATABASE ---
   $existingList
   -------------------------------------------------
   Jika berita baru ini melaporkan PERISTIWA ATAU TOPIK YANG SAMA PERSIS dengan salah satu berita di atas (meskipun judulnya beda dari media lain), set "is_duplicate": true. Jika peristiwa baru/berbeda, set "is_duplicate": false.

2. FILTER RELEVANSI (is_relevant):
   Apakah berita ini berkaitan dengan disabilitas fisik, mobilitas lansia, fasilitas umum inklusif, atau kebijakan aksesibilitas di Indonesia (terutama Sleman / Yogyakarta)?
   Jika relevan, set "is_relevant": true. Jika tidak relevan (hanya menyebut difabel sekilas tanpa konteks fasilitas atau tentang kriminal/politik tak berkaitan), set "is_relevant": false.

3. EDUKASI & PENULISAN ULANG (clean_title, summary, content):
   Jika lolos (tidak duplikat dan relevan):
   - clean_title: Judul yang informatif, menarik, dan sopan (Bahasa Indonesia baku).
   - summary: Ringkasan 1-2 kalimat padat (maks 200 karakter).
   - content: Tuliskan 2 paragraf padat:
     * Paragraf 1: Laporan peristiwa atau konteks faktual berita.
     * Paragraf 2: Nilai edukasi aksesibilitas fasilitas publik bagi masyarakat (misal pentingnya ramp dengan kemiringan tepat, guiding block bebas hambatan, toilet difabel, dsb).

4. KATEGORI (category):
   Pilih salah satu dari 4 opsi:
   - "Aksesibilitas"
   - "Edukasi & Kesadaran"
   - "Regulasi & Kebijakan"
   - "Fasilitas Publik"

5. FASILITAS TERDETEKSI (detected_facilities):
   Daftar fasilitas fisik yang disebut dalam artikel, pilih yang relevan: ["Ramp", "Toilet Difabel", "Parkir Difabel", "Lift", "Guiding Block"]. Jika tidak spesifik, berikan array kosong [].

DATA BERITA BARU YANG DIANALISIS:
Judul: $rawTitle
Cuplikan: $description

Output WAJIB berupa JSON murni dengan skema berikut:
{
  "is_duplicate": boolean,
  "duplicate_reason": string or null,
  "is_relevant": boolean,
  "clean_title": string,
  "summary": string,
  "content": string,
  "category": string,
  "detected_facilities": array of strings
}
PROMPT;
    }

    /**
     * Sanitize and format the AI response.
     */
    private function sanitizeAiOutput(array $data, string $fallbackTitle): array
    {
        $validCategories = [
            'Aksesibilitas',
            'Edukasi & Kesadaran',
            'Regulasi & Kebijakan',
            'Fasilitas Publik',
        ];

        $category = trim($data['category'] ?? '');
        if (!in_array($category, $validCategories, true)) {
            $category = 'Edukasi & Kesadaran';
        }

        $detectedFacilities = is_array($data['detected_facilities'] ?? null)
            ? array_values(array_filter($data['detected_facilities'], 'is_string'))
            : [];

        return [
            'is_duplicate' => (bool) ($data['is_duplicate'] ?? false),
            'duplicate_reason' => !empty($data['duplicate_reason']) ? (string) $data['duplicate_reason'] : null,
            'is_relevant' => (bool) ($data['is_relevant'] ?? true),
            'clean_title' => !empty($data['clean_title']) ? trim((string) $data['clean_title']) : $fallbackTitle,
            'summary' => !empty($data['summary']) ? trim((string) $data['summary']) : '',
            'content' => !empty($data['content']) ? trim((string) $data['content']) : '',
            'category' => $category,
            'detected_facilities' => $detectedFacilities,
        ];
    }
}
