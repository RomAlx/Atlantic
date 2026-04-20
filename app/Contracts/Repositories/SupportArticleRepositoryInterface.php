<?php

namespace App\Contracts\Repositories;

use App\Models\SupportArticle;
use Illuminate\Support\Collection;

interface SupportArticleRepositoryInterface
{
    /**
     * @return Collection<int, SupportArticle>
     */
    public function searchActive(?string $query = null, int $limit = 100): Collection;

    public function findActiveBySlug(string $slug): SupportArticle;
}
