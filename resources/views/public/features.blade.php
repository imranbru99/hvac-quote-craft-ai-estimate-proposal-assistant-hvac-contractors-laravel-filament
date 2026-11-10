@extends('public.layout', ['appName' => $appName, 'title' => 'Features', 'author' => $author])
@section('content')
<main><div class="wrap">
  <h1>Complete feature surface</h1>
  <p class="muted">Every operational capability buyers care about — mapped to modules.</p>
  <div class="grid" style="margin-top:1.4rem">
    @foreach($modules as $module)
      <div class="card"><h3>{{ $module['name'] }}</h3><p>{{ $module['description'] }}</p></div>
    @endforeach
  </div>
</div></main>
@endsection