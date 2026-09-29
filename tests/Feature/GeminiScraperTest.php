<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Services\GeminiService;
use App\Services\NewsScraperService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiScraperTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_gemini_service_failover_to_secondary_key_when_primary_fails(): void
    {
        Config::set('services.gemini.primary_key', 'primary_test_key');
        Config::set('services.gemini.fallback_key', 'fallback_test_key');
        Config::set('services.gemini.model', 'gemini-2.0-flash-lite');

        // Mock Http: Primary fails with 429, Fallback succeeds with 200
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=primary_test_key' => Http::response([
                'error' => ['code' => 429, 'message' => 'Rate limit exceeded']
            ], 429),
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=fallback_test_key' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'is_duplicate' => false,
                                        'duplicate_reason' => null,
                                        'is_relevant' => true,
                                        'clean_title' => 'Uji Coba Ramp Kursi Roda di Fasilitas Sleman',
                                        'summary' => 'Ringkasan edukasi uji coba ramp kursi roda.',
                                        'content' => "Paragraf 1: Fakta peristiwa.\n\nParagraf 2: Edukasi aksesibilitas.",
                                        'category' => 'Aksesibilitas',
                                        'detected_facilities' => ['Ramp', 'Toilet Difabel'],
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = app(GeminiService::class);
        $result = $service->analyzeArticle('Judul Uji Coba', 'Deskripsi cuplikan uji coba', []);

        $this->assertNotNull($result);
        $this->assertEquals('fallback', $result['key_used']);
        $this->assertEquals('Uji Coba Ramp Kursi Roda di Fasilitas Sleman', $result['clean_title']);
        $this->assertEquals('Aksesibilitas', $result['category']);
        $this->assertContains('Ramp', $result['detected_facilities']);
    }

    public function test_gemini_semantic_duplicate_is_prevented_from_saving(): void
    {
        Config::set('services.gemini.primary_key', 'test_key');
        Config::set('services.gemini.fallback_key', '');

        // Mock AI returning is_duplicate = true
        Http::fake([
            'https://news.google.com/*' => Http::response(
                '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel>' .
                '<item><title>Berita Duplikat Peristiwa Sama - Media Lain</title><link>https://news.google.com/test-dup</link><pubDate>' . now()->toRssString() . '</pubDate><description>Cuplikan isi berita</description></item>' .
                '</channel></rss>',
                200
            ),
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'is_duplicate' => true,
                                        'duplicate_reason' => 'Peristiwa sama persis dengan berita trotoar Gejayan yang sudah terdaftar.',
                                        'is_relevant' => true,
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $scraper = app(NewsScraperService::class);
        $result = $scraper->scrapeLatest(1);

        $this->assertGreaterThanOrEqual(1, $result['duplicates_prevented']);
        $this->assertDatabaseMissing('articles', [
            'source_url' => 'https://news.google.com/test-dup',
        ]);
    }

    public function test_gemini_processed_article_saved_with_facilities_and_ai_flag(): void
    {
        Config::set('services.gemini.primary_key', 'test_key');
        Config::set('services.gemini.fallback_key', '');

        Http::fake([
            'https://news.google.com/*' => Http::response(
                '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel>' .
                '<item><title>Berita Baru Segar - Harian Jogja</title><link>https://news.google.com/test-fresh-' . uniqid() . '</link><pubDate>' . now()->toRssString() . '</pubDate><description>Cuplikan berita segar</description></item>' .
                '</channel></rss>',
                200
            ),
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'is_duplicate' => false,
                                        'is_relevant' => true,
                                        'clean_title' => 'Peningkatan Fasilitas Ramah Lansia dan Difabel Sleman',
                                        'summary' => 'Ringkasan padat edukasi dari AI.',
                                        'content' => "Paragraf 1: Fakta lapangan.\n\nParagraf 2: Edukasi hak disabilitas.",
                                        'category' => 'Aksesibilitas',
                                        'detected_facilities' => ['Ramp', 'Guiding Block'],
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $scraper = app(NewsScraperService::class);
        $result = $scraper->scrapeLatest(1);

        $this->assertGreaterThan(0, $result['added']);
        $this->assertGreaterThan(0, $result['ai_processed_count']);

        $article = Article::where('title', 'Peningkatan Fasilitas Ramah Lansia dan Difabel Sleman')->first();
        $this->assertNotNull($article);
        $this->assertTrue($article->ai_processed);
        $this->assertEquals(['Ramp', 'Guiding Block'], $article->detected_facilities);
        $this->assertEquals(Article::STATUS_PENDING, $article->status);
        $this->assertFalse($article->is_published);
    }
}
