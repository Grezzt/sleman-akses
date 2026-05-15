<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapLocationResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $report = $this->validation?->report;
    $reporter = $report?->user;

    return [
      'location_id' => $this->id,
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,
      'publish_date' => $this->publish_date,
      'photo_url' => $report?->photo_url,
      'reported_by' => $reporter?->full_name,
      'facilities' => $report?->details?->map(function ($detail) {
        return [
          'category' => $detail->category?->facility_name,
          'icon_marker' => $detail->category?->icon_marker,
          'available' => $detail->availability_status,
        ];
      })->values() ?? [],
    ];
  }
}
