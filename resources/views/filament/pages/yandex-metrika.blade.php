<x-filament-panels::page>
    <form wire:submit="save" class="mx-auto max-w-2xl space-y-6">
        <x-filament::section
            heading="Счётчик Яндекс.Метрики"
            description="ID — число из раздела «Настройка» → «Счётчик» в кабинете Метрики. После сохранения проверьте срабатывание в интерфейсе Метрики (режим реального времени)."
        >
            <div class="space-y-6">
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <input type="checkbox" wire:model.live="yandex_metrika_enabled"
                        class="mt-1 size-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-white/20 dark:bg-gray-800">
                    <span>
                        <span class="block text-sm font-semibold text-gray-950 dark:text-white">Включить счётчик на сайте</span>
                        <span class="mt-0.5 block text-sm text-gray-600 dark:text-gray-400">Скрипт tag.js загружается в шаблоне публичной части.</span>
                    </span>
                </label>

                <div>
                    <label for="ym-counter-id" class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">
                        ID счётчика
                    </label>
                    <input
                        id="ym-counter-id"
                        type="text"
                        inputmode="numeric"
                        autocomplete="off"
                        wire:model="yandex_metrika_counter_id"
                        maxlength="32"
                        placeholder="Например: 12345678"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-base text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20 dark:border-gray-600 dark:bg-gray-950 dark:text-gray-50 dark:placeholder:text-gray-500 dark:focus:border-primary-500 dark:focus:ring-primary-500/30"
                    >
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        Обязателен, если счётчик включён.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-filament::button type="submit" color="primary">
                        Сохранить
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    </form>
</x-filament-panels::page>
