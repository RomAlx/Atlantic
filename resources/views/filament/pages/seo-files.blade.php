<x-filament-panels::page>
    <form wire:submit="save" class="mx-auto max-w-3xl space-y-6">
        <x-filament::section
            heading="robots.txt"
            description="Текстовый файл с правилами для роботов. Публичный URL: {{ url('/robots.txt') }}"
        >
            @if (filled($this->current_robots_path))
                <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
                    Сейчас: <code class="rounded bg-gray-100 px-1 py-0.5 text-xs dark:bg-white/10">{{ $this->current_robots_path }}</code>
                </p>
            @endif
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-950 dark:text-white">Заменить файл</label>
                <input type="file" wire:model="robots_upload" accept=".txt,text/plain"
                    class="block w-full text-sm text-gray-950 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-500 dark:text-white dark:file:bg-primary-500">
                @error('robots_upload')
                    <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                @enderror
            </div>
            @if (filled($this->current_robots_path))
                <div class="mt-3">
                    <x-filament::button type="button" color="gray" wire:click="deleteRobots" wire:confirm="Удалить загруженный robots.txt?">
                        Сбросить на шаблон по умолчанию
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>

        <x-filament::section
            heading="sitemap.xml"
            description="Карта сайта в формате XML. Публичный URL: {{ url('/sitemap.xml') }}"
        >
            @if (filled($this->current_sitemap_path))
                <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
                    Сейчас: <code class="rounded bg-gray-100 px-1 py-0.5 text-xs dark:bg-white/10">{{ $this->current_sitemap_path }}</code>
                </p>
            @endif
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-950 dark:text-white">Заменить файл</label>
                <input type="file" wire:model="sitemap_upload" accept=".xml,text/xml,application/xml"
                    class="block w-full text-sm text-gray-950 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-500 dark:text-white dark:file:bg-primary-500">
                @error('sitemap_upload')
                    <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                @enderror
            </div>
            @if (filled($this->current_sitemap_path))
                <div class="mt-3">
                    <x-filament::button type="button" color="gray" wire:click="deleteSitemap" wire:confirm="Удалить загруженный sitemap.xml?">
                        Сбросить на шаблон по умолчанию
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>

        <div class="flex flex-wrap gap-2">
            <x-filament::button type="submit" color="primary">
                Сохранить выбранные файлы
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
