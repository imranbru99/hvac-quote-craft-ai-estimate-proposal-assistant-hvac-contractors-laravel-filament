<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ModuleRegistry;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    public function index(ModuleRegistry $modules): JsonResponse
    {
        return response()->json([
            'application' => config('app.name'),
            'author' => config('app.author'),
            'modules' => $modules->all(),
        ]);
    }
}
