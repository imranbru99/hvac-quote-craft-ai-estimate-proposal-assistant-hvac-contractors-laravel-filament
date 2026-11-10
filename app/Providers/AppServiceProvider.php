<?php

namespace App\Providers;

use App\Services\BusinessMetrics;
use App\Services\DocumentPdfService;
use App\Services\ModuleRegistry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleRegistry::class);
        $this->app->singleton(BusinessMetrics::class);
        $this->app->singleton(DocumentPdfService::class);
    }

    public function boot(): void
    {
        //
    }
}
