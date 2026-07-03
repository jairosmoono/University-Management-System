<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted — {{ $uni['university_name'] ?? config('app.name') }}</title>
    @if(!empty($uni['favicon_path']))
    <link rel="icon" href="{{ asset('storage/' . $uni['favicon_path']) }}" type="image/x-icon">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root { --navy:#0B1F3A; --gold:#C9A84C; }
        body { background:#F4F6FA; font-family:'Segoe UI',system-ui,sans-serif; min-height:100vh; display:flex; flex-direction:column; }
        .topbar { background:var(--navy); padding:.75rem 0; }
        .brand-name { color:#fff; font-weight:700; font-size:1.05rem; }
        .brand-tag  { color:var(--gold); font-size:.7rem; letter-spacing:.08em; text-transform:uppercase; }
        .success-card { background:#fff; border-radius:20px; box-shadow:0 4px 32px rgba(0,0,0,.09); padding:3rem 2.5rem; text-align:center; max-width:540px; margin:auto; }
        .check-ring { width:90px; height:90px; border-radius:50%; background:rgba(16,185,129,.12); border:3px solid #10b981; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; }
        .check-ring i { color:#10b981; font-size:2.2rem; }
        .ref-box { background:rgba(11,31,58,.05); border:2px dashed rgba(11,31,58,.2); border-radius:12px; padding:1rem 1.5rem; display:inline-block; margin:1rem 0; }
        .ref-label { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#6b7280; }
        .ref-number { font-size:1.7rem; font-weight:800; color:var(--navy); letter-spacing:.04em; }
        .next-steps { text-align:left; background:var(--navy); border-radius:14px; padding:1.5rem; margin-top:1.5rem; }
        .next-steps .step-item { display:flex; gap:.75rem; align-items:flex-start; margin-bottom:.9rem; }
        .next-steps .step-item:last-child { margin-bottom:0; }
        .next-steps .step-num { width:26px; height:26px; border-radius:50%; background:var(--gold); color:var(--navy); font-size:.8rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .next-steps p { color:rgba(255,255,255,.75); font-size:.88rem; line-height:1.5; margin:0; }
        .next-steps strong { color:#fff; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="container d-flex align-items-center gap-2">
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            @if(!empty($uni['logo_path']))
            <img src="{{ asset('storage/' . $uni['logo_path']) }}" alt="Logo"
                 style="height:36px;max-width:40px;object-fit:contain;border-radius:6px;background:#fff;padding:2px">
            @else
            <div style="width:34px;height:34px;border-radius:8px;background:var(--gold);display:flex;align-items:center;justify-content:center">
                <i class="bi bi-mortarboard-fill" style="color:var(--navy)"></i>
            </div>
            @endif
            <div>
                <div class="brand-name">{{ $uni['university_name'] ?? config('app.name') }}</div>
                <div class="brand-tag">Online Admissions Portal</div>
            </div>
        </a>
    </div>
</div>

<div class="container py-5 flex-fill d-flex align-items-center">
    <div class="success-card w-100">
        <div class="check-ring">
            <i class="bi bi-check-lg"></i>
        </div>

        <h2 style="color:var(--navy);font-weight:800;font-size:1.6rem;margin-bottom:.5rem">
            Application Submitted!
        </h2>
        <p class="text-muted" style="font-size:.95rem;line-height:1.6;margin-bottom:.5rem">
            Thank you for applying to <strong>{{ $uni['university_name'] ?? config('app.name') }}</strong>.
            Your application has been received and is under review.
        </p>

        <div class="ref-box">
            <div class="ref-label">Your Application Number</div>
            <div class="ref-number">{{ $ref }}</div>
        </div>

        <p class="text-muted small mt-2">
            <i class="bi bi-info-circle me-1"></i>
            Please save this number — you will need it to follow up on your application status.
        </p>

        <div class="next-steps">
            <div class="fw-bold text-white mb-3" style="font-size:.85rem;letter-spacing:.06em;text-transform:uppercase">What Happens Next</div>
            <div class="step-item">
                <div class="step-num">1</div>
                <p><strong>Application Review</strong><br>Our admissions team will review your application within 5–10 working days.</p>
            </div>
            <div class="step-item">
                <div class="step-num">2</div>
                <p><strong>Email Notification</strong><br>You will receive an email at the address you provided with the outcome of your application.</p>
            </div>
            <div class="step-item">
                <div class="step-num">3</div>
                <p><strong>Enrolment</strong><br>If approved, you will receive an official admission letter and instructions for enrolment.</p>
            </div>
        </div>

        <div class="d-flex gap-3 justify-content-center mt-4 flex-wrap">
            <a href="{{ route('home') }}" class="btn fw-bold px-4 py-2 rounded-pill"
               style="background:var(--navy);color:#fff">
                <i class="bi bi-house me-1"></i>Back to Home
            </a>
            <a href="{{ route('apply') }}" class="btn fw-bold px-4 py-2 rounded-pill"
               style="background:transparent;border:2px solid var(--navy);color:var(--navy)">
                <i class="bi bi-plus me-1"></i>New Application
            </a>
        </div>

        @if(!empty($uni['university_email']))
        <p class="text-muted small mt-4 mb-0">
            Questions? Email us at
            <a href="mailto:{{ $uni['university_email'] }}" style="color:var(--gold)">{{ $uni['university_email'] }}</a>
        </p>
        @endif
    </div>
</div>

</body>
</html>
