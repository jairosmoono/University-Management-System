<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications Closed — {{ $uni['university_name'] ?? config('app.name') }}</title>
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
        .closed-wrap { flex:1; display:flex; align-items:center; justify-content:center; padding:2rem 1rem; }
        .closed-card { background:#fff; border-radius:18px; box-shadow:0 4px 24px rgba(0,0,0,.08); padding:3rem 2.5rem; max-width:520px; text-align:center; }
        .closed-icon { width:80px; height:80px; border-radius:50%; background:rgba(11,31,58,.06); display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; }
        .closed-icon i { font-size:2.2rem; color:var(--navy); }
        .closed-title { font-size:1.5rem; font-weight:800; color:var(--navy); margin-bottom:.5rem; }
        .closed-text { color:#6b7280; font-size:.95rem; line-height:1.6; margin-bottom:1.5rem; }
        .btn-home { background:var(--navy); color:#fff; font-weight:700; border-radius:50px; padding:.65rem 2rem; text-decoration:none; display:inline-block; }
        .btn-home:hover { background:#1a3a6b; color:#fff; }
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

<div class="closed-wrap">
    <div class="closed-card">
        <div class="closed-icon"><i class="bi bi-door-closed-fill"></i></div>
        <div class="closed-title">Applications Currently Closed</div>
        <p class="closed-text">
            Online admission applications are not being accepted at this time.
            Please check back later or contact the admissions office for more information.
        </p>
        @if(!empty($uni['university_email']) || !empty($uni['university_phone']))
        <p class="closed-text" style="margin-bottom:2rem">
            @if(!empty($uni['university_email']))<i class="bi bi-envelope me-1"></i>{{ $uni['university_email'] }}<br>@endif
            @if(!empty($uni['university_phone']))<i class="bi bi-telephone me-1"></i>{{ $uni['university_phone'] }}@endif
        </p>
        @endif
        <a href="{{ route('home') }}" class="btn-home"><i class="bi bi-arrow-left me-1"></i>Back to Home</a>
    </div>
</div>

</body>
</html>
