<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="{{ $author['name'] ?? 'Imran Ahmed' }}">
    <meta name="description" content="HvacQuoteCraft — AI Estimate & Proposal Assistant for HVAC Contractors. Draft invoices, reconcile payments, send approved reminders.">
    <title>{{ $title ?? $appName }} · {{ $appName }}</title>
    <style>
        :root{--bg:#0c1b2a;--ink:#e8f1f8;--muted:#9fb6c9;--accent:#d9ecff;--text:#0f172a;--soft:#eef3f7;--card:#fff;--line:#d5e0ea;--accent2:#1d6a9f}
        *{box-sizing:border-box} body{margin:0;font-family:"Segoe UI",Tahoma,sans-serif;color:var(--text);background:
          radial-gradient(1200px 500px at 10% -10%,#cfe3f4 0%,transparent 55%),
          linear-gradient(180deg,#eef3f7 0%,#e3ebf2 100%)}
        a{color:inherit;text-decoration:none} .wrap{max-width:1120px;margin:0 auto;padding:0 1.25rem}
        header{background:linear-gradient(135deg,#0c1b2a,#16324a 50%,#1d6a9f);color:var(--ink);padding:1rem 0;position:sticky;top:0;z-index:20;backdrop-filter:blur(8px)}
        nav{display:flex;gap:1rem;flex-wrap:wrap;align-items:center;justify-content:space-between}
        nav .brand{font-family:Georgia,"Times New Roman",serif;font-size:1.35rem;font-weight:700;letter-spacing:.02em}
        nav .links{display:flex;gap:.9rem;flex-wrap:wrap;font-size:.92rem}
        .hero{padding:4.5rem 0 3.2rem;background:
          linear-gradient(120deg,rgba(12,27,42,.92),rgba(29,106,159,.78)),
          url('/images/landing.svg') center/cover;color:var(--ink);animation:fadeUp .7s ease both}
        .hero h1{font-family:Georgia,"Times New Roman",serif;font-size:clamp(2.3rem,5vw,3.7rem);line-height:1.05;margin:0 0 1rem}
        .hero p{font-size:1.12rem;max-width:46rem;color:var(--muted)}
        .cta{display:flex;gap:.8rem;flex-wrap:wrap;margin-top:1.6rem}
        .btn{padding:.85rem 1.15rem;border-radius:10px;font-weight:700;display:inline-block;transition:transform .2s ease,box-shadow .2s ease}
        .btn:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(12,27,42,.18)}
        .btn-primary{background:var(--accent);color:#0c1b2a}
        .btn-ghost{border:1px solid var(--muted);color:var(--ink)}
        main{padding:2.4rem 0 4rem}
        .grid{display:grid;gap:1rem;grid-template-columns:repeat(auto-fit,minmax(240px,1fr))}
        .card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:1.15rem;animation:fadeUp .6s ease both}
        .card h3{margin:.15rem 0 .45rem;font-size:1.12rem;font-family:Georgia,serif}
        .card p,.muted{color:#475569;line-height:1.55}
        footer{border-top:1px solid var(--line);padding:2rem 0;color:#64748b;background:#f7fafc}
        table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden}
        th,td{padding:.75rem 1rem;border-bottom:1px solid var(--line);text-align:left}
        th{background:#e7f1f8}
        .shot{width:100%;border-radius:16px;border:1px solid var(--line);background:#fff;box-shadow:0 18px 40px rgba(12,27,42,.08)}
        .pill{display:inline-block;font-size:.75rem;padding:.2rem .55rem;border-radius:6px;background:#d9ecff;color:#0c3a5a;font-weight:700}
        @keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
    </style>
</head>
<body>
<header>
  <div class="wrap">
    <nav>
      <a class="brand" href="/">{{ $appName }}</a>
      <div class="links">
        <a href="/features">Features</a>
        <a href="/modules">Modules</a>
        <a href="/screens">Screens</a>
        <a href="/pricing">Pricing</a>
        <a href="/demo">Demo</a>
        <a href="/docs">Docs</a>
        <a href="/contact">Contact</a>
        <a href="/admin">Admin</a>
      </div>
    </nav>
  </div>
</header>
@yield('content')
<footer>
  <div class="wrap">
    <strong>{{ $appName }}</strong> · Built by
    <a href="https://imrandev.bd/contact">{{ $author['name'] ?? 'Imran Ahmed' }} (IMRAN Dev BD)</a>
    · <a href="mailto:{{ $author['email'] ?? 'me@imrandev.bd' }}">{{ $author['email'] ?? 'me@imrandev.bd' }}</a>
    · <a href="https://imrandev.bd/contact">https://imrandev.bd/contact</a>
  </div>
</footer>
</body>
</html>
