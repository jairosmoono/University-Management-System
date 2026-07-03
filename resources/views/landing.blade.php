<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $uni['university_name'] ?? config('app.name') }} — Excellence in Education</title>
    @if(!empty($uni['favicon_path']))
    <link rel="icon" href="{{ asset('storage/' . $uni['favicon_path']) }}" type="image/x-icon">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --navy:  #0B1F3A;
            --gold:  #C9A84C;
            --gold2: #e8c66d;
            --light: #F8F9FB;
        }

        /* ── Global ───────────────────────────────────────────────────── */
        body { font-family: 'Segoe UI', system-ui, sans-serif; color: #1a1a2e; overflow-x: hidden; }
        a { text-decoration: none; }
        section { scroll-margin-top: 80px; }

        /* ── Navbar ───────────────────────────────────────────────────── */
        #mainNav {
            background: rgba(11,31,58,0.96);
            backdrop-filter: blur(8px);
            transition: background .3s;
        }
        #mainNav.scrolled { background: var(--navy); box-shadow: 0 2px 20px rgba(0,0,0,.3); }
        .navbar-brand span.brand-uni { font-size: 1.15rem; font-weight: 700; color: #fff; letter-spacing: .02em; }
        .navbar-brand span.brand-tag { font-size: .68rem; color: var(--gold); letter-spacing: .08em; display: block; text-transform: uppercase; }
        .nav-link-custom { color: rgba(255,255,255,.82) !important; font-size: .88rem; font-weight: 500; padding: .5rem .9rem !important; border-radius: 6px; transition: color .2s, background .2s; }
        .nav-link-custom:hover { color: var(--gold) !important; background: rgba(255,255,255,.06); }
        .btn-nav-login { background: var(--gold); color: var(--navy) !important; font-weight: 700; padding: .42rem 1.2rem !important; border-radius: 50px; font-size: .85rem; transition: background .2s, transform .15s; }
        .btn-nav-login:hover { background: var(--gold2); transform: translateY(-1px); }

        /* ── Hero ─────────────────────────────────────────────────────── */
        #hero { min-height: 100vh; position: relative; overflow: hidden; }

        /* Background slides stack absolutely; only .active is visible */
        .hero-bg-slide {
            position: absolute; inset: 0;
            opacity: 0;
            transition: opacity 1.4s ease-in-out;
            background-size: cover;
            background-position: center;
        }
        .hero-bg-slide.active { opacity: 1; }

        /* Gradient fallback slides */
        .hero-slide-1 {
            background: linear-gradient(135deg, rgba(11,31,58,.93) 0%, rgba(11,31,58,.78) 50%, rgba(11,31,58,.60) 100%),
                        radial-gradient(ellipse 100% 90% at 80% 50%, #1a4a8a 0%, #0B1F3A 65%);
        }
        .hero-slide-2 {
            background: linear-gradient(135deg, rgba(5,20,50,.94) 0%, rgba(10,45,90,.78) 50%, rgba(5,20,50,.60) 100%),
                        radial-gradient(ellipse 100% 90% at 78% 50%, #0a5c8a 0%, #05142e 65%);
        }
        .hero-slide-3 {
            background: linear-gradient(135deg, rgba(6,30,18,.94) 0%, rgba(10,55,35,.78) 50%, rgba(6,30,18,.60) 100%),
                        radial-gradient(ellipse 100% 90% at 76% 50%, #0d6644 0%, #051912 65%);
        }
        /* Photo slide dark overlay so text stays readable */
        .hero-slide-photo::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(100deg, rgba(11,31,58,.82) 0%, rgba(11,31,58,.55) 55%, rgba(11,31,58,.30) 100%);
        }

        /* Decorative orb (gradient slides only) */
        .slide-orb {
            position: absolute; top: 50%; right: 6%; transform: translateY(-50%);
            width: clamp(260px, 34vw, 440px); aspect-ratio: 1;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            pointer-events: none;
        }
        .slide-orb-ring {
            position: absolute; inset: 0; border-radius: 50%;
            border: 1px solid rgba(201,168,76,.2);
            animation: orbPulse 4s ease-in-out infinite;
        }
        .slide-orb-ring:nth-child(2) { inset:-20px; animation-delay:.6s; border-color:rgba(201,168,76,.12); }
        .slide-orb-ring:nth-child(3) { inset:-42px; animation-delay:1.2s; border-color:rgba(201,168,76,.07); }
        @keyframes orbPulse { 0%,100%{opacity:.6} 50%{opacity:1} }
        .slide-orb-inner {
            width: 70%; aspect-ratio: 1; border-radius: 50%;
            background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(2px);
            display: flex; align-items: center; justify-content: center;
        }
        .slide-orb-icon { font-size: clamp(4rem,7vw,7rem); color: var(--gold); opacity: .55; }

        /* Content layer sits above slides */
        .hero-content-wrap {
            position: relative; z-index: 5;
            min-height: 100vh; display: flex; align-items: center;
        }

        /* Dot indicators */
        #heroDots {
            position: absolute; bottom: 2.2rem; left: max(1rem, calc((100vw - 1320px)/2 + 1rem));
            z-index: 10; display: flex; gap: .55rem; align-items: center;
        }
        .hero-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: rgba(255,255,255,.35); border: none; padding: 0; cursor: pointer;
            transition: background .3s, transform .3s;
        }
        .hero-dot.active { background: var(--gold); transform: scale(1.35); }

        /* Prev / Next buttons */
        .hero-arrow {
            position: absolute; top: 50%; transform: translateY(-50%);
            z-index: 10; width: 44px; height: 44px; border-radius: 50%;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.2);
            backdrop-filter: blur(4px); color: #fff; font-size: 1rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; opacity: .75; transition: opacity .2s, background .2s;
        }
        .hero-arrow:hover { opacity: 1; background: rgba(201,168,76,.3); }
        #heroPrev { left: 1.25rem; }
        #heroNext { right: 1.25rem; }

        .hero-badge { display: inline-block; background: rgba(201,168,76,.18); border: 1px solid rgba(201,168,76,.4); color: var(--gold); font-size: .78rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; padding: .35rem 1rem; border-radius: 50px; margin-bottom: 1.5rem; }
        .hero-title { font-size: clamp(2.4rem, 5vw, 4rem); font-weight: 800; color: #fff; line-height: 1.15; }
        .hero-title .accent { color: var(--gold); }
        .hero-sub { font-size: 1.1rem; color: rgba(255,255,255,.75); max-width: 540px; line-height: 1.7; }
        .btn-hero-primary { background: var(--gold); color: var(--navy); font-weight: 700; padding: .9rem 2.2rem; border-radius: 50px; font-size: 1rem; border: none; transition: all .2s; }
        .btn-hero-primary:hover { background: var(--gold2); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(201,168,76,.35); color: var(--navy); }
        .btn-hero-outline { background: transparent; color: #fff; font-weight: 600; padding: .9rem 2.2rem; border-radius: 50px; font-size: 1rem; border: 2px solid rgba(255,255,255,.4); transition: all .2s; }
        .btn-hero-outline:hover { border-color: #fff; background: rgba(255,255,255,.08); color: #fff; }
        .hero-scroll { position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%); color: rgba(255,255,255,.4); font-size: .78rem; letter-spacing: .1em; text-transform: uppercase; text-align: center; animation: bounce 2s infinite; z-index: 10; }
        @keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(6px)} }

        /* ── Stats bar ────────────────────────────────────────────────── */
        #stats { background: var(--navy); }
        .stat-item { border-right: 1px solid rgba(255,255,255,.1); }
        .stat-item:last-child { border-right: none; }
        .stat-num { font-size: 2.4rem; font-weight: 800; color: var(--gold); line-height: 1; }
        .stat-label { font-size: .8rem; color: rgba(255,255,255,.6); letter-spacing: .06em; text-transform: uppercase; margin-top: .25rem; }

        /* ── Section labels ───────────────────────────────────────────── */
        .section-eyebrow { font-size: .75rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--gold); margin-bottom: .5rem; }
        .section-title { font-size: clamp(1.7rem, 3vw, 2.4rem); font-weight: 800; color: var(--navy); line-height: 1.2; }
        .section-sub { color: #6b7280; font-size: 1rem; max-width: 520px; line-height: 1.7; }
        .divider-gold { width: 48px; height: 4px; background: var(--gold); border-radius: 2px; margin: 1rem 0 1.5rem; }

        /* ── About ────────────────────────────────────────────────────── */
        #about { background: var(--light); }
        .about-feature { display: flex; gap: 1rem; align-items: flex-start; padding: 1.25rem; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .about-icon { width: 46px; height: 46px; border-radius: 10px; background: linear-gradient(135deg, var(--navy), #1e3a60); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .about-icon i { color: var(--gold); font-size: 1.2rem; }
        .about-card-big { background: linear-gradient(135deg, var(--navy) 0%, #1a3a6b 100%); border-radius: 20px; padding: 2.5rem; color: #fff; position: relative; overflow: hidden; }
        .about-card-big::after { content: ''; position: absolute; top: -40px; right: -40px; width: 200px; height: 200px; border-radius: 50%; background: rgba(201,168,76,.1); }

        /* ── Faculties ────────────────────────────────────────────────── */
        #academics { background: #fff; }
        .faculty-card { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.07); transition: transform .25s, box-shadow .25s; height: 100%; }
        .faculty-card:hover { transform: translateY(-6px); box-shadow: 0 12px 36px rgba(0,0,0,.13); }
        .faculty-header { padding: 1.75rem 1.5rem 1.25rem; position: relative; }
        .faculty-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
        .faculty-icon i { font-size: 1.5rem; color: #fff; }
        .faculty-card .card-footer { background: var(--light); border: none; padding: .75rem 1.5rem; font-size: .82rem; color: #6b7280; }

        /* ── Programs ─────────────────────────────────────────────────── */
        #programs { background: var(--light); }
        .program-card { background: #fff; border-radius: 14px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,.06); border-left: 4px solid var(--gold); transition: transform .2s, box-shadow .2s; height: 100%; }
        .program-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(0,0,0,.1); }
        .program-level { font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: .22rem .7rem; border-radius: 50px; }
        .level-degree                { background: rgba(59,130,246,.12);  color: #1d4ed8; }
        .level-diploma               { background: rgba(16,185,129,.12);  color: #065f46; }
        .level-certificate           { background: rgba(245,158,11,.12);  color: #92400e; }
        .level-craft_certificate     { background: rgba(139,92,246,.12);  color: #6d28d9; }
        .level-trade_test_certificate { background: rgba(107,114,128,.12); color: #374151; }

        /* ── Why us ───────────────────────────────────────────────────── */
        #why { background: linear-gradient(135deg, var(--navy) 0%, #122d55 100%); }
        .why-card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); border-radius: 16px; padding: 2rem 1.5rem; text-align: center; transition: background .25s, transform .25s; }
        .why-card:hover { background: rgba(255,255,255,.1); transform: translateY(-4px); }
        .why-icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(201,168,76,.15); border: 2px solid rgba(201,168,76,.3); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; }
        .why-icon i { color: var(--gold); font-size: 1.6rem; }
        .why-card h5 { color: #fff; font-weight: 700; }
        .why-card p { color: rgba(255,255,255,.65); font-size: .9rem; line-height: 1.6; margin: 0; }

        /* ── News ─────────────────────────────────────────────────────── */
        #news { background: #fff; }
        .news-card { border: none; border-radius: 14px; box-shadow: 0 3px 16px rgba(0,0,0,.07); overflow: hidden; transition: transform .25s, box-shadow .25s; height: 100%; }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 10px 32px rgba(0,0,0,.12); }
        .news-thumb { height: 180px; display: flex; align-items: center; justify-content: center; }
        .news-date { font-size: .78rem; color: var(--gold); font-weight: 600; letter-spacing: .05em; }
        .news-card .card-body { padding: 1.25rem 1.5rem; }

        /* ── Careers ──────────────────────────────────────────────────── */
        #careers { background: var(--navy); }
        .job-card { background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1); border-radius: 16px; padding: 1.5rem; transition: background .25s, transform .2s; display: flex; flex-direction: column; height: 100%; }
        .job-card:hover { background: rgba(255,255,255,.09); transform: translateY(-4px); }
        .job-type-badge { font-size: .7rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: .22rem .75rem; border-radius: 50px; }
        .type-full-time    { background: rgba(16,185,129,.18); color: #6ee7b7; }
        .type-part-time    { background: rgba(245,158,11,.18); color: #fcd34d; }
        .type-contract     { background: rgba(139,92,246,.18); color: #c4b5fd; }
        .type-internship   { background: rgba(59,130,246,.18); color: #93c5fd; }

        /* ── CTA ──────────────────────────────────────────────────────── */
        #cta { background: linear-gradient(120deg, var(--gold) 0%, #b8892e 100%); }

        /* ── Footer ───────────────────────────────────────────────────── */
        footer { background: #060f1e; }
        .footer-logo span.f-name { font-size: 1.1rem; font-weight: 700; color: #fff; }
        .footer-logo span.f-tag  { font-size: .68rem; color: var(--gold); letter-spacing: .08em; text-transform: uppercase; }
        .footer-heading { font-size: .78rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--gold); margin-bottom: 1rem; }
        .footer-link { display: block; color: rgba(255,255,255,.55); font-size: .88rem; padding: .22rem 0; transition: color .2s; }
        .footer-link:hover { color: var(--gold); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.07); }
        .social-btn { width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,.07); display: inline-flex; align-items: center; justify-content: center; color: rgba(255,255,255,.55); transition: background .2s, color .2s; }
        .social-btn:hover { background: var(--gold); color: var(--navy); }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════════════════════════
     NAVBAR
═══════════════════════════════════════════════════════════ --}}
<nav id="mainNav" class="navbar navbar-expand-lg fixed-top py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            @if(!empty($uni['logo_path']))
            <img src="{{ asset('storage/' . $uni['logo_path']) }}" alt="Logo"
                 style="height:40px;max-width:44px;object-fit:contain;border-radius:8px;background:#fff;padding:3px;flex-shrink:0">
            @else
            <div style="width:38px;height:38px;border-radius:10px;background:var(--gold);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-mortarboard-fill" style="color:var(--navy);font-size:1.1rem"></i>
            </div>
            @endif
            <div>
                <span class="brand-uni">{{ $uni['university_name'] ?? config('app.name') }}</span>
                <span class="brand-tag">{{ $uni['university_short_name'] ?? '' }}</span>
            </div>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <i class="bi bi-list text-white fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#academics">Faculties</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#programs">Programs</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#why">Why Us</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#news">News</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#careers">Careers</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#contact">Contact</a></li>
            </ul>
            <div class="d-flex gap-2 mt-2 mt-lg-0">
                @auth
                <a href="{{ route('dashboard') }}" class="nav-link-custom btn-nav-login">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="nav-link nav-link-custom">Sign In</a>
                <a href="{{ route('apply') }}" class="nav-link btn-nav-login">Apply Now</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ══════════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════════ --}}
<section id="hero">

    @php
        $slides = count($heroImages)
            ? collect($heroImages)->map(fn($url) => ['type'=>'photo','url'=>$url])->all()
            : [
                ['type'=>'gradient','class'=>'hero-slide-1','icon'=>'bi-mortarboard-fill'],
                ['type'=>'gradient','class'=>'hero-slide-2','icon'=>'bi-lightbulb-fill'],
                ['type'=>'gradient','class'=>'hero-slide-3','icon'=>'bi-people-fill'],
              ];
    @endphp

    {{-- Background slides --}}
    @foreach($slides as $si => $slide)
    <div class="hero-bg-slide {{ $slide['type'] === 'gradient' ? $slide['class'] : 'hero-slide-photo' }} {{ $si === 0 ? 'active' : '' }}"
         @if($slide['type'] === 'photo') style="background-image:url('{{ $slide['url'] }}')" @endif>
        @if($slide['type'] === 'gradient')
        <div class="slide-orb">
            <div class="slide-orb-ring"></div>
            <div class="slide-orb-ring"></div>
            <div class="slide-orb-ring"></div>
            <div class="slide-orb-inner">
                <i class="bi {{ $slide['icon'] }} slide-orb-icon"></i>
            </div>
        </div>
        @endif
    </div>
    @endforeach

    {{-- Content overlay --}}
    <div class="hero-content-wrap">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-7 py-5">
                    <div class="hero-badge">
                        <i class="bi bi-award-fill me-1"></i>Accredited Centre of Excellence
                    </div>
                    <h1 class="hero-title mb-4">
                        Shape Your Future at<br>
                        <span class="accent">{{ $uni['university_name'] ?? config('app.name') }}</span>
                    </h1>
                    <p class="hero-sub mb-5">
                        Empowering the next generation of leaders, innovators, and scholars
                        through world-class academic programmes, cutting-edge research, and
                        a vibrant campus community.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('apply') }}" class="btn-hero-primary">
                            <i class="bi bi-pencil-square me-2"></i>Apply Now
                        </a>
                        <a href="#programs" class="btn-hero-outline">
                            <i class="bi bi-mortarboard me-2"></i>Explore Programmes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dot indicators --}}
    <div id="heroDots">
        @foreach($slides as $si => $slide)
        <button class="hero-dot {{ $si === 0 ? 'active' : '' }}" data-slide="{{ $si }}" aria-label="Slide {{ $si+1 }}"></button>
        @endforeach
    </div>

    {{-- Prev / Next arrows --}}
    <button id="heroPrev" class="hero-arrow" aria-label="Previous slide">
        <i class="bi bi-chevron-left"></i>
    </button>
    <button id="heroNext" class="hero-arrow" aria-label="Next slide">
        <i class="bi bi-chevron-right"></i>
    </button>

    <div class="hero-scroll">
        <div><i class="bi bi-chevron-down d-block"></i>Scroll</div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     STATS BAR
═══════════════════════════════════════════════════════════ --}}
<section id="stats" class="py-5">
    <div class="container">
        <div class="row g-0 text-center">
            <div class="col-6 col-md-3 stat-item py-3">
                <div class="stat-num" data-count="{{ $stats['students'] }}">0</div>
                <div class="stat-label">Students</div>
            </div>
            <div class="col-6 col-md-3 stat-item py-3">
                <div class="stat-num" data-count="{{ $stats['programs'] }}">0</div>
                <div class="stat-label">Programmes</div>
            </div>
            <div class="col-6 col-md-3 stat-item py-3">
                <div class="stat-num" data-count="{{ $stats['departments'] }}">0</div>
                <div class="stat-label">Departments</div>
            </div>
            <div class="col-6 col-md-3 stat-item py-3">
                <div class="stat-num" data-count="{{ $stats['courses'] }}">0</div>
                <div class="stat-label">Courses</div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     ABOUT
═══════════════════════════════════════════════════════════ --}}
<section id="about" class="py-6" style="padding:80px 0">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="about-card-big mb-4">
                    <div class="section-eyebrow" style="color:var(--gold)">About Us</div>
                    <h2 style="color:#fff;font-size:1.9rem;font-weight:800;line-height:1.25;margin-bottom:1rem">
                        A Legacy of Academic Excellence
                    </h2>
                    <p style="color:rgba(255,255,255,.72);line-height:1.75;margin-bottom:1.5rem">
                        Founded over six years ago, {{ $uni['university_name'] ?? config('app.name') }} has grown into
                        a premier institution renowned for its rigorous academic standards,
                        innovative research, and commitment to producing graduates who make
                        a lasting impact in society.
                    </p>
                    <div class="row g-3">
                        <div class="col-6">
                            <div style="background:rgba(255,255,255,.08);border-radius:10px;padding:1rem;text-align:center">
                                <div style="font-size:1.6rem;font-weight:800;color:var(--gold)">6+</div>
                                <div style="font-size:.78rem;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.05em">Years of Excellence</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="background:rgba(255,255,255,.08);border-radius:10px;padding:1rem;text-align:center">
                                <div style="font-size:1.6rem;font-weight:800;color:var(--gold)">95%</div>
                                <div style="font-size:.78rem;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.05em">Passing Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-eyebrow">Our Pillars</div>
                <h2 class="section-title mb-2">Why We Stand Apart</h2>
                <div class="divider-gold"></div>
                <p class="section-sub mb-4">
                    We deliver education that transforms careers and communities through
                    a blend of academic rigour, practical exposure, and holistic development.
                </p>
                <div class="d-flex flex-column gap-3">
                    @foreach([
                        ['icon'=>'bi-lightbulb-fill','title'=>'Exellence','desc'=>'We are committed to delivering high-quality technical and vocational education, ensuring that all learners acquire the knowledge, skills, and competencies required by industry and society.'],
                        ['icon'=>'bi-globe2','title'=>'Integrity','desc'=>'We uphold honesty, transparency, accountability, and ethical conduct in all our dealings with students, staff, employers, and stakeholders.'],
                        ['icon'=>'bi-people-fill','title'=>'Professionalism','desc'=>'We maintain high standards of competence, discipline, respect, and responsibility in teaching, learning, and institutional management.'],
                        ['icon'=>'bi-graph-up-arrow','title'=>'Innovation and Creativity','desc'=>'We encourage innovative thinking, problem-solving, and the adoption of modern technologies and practices that enhance learning and entrepreneurship.'],
                    ] as $feat)
                    <div class="about-feature">
                        <div class="about-icon">
                            <i class="bi {{ $feat['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="fw-bold mb-1" style="font-size:.95rem">{{ $feat['title'] }}</div>
                            <div class="text-muted small">{{ $feat['desc'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FACULTIES / ACADEMICS
═══════════════════════════════════════════════════════════ --}}
<section id="academics" style="padding:80px 0">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-eyebrow">Our Academics</div>
            <h2 class="section-title">Explore Our Faculties</h2>
            <div class="divider-gold mx-auto"></div>
            <p class="section-sub mx-auto">
                {{ $faculties->count() }} distinct faculties under one roof.
            </p>
        </div>
        @php
            $facultyColors = [
                '#1d4ed8','#7c3aed','#059669','#dc2626','#d97706','#0891b2','#be185d','#65a30d',
            ];
            $facultyIcons  = [
                'bi-cpu-fill','bi-briefcase-fill','bi-heart-pulse-fill','bi-book-fill',
                'bi-flask-fill','bi-pencil-fill','bi-gear-fill','bi-globe2',
            ];
        @endphp
        <div class="row g-4">
            @foreach($faculties as $i => $faculty)
            <div class="col-sm-6 col-lg-4">
                <div class="faculty-card card">
                    <div class="faculty-header" style="background:linear-gradient(135deg,{{ $facultyColors[$i % count($facultyColors)] }}22,{{ $facultyColors[$i % count($facultyColors)] }}08)">
                        <div class="faculty-icon" style="background:{{ $facultyColors[$i % count($facultyColors)] }}">
                            <i class="bi {{ $facultyIcons[$i % count($facultyIcons)] }}"></i>
                        </div>
                        <h5 class="fw-bold mb-1" style="font-size:1rem;color:var(--navy)">{{ $faculty->name }}</h5>
                        <div class="text-muted small">{{ $faculty->departments_count }} {{ Str::plural('Department', $faculty->departments_count) }}</div>
                    </div>
                    <div class="card-body py-2 px-3">
                        <ul class="list-unstyled mb-0">
                            @foreach($faculty->departments->take(3) as $dept)
                            <li class="py-1 border-bottom d-flex align-items-center gap-2 small text-muted">
                                <i class="bi bi-chevron-right" style="color:{{ $facultyColors[$i % count($facultyColors)] }};font-size:.7rem"></i>
                                {{ $dept->name }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer">
                        <i class="bi bi-book me-1"></i>
                        {{ $faculty->departments->sum(fn($d) => $d->programs->count()) }} Programme(s) offered
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     PROGRAMMES
═══════════════════════════════════════════════════════════ --}}
<section id="programs" style="padding:80px 0;background:var(--light)">
    <div class="container">
        @php
            $levelGroups = $programs->groupBy('level');
            $availableLevels = $programs->pluck('level')->unique()->values();
            $levelLabels = \App\Models\Program::levelLabels();
        @endphp
        <div class="row align-items-end mb-4">
            <div class="col-lg-7">
                <div class="section-eyebrow">Academic Programmes</div>
                <h2 class="section-title">Find Your Programme</h2>
                <div class="divider-gold"></div>
                <p class="section-sub">
                    We offer {{ $programs->count() }} accredited programme{{ $programs->count() !== 1 ? 's' : '' }} designed for the modern world.
                </p>
            </div>
            @if($availableLevels->isNotEmpty())
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                    @foreach($availableLevels as $lvl)
                    <span class="program-level level-{{ $lvl }}">{{ $levelLabels[$lvl] ?? ucfirst($lvl) }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @if($programs->isEmpty())
        <div class="text-center py-5">
            <div style="width:72px;height:72px;border-radius:50%;background:rgba(11,31,58,.06);
                        display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                <i class="bi bi-mortarboard" style="font-size:1.8rem;color:#9ca3af"></i>
            </div>
            <p class="text-muted mb-0">No programmes available at this time. Check back soon.</p>
        </div>
        @else
        <div class="row g-4">
            @foreach($programs as $program)
            <div class="col-sm-6 col-lg-4">
                <div class="program-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="program-level level-{{ $program->level }}">
                            {{ $levelLabels[$program->level] ?? ucfirst($program->level ?? '') }}
                        </span>
                        @if($program->code)
                        <span class="text-muted" style="font-size:.75rem;font-weight:600;letter-spacing:.05em">{{ $program->code }}</span>
                        @else
                        <i class="bi bi-arrow-up-right-circle text-muted"></i>
                        @endif
                    </div>
                    <h6 class="fw-bold mb-1" style="color:var(--navy);font-size:.97rem">{{ $program->name }}</h6>
                    <div class="text-muted small mb-2">{{ $program->department?->name }}</div>
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-building-fill text-muted" style="font-size:.75rem"></i>
                            <span class="text-muted" style="font-size:.78rem">{{ $program->department?->faculty?->name }}</span>
                        </div>
                        @if($program->duration_years)
                        <span class="text-muted" style="font-size:.75rem">
                            <i class="bi bi-clock me-1"></i>{{ $program->duration_label }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        <div class="text-center mt-5 d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('programs') }}" class="btn btn-lg fw-bold px-5 py-3"
               style="background:var(--navy);color:#fff;border-radius:50px">
                <i class="bi bi-grid-3x3-gap me-2"></i>View All Programmes
            </a>
            @guest
            <a href="{{ route('apply') }}" class="btn btn-lg fw-bold px-5 py-3"
               style="background:transparent;border:2px solid var(--navy);color:var(--navy);border-radius:50px">
                <i class="bi bi-pencil-square me-2"></i>Apply Now
            </a>
            @endguest
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     WHY CHOOSE US
═══════════════════════════════════════════════════════════ --}}
<section id="why" style="padding:80px 0">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-eyebrow" style="color:var(--gold)">Why Choose Us</div>
            <h2 style="color:#fff;font-size:clamp(1.7rem,3vw,2.4rem);font-weight:800">
                Your Success Is Our Mission
            </h2>
            <div class="divider-gold mx-auto"></div>
        </div>
        <div class="row g-4">
            @foreach([
                ['icon'=>'bi-patch-check-fill', 'title'=>'Accredited Programmes',      'desc'=>'All our Programmes are nationally accredited and internationally recognised, giving your qualification global currency.'],
                ['icon'=>'bi-laptop-fill',       'title'=>'Modern Infrastructure',       'desc'=>'Smart classrooms, high-speed internet, digital libraries, and state-of-the-art laboratories across every campus.'],
                ['icon'=>'bi-person-lines-fill', 'title'=>'Expert Faculty',              'desc'=>'Learn from accomplished academics and industry practitioners who bring real-world insight into every lecture.'],
                ['icon'=>'bi-briefcase-fill',    'title'=>'Career Placement',            'desc'=>'Dedicated career services, employer partnerships, and internship pipelines that fast-track graduate employment.'],
                ['icon'=>'bi-shield-fill-check', 'title'=>'Safe Campus',                 'desc'=>'24/7 security, student wellness services, and a zero-tolerance policy ensuring every student feels safe.'],
                ['icon'=>'bi-currency-dollar',   'title'=>'Flexible Financing',          'desc'=>'Merit scholarships, government bursaries, and affordable payment plans to make education accessible.'],
            ] as $item)
            <div class="col-sm-6 col-lg-4">
                <div class="why-card">
                    <div class="why-icon"><i class="bi {{ $item['icon'] }}"></i></div>
                    <h5 class="mb-2">{{ $item['title'] }}</h5>
                    <p>{{ $item['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     NEWS & ANNOUNCEMENTS
═══════════════════════════════════════════════════════════ --}}
<section id="news" style="padding:80px 0">
    <div class="container">
        <div class="row align-items-end mb-5">
            <div class="col-lg-8 text-center text-lg-start">
                <div class="section-eyebrow">Latest Updates</div>
                <h2 class="section-title">News &amp; Announcements</h2>
                <div class="divider-gold"></div>
            </div>
            <div class="col-lg-4 text-center text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('news.public') }}"
                   class="btn fw-bold px-4 py-2"
                   style="background:var(--navy);color:#fff;border-radius:50px;font-size:.9rem">
                    <i class="bi bi-megaphone me-2"></i>View All Announcements
                </a>
            </div>
        </div>

        {{-- Attachment map: keyed by announcement id, injected once as JS variable --}}
        @php
            $annAttachMap = [];
            foreach ($announcements as $ann) {
                $annAttachMap[$ann->id] = collect($ann->attachments ?? [])->map(fn($p) => [
                    'name' => basename($p),
                    'url'  => asset('storage/' . $p),
                ])->values()->all();
            }
        @endphp
        <script>const annAttachMap = @json($annAttachMap);</script>

        @forelse($announcements as $i => $ann)
        @if($i === 0)<div class="row g-4">@endif
            @php
                $catIcon = match($ann->category ?? '') {
                    'academic'          => 'bi-mortarboard-fill',
                    'finance'           => 'bi-cash-coin',
                    'event', 'events'   => 'bi-calendar-event-fill',
                    'emergency','urgent'=> 'bi-exclamation-triangle-fill',
                    default             => 'bi-megaphone-fill',
                };
                $bgFrom = ['#0B1F3A','#1d4ed8','#059669'][$i % 3];
                $bgTo   = ['#1a3a6b','#3b82f6','#34d399'][$i % 3];
            @endphp
            <div class="col-md-4">
                <div class="news-card card h-100">
                    <div class="news-thumb" style="background:linear-gradient(135deg,{{ $bgFrom }},{{ $bgTo }})">
                        <i class="bi {{ $catIcon }} text-white" style="font-size:2.5rem;opacity:.45"></i>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="news-date">
                                <i class="bi bi-calendar3 me-1"></i>{{ $ann->created_at->format('d M Y') }}
                            </span>
                            @if($ann->category)
                            <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
                                         padding:.18rem .55rem;border-radius:50px;
                                         background:rgba(11,31,58,.08);color:var(--navy)">
                                {{ $ann->category }}
                            </span>
                            @endif
                        </div>
                        <h6 class="fw-bold mb-2" style="color:var(--navy)">{{ $ann->title }}</h6>
                        <p class="text-muted small mb-0" style="line-height:1.6">
                            {{ Str::limit(strip_tags($ann->content ?? ''), 120) }}
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3 pt-0 px-4">
                        @php $hasAttach = !empty($ann->attachments); @endphp
                        <div class="d-flex align-items-center justify-content-between">
                            <button type="button"
                                class="btn btn-link p-0 small fw-semibold text-decoration-none ann-read-more"
                                style="color:var(--gold)"
                                data-ann-id="{{ $ann->id }}"
                                data-title="{{ e($ann->title) }}"
                                data-date="{{ $ann->created_at->format('d F Y') }}"
                                data-category="{{ e($ann->category ?? '') }}"
                                data-content="{{ e($ann->content ?? '') }}"
                                data-bg-from="{{ $bgFrom }}"
                                data-bg-to="{{ $bgTo }}"
                                data-icon="{{ $catIcon }}">
                                Read more <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                            @if($hasAttach)
                            <span title="{{ count($ann->attachments) }} attachment(s)"
                                  style="font-size:.75rem;color:#6b7280;display:flex;align-items:center;gap:.25rem">
                                <i class="bi bi-paperclip"></i>{{ count($ann->attachments) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @if($loop->last)</div>@endif
        @empty
        <div class="text-center py-5">
            <div style="width:72px;height:72px;border-radius:50%;background:rgba(11,31,58,.06);
                        display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                <i class="bi bi-megaphone" style="font-size:1.8rem;color:#9ca3af"></i>
            </div>
            <p class="text-muted mb-0">No announcements at this time. Check back soon.</p>
        </div>
        @endforelse

        {{-- Announcement modal --}}
        <div class="modal fade" id="annModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" style="max-height:90vh">
                <div class="modal-content border-0" style="border-radius:16px;display:flex;flex-direction:column;max-height:85vh">

                    {{-- Fixed coloured header --}}
                    <div id="annModalHeader" style="padding:1.75rem 2rem 1.5rem;position:relative;flex-shrink:0;border-radius:16px 16px 0 0">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.2);
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i id="annModalIconI" class="bi text-white" style="font-size:1.4rem"></i>
                            </div>
                            <div>
                                <div id="annModalCategory"
                                     style="font-size:.72rem;font-weight:700;letter-spacing:.1em;
                                            text-transform:uppercase;color:rgba(255,255,255,.7);margin-bottom:.2rem"></div>
                                <div id="annModalDate" style="font-size:.82rem;color:rgba(255,255,255,.85)"></div>
                            </div>
                        </div>
                        <h4 id="annModalTitle" class="fw-bold text-white mb-0" style="line-height:1.35;padding-right:2rem"></h4>
                        <button type="button" class="btn-close btn-close-white position-absolute"
                                style="top:1.1rem;right:1.1rem" data-bs-dismiss="modal"></button>
                    </div>

                    {{-- Scrollable body --}}
                    <div id="annModalBody"
                         style="flex:1;overflow-y:auto;padding:1.75rem 2rem;
                                font-size:.97rem;line-height:1.85;color:#374151;
                                white-space:pre-wrap;word-break:break-word;
                                overflow-x:hidden;
                                scrollbar-width:thin;scrollbar-color:#c9a84c #f3f4f6"></div>

                    {{-- Attachments strip (hidden when empty) --}}
                    <div id="annModalAttachments" style="flex-shrink:0;display:none;
                         padding:.85rem 2rem 1rem;border-top:1px solid #f3f4f6;background:#fafafa">
                        <div style="font-size:.72rem;font-weight:700;letter-spacing:.1em;
                                    text-transform:uppercase;color:#6b7280;margin-bottom:.6rem">
                            <i class="bi bi-paperclip me-1"></i>Attachments
                        </div>
                        <div id="annModalAttachmentList" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    {{-- Fixed footer --}}
                    <div style="flex-shrink:0;padding:1rem 2rem 1.25rem;border-top:1px solid #f3f4f6;text-align:right">
                        <button type="button" class="btn rounded-pill px-4 fw-semibold"
                                style="background:var(--navy);color:#fff" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     CAREERS / JOB LISTINGS
═══════════════════════════════════════════════════════════ --}}
<section id="careers" style="padding:80px 0">
    <div class="container">
        <div class="row align-items-end mb-5">
            <div class="col-lg-8">
                <div class="section-eyebrow" style="color:var(--gold)">Join Our Team</div>
                <h2 style="color:#fff;font-size:clamp(1.7rem,3vw,2.4rem);font-weight:800">Open Positions</h2>
                <div class="divider-gold"></div>
                <p style="color:rgba(255,255,255,.65);font-size:1rem;max-width:520px;line-height:1.7">
                    Be part of a dynamic team dedicated to academic excellence and community impact.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('jobs.public') }}"
                   style="background:var(--gold);color:var(--navy);border-radius:50px;padding:.7rem 1.8rem;font-weight:700;font-size:.9rem;display:inline-flex;align-items:center;gap:.5rem">
                    <i class="bi bi-briefcase"></i> View All Open Positions
                </a>
            </div>
        </div>

        @if($featuredJobs->isEmpty())
        <div class="text-center py-4" style="color:rgba(255,255,255,.4)">
            <i class="bi bi-briefcase" style="font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.4"></i>
            No open positions at this time. Check back soon.
        </div>
        @else
        <div class="row g-4">
            @foreach($featuredJobs as $job)
            @php
                $typeClass = match($job->employment_type) {
                    'full-time'  => 'type-full-time',
                    'part-time'  => 'type-part-time',
                    'contract'   => 'type-contract',
                    'internship' => 'type-internship',
                    default      => 'type-full-time',
                };
                $typeLabel = ucwords(str_replace('-', ' ', $job->employment_type));
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="job-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="job-type-badge {{ $typeClass }}">{{ $typeLabel }}</span>
                        @if($job->deadline)
                        <span style="font-size:.75rem;color:{{ $job->deadline->isPast() ? '#f87171' : 'rgba(255,255,255,.5)' }}">
                            <i class="bi bi-calendar-x me-1"></i>{{ $job->deadline->format('d M Y') }}
                        </span>
                        @endif
                    </div>
                    <h6 style="color:#fff;font-weight:700;font-size:1rem;margin-bottom:.4rem;line-height:1.35">{{ $job->title }}</h6>
                    <div style="color:rgba(255,255,255,.5);font-size:.82rem;margin-bottom:.75rem">
                        <i class="bi bi-diagram-3 me-1"></i>{{ $job->department?->name ?? 'General' }}
                    </div>
                    @if($job->description)
                    <p style="color:rgba(255,255,255,.55);font-size:.85rem;line-height:1.6;flex:1">
                        {{ Str::limit(strip_tags($job->description), 110) }}
                    </p>
                    @endif
                    <div class="d-flex align-items-center justify-content-between mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,.08)">
                        <span style="color:rgba(255,255,255,.45);font-size:.8rem">
                            <i class="bi bi-person-plus me-1"></i>{{ $job->vacancies }} {{ Str::plural('vacancy', $job->vacancies) }}
                        </span>
                        <a href="{{ route('jobs.public') }}#job-{{ $job->id }}"
                           style="color:var(--gold);font-size:.82rem;font-weight:600">
                            View details <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     CTA BANNER
═══════════════════════════════════════════════════════════ --}}
<section id="cta" style="padding:80px 0">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div style="font-size:.78rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--navy);opacity:.65;margin-bottom:.5rem">
                    Admissions Open
                </div>
                <h2 style="font-size:clamp(1.8rem,4vw,2.8rem);font-weight:800;color:var(--navy);line-height:1.2;margin-bottom:1rem">
                    Begin Your Journey Today
                </h2>
                <p style="color:var(--navy);opacity:.75;font-size:1.05rem;max-width:480px;margin:0 auto 2rem;line-height:1.7">
                    Join thousands of students who have transformed their lives through
                    any descipline from {{ $uni['university_name'] ?? config('app.name') }}.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('apply') }}" class="btn btn-lg fw-bold px-5 py-3"
                       style="background:var(--navy);color:#fff;border-radius:50px">
                        <i class="bi bi-pencil-square me-2"></i>Apply Now
                    </a>
                    <a href="#contact" class="btn btn-lg fw-bold px-5 py-3"
                       style="background:transparent;border:2px solid var(--navy);color:var(--navy);border-radius:50px">
                        <i class="bi bi-envelope me-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════════ --}}
<footer id="contact" style="padding:64px 0 0">
    <div class="container">
        <div class="row g-5 pb-5">
            {{-- Brand col --}}
            <div class="col-lg-4">
                <div class="footer-logo d-flex align-items-center gap-2 mb-3">
                    @if(!empty($uni['logo_path']))
                    <img src="{{ asset('storage/' . $uni['logo_path']) }}" alt="Logo"
                         style="height:44px;max-width:48px;object-fit:contain;border-radius:8px;background:#fff;padding:3px;flex-shrink:0">
                    @else
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--gold);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="bi bi-mortarboard-fill" style="color:var(--navy);font-size:1.1rem"></i>
                    </div>
                    @endif
                    <div>
                        <span class="f-name d-block">{{ $uni['university_name'] ?? config('app.name') }}</span>
                        <span class="f-tag">Quality Education for Sustainable Development</span>
                    </div>
                </div>
                <p style="color:rgba(255,255,255,.5);font-size:.88rem;line-height:1.75;margin-bottom:1.5rem">
                    Committed to transformative education, impactful research, and
                    community development.
                    @if(!empty($uni['university_website']))
                    <a href="{{ $uni['university_website'] }}" target="_blank" style="color:var(--gold)">{{ $uni['university_website'] }}</a>
                    @endif
                </p>
                <div class="d-flex gap-2">
                    @foreach(['facebook','twitter-x','linkedin','youtube','instagram'] as $soc)
                    <a href="#" class="social-btn"><i class="bi bi-{{ $soc }}"></i></a>
                    @endforeach
                </div>
            </div>

            {{-- Quick links --}}
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Quick Links</div>
                <a href="#about"     class="footer-link">About Us</a>
                <a href="#academics" class="footer-link">Faculties</a>
                <a href="#programs"  class="footer-link">Programmes</a>
                <a href="#news"      class="footer-link">News</a>
                <a href="#cta"       class="footer-link">Admissions</a>
            </div>

            {{-- Academics --}}
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Academics</div>
                @foreach($faculties->take(5) as $fac)
                <a href="#academics" class="footer-link">{{ Str::limit($fac->name, 28) }}</a>
                @endforeach
            </div>

            {{-- Contact --}}
            <div class="col-lg-4">
                <div class="footer-heading">Get In Touch</div>
                <div class="d-flex flex-column gap-3">
                    @php
                        $contactItems = [];
                        $addr = trim(implode(', ', array_filter([
                            $uni['university_address'] ?? '',
                            $uni['university_city']    ?? '',
                            $uni['university_country'] ?? '',
                        ])));
                        if ($addr)                            $contactItems[] = ['icon'=>'bi-geo-alt-fill',   'text'=>$addr];
                        if (!empty($uni['university_phone'])) $contactItems[] = ['icon'=>'bi-telephone-fill', 'text'=>$uni['university_phone']];
                        if (!empty($uni['university_email'])) $contactItems[] = ['icon'=>'bi-envelope-fill',  'text'=>$uni['university_email']];
                        $contactItems[] = ['icon'=>'bi-clock-fill', 'text'=>'Mon – Fri: 8:00 AM – 5:00 PM'];
                    @endphp
                    @foreach($contactItems as $ct)
                    <div class="d-flex gap-3 align-items-start">
                        <div style="width:32px;height:32px;border-radius:8px;background:rgba(201,168,76,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="bi {{ $ct['icon'] }}" style="color:var(--gold);font-size:.85rem"></i>
                        </div>
                        <span style="color:rgba(255,255,255,.55);font-size:.88rem;line-height:1.5">{{ $ct['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="footer-bottom py-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <span style="color:rgba(255,255,255,.35);font-size:.82rem">
                        &copy; {{ date('Y') }} {{ $uni['university_name'] ?? config('app.name') }}. All rights reserved.
                    </span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="footer-link d-inline me-3" style="font-size:.8rem">Privacy Policy</a>
                    <a href="#" class="footer-link d-inline me-3" style="font-size:.8rem">Terms of Use</a>
                    <a href="{{ route('login') }}" class="footer-link d-inline" style="font-size:.8rem">Staff Portal</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* Navbar scroll effect */
window.addEventListener('scroll', () => {
    document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 50);
});

/* ── Hero slider ──────────────────────────────────────────── */
(function () {
    const slides = document.querySelectorAll('.hero-bg-slide');
    const dots   = document.querySelectorAll('.hero-dot');
    if (slides.length < 2) return;

    let current = 0;
    let timer;

    function goTo(n) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }

    function startTimer() {
        clearInterval(timer);
        timer = setInterval(() => goTo(current + 1), 60000);
    }

    document.getElementById('heroNext').addEventListener('click', () => { goTo(current + 1); startTimer(); });
    document.getElementById('heroPrev').addEventListener('click', () => { goTo(current - 1); startTimer(); });
    dots.forEach(dot => dot.addEventListener('click', () => { goTo(+dot.dataset.slide); startTimer(); }));

    startTimer();
})();

/* Smooth scroll for anchor links */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});

/* Announcement modal */
document.querySelectorAll('.ann-read-more').forEach(btn => {
    btn.addEventListener('click', () => {
        const d = btn.dataset;

        document.getElementById('annModalHeader').style.background =
            `linear-gradient(135deg, ${d.bgFrom}, ${d.bgTo})`;
        document.getElementById('annModalIconI').className = `bi ${d.icon}`;
        document.getElementById('annModalCategory').textContent = d.category || '';
        document.getElementById('annModalDate').innerHTML =
            `<i class="bi bi-calendar3 me-1"></i>${d.date}`;
        document.getElementById('annModalTitle').textContent = d.title;
        document.getElementById('annModalBody').textContent  = d.content;

        // Attachments
        const strip = document.getElementById('annModalAttachments');
        const list  = document.getElementById('annModalAttachmentList');
        list.innerHTML = '';
        const files = annAttachMap[d.annId] || [];

        if (files.length) {
            files.forEach(f => {
                const ext = f.name.split('.').pop().toLowerCase();
                const icon = ['jpg','jpeg','png','gif','webp'].includes(ext)
                    ? 'bi-file-earmark-image'
                    : ext === 'pdf'
                        ? 'bi-file-earmark-pdf'
                        : ['doc','docx'].includes(ext)
                            ? 'bi-file-earmark-word'
                            : 'bi-file-earmark';

                const iconColor = ext === 'pdf' ? '#dc2626'
                    : ['jpg','jpeg','png','gif'].includes(ext) ? '#7c3aed'
                    : ['doc','docx'].includes(ext) ? '#1d4ed8'
                    : '#6b7280';

                const item = document.createElement('div');
                item.style.cssText = 'background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;padding:.55rem .9rem;display:flex;align-items:center;gap:.6rem;max-width:260px';
                item.innerHTML = `
                    <i class="bi ${icon}" style="font-size:1.25rem;color:${iconColor};flex-shrink:0"></i>
                    <span style="font-size:.8rem;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1" title="${f.name}">${f.name}</span>
                    <a href="${f.url}" target="_blank" title="View"
                       style="width:28px;height:28px;border-radius:6px;background:rgba(11,31,58,.08);
                              display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--navy);text-decoration:none">
                        <i class="bi bi-eye" style="font-size:.85rem"></i>
                    </a>
                    <a href="${f.url}" download="${f.name}" title="Download"
                       style="width:28px;height:28px;border-radius:6px;background:rgba(201,168,76,.15);
                              display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--gold);text-decoration:none">
                        <i class="bi bi-download" style="font-size:.85rem"></i>
                    </a>`;
                list.appendChild(item);
            });
            strip.style.display = 'block';
        } else {
            strip.style.display = 'none';
        }

        new bootstrap.Modal(document.getElementById('annModal')).show();
    });
});

/* Counter animation */
const counters = document.querySelectorAll('.stat-num[data-count]');
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el    = entry.target;
        const end   = parseInt(el.dataset.count, 10);
        const dur   = 1600;
        const step  = Math.ceil(end / (dur / 16));
        let cur = 0;
        const tick = () => {
            cur = Math.min(cur + step, end);
            el.textContent = cur.toLocaleString() + (end > 99 ? '+' : '');
            if (cur < end) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
        observer.unobserve(el);
    });
}, { threshold: 0.5 });
counters.forEach(c => observer.observe(c));
</script>
</body>
</html>
