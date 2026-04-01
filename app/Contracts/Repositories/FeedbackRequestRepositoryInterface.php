<?php

namespace App\Contracts\Repositories;

use App\Models\FeedbackRequest;

interface FeedbackRequestRepositoryInterface
{
    public function create(array $attributes): FeedbackRequest;
}
