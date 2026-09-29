<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminArticleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function createAdmin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin_test@sleman-akses.id'],
            [
                'full_name' => 'Admin Test',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    public function test_admin_can_view_articles_index_with_curation_tabs(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.articles.index'));

        $response->assertStatus(200)
            ->assertSee('Manajemen Berita & Kurasi Edukasi')
            ->assertSee('Antrean Kurasi')
            ->assertSee('Tarik via Bot')
            ->assertSee('Tarik via AI');
    }

    public function test_admin_can_create_article_manually(): void
    {
        $admin = $this->createAdmin();
        $title = 'Panduan Ramp Difabel Standar Nasional ' . uniqid();

        $response = $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => $title,
            'category' => 'Aksesibilitas',
            'summary' => 'Ringkasan panduan ramp difabel sesuai standar nasional.',
            'content' => 'Konten lengkap mengenai panduan teknis pembuatan ramp dengan kemiringan yang tepat.',
            'author_name' => 'Admin Test',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseHas('articles', [
            'title' => $title,
            'category' => 'Aksesibilitas',
            'status' => Article::STATUS_PUBLISHED,
            'is_published' => 1,
        ]);
    }

    public function test_admin_can_approve_pending_article(): void
    {
        $admin = $this->createAdmin();
        $article = Article::create([
            'title' => 'Berita Menunggu Persetujuan ' . uniqid(),
            'slug' => 'berita-menunggu-persetujuan-' . uniqid(),
            'summary' => 'Cuplikan berita menunggu ACC.',
            'content' => 'Konten lengkap berita kurasi.',
            'category' => 'Fasilitas Publik',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
        ]);

        $this->assertTrue($article->isPending());

        $response = $this->actingAs($admin)->post(route('admin.articles.approve', $article->id));

        $response->assertRedirect();
        $fresh = $article->fresh();
        $this->assertEquals(Article::STATUS_PUBLISHED, $fresh->status);
        $this->assertTrue($fresh->is_published);
        $this->assertNotNull($fresh->published_at);
    }

    public function test_admin_can_reject_pending_article(): void
    {
        $admin = $this->createAdmin();
        $article = Article::create([
            'title' => 'Berita Tidak Memenuhi Syarat ' . uniqid(),
            'slug' => 'berita-tidak-memenuhi-syarat-' . uniqid(),
            'summary' => 'Cuplikan berita akan ditolak.',
            'content' => 'Konten berita yang tidak relevan dengan fasilitas.',
            'category' => 'Edukasi & Kesadaran',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.articles.reject', $article->id), [
            'reason' => 'Tidak relevan dengan wilayah Sleman',
        ]);

        $response->assertRedirect();
        $fresh = $article->fresh();
        $this->assertEquals(Article::STATUS_REJECTED, $fresh->status);
        $this->assertFalse($fresh->is_published);
        $this->assertEquals('Tidak relevan dengan wilayah Sleman', $fresh->rejection_reason);
    }

    public function test_pending_article_never_appears_in_public_mobile_api(): void
    {
        $uniqueTitle = 'Berita Rahasia Belum Di-ACC ' . uniqid();
        Article::create([
            'title' => $uniqueTitle,
            'slug' => 'berita-rahasia-' . uniqid(),
            'summary' => 'Ringkasan rahasia.',
            'content' => 'Isi berita belum di-ACC.',
            'category' => 'Aksesibilitas',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
        ]);

        $response = $this->getJson('/api/v1/news');

        $response->assertStatus(200);
        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertNotContains($uniqueTitle, $titles);
    }

    public function test_admin_can_scrape_via_bot_with_time_range_today(): void
    {
        $admin = $this->createAdmin();

        Http::fake([
            'https://news.google.com/*' => Http::response(
                '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel>' .
                '<item><title>Berita Bot Test ' . uniqid() . ' - Media Cetak</title><link>https://news.google.com/test-bot-' . uniqid() . '</link><pubDate>' . now()->toRssString() . '</pubDate><description>Cuplikan info trotoar ramp difabel</description></item>' .
                '</channel></rss>',
                200
            ),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.articles.scrape_bot'), [
            'time_range' => 'today',
        ]);

        $response->assertRedirect(route('admin.articles.index', ['status' => 'pending']))
            ->assertSessionHas('success');
    }

    public function test_admin_can_scrape_via_ai_with_custom_time_range(): void
    {
        $admin = $this->createAdmin();

        Http::fake([
            'https://news.google.com/*' => Http::response(
                '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel>' .
                '<item><title>Berita AI Test ' . uniqid() . ' - Media Online</title><link>https://news.google.com/test-ai-' . uniqid() . '</link><pubDate>' . now()->toRssString() . '</pubDate><description>Cuplikan info fasilitas lift dan toilet</description></item>' .
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
                                        'clean_title' => 'Revitalisasi Fasilitas Difabel Uji AI',
                                        'summary' => 'Ringkasan edukasi AI.',
                                        'content' => "Paragraf 1: Berita terkini.\n\nParagraf 2: Edukasi fasilitas.",
                                        'category' => 'Aksesibilitas',
                                        'detected_facilities' => ['Toilet Difabel'],
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.articles.scrape_ai'), [
            'time_range' => 'custom',
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('admin.articles.index', ['status' => 'pending']))
            ->assertSessionHas('success');
    }

    public function test_admin_can_upload_body_image(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();

        $file = UploadedFile::fake()->image('body_photo.jpg', 600, 400);

        $response = $this->actingAs($admin)->postJson(route('admin.articles.upload_body_image'), [
            'image' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure(['url']);
    }

    public function test_admin_can_view_article_preview(): void
    {
        $admin = $this->createAdmin();

        $article = Article::create([
            'title' => 'Panduan Aksesibilitas Ramp Gedung Publik',
            'slug' => 'panduan-aksesibilitas-ramp-gedung-publik-' . uniqid(),
            'category' => 'Aksesibilitas',
            'summary' => 'Ringkasan panduan ramp difabel standar teknis.',
            'content' => 'Konten lengkap standar kemiringan ramp difabel 1 banding 12.',
            'author_name' => 'Dinas Sosial Sleman',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
            'detected_facilities' => ['Ramp', 'Toilet Difabel'],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.articles.show', $article->id));

        $response->assertStatus(200)
            ->assertSee('Panduan Aksesibilitas Ramp Gedung Publik')
            ->assertSee('Dinas Sosial Sleman')
            ->assertSee('Ringkasan panduan ramp difabel standar teknis.')
            ->assertSee('Konten lengkap standar kemiringan ramp difabel 1 banding 12.')
            ->assertSee('Ramp')
            ->assertSee('Toilet Difabel');
    }

    public function test_admin_can_bulk_approve_articles(): void
    {
        $admin = $this->createAdmin();

        $a1 = Article::create([
            'title' => 'Bulk Approve Test 1 ' . uniqid(),
            'category' => 'Aksesibilitas',
            'summary' => 'Ringkasan 1',
            'content' => 'Konten 1',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
        ]);

        $a2 = Article::create([
            'title' => 'Bulk Approve Test 2 ' . uniqid(),
            'category' => 'Regulasi & Kebijakan',
            'summary' => 'Ringkasan 2',
            'content' => 'Konten 2',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.articles.bulk_action'), [
            'action' => 'approve',
            'article_ids' => [$a1->id, $a2->id],
        ]);

        $response->assertRedirect();
        $this->assertEquals(Article::STATUS_PUBLISHED, $a1->fresh()->status);
        $this->assertTrue($a1->fresh()->is_published);
        $this->assertEquals(Article::STATUS_PUBLISHED, $a2->fresh()->status);
        $this->assertTrue($a2->fresh()->is_published);
    }

    public function test_admin_can_bulk_reject_articles(): void
    {
        $admin = $this->createAdmin();

        $a1 = Article::create([
            'title' => 'Bulk Reject Test 1 ' . uniqid(),
            'category' => 'Aksesibilitas',
            'summary' => 'Ringkasan 1',
            'content' => 'Konten 1',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
        ]);

        $a2 = Article::create([
            'title' => 'Bulk Reject Test 2 ' . uniqid(),
            'category' => 'Aksesibilitas',
            'summary' => 'Ringkasan 2',
            'content' => 'Konten 2',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.articles.bulk_action'), [
            'action' => 'reject',
            'article_ids' => [$a1->id, $a2->id],
            'rejection_reason' => 'Konten tidak memenuhi kurasi',
        ]);

        $response->assertRedirect();
        $this->assertEquals(Article::STATUS_REJECTED, $a1->fresh()->status);
        $this->assertFalse($a1->fresh()->is_published);
        $this->assertEquals('Konten tidak memenuhi kurasi', $a1->fresh()->rejection_reason);
        $this->assertEquals(Article::STATUS_REJECTED, $a2->fresh()->status);
        $this->assertFalse($a2->fresh()->is_published);
    }

    public function test_admin_can_bulk_delete_articles(): void
    {
        $admin = $this->createAdmin();

        $a1 = Article::create([
            'title' => 'Bulk Delete Test 1 ' . uniqid(),
            'category' => 'Aksesibilitas',
            'summary' => 'Ringkasan 1',
            'content' => 'Konten 1',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
        ]);

        $a2 = Article::create([
            'title' => 'Bulk Delete Test 2 ' . uniqid(),
            'category' => 'Aksesibilitas',
            'summary' => 'Ringkasan 2',
            'content' => 'Konten 2',
            'status' => Article::STATUS_PENDING,
            'is_published' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.articles.bulk_action'), [
            'action' => 'delete',
            'article_ids' => [$a1->id, $a2->id],
        ]);

        $response->assertRedirect();
        $this->assertNull(Article::find($a1->id));
        $this->assertNull(Article::find($a2->id));
    }
}
