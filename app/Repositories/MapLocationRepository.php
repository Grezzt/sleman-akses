<?php

namespace App\Repositories;

use App\Models\MapLocation;
use App\Models\Report;
use App\Models\Validation;
use App\Repositories\Contracts\MapLocationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MapLocationRepository implements MapLocationRepositoryInterface
{
  public function upsertFromReport(Validation $validation, Report $report, array $mapData): MapLocation
  {
    return MapLocation::updateOrCreate(
      ['validation_id' => $validation->id],
      [
        'place_name' => $mapData['place_name'],
        'address' => $mapData['address'],
        'status' => $mapData['status'],
        'facility_type' => $mapData['facility_type'],
        'latitude' => $report->latitude,
        'longitude' => $report->longitude,
        'publish_date' => now(),
      ]
    );
  }

  public function deleteByValidationId(int $validationId): bool
  {
    return (bool) MapLocation::where('validation_id', $validationId)->delete();
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

  public function getPublishedPaginated(int $perPage = 10): LengthAwarePaginator
  {
    return MapLocation::with([
      'validation.user',
      'validation.report.user',
      'validation.report.details.category',
    ])
      ->whereNotNull('publish_date')
      ->latest('publish_date')
      ->paginate($perPage);
  }

  public function updateStatus(int $id, string $status): bool
  {
    return (bool) MapLocation::whereKey($id)->update(['status' => $status]);
  }

  public function delete(int $id): bool
  {
    return (bool) MapLocation::whereKey($id)->delete();
  }
}
