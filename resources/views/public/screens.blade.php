@extends('public.layout', ['appName' => $appName, 'title' => 'Screens', 'author' => $author])
@section('content')
<main><div class="wrap">
  <h1>Product screens</h1>
  <p class="muted">Unique visuals generated for {{ $appName }}.</p>
  <div class="grid" style="margin-top:1rem">
    <div class="card"><h3>Landing</h3><img class="shot" src="/images/landing.svg" alt="landing"/></div>
    <div class="card"><h3>Dashboard</h3><img class="shot" src="/images/dashboard.svg" alt="dashboard"/></div>
    <div class="card"><h3>Modules</h3><img class="shot" src="/images/modules.svg" alt="modules"/></div>
    <div class="card"><h3>Workbench</h3><img class="shot" src="/images/workbench.svg" alt="workbench"/></div>
  </div>
</div></main>
@endsection