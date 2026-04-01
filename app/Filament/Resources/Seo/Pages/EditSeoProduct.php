<?php

namespace App\Filament\Resources\Seo\Pages;

use App\Filament\Resources\Seo\SeoProductResource;
use Filament\Resources\Pages\EditRecord;

class EditSeoProduct extends EditRecord
{
    protected static string $resource = SeoProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
