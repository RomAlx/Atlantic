<?php

namespace App\Filament\Resources\FeedbackRequests\Pages;

use App\Filament\Resources\FeedbackRequests\FeedbackRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeedbackRequest extends CreateRecord
{
    protected static string $resource = FeedbackRequestResource::class;
}
