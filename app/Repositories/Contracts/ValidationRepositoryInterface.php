<?php

namespace App\Repositories\Contracts;

use App\Models\Report;
use App\Models\Validation;

interface ValidationRepositoryInterface
{
  public function initPending(Report $report): Validation;

  public function update(Validation $validation, array $data): Validation;

  public function findByReport(int $reportId): ?Validation;
}
