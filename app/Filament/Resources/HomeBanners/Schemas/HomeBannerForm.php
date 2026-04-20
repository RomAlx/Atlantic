<?php

namespace App\Filament\Resources\HomeBanners\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HomeBannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->label('Заголовок'),
            Textarea::make('description')
                ->rows(3)
                ->columnSpanFull()
                ->label('Описание'),
            TextInput::make('button_text')
                ->maxLength(120)
                ->label('Текст кнопки'),
            TextInput::make('button_link')
                ->maxLength(2048)
                ->placeholder('/catalog')
                ->label('Ссылка кнопки'),
            ColorPicker::make('button_color')
                ->label('Цвет кнопки')
                ->default('#ea3a31'),
            FileUpload::make('background_image_path')
                ->image()
                ->disk('public')
                ->directory('home-banners')
                ->visibility('public')
                ->label('Картинка подложка')
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
        ]);
    }
}
