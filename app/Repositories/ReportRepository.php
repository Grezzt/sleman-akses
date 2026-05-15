<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\ReportDetail;
use App\Repositories\Contracts\ReportRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReportRepository implements ReportRepositoryInterface
{
  public function create(array $data): Report
  {
    return Report::create($data);
  }

  public function addDetail(Report $report, int $categoryId, bool $status): ReportDetail
  {
    return ReportDetail::create([
      'report_id' => $report->id,
      'category_id' => $categoryId,
      'availability_status' => $status,
    ]);
  }

  public function findWithRelations(int $id): ?Report
  {
    return Report::with(['user', 'details.category', 'validation'])->find($id);
  }

  public function getByUserPaginated(int $userId, int $perPage = 10): LengthAwarePaginator
  {
    return Report::with(['user', 'details.category', 'validation'])
      ->where('user_id', $userId)
      ->orderByDesc('timestamp')
      ->paginate($perPage);
  }

  public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
  {
    if (!empty($filters['per_page'])) {
      $perPage = (int) $filters['per_page'];
    }

    $query = Report::query()->with(['user', 'details.category', 'validation']);

    if (!empty($filters['user_id'])) {
      $query->where('user_id', $filters['user_id']);
    }

    if (!empty($filters['status'])) {
      $query->whereHas('validation', function ($builder) use ($filters) {
        $builder->where('approval_status', $filters['status']);
      });
    }

    return $query->orderByDesc('timestamp')->paginate($perPage);
  }
}
