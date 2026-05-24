<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Validation;
use App\Models\User;
use App\Services\MapLocationService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
  public function __construct(private readonly MapLocationService $mapService) {}

  public function index(): View
  {
    $totalReports = Validation::count();
    $pendingCount = Validation::where('approval_status', 'pending')->count();
    $approvedCount = Validation::where('approval_status', 'approved')->count();
    $rejectedCount = Validation::where('approval_status', 'rejected')->count();
    $totalUsers = User::where('role', 'user')->count();

    $chartData = $this->getMonthlyReportData();
    $recentLocations = $this->mapService->getPublished()->take(5);
    $recentReports = Validation::with(['report.user', 'report.details.category'])->latest()->take(5)->get();

    $mapPoints = $this->mapService->getPublished()->map(function ($location) {
      return [
        'lat' => (float) $location->latitude,
        'lng' => (float) $location->longitude,
        'label' => $location->validation?->report?->user?->full_name ?? 'Lokasi Publik',
      ];
    })->values();

    return view('admin.dashboard.index', [
      'totalReports' => $totalReports,
      'pendingCount' => $pendingCount,
      'approvedCount' => $approvedCount,
      'rejectedCount' => $rejectedCount,
      'totalUsers' => $totalUsers,
      'chartData' => $chartData,
      'recentLocations' => $recentLocations,
      'recentReports' => $recentReports,
      'mapPoints' => $mapPoints,
    ]);
  }

  private function getMonthlyReportData(): array
  {
    $start = Carbon::now()->startOfMonth()->subMonths(11);
    $end = Carbon::now()->endOfMonth();

    $rawData = Validation::query()
      ->whereBetween('created_at', [$start, $end])
      ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
      ->groupBy('month')
      ->orderBy('month')
      ->pluck('total', 'month');

    $labels = [];
    $values = [];
    $cursor = $start->copy();

    while ($cursor <= $end) {
      $key = $cursor->format('Y-m');
      $labels[] = $cursor->format('M Y');
      $values[] = (int) ($rawData[$key] ?? 0);
      $cursor->addMonth();
    }

    return [
      'labels' => $labels,
      'values' => $values,
    ];
  }
}
