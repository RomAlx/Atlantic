<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Тяжёлый тестовый каталог (10×10×10 товаров): php artisan db:seed --class=CatalogBulkTestSeeder
        $this->call([
            AuthSeeder::class,
            CatalogSeeder::class,
            ContentSeeder::class,
            HomeBannerSeeder::class,
        ]);
    }
}
