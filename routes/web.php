<?php

use App\Http\Controllers\SiteApiController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::post('/feedback', [SiteApiController::class, 'feedback'])
    ->middleware(['web', 'throttle:10,1']);

Route::get('/robots.txt', [SiteController::class, 'robots'])->name('site.robots');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('site.sitemap');
Route::get('/{any?}', [SiteController::class, 'app'])
    ->where('any', '^(?!admin|livewire|api|up|storage).*$');
