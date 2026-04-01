<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Filament\Resources\Pages\ListRecords;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    public function getSubheading(): ?string
    {
        return 'Реквизиты компании, телефоны, соцсети и тексты блоков главной страницы.';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
