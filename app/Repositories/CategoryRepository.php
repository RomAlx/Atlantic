<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getActiveRootForHome(int $limit): Collection
    {
        return Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take($limit)
            ->get(['id', 'name', 'slug', 'description', 'image_path', 'tree_path']);
    }

    public function getActiveRootForCatalog(?string $nameSearch): Collection
    {
        $roots = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->withCount([
                'children' => fn ($q) => $q->where('is_active', true),
            ])
            ->when(filled($nameSearch), fn ($q) => $q->where('name', 'like', '%'.$nameSearch.'%'))
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'description', 'image_path', 'tree_path']);

        foreach ($roots as $root) {
            $root->setAttribute(
                'products_in_subtree_count',
                $this->countActiveProductsInSubtree($root)
            );
        }

        return $roots;
    }

    public function findActiveBySlug(string $slug): Category
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail([
                'id',
                'parent_id',
                'name',
                'slug',
                'description',
                'seo_title',
                'seo_description',
                'image_path',
                'tree_path',
                'is_active',
            ]);

        if (! $category->isEffectiveActive()) {
            throw (new ModelNotFoundException)->setModel(Category::class, [$slug]);
        }

        return $category;
    }

    public function getActiveChildren(int $parentId): Collection
    {
        return Category::query()
            ->where('parent_id', $parentId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'description', 'image_path', 'parent_id', 'tree_path']);
    }

    public function paginateActiveChildren(int $parentId, int $perPage, int $page = 1): LengthAwarePaginator
    {
        return Category::query()
            ->where('parent_id', $parentId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(
                $perPage,
                ['id', 'name', 'slug', 'description', 'image_path', 'parent_id', 'tree_path'],
                'page',
                $page
            );
    }

    public function getActiveSubtreeCategoryIds(Category $root): array
    {
        if (! $root->is_active || ! $root->isEffectiveActive()) {
            return [];
        }

        $ids = [];
        $queue = [(int) $root->id];

        while ($queue !== []) {
            $parentId = array_shift($queue);
            $ids[] = $parentId;

            $childIds = Category::query()
                ->where('parent_id', $parentId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('id')
                ->all();

            foreach ($childIds as $cid) {
                $cid = (int) $cid;
                if (! in_array($cid, $ids, true)) {
                    $queue[] = $cid;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    private function countActiveProductsInSubtree(Category $root): int
    {
        $ids = $this->getActiveSubtreeCategoryIds($root);

        return Product::query()
            ->whereIn('category_id', $ids)
            ->where('is_active', true)
            ->count();
    }
}
