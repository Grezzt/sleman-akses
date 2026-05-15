<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MapLocationResource;
use App\Services\MapLocationService;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
  public function __construct(private readonly MapLocationService $maps) {}

  public function index(): JsonResponse
  {
    $locations = $this->maps->getPublished();

    return response()->json([
      'success' => true,
      'message' => 'Berhasil mengambil data peta.',
      'data' => MapLocationResource::collection($locations),
    ]);
  }

  public function show(int $id): JsonResponse
  {
    $location = $this->maps->getDetail($id);

    return response()->json([
      'success' => true,
      'message' => 'Berhasil mengambil detail lokasi.',
      'data' => new MapLocationResource($location),
    ]);
  }
}
