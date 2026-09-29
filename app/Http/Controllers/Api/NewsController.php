<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class NewsController extends Controller
{
    public function __construct(private readonly ArticleService $articles) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage <= 0 || $perPage > 50) {
            $perPage = 10;
        }

        $filters = [
            'category' => $request->get('category'),
            'search' => $request->get('search'),
        ];

        try {
            $paginated = $this->articles->getPublishedArticles($filters, $perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data berita berhasil diambil.',
                'data' => ArticleResource::collection($paginated->items()),
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat berita: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $article = $this->articles->getArticleDetail($id);
            $article->increment('views_count');

            return response()->json([
                'success' => true,
                'message' => 'Detail berita berhasil diambil.',
                'data' => new ArticleResource($article),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Berita tidak ditemukan.',
            ], 404);
        }
    }
}
