<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\PhotoUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
  public function __construct(private readonly PhotoUploadService $photos) {}

  public function index(Request $request): JsonResponse
  {
    $user = Auth::user();

    $items = Report::query()
      ->where('user_id', $user->id)
      ->whereNotNull('photo_url')
      ->orderBy('created_at', 'desc')
      ->get(['id as report_id', 'photo_url', 'timestamp']);

    return response()->json([
      'success' => true,
      'message' => 'Daftar foto berhasil diambil.',
      'data' => $items,
    ]);
  }

  public function destroy(int $id): JsonResponse
  {
    $user = Auth::user();

    $report = Report::where('id', $id)->where('user_id', $user->id)->first();

    if (!$report) {
      return response()->json([
        'success' => false,
        'message' => 'Laporan tidak ditemukan.',
      ], 404);
    }

    if (empty($report->photo_url)) {
      return response()->json([
        'success' => false,
        'message' => 'Foto tidak ditemukan pada laporan ini.',
      ], 400);
    }

    $this->photos->delete($report->photo_url);

    $report->photo_url = null;
    $report->save();

    return response()->json([
      'success' => true,
      'message' => 'Foto berhasil dihapus dari Cloudinary dan laporan diperbarui.',
    ]);
  }
}
