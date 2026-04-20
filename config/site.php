<?php

return [

    'defaults' => [
        'company_name' => 'Atlantic Group',
        'email' => 'info@atlantic.local',
    ],

    /*
    | Ключи соцсетей для формы настроек и иконок на сайте.
    | Дополнительно: other — произвольная сеть (подпись в ссылке).
    */
    'social_networks' => [
        'telegram' => ['label' => 'Telegram'],
        'max' => ['label' => 'MAX'],
        'vk' => ['label' => 'VK'],
        'ok' => ['label' => 'Одноклассники'],
        'whatsapp' => ['label' => 'WhatsApp'],
        'youtube' => ['label' => 'YouTube'],
        'rutube' => ['label' => 'Rutube'],
        'dzen' => ['label' => 'Дзен'],
        'vkvideo' => ['label' => 'VK Видео'],
        'other' => ['label' => 'Другое'],
    ],

    /*
    | Изображения для сидера каталога: пути относительно public/images/source/
    | (те же файлы, что используются в вёрстке страниц). Если файла нет — сидер
    | запишет нейтральный плейсхолдер в storage и public/images/source/seed/.
    */
    'seed_images' => [
        'categories' => [
            'klei-dlya-mebeli-i-derevoobrabotki' => 'normalized/image-6.jpg',
            'klei-dlya-stroitelstva-i-remonta' => 'normalized/image-7.jpg',
            'pva-i-dispersionnye-sistemy' => 'normalized/image-3.jpg',
        ],
        'products' => [
            'ecol-103' => [
                'normalized/image-3.jpg',
                'normalized/image-5.jpg',
            ],
            'homacoll-135' => [
                'normalized/image-4.jpg',
                'normalized/our-clients-title.jpg',
            ],
            'homacoll-017' => [
                'normalized/image-5.jpg',
                'normalized/image-6.jpg',
            ],
            'homacoll-707' => [
                'normalized/image-3.jpg',
                'normalized/image-7.jpg',
            ],
        ],
    ],

];
