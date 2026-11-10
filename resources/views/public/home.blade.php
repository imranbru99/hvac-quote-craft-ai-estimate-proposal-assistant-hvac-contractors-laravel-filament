@extends('public.layout', ['appName' => $appName, 'title' => 'AI Estimate & Proposal Assistant for HVAC Contractors', 'author' => $author])
@section('content')
<section class="hero"><div class="wrap">
  <div class="pill">{{ $industry }}</div>
  <h1 style="margin-top:1rem">HvacQuoteCraft</h1>
  <p>{{ $tagline }}</p>
  <div class="cta">
    <a class="btn btn-primary" href="/demo">Start Free Demo</a>
    <a class="btn btn-ghost" href="/screens">View Screens</a>
  </div>
</div></section>
<main><div class="wrap">
  <img class="shot" src="/images/dashboard.svg" alt="HvacQuoteCraft estimate dashboard"/>
  <h2 style="margin-top:2.2rem;font-family:Georgia,serif">Core estimate & proposal loop</h2>
  <div class="grid" style="margin-top:1rem">
    <div class="card"><div class="pill">1 · INTAKE</div><h3>Service request</h3><p>Capture HVAC install/replace/service intake with equipment, address, and notes.</p></div>
    <div class="card"><div class="pill">2 · AI DRAFT</div><h3>Itemized estimate</h3><p>AI drafts line items, tax, and totals from intake for estimator review.</p></div>
    <div class="card"><div class="pill">3 · APPROVE</div><h3>Human approval</h3><p>Owner or estimator approves before the customer proposal is sent.</p></div>
    <div class="card"><div class="pill">4 · AUDIT</div><h3>Propose & follow up</h3><p>Customer proposal packs, consent-aware follow-ups, immutable audit log.</p></div>
  </div>
  <h2 style="margin-top:2.2rem;font-family:Georgia,serif">Live demo pulse</h2>
  <div class="grid" style="margin-top:1rem">
    <div class="card"><div class="pill">PIPELINE</div><h3>${{ number_format($metrics['pipeline_value'] ?? 0, 2) }}</h3><p>Open estimate value</p></div>
    <div class="card"><div class="pill">DRAFTS</div><h3>{{ $metrics['draft_estimates'] ?? 0 }}</h3><p>Estimates awaiting approval</p></div>
    <div class="card"><div class="pill">PROPOSALS</div><h3>{{ $metrics['proposals'] ?? 0 }}</h3><p>Customer-facing packs</p></div>
    <div class="card"><div class="pill">AUDIT</div><h3>{{ $metrics['audit_events'] ?? 0 }}</h3><p>Approval trail events</p></div>
  </div>
  <h2 style="margin-top:2.2rem;font-family:Georgia,serif">Toggleable modules</h2>
  <div class="grid" style="margin-top:1rem">
    @foreach($modules as $key => $module)
      <div class="card">
        <div class="pill">{{ ($module['enabled'] ?? false) ? 'ENABLED' : 'OPTIONAL' }}</div>
        <h3>{{ $module['name'] }}</h3>
        <p>{{ $module['description'] }}</p>
      </div>
    @endforeach
  </div>
</div></main>
@endsection
