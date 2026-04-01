<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Url;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    #[Url(as: 'parent')]
    public ?int $parentId = null;

    public function mount(): void
    {
        parent::mount();

        if ($this->parentId !== null && ! Category::query()->whereKey($this->parentId)->exists()) {
            $this->parentId = null;
        }
    }

    public function getSubheading(): ?string
    {
        if ($this->parentId === null) {
            return 'Корневой уровень. Нажмите на название категории или «Подкатегории», чтобы открыть вложенный уровень.';
        }

        $names = [];
        $id = $this->parentId;
        $guard = 0;
        while ($id && $guard++ < 50) {
            $c = Category::query()->find($id);
            if (! $c) {
                break;
            }
            array_unshift($names, $c->name);
            $id = $c->parent_id;
        }

        return 'Путь: '.implode(' → ', $names);
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        $query = parent::getTableQuery();

        if (! $query instanceof Builder) {
            return $query;
        }

        return $query
            ->withCount(['children' => fn (Builder $q) => $q->where('is_active', true)])
            ->when(
                $this->parentId !== null,
                fn (Builder $q) => $q->where('parent_id', $this->parentId),
                fn (Builder $q) => $q->whereNull('parent_id'),
            )
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Активные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)),
            'inactive' => Tab::make('Неактивные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('to_parent')
                ->label('Наверх')
                ->icon(Heroicon::ArrowLeft)
                ->visible(fn (): bool => $this->parentId !== null)
                ->url(function (): string {
                    $cat = Category::query()->find($this->parentId);
                    $pid = $cat?->parent_id;

                    return $pid !== null
                        ? CategoryResource::getUrl('index', ['parent' => $pid])
                        : CategoryResource::getUrl('index');
                }),
            Action::make('to_root')
                ->label('В корень')
                ->icon(Heroicon::Home)
                ->visible(fn (): bool => $this->parentId !== null)
                ->url(fn (): string => CategoryResource::getUrl('index')),
            CreateAction::make(),
        ];
    }
}
