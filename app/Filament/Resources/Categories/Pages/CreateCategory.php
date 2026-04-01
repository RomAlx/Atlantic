<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['is_active'] ?? false) && ! empty($data['parent_id'])) {
            $parent = Category::query()->find((int) $data['parent_id']);
            if (! $parent || ! $parent->is_active) {
                throw ValidationException::withMessages([
                    'is_active' => 'Сначала активируйте родительскую категорию.',
                ]);
            }
        }

        return $data;
    }
}
