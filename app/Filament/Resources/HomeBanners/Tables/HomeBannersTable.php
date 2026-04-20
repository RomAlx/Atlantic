<?php

namespace App\Filament\Resources\HomeBanners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HomeBannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                ImageColumn::make('background_image_path')
                    ->label('Фон')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')->label('Заголовок')->searchable()->sortable(),
                TextColumn::make('button_text')->label('Кнопка')->toggleable(),
                TextColumn::make('button_link')->label('Ссылка')->limit(30)->toggleable(),
                TextColumn::make('sort_order')->label('Порядок')->sortable(),
                ToggleColumn::make('is_active')->label('Активен'),
                TextColumn::make('updated_at')->label('Обновлен')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
