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

  public function findWithRelations(int $id): Report
  {
    return Report::with(['user', 'details.category', 'validation'])->findOrFail($id);
  }

  public function getAllPaginated(array $filters): LengthAwarePaginator
  {
    $perPage = $filters['per_page'] ?? 15;

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
