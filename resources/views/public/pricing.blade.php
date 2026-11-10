@extends('public.layout', ['appName' => $appName, 'title' => 'Pricing', 'author' => $author])
@section('content')
<main><div class="wrap">
  <h1 style="font-family:Georgia,serif">Pilot packaging for HVAC contractors</h1>
  <p class="muted">Hypothesis from opportunity research: Pilot $299–$1,499/mo.</p>
  <div class="grid" style="margin-top:1.4rem">
    <div class="card"><h3>Starter Quoting</h3><p>$299/mo — Service requests, AI estimate drafts, approval queue, proposal PDF stubs, email follow-ups.</p></div>
    <div class="card"><h3>Growth Ops</h3><p>$799/mo — Pipeline dashboard, Jobber/ServiceTitan/QuickBooks hooks, technician roster, SMS/WhatsApp consent flows, win-rate reports.</p></div>
    <div class="card"><h3>Enterprise Field Revenue</h3><p>$1,499/mo — Multi-branch, API, audit packs, Google Calendar sync, advanced AI Hub routing, customer portal.</p></div>
  </div>
</div></main>
@endsection
