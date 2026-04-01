<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CatalogOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->can('categories.viewAny') || $user->can('products.viewAny'));
    }

    protected ?string $heading = 'Каталог';

    protected ?string $description = 'Категории и товары';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Активные категории', (string) Category::query()->where('is_active', true)->count()),
            Stat::make('Неактивные категории', (string) Category::query()->where('is_active', false)->count()),
            Stat::make('Активные товары', (string) Product::query()->where('is_active', true)->count()),
            Stat::make('Неактивные товары', (string) Product::query()->where('is_active', false)->count()),
        ];
    }
}
