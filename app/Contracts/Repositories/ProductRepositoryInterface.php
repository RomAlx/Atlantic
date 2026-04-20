<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

interface ProductRepositoryInterface
{
    /**
     * @return SupportCollection<int, Product>
     */
    public function getActiveFeaturedForHome(int $limit): SupportCollection;

    public function getActiveByCategoryId(int $categoryId): Collection;

    /**
     * @param  list<int>  $categoryIds
     * @return Collection<int, Product>
     */
    public function getActiveByCategoryIds(array $categoryIds): Collection;

    /**
     * @param  list<int>  $categoryIds
     */
    public function paginateActiveByCategoryIds(array $categoryIds, int $perPage, int $page = 1): LengthAwarePaginator;

    public function findActiveBySlugs(string $categorySlug, string $productSlug): Product;

    /**
     * @return Collection<int, Product>
     */
    public function searchActive(?string $query): Collection;

    /**
     * @return Collection<int, Product>
     */
    public function getActivePopular(int $limit, ?int $exceptProductId = null): Collection;

    /**
     * @return Collection<int, Product>
     */
    public function getActiveRelatedByCategory(int $categoryId, int $limit, ?int $exceptProductId = null): Collection;
}
