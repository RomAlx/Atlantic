<?php

namespace App\Filament\Resources\Seo\Pages;

use App\Filament\Resources\Seo\SeoCategoryResource;
use Filament\Resources\Pages\ListRecords;

class ListSeoCategories extends ListRecords
{
    protected static string $resource = SeoCategoryResource::class;

    public function getSubheading(): ?string
    {
        return 'SEO-поля категорий каталога.';
    }
}
