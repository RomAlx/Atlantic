<?php

use App\Http\Controllers\SiteApiController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [SiteApiController::class, 'home']);
Route::get('/catalog', [SiteApiController::class, 'catalog']);
Route::get('/catalog/{categorySlug}/subcategories', [SiteApiController::class, 'categorySubcategories']);
Route::get('/catalog/{categorySlug}/products', [SiteApiController::class, 'categoryProducts']);
Route::get('/catalog/{categorySlug}', [SiteApiController::class, 'category']);
Route::get('/catalog/{categorySlug}/{productSlug}', [SiteApiController::class, 'product']);
Route::get('/pages/{slug}', [SiteApiController::class, 'page']);
Route::get('/search', [SiteApiController::class, 'search']);
Route::get('/settings', [SiteApiController::class, 'settings']);
Route::get('/contacts', [SiteApiController::class, 'contacts']);
Route::post('/feedback', [SiteApiController::class, 'feedback'])->middleware('throttle:10,1');
