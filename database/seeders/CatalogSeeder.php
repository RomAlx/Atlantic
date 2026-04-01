<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $wood = Category::query()->updateOrCreate(
            ['slug' => 'klei-dlya-mebeli-i-derevoobrabotki'],
            [
                'parent_id' => null,
                'name' => 'Клеи для мебели и деревообработки',
                'description' => 'Промышленные клеевые решения для мебельного производства.',
                'sort_order' => 10,
                'is_active' => true,
            ]
        );

        $construction = Category::query()->updateOrCreate(
            ['slug' => 'klei-dlya-stroitelstva-i-remonta'],
            [
                'parent_id' => null,
                'name' => 'Клеи для строительства и ремонта',
                'description' => 'Клеевые продукты для общестроительных задач.',
                'sort_order' => 20,
                'is_active' => true,
            ]
        );

        $pva = Category::query()->updateOrCreate(
            ['slug' => 'pva-i-dispersionnye-sistemy'],
            [
                'parent_id' => $wood->id,
                'name' => 'PVA и дисперсионные системы',
                'description' => 'Водные дисперсии и PVA для дерева и мебельных линий.',
                'sort_order' => 15,
                'is_active' => true,
            ]
        );

        $this->seedCategoryImage($wood, 'klei-dlya-mebeli-i-derevoobrabotki');
        $this->seedCategoryImage($construction, 'klei-dlya-stroitelstva-i-remonta');
        $this->seedCategoryImage($pva, 'pva-i-dispersionnye-sistemy');

        $ecol103 = Product::query()->updateOrCreate(
            ['slug' => 'ecol-103'],
            [
                'category_id' => $pva->id,
                'name' => 'ECOL 103',
                'sku' => 'ECOL-103',
                'short_description' => 'Клей для мебельной и деревообрабатывающей отрасли.',
                'description' => 'Надёжный клей для производственных линий.',
                'specifications' => ['Основа' => 'PVA', 'Цвет' => 'Белый'],
                'sort_order' => 10,
                'is_active' => true,
            ]
        );

        $homacoll135 = Product::query()->updateOrCreate(
            ['slug' => 'homacoll-135'],
            [
                'category_id' => $construction->id,
                'name' => 'HOMACOLL 135',
                'sku' => 'HOM-135',
                'short_description' => 'Универсальный клей для строительных работ.',
                'description' => 'Подходит для широкого спектра отделочных задач.',
                'specifications' => ['Основа' => 'Полимерная дисперсия', 'Расход' => '250 г/м2'],
                'sort_order' => 20,
                'is_active' => true,
            ]
        );

        $homacoll017 = Product::query()->updateOrCreate(
            ['slug' => 'homacoll-017'],
            [
                'category_id' => $wood->id,
                'name' => 'HOMACOLL 017',
                'sku' => 'HOM-017',
                'short_description' => 'Клей D3/D4 для водостойкого склеивания дерева.',
                'description' => 'Профессиональный клей для изделий с повышенными требованиями к влагостойкости.',
                'specifications' => ['Основа' => 'PVA', 'Класс' => 'D3/D4'],
                'sort_order' => 30,
                'is_active' => true,
            ]
        );

        $homacoll707 = Product::query()->updateOrCreate(
            ['slug' => 'homacoll-707'],
            [
                'category_id' => $wood->id,
                'name' => 'HOMACOLL 707',
                'sku' => 'HOM-707',
                'short_description' => 'Полиуретановый клей D4 для сложных условий эксплуатации.',
                'description' => 'Обеспечивает прочное соединение в условиях повышенной влажности.',
                'specifications' => ['Основа' => 'PUR', 'Класс' => 'D4'],
                'sort_order' => 40,
                'is_active' => true,
            ]
        );

        $this->seedProductImages($ecol103);
        $this->seedProductImages($homacoll135);
        $this->seedProductImages($homacoll017);
        $this->seedProductImages($homacoll707);
    }

    private function seedCategoryImage(Category $category, string $configKey): void
    {
        $relative = config("site.seed_images.categories.{$configKey}");
        $normalized = "categories/{$category->slug}.jpg";
        $this->copySourceToStorage(is_string($relative) ? $relative : '', $normalized);
        $category->update(['image_path' => $normalized]);
    }

    private function seedProductImages(Product $product): void
    {
        $list = config('site.seed_images.products.'.$product->slug, []);
        if (! is_array($list) || $list === []) {
            $list = [''];
        }

        foreach (array_values($list) as $i => $relative) {
            $normalized = "products/{$product->slug}-{$i}.jpg";
            $this->copySourceToStorage(is_string($relative) ? $relative : '', $normalized);
            ProductImage::query()->updateOrCreate(
                [
                    'product_id' => $product->id,
                    'path' => $normalized,
                ],
                [
                    'alt' => $product->name,
                    'sort_order' => ($i + 1) * 10,
                    'is_main' => $i === 0,
                ]
            );
        }
    }

    /**
     * Копирует файл из public/images/source/{relative} в storage/app/public/{destRelative}.
     * Поддерживает .jpg / .png (сохраняем как .jpg через пересборку GD при необходимости).
     */
    private function copySourceToStorage(string $relative, string $destRelative): void
    {
        $destFull = storage_path('app/public/'.$destRelative);
        File::ensureDirectoryExists(dirname($destFull));

        $base = public_path('images/source');
        $srcFull = $relative !== '' ? $base.'/'.ltrim($relative, '/') : '';

        if ($relative !== '' && is_file($srcFull)) {
            if (str_ends_with(strtolower($srcFull), '.png') && function_exists('imagecreatefrompng')) {
                $im = @imagecreatefrompng($srcFull);
                if ($im !== false) {
                    imagejpeg($im, $destFull, 90);
                    imagedestroy($im);

                    return;
                }
            }
            File::copy($srcFull, $destFull);

            return;
        }

        $this->writeNeutralPlaceholder($destFull);
    }

    private function writeNeutralPlaceholder(string $fullPath): void
    {
        if (function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor(800, 600);
            $bg = imagecolorallocate($im, 210, 212, 215);
            imagefill($im, 0, 0, $bg);
            imagejpeg($im, $fullPath, 88);
            imagedestroy($im);

            return;
        }

        $minimalJpeg = base64_decode(
            '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA8A/9k='
        );
        file_put_contents($fullPath, $minimalJpeg);
    }
}
