<?php

namespace App\Filament\Widgets;

use App\Services\BusinessMetrics;
use App\Services\ModuleRegistry;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessPulse extends BaseWidget
{
    protected function getStats(): array
    {
        $metrics = app(BusinessMetrics::class)->overview();
        $enabled = collect(app(ModuleRegistry::class)->all())->where('enabled', true)->count();

        return [
            Stat::make('Enabled modules', (string) $enabled)->description('HvacQuoteCraft')->color('success'),
            Stat::make('Pipeline value', '$'.number_format($metrics['pipeline_value'], 2))->description('Open estimates')->color('primary'),
            Stat::make('Draft estimates', (string) $metrics['draft_estimates'])->description('Awaiting approval')->color('warning'),
            Stat::make('Proposals', (string) $metrics['proposals'])->description('Customer-facing packs')->color('info'),
            Stat::make('Job tickets', (string) $metrics['job_tickets'])->description('Field jobs')->color('gray'),
            Stat::make('Audit events', (string) $metrics['audit_events'])->description('Approval trail')->color('success'),
        ];
    }
}
