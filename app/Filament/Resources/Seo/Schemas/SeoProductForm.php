<?php

namespace App\Filament\Resources\Seo\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SeoProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('slug')
                    ->label('Slug')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('sku')
                    ->label('Артикул')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('seo_title')
                    ->maxLength(255)
                    ->label('SEO title'),
                Textarea::make('seo_description')
                    ->rows(4)
                    ->label('SEO description')
                    ->columnSpanFull(),
            ]);
    }
}
