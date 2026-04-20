<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteVisitsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Посещаемость сайта';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $week = now()->subDays(6)->startOfDay();
        $month = now()->subDays(29)->startOfDay();

        return [
            Stat::make('Визиты за сегодня', (string) PageVisit::query()->where('visited_at', '>=', $today)->count()),
            Stat::make('Визиты за 7 дней', (string) PageVisit::query()->where('visited_at', '>=', $week)->count()),
            Stat::make('Визиты за 30 дней', (string) PageVisit::query()->where('visited_at', '>=', $month)->count()),
            Stat::make('Уникальных страниц за 30 дней', (string) PageVisit::query()
                ->where('visited_at', '>=', $month)
                ->distinct('path')
                ->count('path')),
        ];
    }
}
