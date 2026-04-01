<?php

namespace App\Contracts\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function getActiveRootForHome(int $limit): Collection;

    public function getActiveRootForCatalog(?string $nameSearch): Collection;

    public function findActiveBySlug(string $slug): Category;

    /**
     * @return Collection<int, Category>
     */
    public function getActiveChildren(int $parentId): Collection;

    public function paginateActiveChildren(int $parentId, int $perPage, int $page = 1): LengthAwarePaginator;

    /**
     * Активная категория и все активные потомки (по tree_path).
     *
     * @return list<int>
     */
    public function getActiveSubtreeCategoryIds(Category $root): array;
}
