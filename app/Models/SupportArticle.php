<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'preview_image_path',
        'description',
        'content_md',
        'video_url',
        'is_active',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function previewImagePublicUrl(): ?string
    {
        return MediaUrl::publicHref($this->preview_image_path);
    }
}
