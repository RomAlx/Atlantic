<?php

namespace App\Filament\Resources\HomeBanners;

use App\Filament\Resources\HomeBanners\Pages\CreateHomeBanner;
use App\Filament\Resources\HomeBanners\Pages\EditHomeBanner;
use App\Filament\Resources\HomeBanners\Pages\ListHomeBanners;
use App\Filament\Resources\HomeBanners\Schemas\HomeBannerForm;
use App\Filament\Resources\HomeBanners\Tables\HomeBannersTable;
use App\Models\HomeBanner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HomeBannerResource extends Resource
{
    protected static ?string $model = HomeBanner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Баннеры главной';

    protected static ?string $modelLabel = 'Баннер';

    protected static ?string $pluralModelLabel = 'Баннеры';

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
        return HomeBannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeBannersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomeBanners::route('/'),
            'create' => CreateHomeBanner::route('/create'),
            'edit' => EditHomeBanner::route('/{record}/edit'),
        ];
    }
}
