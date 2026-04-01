<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'alt',
        'sort_order',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    /**
     * Same-origin URL for API / SPA (e.g. /storage/products/…).
     */
    public function publicUrl(): string
    {
        return MediaUrl::publicHref($this->path) ?? '';
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
