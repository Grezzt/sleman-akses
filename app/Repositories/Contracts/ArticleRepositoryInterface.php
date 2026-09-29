<?php

namespace App\Repositories\Contracts;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function paginateAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Article;

    public function findBySlugOrId(string|int $identifier): ?Article;

    public function create(array $data): Article;

    public function update(Article $article, array $data): Article;

    public function delete(int $id): bool;

    public function toggleStatus(int $id): Article;
    public function approve(int $id): Article;
    public function reject(int $id, ?string $reason = null): Article;
    public function bulkApprove(array $ids): int;
    public function bulkReject(array $ids, ?string $reason = null): int;
    public function bulkDelete(array $ids): int;
    public function incrementViews(int $id): bool;
}
