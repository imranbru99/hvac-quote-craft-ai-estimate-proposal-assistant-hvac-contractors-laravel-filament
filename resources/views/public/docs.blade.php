@extends('public.layout', ['appName' => $appName, 'title' => 'Docs', 'author' => $author])
@section('content')
<main><div class="wrap">
  <h1 style="font-family:Georgia,serif">Operator handbook</h1>
  <div class="card" style="margin-top:1rem">
    <ol class="muted">
      <li>Run <code>docker compose up -d --build</code> (port 8109).</li>
      <li>Sign in at <code>/admin</code> with a demo user.</li>
      <li>Open a <strong>Service Request</strong>, then click <em>AI Draft Estimate</em>.</li>
      <li>Approve the estimate, build an AI proposal, then Approve &amp; Send.</li>
      <li>Draft consent-aware follow-ups and review the <strong>Audit Log</strong>.</li>
      <li>Toggle modules in Admin → Module Control for your HVAC shop mix.</li>
    </ol>
  </div>
</div></main>
@endsection
