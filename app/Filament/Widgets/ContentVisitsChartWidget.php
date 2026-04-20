<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class ContentVisitsChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Товары и статьи';

    protected ?string $description = 'Топ по посещениям за 30 дней';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $from = now()->subDays(29)->startOfDay();

        $topProducts = PageVisit::query()
            ->selectRaw('product_slug as slug, COUNT(*) as total')
            ->where('visited_at', '>=', $from)
            ->whereNotNull('product_slug')
            ->groupBy('product_slug')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => ['label' => 'Товар: '.$row->slug, 'total' => (int) $row->total]);

        $topArticles = PageVisit::query()
            ->selectRaw('support_article_slug as slug, COUNT(*) as total')
            ->where('visited_at', '>=', $from)
            ->whereNotNull('support_article_slug')
            ->groupBy('support_article_slug')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => ['label' => 'Статья: '.$row->slug, 'total' => (int) $row->total]);

        $rows = $topProducts->concat($topArticles)
            ->sortByDesc('total')
            ->take(10)
            ->values();

        return [
            'datasets' => [
                [
                    'label' => 'Визиты',
                    'data' => $rows->pluck('total')->all(),
                    'backgroundColor' => '#22c55e',
                ],
            ],
            'labels' => $rows->pluck('label')->all(),
        ];
    }
}
