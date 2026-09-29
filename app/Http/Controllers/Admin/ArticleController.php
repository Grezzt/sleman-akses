<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use App\Services\PhotoUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class ArticleController extends Controller
{
    private const CATEGORIES = [
        'Aksesibilitas',
        'Edukasi & Kesadaran',
        'Regulasi & Kebijakan',
        'Fasilitas Publik',
    ];

    public function __construct(
        private readonly ArticleService $articleService,
        private readonly PhotoUploadService $photoUploadService
    ) {}

    public function index(Request $request): View
    {
        $statusFilter = $request->get('status');
        if ($statusFilter === 'all') {
            $statusFilter = null;
        }

        $filters = [
            'status' => $statusFilter,
            'category' => $request->get('category'),
            'search' => $request->get('search'),
            'is_published' => $request->get('is_published'),
        ];

        $articles = $this->articleService->getAdminArticles($filters, 15);

        $stats = [
            'total' => Article::count(),
            'pending' => Article::where('status', Article::STATUS_PENDING)->count(),
            'published' => Article::where('status', Article::STATUS_PUBLISHED)->count(),
            'rejected' => Article::where('status', Article::STATUS_REJECTED)->count(),
            'scraped' => Article::where('is_scraped', true)->count(),
            'ai' => Article::where('ai_processed', true)->count(),
            'total_views' => (int) Article::sum('views_count'),
        ];

        return view('admin.articles.index', [
            'articles' => $articles,
            'categories' => self::CATEGORIES,
            'filters' => $filters,
            'activeStatus' => $request->get('status', 'all'),
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        return view('admin.articles.create', [
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', self::CATEGORIES)],
            'summary' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'author_name' => ['nullable', 'string', 'max:100'],
            'source_name' => ['nullable', 'string', 'max:100'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($file instanceof UploadedFile) {
                try {
                    $userId = (int) (Auth::id() ?? 1);
                    $uploadedUrl = $this->photoUploadService->upload($file, $userId);
                    $data['image_url'] = $uploadedUrl;
                } catch (Throwable) {
                    $path = $file->store('articles', 'public');
                    $data['image_url'] = asset('storage/' . $path);
                }
            }
        }

        if (empty($data['author_name'])) {
            /** @var User|null $user */
            $user = $request->user();
            $data['author_name'] = $user?->full_name ?? 'Admin Sleman Akses';
        }

        $data['is_scraped'] = false;
        $data['is_published'] = $request->boolean('is_published', true);

        $this->articleService->createArticle($data);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berita/edukasi berhasil ditambahkan!');
    }

    public function show(int $id): View
    {
        $article = $this->articleService->getById($id);

        return view('admin.articles.show', [
            'article' => $article,
        ]);
    }

    public function edit(int $id): View
    {
        $article = $this->articleService->getById($id);

        return view('admin.articles.edit', [
            'article' => $article,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', self::CATEGORIES)],
            'summary' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'author_name' => ['nullable', 'string', 'max:100'],
            'source_name' => ['nullable', 'string', 'max:100'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($file instanceof UploadedFile) {
                try {
                    $userId = (int) (Auth::id() ?? 1);
                    $uploadedUrl = $this->photoUploadService->upload($file, $userId);
                    $data['image_url'] = $uploadedUrl;
                } catch (Throwable) {
                    $path = $file->store('articles', 'public');
                    $data['image_url'] = asset('storage/' . $path);
                }
            }
        }

        $data['is_published'] = $request->boolean('is_published');

        $this->articleService->updateArticle($id, $data);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->articleService->deleteArticle($id);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus!');
    }

    public function toggleStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $article = $this->articleService->togglePublishStatus($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status artikel berhasil diubah.',
                'status' => $article->status,
                'is_published' => $article->is_published,
            ]);
        }

        return back()->with('success', 'Status artikel berhasil diubah.');
    }

    /**
     * Curation Action: Approve (ACC) article to publish live.
     */
    public function approve(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $article = $this->articleService->approveArticle($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Artikel \"{$article->title}\" berhasil disetujui (ACC) dan kini telah terbit.",
                'article' => $article,
            ]);
        }

        return back()->with('success', "Artikel \"{$article->title}\" berhasil disetujui (ACC) dan resmi diterbitkan!");
    }

    /**
     * Curation Action: Reject article.
     */
    public function reject(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $reason = $request->input('reason', 'Tidak memenuhi kriteria kurasi');
        $article = $this->articleService->rejectArticle($id, $reason);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Artikel \"{$article->title}\" telah ditolak.",
                'article' => $article,
            ]);
        }

        return back()->with('success', "Artikel \"{$article->title}\" telah ditolak.");
    }

    /**
     * Trigger Scraper via Bot (Rule-based RSS, fast & deterministic).
     */
    public function scrapeBot(Request $request): RedirectResponse
    {
        $timeRange = $request->input('time_range', 'month');
        if (!in_array($timeRange, ['today', 'week', 'month', 'custom'])) {
            $timeRange = 'month';
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $result = $this->articleService->scrapeViaBot(8, $timeRange, $startDate, $endDate);

        $added = $result['added'] ?? 0;
        $skipped = $result['skipped'] ?? 0;
        $rangeName = $result['time_range'] ?? 'Bulan Ini';

        $msg = "Scraping via Bot ({$rangeName}) Selesai! Berhasil mengumpulkan {$added} berita baru ke Antrean Kurasi";
        if ($skipped > 0) {
            $msg .= " ({$skipped} berita dilewati karena sudah ada atau di luar rentang)";
        }
        $msg .= ". Silakan tinjau dan ACC berita yang layak terbit.";

        return redirect()->route('admin.articles.index', ['status' => 'pending'])->with('success', $msg);
    }

    /**
     * Trigger Scraper via AI (Gemini Flash Lite with semantic rewrite & deduplication).
     */
    public function scrapeAi(?Request $request = null): RedirectResponse
    {
        $request = $request ?? request();

        $timeRange = $request->input('time_range', 'month');
        if (!in_array($timeRange, ['today', 'week', 'month', 'custom'])) {
            $timeRange = 'month';
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $result = $this->articleService->scrapeViaAi(8, $timeRange, $startDate, $endDate);

        $added = $result['added'] ?? 0;
        $skipped = $result['skipped'] ?? 0;
        $duplicates = $result['duplicates_prevented'] ?? 0;
        $aiCount = $result['ai_processed_count'] ?? 0;
        $rangeName = $result['time_range'] ?? 'Bulan Ini';

        $msg = "Scraping bertenaga Gemini AI ({$rangeName}) Selesai! Berhasil memproses {$added} artikel baru ke Antrean Kurasi";
        if ($aiCount > 0) {
            $msg .= " ({$aiCount} disempurnakan naskah edukasinya oleh AI)";
        }
        if ($duplicates > 0) {
            $msg .= ", {$duplicates} berita duplikat dicegah";
        }
        if ($skipped > 0) {
            $msg .= ", {$skipped} berita dilewati";
        }
        $msg .= ". Silakan lakukan pemilahan berita (ACC atau Tolak).";

        return redirect()->route('admin.articles.index', ['status' => 'pending'])->with('success', $msg);
    }

    /**
     * Legacy trigger alias
     */
    public function scrape(Request $request): RedirectResponse
    {
        return $this->scrapeAi($request);
    }

    /**
     * Upload an in-body article image and return public URL for embedding.
     */
    public function uploadBodyImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        try {
            $userId = (int) (Auth::id() ?? 1);
            $file = $request->file('image');

            if (!$file instanceof UploadedFile) {
                throw new \RuntimeException('Berkas gambar tidak valid.');
            }

            try {
                $url = $this->photoUploadService->upload($file, $userId);
            } catch (Throwable) {
                $path = $file->store('articles/body', 'public');
                $url = asset('storage/' . $path);
            }

            return response()->json([
                'success' => true,
                'url' => $url,
                'message' => 'Foto body artikel berhasil diunggah.',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle bulk actions (approve, reject, delete) on selected articles.
     */
    public function bulkAction(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:approve,reject,delete'],
            'article_ids' => ['required', 'array', 'min:1'],
            'article_ids.*' => ['integer', 'exists:articles,id'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $action = $validated['action'];
        $ids = array_map('intval', $validated['article_ids']);
        $reason = $validated['rejection_reason'] ?? 'Tidak memenuhi kriteria kurasi';

        $count = 0;
        $message = '';

        switch ($action) {
            case 'approve':
                $count = $this->articleService->bulkApprove($ids);
                $message = "Berhasil menyetujui (ACC) {$count} artikel secara massal!";
                break;
            case 'reject':
                $count = $this->articleService->bulkReject($ids, $reason);
                $message = "Berhasil menolak {$count} artikel secara massal.";
                break;
            case 'delete':
                $count = $this->articleService->bulkDelete($ids);
                $message = "Berhasil menghapus {$count} artikel secara massal.";
                break;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'action' => $action,
                'affected_count' => $count,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
