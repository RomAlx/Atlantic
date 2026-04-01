<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rule;

class YandexMetrikaPage extends Page
{
    protected string $view = 'filament.pages.yandex-metrika';

    protected static ?string $title = 'Яндекс.Метрика';

    protected static ?string $navigationLabel = 'Яндекс.Метрика';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?int $navigationSort = 100;

    public bool $yandex_metrika_enabled = false;

    public ?string $yandex_metrika_counter_id = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function getSubheading(): ?string
    {
        return 'Счётчик подключается на публичном сайте. При переходах между страницами в Vue 3 дополнительно отправляются просмотры (hit), чтобы Метрика видела SPA-навигацию.';
    }

    public function mount(): void
    {
        $s = Setting::query()->first();
        $this->yandex_metrika_enabled = (bool) ($s?->yandex_metrika_enabled ?? false);
        $this->yandex_metrika_counter_id = $s?->yandex_metrika_counter_id;
    }

    public function save(): void
    {
        $this->validate([
            'yandex_metrika_counter_id' => [
                'nullable',
                'string',
                'max:32',
                Rule::requiredIf($this->yandex_metrika_enabled),
            ],
        ]);

        $s = Setting::query()->firstOrCreate([]);
        $counter = $this->yandex_metrika_counter_id !== null && $this->yandex_metrika_counter_id !== ''
            ? trim($this->yandex_metrika_counter_id)
            : null;

        $s->update([
            'yandex_metrika_enabled' => $this->yandex_metrika_enabled,
            'yandex_metrika_counter_id' => $this->yandex_metrika_enabled ? $counter : null,
        ]);

        Notification::make()
            ->title('Настройки счётчика сохранены')
            ->success()
            ->send();
    }
}
