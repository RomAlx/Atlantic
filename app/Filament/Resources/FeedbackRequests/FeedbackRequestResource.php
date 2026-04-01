<?php

namespace App\Filament\Resources\FeedbackRequests;

use App\Filament\Resources\FeedbackRequests\Pages\CreateFeedbackRequest;
use App\Filament\Resources\FeedbackRequests\Pages\EditFeedbackRequest;
use App\Filament\Resources\FeedbackRequests\Pages\ListFeedbackRequests;
use App\Filament\Resources\FeedbackRequests\Schemas\FeedbackRequestForm;
use App\Filament\Resources\FeedbackRequests\Tables\FeedbackRequestsTable;
use App\Models\FeedbackRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FeedbackRequestResource extends Resource
{
    protected static ?string $model = FeedbackRequest::class;

    protected static ?string $navigationLabel = 'Заявки';

    protected static ?string $modelLabel = 'Заявка';

    protected static ?string $pluralModelLabel = 'Заявки';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('feedback.viewAny') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('feedback.manage') ?? false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return FeedbackRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FeedbackRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeedbackRequests::route('/'),
            'create' => CreateFeedbackRequest::route('/create'),
            'edit' => EditFeedbackRequest::route('/{record}/edit'),
        ];
    }
}
