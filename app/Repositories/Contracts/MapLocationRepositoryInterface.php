<?php

namespace App\Repositories\Contracts;

use App\Models\MapLocation;
use App\Models\Report;
use App\Models\Validation;
use Illuminate\Support\Collection;

interface MapLocationRepositoryInterface
{
  public function createFromReport(Validation $validation, Report $report): MapLocation;

  public function getAllPublished(): Collection;

  public function delete(int $id): void;
}
