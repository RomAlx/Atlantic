<?php

namespace App\Filament\Resources\Seo;

use App\Filament\Resources\Seo\Pages\EditSeoCategory;
use App\Filament\Resources\Seo\Pages\ListSeoCategories;
use App\Filament\Resources\Seo\Schemas\SeoCategoryForm;
use App\Filament\Resources\Seo\Tables\SeoCategoriesTable;
use App\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SeoCategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $slug = 'seo-categories';

    protected static ?string $navigationLabel = 'Категории';

    protected static ?string $modelLabel = 'Категория';

    protected static ?string $pluralModelLabel = 'Категории';

    protected static string|\UnitEnum|null $navigationGroup = 'SEO';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('seo.manage') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('seo.manage') ?? false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return SeoCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeoCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoCategories::route('/'),
            'edit' => EditSeoCategory::route('/{record}/edit'),
        ];
    }
}
