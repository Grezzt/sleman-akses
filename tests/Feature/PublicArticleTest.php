<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicArticleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_can_view_published_articles_index(): void
    {
        $article = Article::create([
            'title' => 'Panduan Aksesibilitas Publik di Kabupaten Sleman',
            'slug' => 'panduan-aksesibilitas-publik-sleman',
            'summary' => 'Ringkasan mengenai standar fasilitas ramah disabilitas di fasilitas umum Sleman.',
            'content' => 'Isi artikel edukasi tentang fasilitas difabel di Sleman secara komprehensif.',
            'category' => 'Aksesibilitas',
            'detected_facilities' => ['Ramp', 'Toilet Disabilitas'],
            'image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800',
            'author_name' => 'Tim Sleman Akses',
            'is_scraped' => false,
            'ai_processed' => false,
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.articles.index'));

        $response->assertStatus(200)
            ->assertSee('Kabar & Edukasi Aksesibilitas')
            ->assertSee($article->title)
            ->assertSee('Aksesibilitas')
            ->assertSee('Ramp');
    }

    public function test_pending_and_rejected_articles_never_appear_on_public_index(): void
    {
        $pendingArticle = Article::create([
            'title' => 'Berita Dalam Antrean Kurasi Rahasia',
            'slug' => 'berita-dalam-antrean-kurasi-rahasia',
            'summary' => 'Berita ini belum di ACC oleh admin.',
            'content' => 'Konten rahasia belum terverifikasi.',
            'category' => 'Aksesibilitas',
            'is_published' => false,
            'status' => Article::STATUS_PENDING,
        ]);

        $rejectedArticle = Article::create([
            'title' => 'Berita Yang Telah Ditolak Admin',
            'slug' => 'berita-yang-telah-ditolak-admin',
            'summary' => 'Berita ini tidak lolos verifikasi.',
            'content' => 'Konten tidak memenuhi kriteria.',
            'category' => 'Aksesibilitas',
            'is_published' => false,
            'status' => Article::STATUS_REJECTED,
            'rejection_reason' => 'Tidak valid',
        ]);

        $response = $this->get(route('public.articles.index'));

        $response->assertStatus(200)
            ->assertDontSee($pendingArticle->title)
            ->assertDontSee($rejectedArticle->title);
    }

    public function test_public_can_filter_articles_by_category(): void
    {
        $articleAkses = Article::create([
            'title' => 'Edukasi Khusus Jalur Ramp Kursi Roda',
            'slug' => 'edukasi-khusus-jalur-ramp-kursi-roda',
            'summary' => 'Ringkasan tentang ramp.',
            'content' => 'Konten tentang ramp kursi roda.',
            'category' => 'Aksesibilitas',
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $articleFasilitas = Article::create([
            'title' => 'Revitalisasi Fasilitas Gedung Serbaguna',
            'slug' => 'revitalisasi-fasilitas-gedung-serbaguna',
            'summary' => 'Ringkasan fasilitas.',
            'content' => 'Konten gedung serbaguna.',
            'category' => 'Fasilitas Publik',
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.articles.index', ['category' => 'Aksesibilitas']));

        $response->assertStatus(200)
            ->assertSee($articleAkses->title)
            ->assertDontSee($articleFasilitas->title);
    }

    public function test_public_can_search_articles(): void
    {
        $articleTarget = Article::create([
            'title' => 'Peningkatan Trotoar Inklusif Kaliurang',
            'slug' => 'peningkatan-trotoar-inklusif-kaliurang',
            'summary' => 'Trotoar di jalan kaliurang diperbaiki.',
            'content' => 'Proyek pemkab sleman di kaliurang.',
            'category' => 'Aksesibilitas',
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $articleOther = Article::create([
            'title' => 'Pelatihan Bahasa Isyarat Komunitas',
            'slug' => 'pelatihan-bahasa-isyarat-komunitas',
            'summary' => 'Pelatihan inklusif.',
            'content' => 'Acara bahasa isyarat di sleman.',
            'category' => 'Edukasi & Kesadaran',
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.articles.index', ['search' => 'Kaliurang']));

        $response->assertStatus(200)
            ->assertSee($articleTarget->title)
            ->assertDontSee($articleOther->title);
    }

    public function test_public_can_view_published_article_detail_by_slug(): void
    {
        $article = Article::create([
            'title' => 'Standar Guiding Block Tunanetra Terverifikasi',
            'slug' => 'standar-guiding-block-tunanetra-terverifikasi',
            'summary' => 'Penjelasan mengenai garis pemandu kuning.',
            'content' => 'Guiding block sangat penting bagi teman netra yang melintasi pedestrian.',
            'category' => 'Regulasi & Kebijakan',
            'detected_facilities' => ['Guiding Block'],
            'image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800',
            'author_name' => 'Dinas Perhubungan Sleman',
            'source_name' => 'Pemkab Sleman',
            'source_url' => 'https://slemankab.go.id/berita-guiding-block',
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.articles.show', $article->slug));

        $response->assertStatus(200)
            ->assertSee($article->title)
            ->assertSee('Guiding block sangat penting bagi teman netra')
            ->assertSee('Dinas Perhubungan Sleman')
            ->assertSee('Pemkab Sleman')
            ->assertSee('Kunjungi Sumber Asli');
    }

    public function test_pending_article_detail_returns_404_on_public(): void
    {
        $pendingArticle = Article::create([
            'title' => 'Artikel Pending Yang Belum Siap Terbit',
            'slug' => 'artikel-pending-yang-belum-siap-terbit',
            'summary' => 'Masih dalam proses kurasi.',
            'content' => 'Belum boleh dibaca oleh publik.',
            'category' => 'Aksesibilitas',
            'is_published' => false,
            'status' => Article::STATUS_PENDING,
        ]);

        $response = $this->get(route('public.articles.show', $pendingArticle->slug));

        $response->assertStatus(404);
    }

    public function test_rejected_article_detail_returns_404_on_public(): void
    {
        $rejectedArticle = Article::create([
            'title' => 'Artikel Yang Ditolak Redaksi',
            'slug' => 'artikel-yang-ditolak-redaksi',
            'summary' => 'Ditolak karena tidak sesuai.',
            'content' => 'Tidak valid.',
            'category' => 'Aksesibilitas',
            'is_published' => false,
            'status' => Article::STATUS_REJECTED,
            'rejection_reason' => 'Data tidak akurat',
        ]);

        $response = $this->get(route('public.articles.show', $rejectedArticle->slug));

        $response->assertStatus(404);
    }

    public function test_landing_page_displays_latest_published_articles(): void
    {
        $article = Article::create([
            'title' => 'Kabar Terkini Aksesibilitas Sleman 2026',
            'slug' => 'kabar-terkini-aksesibilitas-sleman-2026',
            'summary' => 'Pemkab Sleman terus meningkatkan fasilitas publik inklusif.',
            'content' => 'Berita terkini pembangunan fasilitas difabel.',
            'category' => 'Fasilitas Publik',
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.landing'));

        $response->assertStatus(200)
            ->assertSee('Kabar Aksesibilitas Terkini')
            ->assertSee($article->title);
    }

    public function test_article_view_count_increments_on_first_visit(): void
    {
        $article = Article::create([
            'title' => 'Edukasi Trotoar Ramah Kursi Roda ' . uniqid(),
            'category' => 'Aksesibilitas',
            'summary' => 'Ringkasan trotoar',
            'content' => 'Konten trotoar',
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'views_count' => 0,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.articles.show', $article->slug));

        $response->assertStatus(200);
        $this->assertEquals(1, $article->fresh()->views_count);
    }

    public function test_article_view_count_does_not_increment_on_repeat_visit_in_same_session(): void
    {
        $article = Article::create([
            'title' => 'Edukasi Guiding Block Difabel ' . uniqid(),
            'category' => 'Aksesibilitas',
            'summary' => 'Ringkasan guiding block',
            'content' => 'Konten guiding block',
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'views_count' => 5,
            'published_at' => now(),
        ]);

        // Visit with session key already set (refresh simulation)
        $response = $this->withSession(['viewed_article_' . $article->id => true])
            ->get(route('public.articles.show', $article->slug));

        $response->assertStatus(200);
        $this->assertEquals(5, $article->fresh()->views_count);
    }

    public function test_detail_page_renders_tts_toolbar_and_font_resizer(): void
    {
        $article = Article::create([
            'title' => 'Edukasi Hak Aksesibilitas Sleman ' . uniqid(),
            'category' => 'Edukasi & Kesadaran',
            'summary' => 'Ringkasan edukasi hak',
            'content' => 'Konten hak aksesibilitas difabel.',
            'is_published' => true,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.articles.show', $article->slug));

        $response->assertStatus(200)
            ->assertSee('accessibilityToolbar')
            ->assertSee('Audio Reader (TTS)')
            ->assertSee('Dengarkan')
            ->assertSee('Ukuran Teks:');
    }
}
