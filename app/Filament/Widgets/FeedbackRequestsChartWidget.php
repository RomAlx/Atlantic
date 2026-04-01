<?php

namespace App\Filament\Widgets;

use App\Models\FeedbackRequest;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class FeedbackRequestsChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->can('feedback.viewAny');
    }

    protected ?string $heading = 'Заявки по дням';

    protected ?string $description = 'Количество новых заявок за последние 14 дней';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $labels = [];
        $values = [];

        for ($i = 13; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->startOfDay();
            $labels[] = $day->format('d.m');
            $values[] = FeedbackRequest::query()
                ->whereDate('created_at', $day->toDateString())
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Заявок',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
