<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->relationship(
                        'parent',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->orderBy('tree_path')->orderBy('sort_order'),
                    )
                    ->searchable()
                    ->preload()
                    ->label('Родительская категория'),
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        $set('slug', Str::slug((string) $state));
                    })
                    ->maxLength(255)
                    ->label('Название'),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull()
                    ->label('Описание'),
                Select::make('related_category_ids')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Связанные категории')
                    ->helperText('Товары из этих категорий показываются в блоке «Сопутствующие» на карточке товара, по популярности.')
                    ->options(function (?Category $record): array {
                        $query = Category::query()
                            ->where('is_active', true)
                            ->orderBy('tree_path')
                            ->orderBy('sort_order');

                        if ($record?->id) {
                            $query->whereKeyNot($record->id);
                        }

                        return $query->pluck('name', 'id')->all();
                    })
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->image()
                    ->disk('public')
                    ->directory('categories')
                    ->visibility('public')
                    ->label('Изображение')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->label('Порядок сортировки'),
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
