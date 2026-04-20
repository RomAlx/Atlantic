<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'route_name',
        'section',
        'product_slug',
        'support_article_slug',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];
}
