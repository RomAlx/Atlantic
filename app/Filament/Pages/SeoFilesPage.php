<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class SeoFilesPage extends Page
{
    protected string $view = 'filament.pages.seo-files';

    protected static ?string $title = 'Файлы для поисковиков';

    protected static ?string $navigationLabel = 'Robots и sitemap';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string|\UnitEnum|null $navigationGroup = 'SEO';

    protected static ?int $navigationSort = 1;

    public ?string $current_robots_path = null;

    public ?string $current_sitemap_path = null;

    public mixed $robots_upload = null;

    public mixed $sitemap_upload = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('seo.manage') ?? false;
    }

    public function getSubheading(): ?string
    {
        return 'Загруженные файлы отдаются по адресам /robots.txt и /sitemap.xml. Если файл не загружен, используется шаблон по умолчанию.';
    }

    public function mount(): void
    {
        $s = Setting::query()->first();
        $this->current_robots_path = $s?->seo_robots_txt_path;
        $this->current_sitemap_path = $s?->seo_sitemap_xml_path;
    }

    public function save(): void
    {
        $this->validate([
            'robots_upload' => ['nullable', 'file', 'max:2048'],
            'sitemap_upload' => ['nullable', 'file', 'max:10240'],
        ]);

        if ($this->robots_upload === null && $this->sitemap_upload === null) {
            Notification::make()
                ->title('Выберите файл для загрузки')
                ->warning()
                ->send();

            return;
        }

        $setting = Setting::query()->firstOrCreate([]);
        $disk = Storage::disk('public');

        if ($this->robots_upload) {
            if (filled($setting->seo_robots_txt_path)) {
                $disk->delete($setting->seo_robots_txt_path);
            }
            $path = $this->robots_upload->store('seo', 'public');
            $setting->seo_robots_txt_path = $path;
            $this->current_robots_path = $path;
            $this->robots_upload = null;
        }

        if ($this->sitemap_upload) {
            if (filled($setting->seo_sitemap_xml_path)) {
                $disk->delete($setting->seo_sitemap_xml_path);
            }
            $path = $this->sitemap_upload->store('seo', 'public');
            $setting->seo_sitemap_xml_path = $path;
            $this->current_sitemap_path = $path;
            $this->sitemap_upload = null;
        }

        $setting->save();

        Notification::make()
            ->title('Файлы сохранены')
            ->success()
            ->send();
    }

    public function deleteRobots(): void
    {
        $setting = Setting::query()->first();
        if ($setting && filled($setting->seo_robots_txt_path)) {
            Storage::disk('public')->delete($setting->seo_robots_txt_path);
            $setting->update(['seo_robots_txt_path' => null]);
            $this->current_robots_path = null;
        }

        Notification::make()
            ->title('Используется robots.txt по умолчанию')
            ->success()
            ->send();
    }

    public function deleteSitemap(): void
    {
        $setting = Setting::query()->first();
        if ($setting && filled($setting->seo_sitemap_xml_path)) {
            Storage::disk('public')->delete($setting->seo_sitemap_xml_path);
            $setting->update(['seo_sitemap_xml_path' => null]);
            $this->current_sitemap_path = null;
        }

        Notification::make()
            ->title('Используется sitemap.xml по умолчанию')
            ->success()
            ->send();
    }
}
