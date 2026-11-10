@extends('public.layout', ['appName' => $appName, 'title' => 'Demo', 'author' => $author])
@section('content')
<main><div class="wrap">
  <h1 style="font-family:Georgia,serif">Demo access</h1>
  <p class="muted">Seeded multi-role users for HvacQuoteCraft (password for all: <code>password</code>).</p>
  <table style="margin-top:1rem">
    <thead><tr><th>Role</th><th>Email</th><th>Password</th></tr></thead>
    <tbody>
      <tr><td>Super Admin</td><td><code>admin@demo.local</code></td><td><code>password</code></td></tr>
      <tr><td>HVAC Owner</td><td><code>owner@demo.local</code></td><td><code>password</code></td></tr>
      <tr><td>Operations Lead</td><td><code>manager@demo.local</code></td><td><code>password</code></td></tr>
      <tr><td>Estimator</td><td><code>estimator@demo.local</code></td><td><code>password</code></td></tr>
      <tr><td>Office Staff</td><td><code>staff@demo.local</code></td><td><code>password</code></td></tr>
      <tr><td>Field Technician</td><td><code>tech@demo.local</code></td><td><code>password</code></td></tr>
      <tr><td>Customer Portal</td><td><code>customer@demo.local</code></td><td><code>password</code></td></tr>
    </tbody>
  </table>
  <p style="margin-top:1.4rem"><a class="btn btn-primary" style="background:#1d6a9f;color:#fff" href="/admin">Open Admin Panel</a></p>
</div></main>
@endsection
