<?php

namespace App\Filament\Resources\Seo\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SeoCategoryForm
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
                Select::make('related_category_ids')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Связанные категории')
                    ->helperText('Для блока «Сопутствующие» на карточке товара: товары из выбранных категорий, по популярности.')
                    ->options(function (?Category $record): array {
                        $query = Category::query()
                            ->where('is_active', true)
                            ->orderBy('tree_path')
                            ->orderBy('sort_order');

                        if ($record?->id) {
                            $query->whereKeyNot($record->id);
                        }

                        return $query->pluck('name', 'id')->all();
                    })
                    ->columnSpanFull(),
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
