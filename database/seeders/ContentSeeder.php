<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = config('site.defaults', []);

        $setting = Setting::query()->firstOrNew(['id' => 1]);
        $setting->forceFill([
            'company_name' => $defaults['company_name'] ?? 'Atlantic Group',
            'email' => $defaults['email'] ?? null,
            'address' => $defaults['address'] ?? null,
            'warehouse_address' => $defaults['warehouse_address'] ?? null,
            'phones' => [
                [
                    'label' => 'Отдел продаж',
                    'number' => '+7 (901) 123 45 67',
                    'is_main' => true,
                ],
            ],
            'social_links' => [
                ['network' => 'telegram', 'url' => 'https://t.me/example'],
                ['network' => 'vk', 'url' => 'https://vk.com/example'],
            ],
        ]);
        $setting->save();

        Page::query()->updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'О компании',
                'content' => '<p><img src="/images/source/normalized/image-6.jpg" alt="О компании"></p><p>Atlantic Group поставляет клеевые решения и материалы для промышленности и строительства. Мы сопровождаем клиентов технической поддержкой на всех этапах внедрения материалов.</p>',
                'is_active' => true,
            ]
        );

        Page::query()->where('slug', 'contacts')->delete();
    }
}
