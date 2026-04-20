<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        $mdHint = <<<'TXT'
Контент страницы в Markdown: заголовки, списки, ссылки, таблицы, цитаты, чек-листы.
Видео можно вывести ниже контента по ссылке YouTube/RuTube.
TXT;

        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        $set('slug', Str::slug((string) $state));
                    })
                    ->maxLength(255)
                    ->label('Заголовок'),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->disabled(fn (): bool => ! (auth()->user()?->hasRole('admin') ?? false))
                    ->dehydrated()
                    ->label('Slug (URL)'),
                Textarea::make('content_md')
                    ->rows(22)
                    ->columnSpanFull()
                    ->label('Контент (Markdown)')
                    ->helperText($mdHint)
                    ->extraInputAttributes([
                        'class' => 'font-mono text-sm',
                        'spellcheck' => 'false',
                    ]),
                TextInput::make('video_url')
                    ->url()
                    ->maxLength(2048)
                    ->placeholder('https://...')
                    ->label('Ссылка на видео')
                    ->helperText('Поддерживаются YouTube и RuTube ссылки.'),
                Toggle::make('is_active')
                    ->default(true)
                    ->required()
                    ->label('Активна'),
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
