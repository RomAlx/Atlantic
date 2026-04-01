<?php

namespace App\Filament\Resources\Seo;

use App\Filament\Resources\Seo\Pages\EditSeoPage;
use App\Filament\Resources\Seo\Pages\ListSeoPages;
use App\Filament\Resources\Seo\Schemas\SeoPageForm;
use App\Filament\Resources\Seo\Tables\SeoPagesTable;
use App\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SeoPageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $slug = 'seo-pages';

    protected static ?string $navigationLabel = 'Страницы';

    protected static ?string $modelLabel = 'Страница';

    protected static ?string $pluralModelLabel = 'Страницы';

    protected static string|\UnitEnum|null $navigationGroup = 'SEO';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

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
        return SeoPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeoPagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoPages::route('/'),
            'edit' => EditSeoPage::route('/{record}/edit'),
        ];
    }
}
