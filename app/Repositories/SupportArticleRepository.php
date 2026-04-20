<?php

namespace App\Repositories;

use App\Contracts\Repositories\SupportArticleRepositoryInterface;
use App\Models\SupportArticle;
use Illuminate\Support\Collection;

class SupportArticleRepository implements SupportArticleRepositoryInterface
{
    /**
     * @return Collection<int, SupportArticle>
     */
    public function searchActive(?string $query = null, int $limit = 100): Collection
    {
        $builder = SupportArticle::query()
            ->where('is_active', true);

        if (filled($query)) {
            $builder->where(function ($q) use ($query): void {
                $q->where('title', 'like', '%'.$query.'%')
                    ->orWhere('description', 'like', '%'.$query.'%')
                    ->orWhere('content_md', 'like', '%'.$query.'%');
            });
        }

        return $builder
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get([
                'id',
                'title',
                'slug',
                'preview_image_path',
                'description',
                'video_url',
                'seo_title',
                'seo_description',
                'updated_at',
            ]);
    }

    public function findActiveBySlug(string $slug): SupportArticle
    {
        return SupportArticle::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail([
                'id',
                'title',
                'slug',
                'preview_image_path',
                'description',
                'content_md',
                'video_url',
                'seo_title',
                'seo_description',
            ]);
    }
}
