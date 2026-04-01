<?php

namespace App\Filament\Resources\FeedbackRequests\Pages;

use App\Filament\Resources\FeedbackRequests\FeedbackRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFeedbackRequests extends ListRecords
{
    protected static string $resource = FeedbackRequestResource::class;

    public function getSubheading(): ?string
    {
        return 'Заявки с сайта: статус, менеджер, фильтры по дате и отбору.';
    }

    public function getTabs(): array
    {
        return [
            'new' => Tab::make('Новые')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'new')),
            'in_progress' => Tab::make('В работе')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in_progress')),
            'done' => Tab::make('Закрытые')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'done')),
            'all' => Tab::make('Все')
                ->modifyQueryUsing(fn (Builder $query) => $query),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'new';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
