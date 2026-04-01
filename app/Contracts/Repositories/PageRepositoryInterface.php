<?php

namespace App\Contracts\Repositories;

use App\Models\Page;

interface PageRepositoryInterface
{
    public function findActiveBySlug(string $slug): Page;
}
