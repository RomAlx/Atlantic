<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class SectionVisitsChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Посещения по разделам';

    protected ?string $description = 'За последние 30 дней';

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
        $rows = PageVisit::query()
            ->selectRaw('section, COUNT(*) as total')
            ->where('visited_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('section')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Визиты',
                    'data' => $rows->pluck('total')->all(),
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $rows->pluck('section')->all(),
        ];
    }
}
