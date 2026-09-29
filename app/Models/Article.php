<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'category',
        'detected_facilities',
        'image_url',
        'author_name',
        'source_name',
        'source_url',
        'is_scraped',
        'ai_processed',
        'is_published',
        'status',
        'rejection_reason',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'detected_facilities' => 'array',
        'is_scraped' => 'boolean',
        'ai_processed' => 'boolean',
        'is_published' => 'boolean',
        'views_count' => 'integer',
        'published_at' => 'datetime',
    ];

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    protected $appends = [
        'reading_time_minutes',
    ];

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)->where('is_published', true);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && (bool) $this->is_published;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'berita';
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            $sourceForSlug = !empty($article->slug) ? $article->slug : $article->title;
            $article->slug = static::generateUniqueSlug($sourceForSlug);

            if ($article->status === self::STATUS_PUBLISHED) {
                $article->is_published = true;
            } elseif ($article->is_published && empty($article->status)) {
                $article->status = self::STATUS_PUBLISHED;
            } elseif (empty($article->status)) {
                $article->status = self::STATUS_PENDING;
                $article->is_published = false;
            }

            if ($article->is_published && empty($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function getReadingTimeMinutesAttribute(): int
    {
        $text = strip_tags(($this->summary ?? '') . ' ' . ($this->content ?? ''));
        $wordCount = str_word_count($text);
        return max(1, (int) ceil($wordCount / 180));
    }
}
