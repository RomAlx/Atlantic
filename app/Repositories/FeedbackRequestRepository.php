<?php

namespace App\Repositories;

use App\Contracts\Repositories\FeedbackRequestRepositoryInterface;
use App\Models\FeedbackRequest;

class FeedbackRequestRepository implements FeedbackRequestRepositoryInterface
{
    public function create(array $attributes): FeedbackRequest
    {
        return FeedbackRequest::query()->create($attributes);
    }
}
