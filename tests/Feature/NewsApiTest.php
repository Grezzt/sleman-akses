<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NewsApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_fetch_news_list(): void
    {
        Article::firstOrCreate(
            ['slug' => 'uji-coba-berita-resmi'],
            [
                'title' => 'Uji Coba Berita Aksesibilitas Resmi',
                'summary' => 'Ringkasan pengujian berita resmi.',
                'content' => 'Konten lengkap berita aksesibilitas resmi.',
                'category' => 'Aksesibilitas',
                'status' => Article::STATUS_PUBLISHED,
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        $response = $this->getJson('/api/v1/news');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'summary',
                        'category',
                        'image_url',
                        'author_name',
                        'published_at',
                        'reading_time_minutes',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);
    }

    public function test_can_fetch_news_detail(): void
    {
        $article = Article::firstOrCreate(
            ['slug' => 'uji-coba-detail-berita'],
            [
                'title' => 'Uji Coba Detail Berita Aksesibel',
                'summary' => 'Ringkasan detail berita.',
                'content' => 'Konten lengkap detail berita.',
                'category' => 'Aksesibilitas',
                'status' => Article::STATUS_PUBLISHED,
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        $response = $this->getJson('/api/v1/news/' . $article->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $article->id,
                    'title' => $article->title,
                ]
            ]);
    }
}
