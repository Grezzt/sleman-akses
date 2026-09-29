<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ArticleService
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articles,
        private readonly NewsScraperService $scraper
    ) {}

    public function getPublishedArticles(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        try {
            // Strictly fetch approved and published articles only. Zero dummy fallbacks!
            return $this->articles->paginate($filters, $perPage);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal mengambil daftar berita: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getArticleDetail(string|int $identifier): Article
    {
        $article = $this->articles->findBySlugOrId($identifier);

        if (!$article) {
            throw new RuntimeException('Berita tidak ditemukan.');
        }

        return $article;
    }

    public function getAdminArticles(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->articles->paginateAdmin($filters, $perPage);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal mengambil data berita admin: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getById(int $id): Article
    {
        $article = $this->articles->find($id);

        if (!$article) {
            throw new RuntimeException('Berita tidak ditemukan.');
        }

        return $article;
    }

    public function createArticle(array $data): Article
    {
        try {
            if (empty($data['slug']) && !empty($data['title'])) {
                $data['slug'] = Article::generateUniqueSlug($data['title']);
            }

            if (empty($data['summary']) && !empty($data['content'])) {
                $data['summary'] = Str::limit(strip_tags($data['content']), 200);
            }

            $isPublished = (bool) ($data['is_published'] ?? true);
            $data['is_published'] = $isPublished;
            $data['status'] = $isPublished ? Article::STATUS_PUBLISHED : Article::STATUS_PENDING;

            if ($isPublished && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            return $this->articles->create($data);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal menyimpan berita baru: ' . $e->getMessage(), 0, $e);
        }
    }

    public function updateArticle(int $id, array $data): Article
    {
        $article = $this->getById($id);

        try {
            if (!empty($data['title']) && empty($data['slug'])) {
                $data['slug'] = Article::generateUniqueSlug($data['title'], $id);
            }

            if (isset($data['is_published'])) {
                $isPublished = (bool) $data['is_published'];
                $data['is_published'] = $isPublished;
                $data['status'] = $isPublished ? Article::STATUS_PUBLISHED : Article::STATUS_PENDING;

                if ($isPublished && empty($article->published_at)) {
                    $data['published_at'] = now();
                }
            }

            return $this->articles->update($article, $data);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal memperbarui berita: ' . $e->getMessage(), 0, $e);
        }
    }

    public function deleteArticle(int $id): bool
    {
        $this->getById($id);

        try {
            return $this->articles->delete($id);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal menghapus berita: ' . $e->getMessage(), 0, $e);
        }
    }

    public function togglePublishStatus(int $id): Article
    {
        try {
            return $this->articles->toggleStatus($id);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal mengubah status publikasi berita: ' . $e->getMessage(), 0, $e);
        }
    }

    public function approveArticle(int $id): Article
    {
        try {
            return $this->articles->approve($id);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal menyetujui artikel berita: ' . $e->getMessage(), 0, $e);
        }
    }

    public function rejectArticle(int $id, ?string $reason = null): Article
    {
        try {
            return $this->articles->reject($id, $reason);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal menolak artikel berita: ' . $e->getMessage(), 0, $e);
        }
    }

    public function bulkApprove(array $ids): int
    {
        try {
            return $this->articles->bulkApprove($ids);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal menyetujui artikel secara massal: ' . $e->getMessage(), 0, $e);
        }
    }

    public function bulkReject(array $ids, ?string $reason = null): int
    {
        try {
            return $this->articles->bulkReject($ids, $reason);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal menolak artikel secara massal: ' . $e->getMessage(), 0, $e);
        }
    }

    public function bulkDelete(array $ids): int
    {
        try {
            return $this->articles->bulkDelete($ids);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal menghapus artikel secara massal: ' . $e->getMessage(), 0, $e);
        }
    }

    public function recordView(Article $article): void
    {
        try {
            $this->articles->incrementViews($article->id);
            $article->views_count++;
        } catch (Throwable) {
            // Silently ignore tracking exceptions so content viewing is never interrupted
        }
    }

    public function scrapeViaBot(
        int $limit = 10,
        string $timeRange = 'month',
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        return $this->scraper->scrapeLatest($limit, useAi: false, timeRange: $timeRange, startDate: $startDate, endDate: $endDate);
    }

    public function scrapeViaAi(
        int $limit = 10,
        string $timeRange = 'month',
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        return $this->scraper->scrapeLatest($limit, useAi: true, timeRange: $timeRange, startDate: $startDate, endDate: $endDate);
    }

    public function triggerScraping(): array
    {
        return $this->scrapeViaAi(10);
    }
}
