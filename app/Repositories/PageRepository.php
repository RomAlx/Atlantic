<?php

namespace App\Repositories;

use App\Contracts\Repositories\PageRepositoryInterface;
use App\Models\Page;

class PageRepository implements PageRepositoryInterface
{
    public function findActiveBySlug(string $slug): Page
    {
        return Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail(['id', 'title', 'slug', 'content', 'seo_title', 'seo_description']);
    }
}
