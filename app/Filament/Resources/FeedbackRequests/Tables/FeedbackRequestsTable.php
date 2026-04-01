<?php

namespace App\Filament\Resources\FeedbackRequests\Tables;

use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeedbackRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Имя'),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('source_page')
                    ->label('Страница')
                    ->limit(24)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('message')
                    ->label('Сообщение')
                    ->limit(40)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('manager_notes')
                    ->label('Комментарий')
                    ->limit(32)
                    ->tooltip(fn ($state) => $state)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->label('Статус')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Новая',
                        'in_progress' => 'В работе',
                        'done' => 'Закрыта',
                        default => $state,
                    }),
                TextColumn::make('manager.name')
                    ->label('Менеджер')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->label('Создана'),
                TextColumn::make('updated_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->label('Обновлена')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('in_progress_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->label('В работе с')
                    ->placeholder('—'),
                TextColumn::make('completed_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->label('Закрыта')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'new' => 'Новая',
                        'in_progress' => 'В работе',
                        'done' => 'Закрыта',
                    ]),
                SelectFilter::make('manager_id')
                    ->label('Менеджер')
                    ->relationship('manager', 'name')
                    ->searchable()
                    ->preload(),
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
            ]);
    }
}
