<?php

namespace App\Filament\Resources\Seo\Pages;

use App\Filament\Resources\Seo\SeoPageResource;
use Filament\Resources\Pages\ListRecords;

class ListSeoPages extends ListRecords
{
    protected static string $resource = SeoPageResource::class;

    public function getSubheading(): ?string
    {
        return 'Мета-теги и описания для страниц контента (title, description).';
    }
}
