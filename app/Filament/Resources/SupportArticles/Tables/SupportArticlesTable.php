<?php

namespace App\Filament\Resources\SupportArticles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupportArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                ImageColumn::make('preview_image_path')
                    ->label('Превью')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label('Описание')
                    ->limit(60)
                    ->toggleable(),
                ToggleColumn::make('is_active')
                    ->label('Опубликована'),
                TextColumn::make('seo_title')
                    ->label('SEO title')
                    ->limit(28)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (): bool => auth()->user()?->can('seo.manage') ?? false),
                TextColumn::make('seo_description')
                    ->label('SEO description')
                    ->limit(32)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (): bool => auth()->user()?->can('seo.manage') ?? false),
                TextColumn::make('updated_at')
                    ->label('Обновлена')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Опубликована'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Создана с'),
                        DatePicker::make('until')->label('по'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['from']) && empty($data['until'])) {
                            return $query;
                        }
                        if (! empty($data['from'])) {
                            $query->whereDate('created_at', '>=', $data['from']);
                        }
                        if (! empty($data['until'])) {
                            $query->whereDate('created_at', '<=', $data['until']);
                        }

                        return $query;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
