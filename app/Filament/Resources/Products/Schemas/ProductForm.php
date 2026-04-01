<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Категория'),
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                        if (blank($get('slug'))) {
                            $set('slug', Str::slug((string) $state));
                        }
                    })
                    ->maxLength(255)
                    ->label('Название'),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('sku')
                    ->maxLength(255)
                    ->label('Артикул'),
                Textarea::make('short_description')
                    ->rows(3)
                    ->label('Краткое описание'),
                Textarea::make('description')
                    ->rows(6)
                    ->columnSpanFull()
                    ->label('Описание'),
                KeyValue::make('specifications')
                    ->keyLabel('Параметр')
                    ->valueLabel('Значение')
                    ->label('Характеристики')
                    ->columnSpanFull(),
                Repeater::make('images')
                    ->relationship('images')
                    ->label('Изображения')
                    ->schema([
                        FileUpload::make('path')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->required()
                            ->label('Файл'),
                        TextInput::make('alt')
                            ->maxLength(255)
                            ->label('Alt'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->label('Сортировка'),
                        Toggle::make('is_main')
                            ->default(false)
                            ->label('Главное'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->label('Порядок сортировки'),
                Toggle::make('is_active')
                    ->default(true)
                    ->required()
                    ->label('Активен'),
                TextInput::make('seo_title')
                    ->maxLength(255)
                    ->label('SEO title')
                    ->visible(fn (): bool => auth()->user()?->can('seo.manage') ?? false),
                Textarea::make('seo_description')
                    ->rows(3)
                    ->label('SEO description')
                    ->visible(fn (): bool => auth()->user()?->can('seo.manage') ?? false),
            ]);
    }
}
