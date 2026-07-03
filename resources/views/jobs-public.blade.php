<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers — {{ $uni['university_name'] ?? config('app.name') }}</title>
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

        /* Hero */
        .page-hero { background:linear-gradient(135deg,var(--navy) 0%,#1a3a6b 100%); padding:80px 0 60px; }
        .page-hero h1 { font-size:clamp(1.8rem,3.5vw,2.8rem); font-weight:800; color:#fff; }
        .page-hero p  { color:rgba(255,255,255,.7); font-size:1rem; }
        .section-eyebrow { font-size:.75rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--gold); margin-bottom:.5rem; }

        /* Stats bar */
        .stat-pill { background:#fff; border-radius:12px; padding:.75rem 1.5rem; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,.06); }
        .stat-pill .num { font-size:1.6rem; font-weight:800; color:var(--navy); line-height:1; }
        .stat-pill .lbl { font-size:.72rem; color:#6b7280; text-transform:uppercase; letter-spacing:.06em; margin-top:.2rem; }

        /* Filter bar */
        .filter-bar { background:#fff; border-bottom:1px solid #e5e7eb; position:sticky; top:0; z-index:100; padding:.75rem 0; }
        .filter-select { border:1.5px solid #e5e7eb; border-radius:50px; padding:.35rem 1rem; font-size:.82rem; font-weight:600; color:#374151; background:#fff; cursor:pointer; outline:none; }
        .filter-select:focus { border-color:var(--navy); }
        .search-wrap { position:relative; }
        .search-wrap .bi-search { position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:#9ca3af; }
        .search-input { padding:.45rem 1rem .45rem 2.4rem; border:1.5px solid #e5e7eb; border-radius:50px; font-size:.88rem; width:260px; outline:none; }
        .search-input:focus { border-color:var(--navy); }
        .btn-reset { border:1.5px solid #e5e7eb; background:#fff; border-radius:50px; padding:.35rem 1rem; font-size:.82rem; font-weight:600; color:#6b7280; cursor:pointer; }

        /* Job cards */
        .job-card { background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; transition:transform .2s,box-shadow .2s; height:100%; display:flex; flex-direction:column; }
        .job-card:hover { transform:translateY(-4px); box-shadow:0 8px 28px rgba(0,0,0,.1); }
        .job-card-header { padding:1.5rem 1.5rem 1rem; border-bottom:1px solid #f3f4f6; }
        .job-card-body   { padding:1rem 1.5rem; flex:1; display:flex; flex-direction:column; }
        .job-card-footer { padding:.85rem 1.5rem 1.25rem; border-top:1px solid #f3f4f6; background:#fafafa; }

        .job-type-badge { font-size:.7rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; padding:.22rem .75rem; border-radius:50px; }
        .type-full-time  { background:rgba(16,185,129,.12); color:#065f46; }
        .type-part-time  { background:rgba(245,158,11,.12); color:#92400e; }
        .type-contract   { background:rgba(139,92,246,.12); color:#6d28d9; }
        .type-internship { background:rgba(59,130,246,.12); color:#1d4ed8; }

        /* Detail modal */
        .modal-job-header { background:linear-gradient(135deg,var(--navy),#1a3a6b); padding:2rem 2rem 1.5rem; border-radius:16px 16px 0 0; }

        /* Empty */
        .empty-state { text-align:center; padding:4rem 1rem; }
        .empty-icon  { width:72px; height:72px; border-radius:50%; background:rgba(11,31,58,.06); display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; }

        /* Footer */
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

{{-- Hero --}}
<div class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="background:transparent;padding:0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:var(--gold)">Home</a></li>
                <li class="breadcrumb-item active" style="color:rgba(255,255,255,.6)">Careers</li>
            </ol>
        </nav>
        <div class="section-eyebrow">Join Our Team</div>
        <h1 class="mb-2">Open Job Positions</h1>
        <p class="mb-4">Explore exciting career opportunities at {{ $uni['university_name'] ?? config('app.name') }}.</p>

        {{-- Stats --}}
        <div class="row g-3" style="max-width:480px">
            <div class="col-4">
                <div class="stat-pill">
                    <div class="num">{{ $jobs->total() }}</div>
                    <div class="lbl">Open Jobs</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-pill">
                    <div class="num">{{ $departments->count() }}</div>
                    <div class="lbl">Departments</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-pill">
                    <div class="num">{{ $jobs->sum('vacancies') }}</div>
                    <div class="lbl">Vacancies</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <div class="container d-flex align-items-center gap-3 flex-wrap">
        <select id="typeFilter" class="filter-select">
            <option value="">All Types</option>
            <option value="full-time">Full-Time</option>
            <option value="part-time">Part-Time</option>
            <option value="contract">Contract</option>
            <option value="internship">Internship</option>
        </select>
        <select id="deptFilter" class="filter-select">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
            <option value="{{ strtolower($dept->name) }}">{{ $dept->name }}</option>
            @endforeach
        </select>
        <button id="resetFilters" class="btn-reset"><i class="bi bi-x me-1"></i>Reset</button>
        <div class="ms-auto search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="jobSearch" class="search-input" placeholder="Search positions…">
        </div>
    </div>
</div>

{{-- Job Grid --}}
<div class="container py-5">
    @if($jobs->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="bi bi-briefcase" style="font-size:1.8rem;color:#9ca3af"></i></div>
        <h5 style="color:var(--navy)">No open positions at this time</h5>
        <p class="text-muted">Check back soon — new opportunities are added regularly.</p>
        <a href="{{ route('home') }}" class="btn fw-bold px-4 py-2 mt-2"
           style="background:var(--navy);color:#fff;border-radius:50px">
            <i class="bi bi-arrow-left me-1"></i>Back to Home
        </a>
    </div>
    @else
    <div class="row g-4" id="jobGrid">
        @foreach($jobs as $job)
        @php
            $typeClass = match($job->employment_type) {
                'full-time'  => 'type-full-time',
                'part-time'  => 'type-part-time',
                'contract'   => 'type-contract',
                'internship' => 'type-internship',
                default      => 'type-full-time',
            };
            $typeLabel   = ucwords(str_replace('-', ' ', $job->employment_type));
            $isUrgent    = $job->deadline && $job->deadline->diffInDays(now()) <= 7 && !$job->deadline->isPast();
        @endphp
        <div class="col-md-6 col-lg-4 job-item"
             id="job-{{ $job->id }}"
             data-type="{{ $job->employment_type }}"
             data-dept="{{ strtolower($job->department?->name ?? '') }}"
             data-text="{{ strtolower($job->title . ' ' . strip_tags($job->description ?? '') . ' ' . strip_tags($job->requirements ?? '')) }}">
            <div class="job-card">
                <div class="job-card-header">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="job-type-badge {{ $typeClass }}">{{ $typeLabel }}</span>
                        @if($isUrgent)
                        <span style="font-size:.7rem;font-weight:700;background:rgba(239,68,68,.1);color:#dc2626;padding:.2rem .6rem;border-radius:50px">
                            <i class="bi bi-fire me-1"></i>Closing Soon
                        </span>
                        @endif
                    </div>
                    <h5 style="color:var(--navy);font-weight:700;font-size:1rem;line-height:1.35;margin-bottom:.4rem">
                        {{ $job->title }}
                    </h5>
                    <div class="text-muted small">
                        <i class="bi bi-diagram-3 me-1"></i>{{ $job->department?->name ?? 'General' }}
                    </div>
                </div>
                <div class="job-card-body">
                    @if($job->description)
                    <p class="text-muted small mb-0" style="line-height:1.65">
                        {{ Str::limit(strip_tags($job->description), 130) }}
                    </p>
                    @endif
                </div>
                <div class="job-card-footer">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex flex-column gap-1">
                            <span class="text-muted" style="font-size:.78rem">
                                <i class="bi bi-person-plus me-1"></i>{{ $job->vacancies }} {{ Str::plural('vacancy', $job->vacancies) }}
                            </span>
                            @if($job->deadline)
                            <span style="font-size:.75rem;color:{{ $job->deadline->isPast() ? '#dc2626' : '#6b7280' }}">
                                <i class="bi bi-calendar-event me-1"></i>Deadline: {{ $job->deadline->format('d M Y') }}
                            </span>
                            @else
                            <span class="text-muted" style="font-size:.75rem">
                                <i class="bi bi-infinity me-1"></i>Open until filled
                            </span>
                            @endif
                        </div>
                        <button class="btn btn-sm fw-semibold job-detail-btn"
                                style="background:var(--navy);color:#fff;border-radius:50px;padding:.35rem 1rem;font-size:.82rem"
                                data-id="{{ $job->id }}"
                                data-title="{{ e($job->title) }}"
                                data-type="{{ $typeLabel }}"
                                data-type-class="{{ $typeClass }}"
                                data-dept="{{ e($job->department?->name ?? 'General') }}"
                                data-vacancies="{{ $job->vacancies }}"
                                data-deadline="{{ $job->deadline ? $job->deadline->format('d M Y') : 'Open until filled' }}"
                                data-description="{{ e($job->description ?? '') }}"
                                data-requirements="{{ e($job->requirements ?? '') }}">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="noResults" class="empty-state" style="display:none">
        <div class="empty-icon"><i class="bi bi-search" style="font-size:1.8rem;color:#9ca3af"></i></div>
        <h5 style="color:var(--navy)">No positions found</h5>
        <p class="text-muted">Try a different search term or filter.</p>
    </div>

    @if($jobs->hasPages())
    <div class="d-flex justify-content-center mt-5">{{ $jobs->links() }}</div>
    @endif
    @endif
</div>

{{-- Job Detail Modal --}}
<div class="modal fade" id="jobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius:16px">
            <div class="modal-job-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div id="jmType" class="mb-2" style="font-size:.75rem"></div>
                        <h4 id="jmTitle" class="fw-bold text-white mb-1"></h4>
                        <div id="jmDept" style="color:rgba(255,255,255,.65);font-size:.9rem"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-3 flex-shrink-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="d-flex gap-4 mt-3 flex-wrap">
                    <div style="color:rgba(255,255,255,.75);font-size:.85rem">
                        <i class="bi bi-person-plus me-1" style="color:var(--gold)"></i>
                        <span id="jmVacancies"></span>
                    </div>
                    <div style="color:rgba(255,255,255,.75);font-size:.85rem">
                        <i class="bi bi-calendar-event me-1" style="color:var(--gold)"></i>
                        <span id="jmDeadline"></span>
                    </div>
                </div>
            </div>
            <div class="modal-body p-4">
                <div id="jmDescription" class="mb-4" style="display:none">
                    <h6 class="fw-bold mb-2" style="color:var(--navy)"><i class="bi bi-file-text me-2"></i>Job Description</h6>
                    <div id="jmDescriptionText" style="color:#374151;line-height:1.8;font-size:.95rem;white-space:pre-wrap"></div>
                </div>
                <div id="jmRequirements" style="display:none">
                    <hr>
                    <h6 class="fw-bold mb-2" style="color:var(--navy)"><i class="bi bi-check2-circle me-2"></i>Requirements</h6>
                    <div id="jmRequirementsText" style="color:#374151;line-height:1.8;font-size:.95rem;white-space:pre-wrap"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="{{ route('login') }}" class="btn fw-bold px-4"
                   style="background:var(--gold);color:var(--navy);border-radius:50px">
                    <i class="bi bi-send me-1"></i>Apply via Staff Portal
                </a>
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Footer --}}
<div class="footer-strip">
    <div class="container d-flex justify-content-between flex-wrap gap-2">
        <span>&copy; {{ date('Y') }} {{ $uni['university_name'] ?? config('app.name') }}. All rights reserved.</span>
        <span>
            <a href="{{ route('home') }}">Home</a> &middot;
            <a href="{{ route('programs') }}">Programmes</a> &middot;
            <a href="{{ route('news.public') }}">News</a> &middot;
            <a href="{{ route('jobs.public') }}">Careers</a> &middot;
            <a href="{{ route('apply') }}">Apply</a> &middot;
            <a href="{{ route('login') }}">Staff Portal</a>
        </span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    /* ── Filters ─────────────────────────────────────────── */
    const items      = document.querySelectorAll('.job-item');
    const noResults  = document.getElementById('noResults');
    const typeFilter = document.getElementById('typeFilter');
    const deptFilter = document.getElementById('deptFilter');
    const searchEl   = document.getElementById('jobSearch');
    const resetBtn   = document.getElementById('resetFilters');

    function applyFilters() {
        const type   = typeFilter.value;
        const dept   = deptFilter.value;
        const search = searchEl.value.toLowerCase().trim();
        let visible  = 0;

        items.forEach(item => {
            const match =
                (!type   || item.dataset.type === type) &&
                (!dept   || item.dataset.dept.includes(dept)) &&
                (!search || item.dataset.text.includes(search));
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        noResults.style.display = visible === 0 ? 'block' : 'none';
    }

    typeFilter.addEventListener('change', applyFilters);
    deptFilter.addEventListener('change', applyFilters);
    searchEl.addEventListener('input',   applyFilters);
    resetBtn.addEventListener('click', () => {
        typeFilter.value = '';
        deptFilter.value = '';
        searchEl.value   = '';
        applyFilters();
    });

    /* ── Job Detail Modal ────────────────────────────────── */
    const modal = new bootstrap.Modal(document.getElementById('jobModal'));

    document.querySelectorAll('.job-detail-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const d = btn.dataset;

            document.getElementById('jmTitle').textContent    = d.title;
            document.getElementById('jmDept').innerHTML       = `<i class="bi bi-diagram-3 me-1"></i>${d.dept}`;
            document.getElementById('jmVacancies').textContent = `${d.vacancies} ${parseInt(d.vacancies) === 1 ? 'Vacancy' : 'Vacancies'}`;
            document.getElementById('jmDeadline').textContent  = `Deadline: ${d.deadline}`;

            // Type badge
            const typeEl = document.getElementById('jmType');
            typeEl.innerHTML = `<span class="job-type-badge ${d.typeClass}">${d.type}</span>`;

            // Description
            const descWrap = document.getElementById('jmDescription');
            const descText = document.getElementById('jmDescriptionText');
            if (d.description.trim()) {
                descText.textContent = d.description;
                descWrap.style.display = 'block';
            } else {
                descWrap.style.display = 'none';
            }

            // Requirements
            const reqWrap = document.getElementById('jmRequirements');
            const reqText = document.getElementById('jmRequirementsText');
            if (d.requirements.trim()) {
                reqText.textContent = d.requirements;
                reqWrap.style.display = 'block';
            } else {
                reqWrap.style.display = 'none';
            }

            modal.show();
        });
    });

    /* ── Auto-open if URL has #job-{id} ─────────────────── */
    const hash = window.location.hash;
    if (hash && hash.startsWith('#job-')) {
        const target = document.querySelector(`[data-id="${hash.slice(5)}"] .job-detail-btn`);
        if (target) setTimeout(() => target.click(), 400);
    }
})();
</script>
</body>
</html>
