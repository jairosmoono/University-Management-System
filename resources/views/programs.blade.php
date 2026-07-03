<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Programmes — {{ $uni['university_name'] ?? config('app.name') }}</title>
    @if(!empty($uni['favicon_path']))
    <link rel="icon" href="{{ asset('storage/' . $uni['favicon_path']) }}" type="image/x-icon">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root { --navy:#0B1F3A; --gold:#C9A84C; --gold2:#e8c66d; --light:#F8F9FB; }
        body { font-family:'Segoe UI',system-ui,sans-serif; color:#1a1a2e; background:var(--light); }
        a { text-decoration:none; }

        /* Navbar */
        #mainNav { background:rgba(11,31,58,.96); backdrop-filter:blur(8px); }
        .navbar-brand span.brand-uni { font-size:1.1rem; font-weight:700; color:#fff; }
        .navbar-brand span.brand-tag { font-size:.65rem; color:var(--gold); letter-spacing:.08em; display:block; text-transform:uppercase; }
        .nav-link-custom { color:rgba(255,255,255,.82)!important; font-size:.88rem; font-weight:500; padding:.5rem .9rem!important; border-radius:6px; transition:color .2s,background .2s; }
        .nav-link-custom:hover { color:var(--gold)!important; background:rgba(255,255,255,.06); }
        .btn-nav-login { background:var(--gold); color:var(--navy)!important; font-weight:700; padding:.42rem 1.2rem!important; border-radius:50px; font-size:.85rem; }
        .btn-nav-login:hover { background:var(--gold2); }

        /* Page header */
        .page-hero { background:linear-gradient(135deg,var(--navy) 0%,#1a3a6b 100%); padding:80px 0 60px; }
        .page-hero h1 { font-size:clamp(1.8rem,3.5vw,2.8rem); font-weight:800; color:#fff; }
        .page-hero p { color:rgba(255,255,255,.7); font-size:1rem; max-width:520px; }
        .section-eyebrow { font-size:.75rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--gold); margin-bottom:.5rem; }

        /* Filter bar */
        .filter-bar { background:#fff; border-bottom:1px solid #e5e7eb; position:sticky; top:0; z-index:100; padding:.75rem 0; }
        .filter-btn { border:1.5px solid #e5e7eb; background:#fff; border-radius:50px; padding:.35rem 1.1rem; font-size:.82rem; font-weight:600; color:#374151; cursor:pointer; transition:all .2s; }
        .filter-btn:hover, .filter-btn.active { background:var(--navy); border-color:var(--navy); color:#fff; }

        /* Program cards */
        .program-card { background:#fff; border-radius:14px; padding:1.5rem; box-shadow:0 2px 12px rgba(0,0,0,.06); border-left:4px solid var(--gold); transition:transform .2s,box-shadow .2s; height:100%; display:flex; flex-direction:column; }
        .program-card:hover { transform:translateY(-4px); box-shadow:0 8px 28px rgba(0,0,0,.1); }
        .program-level { font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; padding:.22rem .7rem; border-radius:50px; }
        .level-degree                 { background:rgba(59,130,246,.12);  color:#1d4ed8; }
        .level-diploma                { background:rgba(16,185,129,.12);  color:#065f46; }
        .level-certificate            { background:rgba(245,158,11,.12);  color:#92400e; }
        .level-craft_certificate      { background:rgba(139,92,246,.12);  color:#6d28d9; }
        .level-trade_test_certificate { background:rgba(107,114,128,.12); color:#374151; }

        /* Section heading */
        .level-section-heading { font-size:1.25rem; font-weight:800; color:var(--navy); border-left:4px solid var(--gold); padding-left:.85rem; margin-bottom:1.5rem; }

        /* Search */
        .search-wrap { position:relative; }
        .search-wrap .bi-search { position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:#9ca3af; }
        .search-input { padding:.5rem 1rem .5rem 2.4rem; border:1.5px solid #e5e7eb; border-radius:50px; font-size:.88rem; width:260px; outline:none; transition:border-color .2s; }
        .search-input:focus { border-color:var(--navy); }

        /* Empty state */
        .empty-state { text-align:center; padding:4rem 1rem; }
        .empty-icon { width:72px; height:72px; border-radius:50%; background:rgba(11,31,58,.06); display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; }

        /* Footer strip */
        .footer-strip { background:var(--navy); padding:2rem 0; color:rgba(255,255,255,.55); font-size:.85rem; }
        .footer-strip a { color:var(--gold); }
    </style>
</head>
<body>

{{-- Navbar --}}
<nav id="mainNav" class="navbar navbar-expand-lg py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            @if(!empty($uni['logo_path']))
            <img src="{{ asset('storage/' . $uni['logo_path']) }}" alt="Logo"
                 style="height:38px;max-width:42px;object-fit:contain;border-radius:8px;background:#fff;padding:3px;flex-shrink:0">
            @else
            <div style="width:36px;height:36px;border-radius:10px;background:var(--gold);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-mortarboard-fill" style="color:var(--navy);font-size:1rem"></i>
            </div>
            @endif
            <div>
                <span class="brand-uni">{{ $uni['university_name'] ?? config('app.name') }}</span>
                <span class="brand-tag">{{ $uni['university_short_name'] ?? '' }}</span>
            </div>
        </a>
        <div class="d-flex gap-2 ms-auto">
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
</nav>

{{-- Page Hero --}}
<div class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="background:transparent;padding:0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:var(--gold)">Home</a></li>
                <li class="breadcrumb-item active" style="color:rgba(255,255,255,.6)">Programmes</li>
            </ol>
        </nav>
        <div class="section-eyebrow">Academic Programmes</div>
        <h1 class="mb-2">All Available Programmes</h1>
        <p class="mb-0">
            Explore our {{ $programs->count() }} accredited programme{{ $programs->count() !== 1 ? 's' : '' }}
            across all faculties and departments.
        </p>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <div class="container d-flex align-items-center gap-3 flex-wrap">
        <button class="filter-btn active" data-level="all">All ({{ $programs->count() }})</button>
        @foreach($levelLabels as $lvl => $lbl)
            @if($grouped->has($lvl))
            <button class="filter-btn" data-level="{{ $lvl }}">
                {{ $lbl }} ({{ $grouped[$lvl]->count() }})
            </button>
            @endif
        @endforeach
        <div class="ms-auto search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="programSearch" class="search-input" placeholder="Search programmes…">
        </div>
    </div>
</div>

{{-- Programs Grid --}}
<div class="container py-5">
    @if($programs->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="bi bi-mortarboard" style="font-size:1.8rem;color:#9ca3af"></i></div>
        <h5 style="color:var(--navy)">No programmes available yet</h5>
        <p class="text-muted">Check back soon for upcoming programmes.</p>
        <a href="{{ route('home') }}" class="btn fw-bold px-4 py-2 mt-2"
           style="background:var(--navy);color:#fff;border-radius:50px">
            <i class="bi bi-arrow-left me-1"></i>Back to Home
        </a>
    </div>
    @else

    @foreach($levelLabels as $lvl => $lbl)
        @if($grouped->has($lvl))
        <div class="level-section mb-5" data-section="{{ $lvl }}">
            <div class="level-section-heading mb-4">
                {{ $lbl }}
                <span class="text-muted fw-normal ms-2" style="font-size:.85rem">
                    ({{ $grouped[$lvl]->count() }} programme{{ $grouped[$lvl]->count() !== 1 ? 's' : '' }})
                </span>
            </div>
            <div class="row g-4">
                @foreach($grouped[$lvl] as $program)
                <div class="col-sm-6 col-lg-4 program-item" data-level="{{ $program->level }}"
                     data-name="{{ strtolower($program->name) }}"
                     data-dept="{{ strtolower($program->department?->name ?? '') }}"
                     data-faculty="{{ strtolower($program->department?->faculty?->name ?? '') }}">
                    <div class="program-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="program-level level-{{ $program->level }}">
                                {{ $levelLabels[$program->level] ?? ucfirst($program->level ?? '') }}
                            </span>
                            @if($program->code)
                            <span class="text-muted fw-semibold" style="font-size:.75rem;letter-spacing:.05em">{{ $program->code }}</span>
                            @endif
                        </div>
                        <h6 class="fw-bold mb-1" style="color:var(--navy);font-size:.97rem;flex:1">{{ $program->name }}</h6>
                        @if($program->description)
                        <p class="text-muted small mb-3" style="line-height:1.55">
                            {{ Str::limit($program->description, 100) }}
                        </p>
                        @endif
                        <div class="mt-auto pt-2 border-top">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-diagram-3-fill text-muted" style="font-size:.75rem"></i>
                                <span class="text-muted" style="font-size:.8rem">{{ $program->department?->name ?? '—' }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-building-fill text-muted" style="font-size:.75rem"></i>
                                    <span class="text-muted" style="font-size:.78rem">{{ $program->department?->faculty?->name ?? '—' }}</span>
                                </div>
                                @if($program->duration_years)
                                <span class="text-muted" style="font-size:.75rem">
                                    <i class="bi bi-clock me-1"></i>{{ $program->duration_label }}
                                </span>
                                @endif
                            </div>
                            @if($program->credit_hours_required)
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <i class="bi bi-book-fill text-muted" style="font-size:.75rem"></i>
                                <span class="text-muted" style="font-size:.78rem">{{ $program->credit_hours_required }} credit hours</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach

    {{-- No results after search --}}
    <div id="noResults" class="empty-state" style="display:none">
        <div class="empty-icon"><i class="bi bi-search" style="font-size:1.8rem;color:#9ca3af"></i></div>
        <h5 style="color:var(--navy)">No programmes found</h5>
        <p class="text-muted">Try a different search term or filter.</p>
    </div>

    {{-- CTA --}}
    <div class="text-center mt-5 pt-4 border-top">
        <h5 style="color:var(--navy);font-weight:700" class="mb-2">Ready to enroll?</h5>
        <p class="text-muted mb-4">Submit your application online and take the first step toward your future.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            @guest
            <a href="{{ route('apply') }}" class="btn btn-lg fw-bold px-5 py-3"
               style="background:var(--navy);color:#fff;border-radius:50px">
                <i class="bi bi-pencil-square me-2"></i>Apply Now
            </a>
            @endguest
            <a href="{{ route('home') }}" class="btn btn-lg fw-bold px-5 py-3"
               style="background:transparent;border:2px solid var(--navy);color:var(--navy);border-radius:50px">
                <i class="bi bi-arrow-left me-2"></i>Back to Home
            </a>
        </div>
    </div>
    @endif
</div>

{{-- Footer strip --}}
<div class="footer-strip">
    <div class="container d-flex justify-content-between flex-wrap gap-2">
        <span>&copy; {{ date('Y') }} {{ $uni['university_name'] ?? config('app.name') }}. All rights reserved.</span>
        <span><a href="{{ route('home') }}">Home</a> &middot; <a href="{{ route('apply') }}">Apply</a> &middot; <a href="{{ route('login') }}">Staff Portal</a></span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const filterBtns  = document.querySelectorAll('.filter-btn');
    const items       = document.querySelectorAll('.program-item');
    const sections    = document.querySelectorAll('.level-section');
    const noResults   = document.getElementById('noResults');
    const searchInput = document.getElementById('programSearch');

    let currentLevel = 'all';
    let searchTerm   = '';

    function applyFilters() {
        let visible = 0;

        items.forEach(item => {
            const levelMatch  = currentLevel === 'all' || item.dataset.level === currentLevel;
            const q           = searchTerm;
            const searchMatch = !q ||
                item.dataset.name.includes(q) ||
                item.dataset.dept.includes(q) ||
                item.dataset.faculty.includes(q);

            const show = levelMatch && searchMatch;
            item.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Show/hide section headings based on visible children
        sections.forEach(sec => {
            const hasVisible = [...sec.querySelectorAll('.program-item')]
                .some(i => i.style.display !== 'none');
            sec.style.display = hasVisible ? '' : 'none';
        });

        noResults.style.display = visible === 0 ? 'block' : 'none';
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentLevel = btn.dataset.level;
            applyFilters();
        });
    });

    searchInput.addEventListener('input', () => {
        searchTerm = searchInput.value.toLowerCase().trim();
        applyFilters();
    });
})();
</script>
</body>
</html>
