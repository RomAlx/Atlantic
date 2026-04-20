<?php

namespace App\Contracts\Repositories;

use App\Models\Category;
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
     * Самые «популярные» товары внутри одной категории: score = view_count + (boost_popular ? вес : 0).
     *
     * @return Collection<int, Product>
     */
    public function getActivePopularInCategory(int $categoryId, int $limit, ?int $exceptProductId = null): Collection;

    /**
     * Товары из связанных категорий (поле у категории), сортировка по тому же score популярности.
     *
     * @return Collection<int, Product>
     */
    public function getActiveRelatedByLinkedCategories(Category $category, int $limit, ?int $exceptProductId = null): Collection;
}
