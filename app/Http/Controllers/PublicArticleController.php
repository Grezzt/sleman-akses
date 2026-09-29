<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PublicArticleController extends Controller
{
    private const CATEGORIES = [
        'Semua',
        'Aksesibilitas',
        'Edukasi & Kesadaran',
        'Regulasi & Kebijakan',
        'Fasilitas Publik',
    ];

    public function __construct(
        private readonly ArticleService $articleService
    ) {}

    /**
     * Display public educational articles catalog.
     */
    public function index(Request $request): View
    {
        $category = $request->query('category', 'Semua');
        if (!in_array($category, self::CATEGORIES, true)) {
            $category = 'Semua';
        }

        $search = $request->query('search');

        $filters = [
            'category' => $category,
            'search' => $search,
        ];

        // Fetch strictly published and approved articles
        $articles = $this->articleService->getPublishedArticles($filters, 9);

        // Highlight latest featured article if viewing first page without specific keyword search
        $featuredArticle = null;
        $items = $articles->items();
        if ($articles->currentPage() === 1 && empty($search) && $category === 'Semua' && !empty($items)) {
            $featuredArticle = $items[0] ?? null;
        }

        return view('public.articles.index', [
            'articles' => $articles,
            'featuredArticle' => $featuredArticle,
            'categories' => self::CATEGORIES,
            'activeCategory' => $category,
            'search' => $search,
        ]);
    }

    /**
     * Display single public educational article detail.
     */
    public function show(string $slug): View
    {
        try {
            $article = $this->articleService->getArticleDetail($slug);
        } catch (Throwable) {
            abort(404, 'Berita atau artikel edukasi tidak ditemukan atau belum dipublikasikan.');
        }

        // Strict guardrail: must be live & published
        if (!$article->isPublished()) {
            abort(404, 'Berita atau artikel edukasi belum dipublikasikan untuk publik.');
        }

        // Session-guarded view tracking to prevent refresh spam
        $sessionKey = 'viewed_article_' . $article->id;
        if (!session()->has($sessionKey)) {
            $this->articleService->recordView($article);
            session()->put($sessionKey, true);
        }

        // Fetch related educational articles
        $relatedArticles = Article::query()
            ->where('status', Article::STATUS_PUBLISHED)
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        if ($relatedArticles->count() < 3) {
            $fallback = Article::query()
                ->where('status', Article::STATUS_PUBLISHED)
                ->where('is_published', true)
                ->where('id', '!=', $article->id)
                ->whereNotIn('id', $relatedArticles->pluck('id'))
                ->orderByDesc('published_at')
                ->limit(3 - $relatedArticles->count())
                ->get();

            $relatedArticles = $relatedArticles->concat($fallback);
        }

        return view('public.articles.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
