<?php

namespace App\Filament\Resources\FeedbackRequests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeedbackRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Имя'),
                TextInput::make('phone')
                    ->maxLength(255)
                    ->label('Телефон'),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->label('Email'),
                TextInput::make('source_page')
                    ->maxLength(255)
                    ->label('Страница-источник'),
                Select::make('status')
                    ->options([
                        'new' => 'Новая',
                        'in_progress' => 'В работе',
                        'done' => 'Закрыта',
                    ])
                    ->required()
                    ->default('new')
                    ->label('Статус'),
                Select::make('manager_id')
                    ->label('Менеджер')
                    ->relationship(
                        'manager',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereHas(
                            'roles',
                            fn (Builder $q) => $q->whereIn('name', ['admin', 'feedback_manager'])
                        ),
                    )
                    ->searchable()
                    ->preload(),
                Textarea::make('message')
                    ->rows(6)
                    ->columnSpanFull()
                    ->label('Сообщение клиента')
                    ->visible(fn (?Model $record): bool => $record !== null)
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('manager_notes')
                    ->rows(5)
                    ->columnSpanFull()
                    ->label('Комментарий')
                    ->placeholder('Комментарий менеджера по заявке (на сайте не отображается)'),
            ]);
    }
}
