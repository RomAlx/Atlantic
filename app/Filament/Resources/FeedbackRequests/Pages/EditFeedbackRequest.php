<?php

namespace App\Filament\Resources\FeedbackRequests\Pages;

use App\Filament\Resources\FeedbackRequests\FeedbackRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditFeedbackRequest extends EditRecord
{
    protected static string $resource = FeedbackRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
