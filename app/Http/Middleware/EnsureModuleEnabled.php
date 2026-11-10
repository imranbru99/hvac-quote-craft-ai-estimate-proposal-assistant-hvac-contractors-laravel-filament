<?php

namespace App\Http\Middleware;

use App\Services\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function __construct(protected ModuleRegistry $modules)
    {
    }

    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! $this->modules->enabled($module)) {
            abort(404, "Module [{$module}] is disabled.");
        }

        return $next($request);
    }
}
