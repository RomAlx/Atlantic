<?php

namespace Database\Seeders;

use App\Models\HomeBanner;
use Illuminate\Database\Seeder;

class HomeBannerSeeder extends Seeder
{
    public function run(): void
    {
        HomeBanner::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Каталог материалов',
                'description' => 'Широкий выбор строительных и отделочных материалов для объектов любого масштаба.',
                'button_text' => 'Перейти в каталог',
                'button_link' => '/catalog',
                'button_color' => '#ea3a31',
                'background_image_path' => '/images/original/at_img_header_home.png',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        HomeBanner::query()->updateOrCreate(
            ['id' => 2],
            [
                'title' => 'Техническая поддержка',
                'description' => 'Изучайте статьи, рекомендации и видео по материалам и их правильному применению.',
                'button_text' => 'Открыть техподдержку',
                'button_link' => '/support',
                'button_color' => '#2f6fed',
                'background_image_path' => '/images/original/at_img_header_home.png',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );
    }
}
