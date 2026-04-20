<?php

namespace App\Filament\Resources\SupportArticles;

use App\Filament\Resources\SupportArticles\Pages\CreateSupportArticle;
use App\Filament\Resources\SupportArticles\Pages\EditSupportArticle;
use App\Filament\Resources\SupportArticles\Pages\ListSupportArticles;
use App\Filament\Resources\SupportArticles\Schemas\SupportArticleForm;
use App\Filament\Resources\SupportArticles\Tables\SupportArticlesTable;
use App\Models\SupportArticle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupportArticleResource extends Resource
{
    protected static ?string $model = SupportArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static ?string $navigationLabel = 'Техподдержка';

    protected static ?string $modelLabel = 'Статья';

    protected static ?string $pluralModelLabel = 'Статьи техподдержки';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('pages.viewAny') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('pages.manage') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return SupportArticleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupportArticlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupportArticles::route('/'),
            'create' => CreateSupportArticle::route('/create'),
            'edit' => EditSupportArticle::route('/{record}/edit'),
        ];
    }
}
