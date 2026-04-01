<?php

namespace App\Filament\Resources\Seo\Pages;

use App\Filament\Resources\Seo\SeoCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditSeoCategory extends EditRecord
{
    protected static string $resource = SeoCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
