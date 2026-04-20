<?php

namespace App\Filament\Resources\SupportArticles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SupportArticleForm
{
    public static function configure(Schema $schema): Schema
    {
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
                    ->label('Slug (URL)'),
                FileUpload::make('preview_image_path')
                    ->image()
                    ->disk('public')
                    ->directory('support-articles')
                    ->visibility('public')
                    ->label('Превью-изображение')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->rows(3)
                    ->label('Краткое описание')
                    ->columnSpanFull(),
                Textarea::make('content_md')
                    ->rows(22)
                    ->columnSpanFull()
                    ->label('Статья (Markdown)')
                    ->helperText('Поддерживаются заголовки, списки, ссылки, изображения и таблицы в формате Markdown.')
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
                    ->label('Опубликована'),
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
