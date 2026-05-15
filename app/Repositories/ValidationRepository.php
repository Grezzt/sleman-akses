<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\Validation;
use App\Repositories\Contracts\ValidationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ValidationRepository implements ValidationRepositoryInterface
{
  public function initPending(Report $report): Validation
  {
    return Validation::create([
      'report_id' => $report->id,
      'approval_status' => 'pending',
    ]);
  }

  public function update(Validation $validation, array $data): Validation
  {
    $validation->fill($data);
    $validation->save();

    return $validation;
  }

  public function findByReport(int $reportId): ?Validation
  {
    return Validation::where('report_id', $reportId)->first();
  }

  public function getCountByStatus(): array
  {
    $rows = Validation::query()
      ->select('approval_status', DB::raw('count(*) as total'))
      ->groupBy('approval_status')
      ->pluck('total', 'approval_status')
      ->all();

    return [
      'pending' => (int) ($rows['pending'] ?? 0),
      'approved' => (int) ($rows['approved'] ?? 0),
      'rejected' => (int) ($rows['rejected'] ?? 0),
    ];
  }
}
