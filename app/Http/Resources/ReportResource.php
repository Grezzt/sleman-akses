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
    $validationStatus = $this->validation?->approval_status ?? 'pending';

    return [
      'report_id' => $this->id,
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,
      'photo_url' => $this->photo_url,
      'timestamp' => $this->timestamp,
      'validation_status' => $validationStatus,
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
