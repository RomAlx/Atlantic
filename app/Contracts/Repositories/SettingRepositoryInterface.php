<?php

namespace App\Contracts\Repositories;

use App\Models\Setting;

interface SettingRepositoryInterface
{
    public function first(): ?Setting;
}
