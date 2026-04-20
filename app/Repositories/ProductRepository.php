<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

class ProductRepository implements ProductRepositoryInterface
{
    /** Вес ручного «популярного» флага в формуле score (view_count + вес при boost). */
    private const POPULAR_ADMIN_WEIGHT = 1000;

    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
    ) {}

    public function getActiveFeaturedForHome(int $limit): SupportCollection
    {
        return Product::query()
            ->with('category:id,name,slug,parent_id,is_active')
            ->with(['images' => fn ($q) => $q->orderByDesc('is_main')->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'category_id', 'name', 'slug', 'short_description', 'sku'])
            ->filter(fn (Product $p) => $p->category->isEffectiveActive())
            ->take($limit)
            ->values();
    }

    public function getActiveByCategoryId(int $categoryId): Collection
    {
        return Product::query()
            ->with(['images' => fn ($q) => $q->orderByDesc('is_main')->orderBy('sort_order')])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'category_id', 'name', 'slug', 'short_description', 'sku']);
    }

    public function getActiveByCategoryIds(array $categoryIds): Collection
    {
        $categoryIds = array_values(array_unique(array_filter($categoryIds)));

        if ($categoryIds === []) {
            return new Collection;
        }

        return Product::query()
            ->with(['category:id,name,slug,parent_id,is_active'])
            ->with(['images' => fn ($q) => $q->orderByDesc('is_main')->orderBy('sort_order')])
            ->whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'category_id', 'name', 'slug', 'short_description', 'sku'])
            ->filter(fn (Product $p) => $p->category->isEffectiveActive())
            ->values();
    }

    public function paginateActiveByCategoryIds(array $categoryIds, int $perPage, int $page = 1): LengthAwarePaginator
    {
        $categoryIds = array_values(array_unique(array_filter($categoryIds)));

        if ($categoryIds === []) {
            return new LengthAwarePaginator([], 0, max(1, $perPage), max(1, $page));
        }

        return Product::query()
            ->with(['category:id,name,slug,parent_id,is_active'])
            ->with(['images' => fn ($q) => $q->orderByDesc('is_main')->orderBy('sort_order')])
            ->whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(
                $perPage,
                ['id', 'category_id', 'name', 'slug', 'short_description', 'sku'],
                'page',
                $page
            );
    }

    public function findActiveBySlugs(string $categorySlug, string $productSlug): Product
    {
        $context = Category::query()
            ->where('slug', $categorySlug)
            ->where('is_active', true)
            ->first(['id', 'parent_id', 'slug', 'is_active', 'tree_path']);

        if (! $context || ! $context->isEffectiveActive()) {
            throw (new ModelNotFoundException)->setModel(Product::class, [$productSlug]);
        }

        $allowedCategoryIds = $this->categories->getActiveSubtreeCategoryIds($context);

        $product = Product::query()
            ->with([
                'category:id,name,slug,parent_id,is_active,related_category_ids',
                'images' => fn ($q) => $q
                    ->select('id', 'product_id', 'path', 'alt', 'sort_order', 'is_main')
                    ->orderByDesc('is_main')
                    ->orderBy('sort_order'),
            ])
            ->where('slug', $productSlug)
            ->whereIn('category_id', $allowedCategoryIds)
            ->where('is_active', true)
            ->first([
                'id',
                'category_id',
                'name',
                'slug',
                'sku',
                'short_description',
                'description',
                'specifications',
                'seo_title',
                'seo_description',
            ]);

        if (! $product || ! $product->category->isEffectiveActive()) {
            throw (new ModelNotFoundException)->setModel(Product::class, [$productSlug]);
        }

        return $product;
    }

    public function searchActive(?string $query): Collection
    {
        $products = Product::query()
            ->with('category:id,name,slug,parent_id,is_active')
            ->with(['images' => fn ($q) => $q->orderByDesc('is_main')->orderBy('sort_order')])
            ->where('is_active', true)
            ->when(filled($query), function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner
                        ->where('name', 'like', '%'.$query.'%')
                        ->orWhere('sku', 'like', '%'.$query.'%')
                        ->orWhere('short_description', 'like', '%'.$query.'%');
                });
            })
            ->orderBy('sort_order')
            ->get(['id', 'category_id', 'name', 'slug', 'short_description', 'sku']);

        return $products
            ->filter(fn (Product $p) => $p->category->isEffectiveActive())
            ->values();
    }

    public function getActivePopularInCategory(int $categoryId, int $limit, ?int $exceptProductId = null): Collection
    {
        $w = self::POPULAR_ADMIN_WEIGHT;

        return Product::query()
            ->with('category:id,name,slug,parent_id,is_active,related_category_ids')
            ->with(['images' => fn ($q) => $q->orderByDesc('is_main')->orderBy('sort_order')])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->when($exceptProductId !== null, fn ($q) => $q->where('id', '!=', $exceptProductId))
            ->orderByRaw('(view_count + CASE WHEN boost_popular = 1 THEN ? ELSE 0 END) DESC', [$w])
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get(['id', 'category_id', 'name', 'slug', 'short_description', 'sku', 'view_count', 'boost_popular'])
            ->filter(fn (Product $p) => $p->category->isEffectiveActive())
            ->values();
    }

    public function getActiveRelatedByLinkedCategories(Category $category, int $limit, ?int $exceptProductId = null): Collection
    {
        $raw = $category->related_category_ids;
        if (! is_array($raw) || $raw === []) {
            return new Collection;
        }

        $validIds = Category::query()
            ->whereIn('id', $raw)
            ->where('is_active', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if ($validIds === []) {
            return new Collection;
        }

        $w = self::POPULAR_ADMIN_WEIGHT;

        return Product::query()
            ->with('category:id,name,slug,parent_id,is_active,related_category_ids')
            ->with(['images' => fn ($q) => $q->orderByDesc('is_main')->orderBy('sort_order')])
            ->whereIn('category_id', $validIds)
            ->where('is_active', true)
            ->when($exceptProductId !== null, fn ($q) => $q->where('id', '!=', $exceptProductId))
            ->orderByRaw('(view_count + CASE WHEN boost_popular = 1 THEN ? ELSE 0 END) DESC', [$w])
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get(['id', 'category_id', 'name', 'slug', 'short_description', 'sku', 'view_count', 'boost_popular'])
            ->filter(fn (Product $p) => $p->category->isEffectiveActive())
            ->values();
    }
}
