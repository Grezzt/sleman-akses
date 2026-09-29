<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        // Public & Mobile API ONLY receives approved and published articles
        $query = Article::query()->where('status', Article::STATUS_PUBLISHED)
                                 ->where('is_published', true);

        if (!empty($filters['category']) && $filters['category'] !== 'Semua') {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('published_at')
                     ->orderByDesc('created_at')
                     ->paginate($perPage);
    }

    public function paginateAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Article::query();

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category']) && $filters['category'] !== 'Semua') {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['is_published']) && $filters['is_published'] !== '') {
            $query->where('is_published', (bool) $filters['is_published']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%")
                  ->orWhere('source_name', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function find(int $id): ?Article
    {
        return Article::find($id);
    }

    public function findBySlugOrId(string|int $identifier): ?Article
    {
        $query = Article::query()->where('status', Article::STATUS_PUBLISHED)
                                 ->where('is_published', true);

        if (is_numeric($identifier)) {
            return $query->where('id', $identifier)->first();
        }

        return $query->where('slug', $identifier)->first();
    }

    public function create(array $data): Article
    {
        return Article::create($data);
    }

    public function update(Article $article, array $data): Article
    {
        $article->fill($data);
        $article->save();

        return $article;
    }

    public function delete(int $id): bool
    {
        return (bool) Article::whereKey($id)->delete();
    }

    public function toggleStatus(int $id): Article
    {
        $article = Article::findOrFail($id);
        $article->is_published = !$article->is_published;
        if ($article->is_published) {
            $article->status = Article::STATUS_PUBLISHED;
            if (empty($article->published_at)) {
                $article->published_at = now();
            }
        } else {
            $article->status = Article::STATUS_PENDING;
        }
        $article->save();

        return $article;
    }

    public function approve(int $id): Article
    {
        $article = Article::findOrFail($id);
        $article->status = Article::STATUS_PUBLISHED;
        $article->is_published = true;
        $article->published_at = now();
        $article->rejection_reason = null;
        $article->save();

        return $article;
    }

    public function reject(int $id, ?string $reason = null): Article
    {
        $article = Article::findOrFail($id);
        $article->status = Article::STATUS_REJECTED;
        $article->is_published = false;
        $article->rejection_reason = $reason;
        $article->save();

        return $article;
    }

    public function bulkApprove(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        return Article::whereIn('id', $ids)->update([
            'status' => Article::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
            'rejection_reason' => null,
            'updated_at' => now(),
        ]);
    }

    public function bulkReject(array $ids, ?string $reason = null): int
    {
        if (empty($ids)) {
            return 0;
        }

        return Article::whereIn('id', $ids)->update([
            'status' => Article::STATUS_REJECTED,
            'is_published' => false,
            'rejection_reason' => $reason,
            'updated_at' => now(),
        ]);
    }

    public function bulkDelete(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        return Article::whereIn('id', $ids)->delete();
    }

    public function incrementViews(int $id): bool
    {
        return (bool) Article::whereKey($id)->increment('views_count');
    }
}
