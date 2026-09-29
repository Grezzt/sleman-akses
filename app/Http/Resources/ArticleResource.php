<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'content' => $this->when($request->routeIs('api.news.show') || $request->filled('include_content'), $this->content),
            'category' => $this->category,
            'detected_facilities' => $this->detected_facilities ?? [],
            'image_url' => $this->image_url,
            'author_name' => $this->author_name,
            'source_name' => $this->source_name,
            'source_url' => $this->source_url,
            'is_scraped' => (bool) $this->is_scraped,
            'ai_processed' => (bool) $this->ai_processed,
            'published_at' => $this->published_at?->toIso8601String(),
            'views_count' => (int) ($this->views_count ?? 0),
            'reading_time_minutes' => $this->reading_time_minutes,
        ];
    }
}
