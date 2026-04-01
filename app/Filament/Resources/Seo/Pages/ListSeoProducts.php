<?php

namespace App\Filament\Resources\Seo\Pages;

use App\Filament\Resources\Seo\SeoProductResource;
use Filament\Resources\Pages\ListRecords;

class ListSeoProducts extends ListRecords
{
    protected static string $resource = SeoProductResource::class;

    public function getSubheading(): ?string
    {
        return 'SEO-поля карточек товаров: заголовок и описание для поисковых систем.';
    }
}
