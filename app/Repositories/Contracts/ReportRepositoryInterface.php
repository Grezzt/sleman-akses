<?php

namespace App\Repositories\Contracts;

use App\Models\Report;
use App\Models\ReportDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReportRepositoryInterface
{
  public function create(array $data): Report;

  public function addDetail(Report $report, int $categoryId, bool $status): ReportDetail;

  public function findWithRelations(int $id): ?Report;

  public function getByUserPaginated(int $userId, int $perPage = 10): LengthAwarePaginator;

  public function getStatsByUser(int $userId): array;

  public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
