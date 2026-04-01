<?php

namespace App\Filament\Widgets;

use App\Models\FeedbackRequest;
use Filament\Widgets\ChartWidget;

class FeedbackStatusChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->can('feedback.viewAny');
    }

    protected ?string $heading = 'Заявки по статусам';

    protected ?string $description = 'Текущее распределение';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $new = FeedbackRequest::query()->where('status', 'new')->count();
        $inProgress = FeedbackRequest::query()->where('status', 'in_progress')->count();
        $done = FeedbackRequest::query()->where('status', 'done')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Заявки',
                    'data' => [$new, $inProgress, $done],
                    'backgroundColor' => [
                        '#f59e0b',
                        '#3b82f6',
                        '#22c55e',
                    ],
                ],
            ],
            'labels' => ['Новые', 'В работе', 'Закрытые'],
        ];
    }
}
