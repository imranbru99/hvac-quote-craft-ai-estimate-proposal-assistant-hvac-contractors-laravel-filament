<?php

namespace App\Http\Controllers;

use App\Services\BusinessMetrics;
use App\Services\ModuleRegistry;
use Illuminate\View\View;

class PublicSiteController extends Controller
{
    public function __construct(
        protected ModuleRegistry $modules,
        protected BusinessMetrics $metrics,
    ) {
    }

    public function home(): View
    {
        return view('public.home', $this->shared() + [
            'tagline' => 'AI estimate & proposal assistant for HVAC contractors: draft itemized quotes from intake details, build customer proposals, and require human approval with a full audit trail.',
            'industry' => 'HVAC Contractors · Home & Field Services',
        ]);
    }

    public function features(): View
    {
        return view('public.features', $this->shared());
    }

    public function modules(): View
    {
        return view('public.modules', $this->shared());
    }

    public function pricing(): View
    {
        return view('public.pricing', $this->shared());
    }

    public function demo(): View
    {
        return view('public.demo', $this->shared());
    }

    public function docs(): View
    {
        return view('public.docs', $this->shared());
    }

    public function contact(): View
    {
        return view('public.contact', $this->shared());
    }

    public function screens(): View
    {
        return view('public.screens', $this->shared());
    }

    protected function shared(): array
    {
        return [
            'appName' => config('app.name'),
            'modules' => $this->modules->all(),
            'author' => config('app.author'),
            'metrics' => $this->metrics->overview(),
        ];
    }
}
