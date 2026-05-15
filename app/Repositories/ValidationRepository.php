<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\Validation;
use App\Repositories\Contracts\ValidationRepositoryInterface;

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
}
