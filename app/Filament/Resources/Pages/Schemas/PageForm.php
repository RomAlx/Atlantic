<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        $htmlHint = <<<'TXT'
HTML для блока с классами фронта: обёртка с «prose max-w-none» задаётся в шаблоне Vue.
Используйте семантику (h2, p, ul, figure), картинки с классом img-fluid rounded-3, пути /images/source/…
Пример: <p><img class="img-fluid rounded-3 mx-auto d-block" src="/images/source/normalized/image-6.jpg" alt=""></p><p>Текст абзаца.</p>
TXT;

        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                        if (blank($get('slug'))) {
                            $set('slug', Str::slug((string) $state));
                        }
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
                Textarea::make('content')
                    ->rows(22)
                    ->columnSpanFull()
                    ->label('HTML-контент')
                    ->helperText($htmlHint)
                    ->extraInputAttributes([
                        'class' => 'font-mono text-sm',
                        'spellcheck' => 'false',
                    ]),
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
