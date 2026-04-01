<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['is_active'] ?? false) && ! empty($data['parent_id'])) {
            $parent = Category::query()->find((int) $data['parent_id']);
            if (! $parent || ! $parent->is_active) {
                throw ValidationException::withMessages([
                    'is_active' => 'Сначала активируйте родительскую категорию.',
                ]);
            }
        }

        if (! empty($data['parent_id']) && $this->record instanceof Category) {
            $pid = (int) $data['parent_id'];
            $exclude = array_merge([$this->record->id], $this->record->descendantIds());
            if (in_array($pid, $exclude, true)) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Нельзя назначить эту категорию или её подкатегорию родителем.',
                ]);
            }
        }

        return $data;
    }
}
