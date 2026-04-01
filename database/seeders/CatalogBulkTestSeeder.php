<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Создаёт тестовое дерево каталога: 10 корневых категорий,
 * в каждой по 10 подкатегорий, в каждой подкатегории по 10 товаров (всего 1000 товаров).
 *
 * Запуск: php artisan db:seed --class=CatalogBulkTestSeeder
 *
 * Slug с префиксом test-bulk-; при повторном запуске старые такие записи удаляются.
 */
class CatalogBulkTestSeeder extends Seeder
{
    private const ROOT_COUNT = 10;

    private const SUB_COUNT = 10;

    private const PRODUCTS_PER_SUB = 10;

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->cleanupPreviousRun();

            for ($r = 1; $r <= self::ROOT_COUNT; $r++) {
                $root = Category::query()->create([
                    'parent_id' => null,
                    'name' => "Тестовая категория {$r}",
                    'slug' => "test-bulk-root-{$r}",
                    'description' => "Сгенерировано CatalogBulkTestSeeder, корень {$r}.",
                    'sort_order' => 5000 + $r * 10,
                    'is_active' => true,
                ]);

                for ($s = 1; $s <= self::SUB_COUNT; $s++) {
                    $sub = Category::query()->create([
                        'parent_id' => $root->id,
                        'name' => "Тестовая подкатегория {$r}-{$s}",
                        'slug' => "test-bulk-sub-{$root->id}-{$s}",
                        'description' => "Подкатегория {$s} в тестовой категории {$r}.",
                        'sort_order' => $s * 10,
                        'is_active' => true,
                    ]);

                    for ($p = 1; $p <= self::PRODUCTS_PER_SUB; $p++) {
                        Product::query()->create([
                            'category_id' => $sub->id,
                            'name' => "Тестовый товар {$r}-{$s}-{$p}",
                            'slug' => "test-bulk-p-{$sub->id}-{$p}",
                            'sku' => sprintf('TB-%d-%d-%02d', $r, $s, $p),
                            'short_description' => "Краткое описание тестового товара {$r}-{$s}-{$p}.",
                            'description' => "Полное описание тестового товара в подкатегории «{$sub->name}».",
                            'specifications' => [
                                'Тестовый набор' => "{$r}/{$s}/{$p}",
                                'Артикул' => sprintf('TB-%d-%d-%02d', $r, $s, $p),
                            ],
                            'sort_order' => $p * 10,
                            'is_active' => true,
                        ]);
                    }
                }
            }
        });

        $this->command?->info(sprintf(
            'CatalogBulkTestSeeder: %d корней, %d подкатегорий, %d товаров.',
            self::ROOT_COUNT,
            self::ROOT_COUNT * self::SUB_COUNT,
            self::ROOT_COUNT * self::SUB_COUNT * self::PRODUCTS_PER_SUB
        ));
    }

    private function cleanupPreviousRun(): void
    {
        Product::query()->where('slug', 'like', 'test-bulk-p-%')->delete();
        Category::query()->where('slug', 'like', 'test-bulk-sub-%')->delete();
        Category::query()->where('slug', 'like', 'test-bulk-root-%')->delete();
    }
}
