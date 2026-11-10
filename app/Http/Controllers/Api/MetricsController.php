<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessMetrics;
use Illuminate\Http\JsonResponse;

class MetricsController extends Controller
{
    public function __invoke(BusinessMetrics $metrics): JsonResponse
    {
        return response()->json($metrics->overview());
    }
}
