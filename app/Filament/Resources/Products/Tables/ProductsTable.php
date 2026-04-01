<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'images' => fn ($q) => $q->orderByDesc('is_main')->orderBy('sort_order'),
                'category',
            ]))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                ImageColumn::make('thumb')
                    ->label('Фото')
                    ->getStateUsing(fn (Product $record): ?string => $record->images->first()?->path)
                    ->disk('public')
                    ->visibility('public')
                    ->checkFileExistence(false)
                    ->height(40),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Название'),
                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->label('Категория'),
                TextColumn::make('sku')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Артикул'),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('short_description')
                    ->label('Краткое описание')
                    ->limit(36)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_active')
                    ->label('Активен'),
                TextColumn::make('seo_title')
                    ->label('SEO title')
                    ->limit(28)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (): bool => auth()->user()?->can('seo.manage') ?? false),
                TextColumn::make('sort_order')
                    ->sortable()
                    ->label('Порядок'),
                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Активен'),
                SelectFilter::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Создан с'),
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
            ]);
    }
}
