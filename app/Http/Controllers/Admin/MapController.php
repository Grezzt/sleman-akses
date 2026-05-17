<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MapLocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MapController extends Controller
{
  public function __construct(private readonly MapLocationService $mapService) {}

  public function index(): View
  {
    $locations = $this->mapService->getPublished();

    $markers = $locations->map(function ($location) {
      $report = $location->validation?->report;

      return [
        'id' => $location->id,
        'lat' => (float) $location->latitude,
        'lng' => (float) $location->longitude,
        'label' => $report?->user?->name ?? 'Lokasi Publik',
        'published' => $location->publish_date?->format('d M Y'),
      ];
    })->values();

    return view('admin.map.index', [
      'locations' => $locations,
      'markers' => $markers,
      'maptilerKey' => config('services.maptiler.key'),
    ]);
  }

  public function destroy(int $id): RedirectResponse
  {
    $this->mapService->delete($id);

    return back()->with('success', 'Lokasi berhasil dihapus.');
  }
}
