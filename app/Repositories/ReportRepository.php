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
    return Report::with(['user', 'details.category', 'validation.mapLocation'])->find($id);
  }

  public function findByUuid(string $uuid): ?Report
  {
    return Report::with(['user', 'details.category', 'validation.mapLocation'])->where('uuid', $uuid)->first();
  }

  public function getByUserPaginated(int $userId, int $perPage = 10): LengthAwarePaginator
  {
    return Report::with(['user', 'details.category', 'validation.mapLocation'])
      ->where('user_id', $userId)
      ->orderByDesc('timestamp')
      ->paginate($perPage);
  }

  public function getStatsByUser(int $userId): array
  {
    $reports = Report::with('validation')
      ->where('user_id', $userId)
      ->get();

    return [
      'total_dikirim' => $reports->count(),
      'total_disetujui' => $reports->filter(fn($r) => $r->validation?->approval_status === 'approved')->count(),
      'total_ditolak' => $reports->filter(fn($r) => $r->validation?->approval_status === 'rejected')->count(),
    ];
  }

  public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
  {
    if (!empty($filters['per_page'])) {
      $perPage = (int) $filters['per_page'];
    }

    $query = Report::query()->with(['user', 'details.category', 'validation.mapLocation']);

    if (!empty($filters['user_id'])) {
      $query->where('user_id', $filters['user_id']);
    }

    if (!empty($filters['status'])) {
      $query->whereHas('validation', function ($builder) use ($filters) {
        $builder->where('approval_status', $filters['status']);
      });
    }

    if (!empty($filters['category_id'])) {
      $query->whereHas('details', function ($builder) use ($filters) {
        $builder->where('category_id', $filters['category_id'])
                ->where('availability_status', true);
      });
    }

    return $query->orderByDesc('timestamp')->paginate($perPage);
  }
}
