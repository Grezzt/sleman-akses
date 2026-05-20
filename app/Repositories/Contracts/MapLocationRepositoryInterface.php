<?php

namespace App\Repositories\Contracts;

use App\Models\MapLocation;
use App\Models\Report;
use App\Models\Validation;
use Illuminate\Support\Collection;

interface MapLocationRepositoryInterface
{
  public function upsertFromReport(Validation $validation, Report $report, array $mapData): MapLocation;

  public function deleteByValidationId(int $validationId): bool;

  public function getAllPublished(): Collection;

  public function findWithDetails(int $id): ?MapLocation;

  public function delete(int $id): bool;
}
