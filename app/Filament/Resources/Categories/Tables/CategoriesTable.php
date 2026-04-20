<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                ImageColumn::make('image_path')
                    ->disk('public')
                    ->visibility('public')
                    ->checkFileExistence(false)
                    ->height(40)
                    ->label('Фото'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Название')
                    ->url(fn (Category $record): string => ($record->children_count ?? 0) > 0
                        ? CategoryResource::getUrl('index', ['parent' => $record->id])
                        : CategoryResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('related_category_ids')
                    ->label('Связанные')
                    ->toggleable()
                    ->getStateUsing(function (Category $record): string {
                        $ids = $record->related_category_ids;
                        if (! is_array($ids) || $ids === []) {
                            return '—';
                        }

                        $names = Category::query()->whereIn('id', $ids)->pluck('name', 'id');

                        return collect($ids)
                            ->map(fn ($id) => $names->get((int) $id))
                            ->filter()
                            ->implode(', ') ?: '—';
                    }),
                TextColumn::make('children_count')
                    ->label('Подкатегорий')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Описание')
                    ->limit(40)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_order')
                    ->sortable()
                    ->label('Порядок'),
                ToggleColumn::make('is_active')
                    ->label('Активна'),
                TextColumn::make('seo_title')
                    ->label('SEO title')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (): bool => auth()->user()?->can('seo.manage') ?? false),
                TextColumn::make('tree_path')
                    ->label('Tree path')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Обновлена')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Активна'),
                SelectFilter::make('parent_id')
                    ->label('Родитель (фильтр)')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('roots')
                    ->label('Только родительские (без вложенных)')
                    ->query(fn (Builder $q) => $q->whereNull('parent_id'))
                    ->toggle(),
            ])
            ->recordActions([
                Action::make('subcategories')
                    ->label('Подкатегории')
                    ->icon(Heroicon::ChevronRight)
                    ->url(fn (Category $record): string => CategoryResource::getUrl('index', ['parent' => $record->id]))
                    ->visible(fn (Category $record): bool => (int) ($record->children_count ?? 0) > 0),
                EditAction::make(),
            ]);
    }
}
