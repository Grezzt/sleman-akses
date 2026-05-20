<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $validation = $this->validation;
    $validationStatus = $validation?->approval_status ?? 'pending';
    $mapLocation = $validation?->mapLocation;

    return [
      'report_id' => $this->id,
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,
      'photo_url' => $this->photo_url ? explode(',', $this->photo_url)[0] : null,
      'photo_urls' => $this->photo_url ? explode(',', $this->photo_url) : [],
      'timestamp' => $this->timestamp,
      'validation_status' => $validationStatus,
      'map_location' => $mapLocation ? [
        'location_id' => $mapLocation->id,
        'place_name' => $mapLocation->place_name,
        'address' => $mapLocation->address,
        'status' => $mapLocation->status,
        'facility_type' => $mapLocation->facility_type,
        'latitude' => $mapLocation->latitude,
        'longitude' => $mapLocation->longitude,
        'publish_date' => $mapLocation->publish_date,
      ] : null,
      'details' => $this->details->map(function ($detail) {
        return [
          'category' => $detail->category?->facility_name,
          'icon_marker' => $detail->category?->icon_marker,
          'availability_status' => $detail->availability_status,
        ];
      })->values(),
    ];
  }
}
