<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        $socialOptions = collect(config('site.social_networks', []))
            ->mapWithKeys(fn (array $meta, string $key): array => [$key => $meta['label']])
            ->all();

        return $schema
            ->components([
                TextInput::make('company_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Название компании'),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->label('Email'),
                TextInput::make('feedback_email')
                    ->email()
                    ->default('aaromanovsky@ya.ru')
                    ->required()
                    ->maxLength(255)
                    ->label('Email для заявок')
                    ->helperText('Используется только для отправки заявок с сайта, на фронте не отображается.'),
                TextInput::make('address')
                    ->maxLength(255)
                    ->label('Адрес'),
                TextInput::make('warehouse_address')
                    ->maxLength(255)
                    ->label('Адрес склада'),
                Repeater::make('phones')
                    ->label('Телефоны компании')
                    ->schema([
                        TextInput::make('label')
                            ->label('Для чего')
                            ->placeholder('Отдел продаж, бухгалтерия…')
                            ->maxLength(120)
                            ->required(),
                        TextInput::make('number')
                            ->label('Номер')
                            ->tel()
                            ->maxLength(64)
                            ->required(),
                        Toggle::make('is_main')
                            ->label('Основной в шапке сайта')
                            ->inline(false),
                    ])
                    ->columns(3)
                    ->defaultItems(0)
                    ->addActionLabel('Добавить телефон')
                    ->columnSpanFull(),
                Repeater::make('social_links')
                    ->label('Соцсети')
                    ->schema([
                        Select::make('network')
                            ->label('Площадка')
                            ->options($socialOptions)
                            ->searchable()
                            ->required(),
                        TextInput::make('url')
                            ->label('Ссылка')
                            ->url()
                            ->maxLength(2048)
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Добавить ссылку')
                    ->columnSpanFull(),
                Section::make('Тексты главной страницы')
                    ->description('Доступно только администраторам. Редактирует блок «О компании» и карточки «Информация для клиентов».')
                    ->visible(fn (): bool => auth()->user()?->hasRole('admin') ?? false)
                    ->schema([
                        Textarea::make('home_about_paragraph_1')
                            ->label('Абзац 1 («О компании»)')
                            ->rows(4)
                            ->columnSpanFull(),
                        Textarea::make('home_about_paragraph_2')
                            ->label('Абзац 2 («О компании»)')
                            ->rows(4)
                            ->columnSpanFull(),
                        Repeater::make('home_client_blocks')
                            ->label('Карточки «Информация для клиентов»')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Заголовок')
                                    ->maxLength(255)
                                    ->required(),
                                Textarea::make('text')
                                    ->label('Текст')
                                    ->rows(3)
                                    ->required(),
                            ])
                            ->columns(1)
                            ->defaultItems(4)
                            ->minItems(0)
                            ->maxItems(8)
                            ->addActionLabel('Добавить карточку')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),
                Section::make('Яндекс')
                    ->schema([
                        TextInput::make('yandex_metrika_counter_id')
                            ->label('Metrika counter ID')
                            ->maxLength(32)
                            ->default('108684147')
                            ->helperText('ID счетчика Яндекс.Метрики для фронта.'),
                        TextInput::make('yandex_maps_api_key')
                            ->label('Maps API key')
                            ->maxLength(255)
                            ->helperText('Ключ API для геокодирования и карт на странице контактов.'),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }
}
