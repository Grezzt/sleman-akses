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
      $availableDetail = $details->firstWhere('availability_status', true) ?? $details->first();
      $primaryCategory = $availableDetail?->category;

      $facilities = $details->map(function ($detail) {
        $category = $detail->category;
        $nameStr = strtolower($category?->facility_name ?? '');
        $iconFile = 'parkir-difabel.svg';
        
        if (\Illuminate\Support\Str::contains($nameStr, 'parkir')) {
            $iconFile = 'parkir-difabel.svg';
        } elseif (\Illuminate\Support\Str::contains($nameStr, ['toilet', 'kamar mandi'])) {
            $iconFile = 'toilet-difabel.svg';
        } elseif (\Illuminate\Support\Str::contains($nameStr, ['ramp', 'landai', 'kursi roda'])) {
            $iconFile = 'ramp.svg';
        } elseif (\Illuminate\Support\Str::contains($nameStr, ['lift', 'elevator'])) {
            $iconFile = 'lift.svg';
        } elseif (!empty($category->icon_marker) && \Illuminate\Support\Str::endsWith($category->icon_marker, '.svg')) {
            $iconFile = $category->icon_marker;
        }

        return [
          'name' => $category?->facility_name ?? 'Tidak diketahui',
          'available' => (bool) $detail->availability_status,
          'icon_url' => asset($iconFile),
        ];
      })->values();

      $categoryNames = $facilities
        ->filter(function ($fac) {
            return $fac['available'] === true;
        })
        ->pluck('name')
        ->filter()
        ->unique()
        ->values();

      $reporterName = $report?->user?->name ?? 'Anonim';
      
      $primaryNameStr = strtolower($primaryCategory?->facility_name ?? '');
      $primaryIconFile = 'parkir-difabel.svg';
      if (\Illuminate\Support\Str::contains($primaryNameStr, 'parkir')) {
          $primaryIconFile = 'parkir-difabel.svg';
      } elseif (\Illuminate\Support\Str::contains($primaryNameStr, ['toilet', 'kamar mandi'])) {
          $primaryIconFile = 'toilet-difabel.svg';
      } elseif (\Illuminate\Support\Str::contains($primaryNameStr, ['ramp', 'landai', 'kursi roda'])) {
          $primaryIconFile = 'ramp.svg';
      } elseif (\Illuminate\Support\Str::contains($primaryNameStr, ['lift', 'elevator'])) {
          $primaryIconFile = 'lift.svg';
      } elseif (!empty($primaryCategory->icon_marker) && \Illuminate\Support\Str::endsWith($primaryCategory->icon_marker, '.svg')) {
          $primaryIconFile = $primaryCategory->icon_marker;
      }

      return [
        'id' => $location->id,
        'lat' => (float) $location->latitude,
        'lng' => (float) $location->longitude,
        'place_name' => $location->place_name ?? 'Lokasi Publik',
        'address' => $location->address ?? 'Alamat belum tersedia',
        'photo_url' => $report?->photo_url,
        'categories' => $categoryNames,
        'primary_category' => $primaryCategory?->facility_name,
        'primary_icon' => $primaryCategory?->icon_marker,
        'primary_icon_url' => asset($primaryIconFile),
        'facilities' => $facilities,
        'published' => $location->publish_date?->format('d M Y'),
        'reporter_name' => $reporterName,
        'status' => $location->status ?? 'Aktif',
      ];
    })->values();

    $categoryList = $categories->map(function ($category) {
      $name = strtolower($category->facility_name ?? '');
      $iconFile = 'parkir-difabel.svg'; // fallback
      
      if (\Illuminate\Support\Str::contains($name, 'parkir')) {
          $iconFile = 'parkir-difabel.svg';
      } elseif (\Illuminate\Support\Str::contains($name, ['toilet', 'kamar mandi'])) {
          $iconFile = 'toilet-difabel.svg';
      } elseif (\Illuminate\Support\Str::contains($name, ['ramp', 'landai', 'kursi roda'])) {
          $iconFile = 'ramp.svg';
      } elseif (\Illuminate\Support\Str::contains($name, ['lift', 'elevator'])) {
          $iconFile = 'lift.svg';
      } elseif (!empty($category->icon_marker) && \Illuminate\Support\Str::endsWith($category->icon_marker, '.svg')) {
          $iconFile = $category->icon_marker;
      }
      
      return [
        'id' => $category->id,
        'name' => $category->facility_name,
        'icon_marker' => $category->icon_marker, // Keep for backward compatibility or FontAwesome fallback
        'icon_url' => asset($iconFile),
      ];
    })->values();

    return view('public.map', [
      'markers' => $markers,
      'categories' => $categoryList,
      'maptilerKey' => config('services.maptiler.key'),
    ]);
  }
}
