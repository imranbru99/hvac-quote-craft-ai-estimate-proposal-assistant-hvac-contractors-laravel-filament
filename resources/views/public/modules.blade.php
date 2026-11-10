@extends('public.layout', ['appName' => $appName, 'title' => 'Modules', 'author' => $author])
@section('content')
<main><div class="wrap">
  <h1>Module control matrix</h1>
  <img class="shot" style="margin:1rem 0 1.5rem" src="/images/modules.svg" alt="modules"/>
  <table>
    <thead><tr><th>Key</th><th>Module</th><th>Description</th><th>Default</th></tr></thead>
    <tbody>
    @foreach($modules as $key => $module)
      <tr><td><code>{{ $key }}</code></td><td>{{ $module['name'] }}</td><td>{{ $module['description'] }}</td><td>{{ ($module['enabled'] ?? false) ? 'ON' : 'OFF' }}</td></tr>
    @endforeach
    </tbody>
  </table>
</div></main>
@endsection