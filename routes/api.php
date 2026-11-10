<?php

use App\Http\Controllers\Api\MetricsController;
use App\Http\Controllers\Api\ModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());
Route::get('/modules', [ModuleController::class, 'index'])->middleware('module:api');
Route::get('/metrics', MetricsController::class)->middleware('module:api');
