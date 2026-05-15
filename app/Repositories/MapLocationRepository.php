<?php

namespace App\Repositories;

use App\Models\MapLocation;
use App\Models\Report;
use App\Models\Validation;
use App\Repositories\Contracts\MapLocationRepositoryInterface;
use Illuminate\Support\Collection;

class MapLocationRepository implements MapLocationRepositoryInterface
{
  public function createFromReport(Validation $validation, Report $report): MapLocation
  {
    return MapLocation::create([
      'validation_id' => $validation->id,
      'latitude' => $report->latitude,
      'longitude' => $report->longitude,
      'publish_date' => now(),
    ]);
  }

  public function getAllPublished(): Collection
  {
    return MapLocation::with([
      'validation.user',
      'validation.report.user',
      'validation.report.details.category',
    ])
      ->whereNotNull('publish_date')
      ->get();
  }

  public function findWithDetails(int $id): ?MapLocation
  {
    return MapLocation::with([
      'validation.user',
      'validation.report.user',
      'validation.report.details.category',
    ])->find($id);
  }

  public function delete(int $id): bool
  {
    return (bool) MapLocation::whereKey($id)->delete();
  }
}
