<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance — {{ $uni['university_name'] ?? config('app.name') }}</title>
    @if(!empty($uni['favicon_path']))
    <link rel="icon" href="{{ asset('storage/' . $uni['favicon_path']) }}" type="image/x-icon">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root { --navy:#0B1F3A; --gold:#C9A84C; }
        body { background:#F4F6FA; font-family:'Segoe UI',system-ui,sans-serif; min-height:100vh; display:flex; flex-direction:column; }
        .topbar { background:var(--navy); padding:.75rem 0; }
        .topbar .brand-name { color:#fff; font-weight:700; font-size:1.05rem; }
        .topbar .brand-tag  { color:var(--gold); font-size:.7rem; letter-spacing:.08em; text-transform:uppercase; }
        .wrap { flex:1; display:flex; align-items:center; justify-content:center; padding:2rem 1rem; }
        .card-box { background:#fff; border-radius:18px; box-shadow:0 4px 24px rgba(0,0,0,.08); padding:3rem 2.5rem; max-width:520px; text-align:center; }
        .icon-circle { width:80px; height:80px; border-radius:50%; background:rgba(201,168,76,.12); display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; }
        .icon-circle i { font-size:2.2rem; color:var(--gold); }
        .title { font-size:1.5rem; font-weight:800; color:var(--navy); margin-bottom:.5rem; }
        .text { color:#6b7280; font-size:.95rem; line-height:1.6; margin-bottom:1.5rem; }
        .btn-login { background:var(--navy); color:#fff; font-weight:700; border-radius:50px; padding:.65rem 2rem; text-decoration:none; display:inline-block; }
        .btn-login:hover { background:#1a3a6b; color:#fff; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="container d-flex align-items-center gap-2">
        @if(!empty($uni['logo_path']))
        <img src="{{ asset('storage/' . $uni['logo_path']) }}" alt="Logo" style="height:32px;max-width:38px;object-fit:contain;border-radius:6px;background:#fff;padding:2px">
        @endif
        <div>
            <div class="brand-name">{{ $uni['university_name'] ?? config('app.name') }}</div>
            <div class="brand-tag">{{ $uni['university_short_name'] ?? '' }}</div>
        </div>
    </div>
</div>

<div class="wrap">
    <div class="card-box">
        <div class="icon-circle"><i class="bi bi-tools"></i></div>
        <div class="title">We'll Be Right Back</div>
        <p class="text">
            {{ $uni['university_name'] ?? config('app.name') }} is currently undergoing scheduled maintenance.
            Please check back shortly. We apologise for the inconvenience.
        </p>
        <a href="{{ route('login') }}" class="btn-login"><i class="bi bi-box-arrow-in-right me-1"></i>Administrator Login</a>
    </div>
</div>

</body>
</html>
