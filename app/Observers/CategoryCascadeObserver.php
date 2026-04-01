<?php

namespace App\Observers;

use App\Models\Category;

class CategoryCascadeObserver
{
    public function updated(Category $category): void
    {
        if ($category->wasChanged('is_active') && ! $category->is_active) {
            foreach ($category->children()->where('is_active', true)->get() as $child) {
                $child->update(['is_active' => false]);
            }
            $category->products()->where('is_active', true)->update(['is_active' => false]);
        }
    }
}
