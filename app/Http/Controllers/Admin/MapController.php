<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MapLocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
  public function __construct(private readonly MapLocationService $mapService) {}

  public function index(Request $request): View
  {
    $locations = $this->mapService->getPublishedPaginated($request->get('per_page', 5));
    $allLocations = $this->mapService->getPublished();

    $markers = $allLocations->map(function ($location) {
      $report = $location->validation?->report;
      $primaryCategory = $report?->details->first()?->category;
      $primaryCategoryName = $primaryCategory?->facility_name ?? 'LOKASI';
      
      $primaryNameStr = strtolower($primaryCategoryName);
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

      $facilities = $report ? $report->details->map(function ($detail) {
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
              'name' => $category?->facility_name ?? 'Fasilitas',
              'icon_url' => asset($iconFile),
              'available' => (bool) $detail->availability_status,
          ];
      })->values()->toArray() : [];

      $photoUrls = $report?->photo_url ? explode(',', $report->photo_url) : [];
      $fullPhotoUrls = array_map(function ($path) {
          return asset(trim($path));
      }, $photoUrls);

      return [
        'id' => $location->id,
        'lat' => (float) $location->latitude,
        'lng' => (float) $location->longitude,
        'place_name' => $location->place_name,
        'address' => $location->address,
        'reporter_name' => $report?->user?->full_name ?? 'Anonim',
        'primary_category' => strtoupper($primaryCategoryName),
        'status' => strtoupper($location->status),
        'raw_status' => $location->status,
        'facilities' => $facilities,
        'photo_url' => count($fullPhotoUrls) > 0 ? implode(',', $fullPhotoUrls) : null,
        'label' => $report?->user?->full_name ?? 'Lokasi Publik',
        'published' => $location->publish_date?->format('d M Y'),
        'icon_url' => asset($primaryIconFile),
      ];
    })->values();

    return view('admin.map.index', [
      'locations' => $locations,
      'markers' => $markers,
      'maptilerKey' => config('services.maptiler.key'),
    ]);
  }

  public function toggleStatus(Request $request, int $id)
  {
    try {
        $this->mapService->toggleStatus($id);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Status lokasi berhasil diubah.']);
        }
        return back()->with('success', 'Status lokasi berhasil diubah.');
    } catch (\Throwable $e) {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Gagal mengubah status lokasi.'], 500);
        }
        return back()->with('error', 'Gagal mengubah status lokasi.');
    }
  }

  public function destroy(int $id): RedirectResponse
  {
    $this->mapService->delete($id);

    return back()->with('success', 'Lokasi berhasil dihapus.');
  }
}
