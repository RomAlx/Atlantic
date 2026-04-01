<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'tree_path',
        'name',
        'slug',
        'description',
        'image_path',
        'sort_order',
        'is_active',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (! $category->parent_id) {
                $category->tree_path = '';
            } else {
                $parent = self::query()->find($category->parent_id);
                $category->tree_path = $parent
                    ? ($parent->tree_path === '' ? (string) $parent->id : $parent->tree_path.'/'.$parent->id)
                    : '';
            }
        });

        static::saved(function (Category $category): void {
            if ($category->wasChanged('parent_id')) {
                foreach ($category->children()->orderBy('id')->get() as $child) {
                    $child->recalculateTreePathAndSaveDown();
                }
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function imagePublicUrl(): ?string
    {
        return MediaUrl::publicHref($this->image_path);
    }

    public function depth(): int
    {
        if (! $this->parent_id) {
            return 0;
        }

        return $this->tree_path === '' ? 1 : substr_count($this->tree_path, '/') + 1;
    }

    /**
     * @return list<int>
     */
    public function descendantIds(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->descendantIds());
        }

        return $ids;
    }

    public function isEffectiveActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $parentId = $this->parent_id;
        $guard = 0;
        while ($parentId && $guard++ < 100) {
            $parent = self::query()->find($parentId);
            if (! $parent || ! $parent->is_active) {
                return false;
            }
            $parentId = $parent->parent_id;
        }

        return true;
    }

    public function recalculateTreePathAndSaveDown(): void
    {
        if (! $this->parent_id) {
            $this->tree_path = '';
        } else {
            $parent = self::query()->find($this->parent_id);
            $this->tree_path = $parent
                ? ($parent->tree_path === '' ? (string) $parent->id : $parent->tree_path.'/'.$parent->id)
                : '';
        }
        $this->saveQuietly();
        foreach ($this->children()->orderBy('id')->get() as $child) {
            $child->recalculateTreePathAndSaveDown();
        }
    }
}
