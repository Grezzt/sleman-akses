<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
  public function __construct(private readonly CategoryService $categories) {}

  public function index(): JsonResponse
  {
    $categories = $this->categories->getAll();

    return response()->json([
      'success' => true,
      'message' => 'Berhasil mengambil kategori.',
      'data' => CategoryResource::collection($categories),
    ]);
  }
}
