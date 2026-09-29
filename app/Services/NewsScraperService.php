<?php

namespace App\Services;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class NewsScraperService
{
    /**
     * Search queries for RSS public scraping focused on Sleman / DIY accessibility.
     */
    private const RSS_QUERIES = [
        'disabilitas aksesibilitas sleman OR yogyakarta',
        'fasilitas ramah difabel sleman OR jogja',
    ];

    public function __construct(private readonly GeminiService $gemini) {}

    /**
     * Scrape news from public RSS feeds with configurable time ranges.
     *
     * @param int $limit Maximum number of articles to add per query
     * @param bool $useAi If true, process with Gemini AI. If false, run rule-based bot.
     * @param string $timeRange Quick options: 'today', 'week', 'month', or 'custom'
     * @param string|null $startDate Required if $timeRange === 'custom' (YYYY-MM-DD)
     * @param string|null $endDate Optional if $timeRange === 'custom' (YYYY-MM-DD)
     * @return array
     */
    public function scrapeLatest(
        int $limit = 10,
        bool $useAi = true,
        string $timeRange = 'month',
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $added = 0;
        $skipped = 0;
        $duplicateCount = 0;
        $irrelevantCount = 0;
        $aiProcessedCount = 0;
        $errors = [];

        // 1. Calculate temporal filter bounds and Google News query modifier
        $timeModifier = '';
        $minDate = null;
        $maxDate = null;
        $timeRangeLabel = 'Bulan Ini (30 Hari)';

        switch ($timeRange) {
            case 'today':
                $timeModifier = 'when:1d';
                $minDate = now()->subHours(24);
                $timeRangeLabel = 'Hari Ini (24 Jam)';
                break;
            case 'week':
                $timeModifier = 'when:7d';
                $minDate = now()->subDays(7)->startOfDay();
                $timeRangeLabel = 'Minggu Ini (7 Hari)';
                break;
            case 'custom':
                $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
                $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;
                $minDate = $start;
                $maxDate = $end;

                $customQueryParts = [];
                if ($start) {
                    $customQueryParts[] = 'after:' . $start->format('Y-m-d');
                }
                if ($end) {
                    $customQueryParts[] = 'before:' . $end->copy()->addDay()->format('Y-m-d');
                }
                $timeModifier = implode(' ', $customQueryParts);
                $timeRangeLabel = 'Rentang Kustom (' . ($startDate ?: 'Awal') . ' s/d ' . ($endDate ?: 'Sekarang') . ')';
                break;
            case 'month':
            default:
                $timeModifier = 'when:30d';
                $minDate = now()->subDays(30)->startOfDay();
                $timeRangeLabel = 'Bulan Ini (30 Hari)';
                break;
        }

        // Fetch recent article titles for semantic duplicate detection context
        $existingTitles = Article::latest()->limit(30)->pluck('title')->toArray();

        foreach (self::RSS_QUERIES as $query) {
            try {
                $fullQuery = trim($query . ($timeModifier ? ' ' . $timeModifier : ''));
                $url = 'https://news.google.com/rss/search?q=' . urlencode($fullQuery) . '&hl=id&gl=ID&ceid=ID:id';
                $response = Http::timeout(10)->get($url);

                if (!$response->successful()) {
                    continue;
                }

                $xmlString = $response->body();
                $xml = @simplexml_load_string($xmlString);

                if ($xml === false || !isset($xml->channel->item)) {
                    continue;
                }

                $items = $xml->channel->item;
                $count = 0;

                foreach ($items as $item) {
                    if ($count >= $limit) {
                        break;
                    }

                    $rawTitle = trim((string) $item->title);
                    $link = trim((string) $item->link);
                    $pubDate = (string) $item->pubDate;
                    $description = (string) $item->description;

                    if (empty($rawTitle) || empty($link)) {
                        continue;
                    }

                    // 2. Filter Temporal Freshness (Wajib sesuai rentang waktu yang dipilih)
                    try {
                        $publishedAt = Carbon::parse($pubDate);
                    } catch (Throwable) {
                        $publishedAt = now();
                    }

                    if ($minDate && $publishedAt->lt($minDate)) {
                        $skipped++;
                        continue;
                    }

                    if ($maxDate && $publishedAt->gt($maxDate)) {
                        $skipped++;
                        continue;
                    }

                    // 2. Extract source name and clean title
                    $sourceName = 'Media Publik';
                    $title = $rawTitle;
                    if (isset($item->source) && !empty((string) $item->source)) {
                        $sourceName = trim((string) $item->source);
                        $suffix = ' - ' . $sourceName;
                        if (str_ends_with($title, $suffix)) {
                            $title = trim(substr($title, 0, -strlen($suffix)));
                        }
                    } elseif (preg_match('/^(.*?)\s*-\s*([^-]+)$/', $rawTitle, $matches)) {
                        $title = trim($matches[1]);
                        $sourceName = trim($matches[2]);
                    }

                    // 3. Resolve direct publisher URL & authentic image
                    $decodedPublisherUrl = $this->resolvePublisherUrl($link);
                    $effectiveSourceUrl = $decodedPublisherUrl ?: $link;

                    // Check exact duplicate by source_url or title
                    $exists = Article::where('source_url', $effectiveSourceUrl)
                        ->orWhere('source_url', $link)
                        ->orWhere('title', $title)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    // Extract authentic photos from publisher or RSS (No placeholders!)
                    $extractedMedia = $this->extractAuthenticMedia($item, $description, $effectiveSourceUrl);
                    $coverImageUrl = $extractedMedia['cover_url'];
                    $bodyImageUrl = $extractedMedia['body_url'];

                    // Clean raw summary from description
                    $cleanSummary = strip_tags(html_entity_decode($description));
                    if (empty($cleanSummary) || strlen($cleanSummary) < 30) {
                        $cleanSummary = "Informasi publik mengenai perkembangan sarana dan prasarana aksesibilitas ramah disabilitas di wilayah Sleman dan Daerah Istimewa Yogyakarta.";
                    }

                    // Rule-based fallback content
                    $content = $cleanSummary . "\n\n" .
                        "Aksesibilitas fasilitas umum bagi penyandang disabilitas fisik merupakan wujud pemenuhan hak asasi manusia serta standar kesetaraan ruang publik yang diatur dalam peraturan perundang-undangan. " .
                        "Melalui pemetaan partisipatif Sleman Akses, informasi mengenai fasilitas seperti ramp kursi roda, toilet difabel, parkir khusus, dan guiding block terus diperbarui guna mendukung mobilitas mandiri masyarakat.\n\n" .
                        "Untuk membaca liputan selengkapnya dari naskah asli, silakan kunjungi portal sumber berita resmi melalui tautan rujukan di bawah ini.";

                    $category = $this->determineCategory($title . ' ' . $description);
                    $detectedFacilities = $this->extractFacilitiesRuleBased($title . ' ' . $description);
                    $aiProcessed = false;

                    // 4. Analisis Cerdas Gemini AI (Hanya jika mode AI aktif)
                    if ($useAi) {
                        $aiAnalysis = $this->gemini->analyzeArticle($title, $cleanSummary, $existingTitles);

                        if ($aiAnalysis !== null) {
                            if (!empty($aiAnalysis['is_duplicate'])) {
                                Log::info("NewsScraper: Semantic duplicate prevented by AI: '{$title}' ({$aiAnalysis['duplicate_reason']})");
                                $duplicateCount++;
                                $skipped++;
                                continue;
                            }

                            if (isset($aiAnalysis['is_relevant']) && !$aiAnalysis['is_relevant']) {
                                Log::info("NewsScraper: Irrelevant news filtered out by AI: '{$title}'");
                                $irrelevantCount++;
                                $skipped++;
                                continue;
                            }

                            if (!empty($aiAnalysis['clean_title'])) {
                                $title = $aiAnalysis['clean_title'];
                            }
                            if (!empty($aiAnalysis['summary'])) {
                                $cleanSummary = $aiAnalysis['summary'];
                            }
                            if (!empty($aiAnalysis['content'])) {
                                $content = $aiAnalysis['content'];
                            }
                            if (!empty($aiAnalysis['category'])) {
                                $category = $aiAnalysis['category'];
                            }
                            if (!empty($aiAnalysis['detected_facilities'])) {
                                $detectedFacilities = $aiAnalysis['detected_facilities'];
                            }
                            $aiProcessed = true;
                            $aiProcessedCount++;
                        }
                    }

                    // 5. Jika ada foto body asli yang terdeteksi, sisipkan ke dalam body artikel
                    if ($bodyImageUrl && !str_contains($content, $bodyImageUrl)) {
                        $figureHtml = "\n\n<figure class=\"my-4\"><img src=\"{$bodyImageUrl}\" alt=\"" . htmlspecialchars($title) . "\" class=\"w-full rounded-xl border-2 border-border-solid shadow-md\"><figcaption class=\"text-xs text-text-muted mt-1 text-center font-bold\">Dokumentasi: {$sourceName}</figcaption></figure>\n\n";
                        $content = $figureHtml . $content;
                    }

                    // 6. Simpan artikel ke database dengan status 'pending' (TAHAP PEMILAHAN/KURASI)
                    // Berita TIDAK LANGSUNG TERBIT (is_published = false) sampai di-ACC oleh Admin
                    Article::create([
                        'title' => $title,
                        'slug' => Article::generateUniqueSlug($title),
                        'summary' => Str::limit($cleanSummary, 300),
                        'content' => $content,
                        'category' => $category,
                        'detected_facilities' => $detectedFacilities,
                        'image_url' => $coverImageUrl,
                        'author_name' => $sourceName,
                        'source_name' => $sourceName,
                        'source_url' => $effectiveSourceUrl,
                        'is_scraped' => true,
                        'ai_processed' => $aiProcessed,
                        'is_published' => false,
                        'status' => Article::STATUS_PENDING,
                        'published_at' => null, // akan diisi waktu admin menyetujui (ACC)
                    ]);

                    $existingTitles[] = $title;
                    $added++;
                    $count++;
                }
            } catch (Throwable $e) {
                Log::warning('NewsScraperService error on query ' . $query . ': ' . $e->getMessage());
                $errors[] = $e->getMessage();
            }
        }

        return [
            'success' => true,
            'mode' => $useAi ? 'AI (Google Gemini)' : 'Bot (Rule-based)',
            'time_range' => $timeRangeLabel,
            'added' => $added,
            'skipped' => $skipped,
            'duplicates_prevented' => $duplicateCount,
            'irrelevant_filtered' => $irrelevantCount,
            'ai_processed_count' => $aiProcessedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Resolve Google News encoded article redirect to the original publisher URL using Google's RPC endpoint.
     */
    public function resolvePublisherUrl(string $googleNewsUrl): ?string
    {
        try {
            // Check if it's a Google News article URL
            if (!str_contains($googleNewsUrl, 'news.google.com')) {
                return $googleNewsUrl;
            }

            $pathParts = explode('/', parse_url($googleNewsUrl, PHP_URL_PATH) ?? '');
            $articleId = end($pathParts);

            if (empty($articleId)) {
                return null;
            }

            // Step 1: Fetch signature & timestamp from the article landing page
            $landingUrl = "https://news.google.com/rss/articles/{$articleId}?hl=id&gl=ID&ceid=ID:id";
            $res = Http::timeout(4)
                ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
                ->get($landingUrl);

            if (!$res->successful()) {
                return null;
            }

            $html = $res->body();
            preg_match('/data-n-a-sg="([^"]+)"/', $html, $sgMatch);
            preg_match('/data-n-a-ts="([^"]+)"/', $html, $tsMatch);

            if (empty($sgMatch[1]) || empty($tsMatch[1])) {
                return null;
            }

            $sig = $sgMatch[1];
            $ts = (int) $tsMatch[1];

            // Step 2: Request original URL via batchexecute RPC
            $ctx = [
                ["X", "X", ["X", "X"], null, null, 1, 1, "US:en", null, 1, null, null, null, null, null, 0, 1],
                "X", "X", 1, [1, 1, 1], 1, 1, null, 0, 0, null, 0,
            ];

            $inner = ["garturlreq", $ctx, $articleId, $ts, $sig];
            $envelopes = [["Fbv4je", json_encode($inner, JSON_UNESCAPED_SLASHES), null, "1"]];
            $body = "f.req=" . urlencode(json_encode([$envelopes], JSON_UNESCAPED_SLASHES));

            $rpcRes = Http::timeout(4)
                ->asForm()
                ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
                ->post("https://news.google.com/_/DotsSplashUi/data/batchexecute", [
                    'f.req' => json_encode([$envelopes], JSON_UNESCAPED_SLASHES)
                ]);

            if ($rpcRes->successful() && preg_match('/"(https?:\/\/[^"]+)"/', $rpcRes->body(), $urlMatch)) {
                $decoded = rtrim($urlMatch[1], "\\/");
                if (!str_contains($decoded, 'google.com') && filter_var($decoded, FILTER_VALIDATE_URL)) {
                    return $decoded;
                }
            }
        } catch (Throwable $e) {
            Log::debug('Failed to resolve Google News URL: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Extract authentic media (cover and body images) directly from the publisher or RSS.
     * ZERO PLACEHOLDERS: If no authentic photo exists, returns null.
     */
    private function extractAuthenticMedia(\SimpleXMLElement $item, string $description, ?string $publisherUrl): array
    {
        $coverUrl = null;
        $bodyUrl = null;

        // 1. Check RSS item media tags & enclosures
        $namespaces = $item->getNamespaces(true);
        if (isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            if (isset($media->content) && isset($media->content->attributes()->url)) {
                $url = (string) $media->content->attributes()->url;
                if ($this->isValidImageUrl($url)) {
                    $coverUrl = $url;
                }
            }
            if (isset($media->thumbnail) && isset($media->thumbnail->attributes()->url)) {
                $url = (string) $media->thumbnail->attributes()->url;
                if ($this->isValidImageUrl($url)) {
                    if (!$coverUrl) {
                        $coverUrl = $url;
                    } else {
                        $bodyUrl = $url;
                    }
                }
            }
        }

        if (!$coverUrl && isset($item->enclosure) && isset($item->enclosure->attributes()->url)) {
            $type = (string) ($item->enclosure->attributes()->type ?? '');
            if (str_starts_with($type, 'image/') || empty($type)) {
                $url = (string) $item->enclosure->attributes()->url;
                if ($this->isValidImageUrl($url)) {
                    $coverUrl = $url;
                }
            }
        }

        if (!$coverUrl && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $description, $matches)) {
            if ($this->isValidImageUrl($matches[1])) {
                $coverUrl = $matches[1];
            }
        }

        // 2. If publisherUrl is valid and not google.com, fetch real OpenGraph & in-article photos
        if ($publisherUrl && filter_var($publisherUrl, FILTER_VALIDATE_URL) && !str_contains($publisherUrl, 'google.com')) {
            try {
                $pubResponse = Http::timeout(4)
                    ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
                    ->get($publisherUrl);

                if ($pubResponse->successful()) {
                    $html = $pubResponse->body();

                    // OpenGraph Image
                    if (!$coverUrl) {
                        if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $ogMatch)) {
                            if ($this->isValidImageUrl($ogMatch[1])) {
                                $coverUrl = $ogMatch[1];
                            }
                        } elseif (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']/i', $html, $ogMatch)) {
                            if ($this->isValidImageUrl($ogMatch[1])) {
                                $coverUrl = $ogMatch[1];
                            }
                        } elseif (preg_match('/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $twMatch)) {
                            if ($this->isValidImageUrl($twMatch[1])) {
                                $coverUrl = $twMatch[1];
                            }
                        }
                    }

                    // Look for secondary in-body article image
                    if (!$bodyUrl && preg_match_all('/<article[^>]*>.*?<img[^>]+src=["\']([^"\']+)["\']/is', $html, $bodyImgMatches)) {
                        foreach ($bodyImgMatches[1] as $imgCandidate) {
                            if ($this->isValidImageUrl($imgCandidate) && $imgCandidate !== $coverUrl) {
                                $bodyUrl = $imgCandidate;
                                break;
                            }
                        }
                    }
                }
            } catch (Throwable) {
                // If fetching publisher page fails, keep whatever we found or null
            }
        }

        // If coverUrl is found and bodyUrl is not, and coverUrl is genuine, we don't force a bodyUrl
        return [
            'cover_url' => $coverUrl,
            'body_url' => $bodyUrl,
        ];
    }

    /**
     * Validate image URL to ensure it is authentic and not a generic icon or tracker.
     */
    private function isValidImageUrl(?string $url): bool
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Exclude common trackers, 1x1 pixels, and google default avatars
        $lower = strtolower($url);
        if (str_contains($lower, 'googleusercontent.com/j6_cofbogxhr') ||
            str_contains($lower, 'favicon') ||
            str_contains($lower, 'pixel') ||
            str_contains($lower, 'tracker') ||
            str_contains($lower, 'logo_white') ||
            str_contains($lower, 'icon-')) {
            return false;
        }

        return true;
    }

    /**
     * Determine category based on keywords (rule-based fallback).
     */
    private function determineCategory(string $text): string
    {
        $text = strtolower($text);

        if (Str::contains($text, ['regulasi', 'perda', 'pemkab', 'bupati', 'dinas', 'pemerintah', 'kebijakan', 'aturan', 'uu'])) {
            return 'Regulasi & Kebijakan';
        }

        if (Str::contains($text, ['fasilitas', 'trotoar', 'ramp', 'toilet', 'gedung', 'taman', 'halte', 'stasiun', 'mall', 'pasar'])) {
            return 'Fasilitas Publik';
        }

        if (Str::contains($text, ['akses', 'ramah', 'pemandu', 'guiding', 'kursi roda', 'inklusi', 'inklusif'])) {
            return 'Aksesibilitas';
        }

        return 'Edukasi & Kesadaran';
    }

    /**
     * Rule-based keyword facility extraction for Bot scraping.
     */
    private function extractFacilitiesRuleBased(string $text): array
    {
        $text = strtolower($text);
        $facilities = [];

        if (Str::contains($text, ['ramp', 'tanjakan', 'bidang miring', 'kursi roda'])) {
            $facilities[] = 'Ramp';
        }
        if (Str::contains($text, ['guiding block', 'jalur pemandu', 'ubin pemandu', 'tunanetra', 'sensorik'])) {
            $facilities[] = 'Guiding Block';
        }
        if (Str::contains($text, ['toilet', 'kamar mandi', 'sanitasi'])) {
            $facilities[] = 'Toilet Difabel';
        }
        if (Str::contains($text, ['parkir', 'area parkir', 'slot khusus'])) {
            $facilities[] = 'Parkir Difabel';
        }
        if (Str::contains($text, ['lift', 'elevator', 'lift difabel'])) {
            $facilities[] = 'Lift';
        }

        return array_values(array_unique($facilities));
    }
}
