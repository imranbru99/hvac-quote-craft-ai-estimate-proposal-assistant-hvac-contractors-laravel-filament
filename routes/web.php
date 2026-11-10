<?php

use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::controller(PublicSiteController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/features', 'features')->name('features');
    Route::get('/modules', 'modules')->name('modules');
    Route::get('/pricing', 'pricing')->name('pricing');
    Route::get('/demo', 'demo')->name('demo');
    Route::get('/docs', 'docs')->name('docs');
    Route::get('/screens', 'screens')->name('screens');
    Route::get('/contact', 'contact')->name('contact');
});
