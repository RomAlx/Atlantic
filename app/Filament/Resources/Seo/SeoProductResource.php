<?php

namespace App\Filament\Resources\Seo;

use App\Filament\Resources\Seo\Pages\EditSeoProduct;
use App\Filament\Resources\Seo\Pages\ListSeoProducts;
use App\Filament\Resources\Seo\Schemas\SeoProductForm;
use App\Filament\Resources\Seo\Tables\SeoProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SeoProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $slug = 'seo-products';

    protected static ?string $navigationLabel = 'Товары';

    protected static ?string $modelLabel = 'Товар';

    protected static ?string $pluralModelLabel = 'Товары';

    protected static string|\UnitEnum|null $navigationGroup = 'SEO';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

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
        return SeoProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeoProductsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoProducts::route('/'),
            'edit' => EditSeoProduct::route('/{record}/edit'),
        ];
    }
}
