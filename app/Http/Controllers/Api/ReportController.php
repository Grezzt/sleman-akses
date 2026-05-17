<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReportRequest;
use App\Http\Resources\ReportResource;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
  public function __construct(private readonly ReportService $reports) {}

  public function index(Request $request): JsonResponse
  {
    $user = Auth::user();
    $perPage = (int) $request->query('per_page', 10);

    $paginator = $this->reports->getUserReports($user->id, $perPage);

    return response()->json([
      'success' => true,
      'message' => 'Berhasil mengambil daftar laporan.',
      'data' => [
        'reports' => ReportResource::collection(collect($paginator->items())),
        'pagination' => [
          'current_page' => $paginator->currentPage(),
          'last_page' => $paginator->lastPage(),
          'per_page' => $paginator->perPage(),
          'total' => $paginator->total(),
        ],
      ],
    ]);
  }

  public function store(StoreReportRequest $request): JsonResponse
  {
    $user = Auth::user();
    $report = $this->reports->createReport(
      $request->validated(),
      $user->id,
      $request->file('photo')
    );

    return response()->json([
      'success' => true,
      'message' => 'Laporan berhasil dikirim.',
      'data' => new ReportResource($report->load(['details.category', 'validation'])),
    ], 201);
  }

  public function show(int $id): JsonResponse
  {
    $user = Auth::user();
    $report = $this->reports->getReportDetail($id, $user->id);

    return response()->json([
      'success' => true,
      'message' => 'Berhasil mengambil detail laporan.',
      'data' => new ReportResource($report),
    ]);
  }
}
