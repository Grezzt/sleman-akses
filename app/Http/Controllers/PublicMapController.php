<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\MapLocationService;
use Illuminate\View\View;

class PublicMapController extends Controller
{
  public function __construct(
    private readonly MapLocationService $mapService,
    private readonly CategoryService $categoryService
  ) {}

  public function index(): View
  {
    $locations = $this->mapService->getPublishedWithDetails();
    $categories = $this->categoryService->getAll();

    $markers = $locations->map(function ($location) {
      $report = $location->validation?->report;
      $details = $report?->details ?? collect();
      $primaryCategory = $details->first()?->category;

      $facilities = $details->map(function ($detail) {
        return [
          'name' => $detail->category?->facility_name ?? 'Tidak diketahui',
          'available' => (bool) $detail->availability_status,
        ];
      })->values();

      $categoryNames = $facilities
        ->pluck('name')
        ->filter()
        ->unique()
        ->values();

      return [
        'id' => $location->id,
        'lat' => (float) $location->latitude,
        'lng' => (float) $location->longitude,
        'photo_url' => $report?->photo_url,
        'categories' => $categoryNames,
        'primary_category' => $primaryCategory?->facility_name,
        'primary_icon' => $primaryCategory?->icon_marker,
        'facilities' => $facilities,
        'published' => $location->publish_date?->format('d M Y'),
      ];
    })->values();

    $categoryList = $categories->map(function ($category) {
      return [
        'id' => $category->id,
        'name' => $category->facility_name,
        'icon_marker' => $category->icon_marker,
      ];
    })->values();

    return view('public.map', [
      'markers' => $markers,
      'categories' => $categoryList,
      'maptilerKey' => config('services.maptiler.key'),
    ]);
  }
}
