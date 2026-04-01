<?php

namespace App\Support;

use Illuminate\Support\Facades\URL;

/**
 * Absolute URLs for public-disk files so the SPA and API work across dev ports and production.
 */
final class MediaUrl
{
    public static function publicHref(?string $storedPath): ?string
    {
        if (blank($storedPath)) {
            return null;
        }

        if (str_starts_with($storedPath, 'http://') || str_starts_with($storedPath, 'https://')) {
            return $storedPath;
        }

        $path = str_starts_with($storedPath, '/')
            ? $storedPath
            : '/storage/'.ltrim($storedPath, '/');

        return URL::to($path);
    }
}
