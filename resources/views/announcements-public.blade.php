<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements — {{ $uni['university_name'] ?? config('app.name') }}</title>
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

        /* Page hero */
        .page-hero { background:linear-gradient(135deg,var(--navy) 0%,#1a3a6b 100%); padding:80px 0 60px; }
        .page-hero h1 { font-size:clamp(1.8rem,3.5vw,2.8rem); font-weight:800; color:#fff; }
        .page-hero p { color:rgba(255,255,255,.7); font-size:1rem; max-width:520px; }
        .section-eyebrow { font-size:.75rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--gold); margin-bottom:.5rem; }

        /* Filter bar */
        .filter-bar { background:#fff; border-bottom:1px solid #e5e7eb; position:sticky; top:0; z-index:100; padding:.75rem 0; }
        .filter-btn { border:1.5px solid #e5e7eb; background:#fff; border-radius:50px; padding:.35rem 1.1rem; font-size:.82rem; font-weight:600; color:#374151; cursor:pointer; transition:all .2s; }
        .filter-btn:hover, .filter-btn.active { background:var(--navy); border-color:var(--navy); color:#fff; }

        /* Cards */
        .ann-card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; transition:transform .2s,box-shadow .2s; height:100%; display:flex; flex-direction:column; }
        .ann-card:hover { transform:translateY(-4px); box-shadow:0 8px 28px rgba(0,0,0,.1); }
        .ann-thumb { height:160px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .ann-body { padding:1.25rem 1.5rem; flex:1; display:flex; flex-direction:column; }
        .ann-date { font-size:.78rem; color:var(--gold); font-weight:600; letter-spacing:.04em; }
        .ann-cat { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
                   padding:.18rem .55rem; border-radius:50px; background:rgba(11,31,58,.08); color:var(--navy); }
        .ann-title { font-size:.97rem; font-weight:700; color:var(--navy); line-height:1.4; }
        .ann-excerpt { font-size:.86rem; color:#6b7280; line-height:1.6; flex:1; }

        /* Modal scrollable body */
        #annModalBody { white-space:pre-wrap; word-break:break-word; overflow-x:hidden;
                        scrollbar-width:thin; scrollbar-color:#c9a84c #f3f4f6; }

        /* Empty state */
        .empty-state { text-align:center; padding:4rem 1rem; }
        .empty-icon { width:72px; height:72px; border-radius:50%; background:rgba(11,31,58,.06); display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; }

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

{{-- Page Hero --}}
<div class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="background:transparent;padding:0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:var(--gold)">Home</a></li>

                <li class="breadcrumb-item active" style="color:rgba(255,255,255,.6)">Announcements</li>
            </ol>
        </nav>
        <div class="section-eyebrow">News &amp; Updates</div>
        <h1 class="mb-2">Announcements</h1>
        <p class="mb-0">Stay up to date with the latest news, general notices, and upcoming events.</p>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <div class="container d-flex align-items-center gap-3 flex-wrap">
        <button class="filter-btn active" data-cat="all">All ({{ $announcements->total() }})</button>
        <button class="filter-btn" data-cat="general">General</button>
        <button class="filter-btn" data-cat="event">Events</button>
        <div class="ms-auto search-wrap" style="position:relative">
            <i class="bi bi-search" style="position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#9ca3af"></i>
            <input type="text" id="annSearch" placeholder="Search announcements…"
                   style="padding:.5rem 1rem .5rem 2.4rem;border:1.5px solid #e5e7eb;border-radius:50px;font-size:.88rem;width:260px;outline:none">
        </div>
    </div>
</div>

{{-- Attachment map for JS modal --}}
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

{{-- Grid --}}
<div class="container py-5">
    @if($announcements->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="bi bi-megaphone" style="font-size:1.8rem;color:#9ca3af"></i></div>
        <h5 style="color:var(--navy)">No announcements yet</h5>
        <p class="text-muted">Check back soon for the latest news and events.</p>
        <a href="{{ route('home') }}" class="btn fw-bold px-4 py-2 mt-2"
           style="background:var(--navy);color:#fff;border-radius:50px">
            <i class="bi bi-arrow-left me-1"></i>Back to Home
        </a>
    </div>
    @else
    <div class="row g-4" id="annGrid">
        @foreach($announcements as $i => $ann)
        @php
            $catGroup = in_array($ann->category, ['event','events']) ? 'event' : 'general';
            $catIcon  = in_array($ann->category, ['event','events'])
                ? 'bi-calendar-event-fill'
                : 'bi-megaphone-fill';
            $bgFrom = ['#0B1F3A','#1d4ed8','#059669','#7c3aed','#d97706','#0891b2'][$i % 6];
            $bgTo   = ['#1a3a6b','#3b82f6','#34d399','#a855f7','#fbbf24','#22d3ee'][$i % 6];
        @endphp
        <div class="col-md-6 col-lg-4 ann-item" data-cat="{{ $catGroup }}"
             data-text="{{ strtolower($ann->title . ' ' . strip_tags($ann->content ?? '')) }}">
            <div class="ann-card">
                <div class="ann-thumb" style="background:linear-gradient(135deg,{{ $bgFrom }},{{ $bgTo }})">
                    <i class="bi {{ $catIcon }} text-white" style="font-size:2.5rem;opacity:.45"></i>
                </div>
                <div class="ann-body">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="ann-date"><i class="bi bi-calendar3 me-1"></i>{{ $ann->created_at->format('d M Y') }}</span>
                        <span class="ann-cat">{{ ucfirst($ann->category) }}</span>
                    </div>
                    <div class="ann-title mb-2">{{ $ann->title }}</div>
                    <p class="ann-excerpt mb-3">{{ Str::limit(strip_tags($ann->content ?? ''), 130) }}</p>
                    <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
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
                        @if(!empty($ann->attachments))
                        <span style="font-size:.75rem;color:#6b7280;display:flex;align-items:center;gap:.25rem">
                            <i class="bi bi-paperclip"></i>{{ count($ann->attachments) }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- No results --}}
    <div id="noResults" class="empty-state" style="display:none">
        <div class="empty-icon"><i class="bi bi-search" style="font-size:1.8rem;color:#9ca3af"></i></div>
        <h5 style="color:var(--navy)">No announcements found</h5>
        <p class="text-muted">Try a different search term or filter.</p>
    </div>

    {{-- Pagination --}}
    @if($announcements->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $announcements->links() }}
    </div>
    @endif
    @endif
</div>

{{-- Announcement detail modal --}}
<div class="modal fade" id="annModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-height:90vh">
        <div class="modal-content border-0" style="border-radius:16px;display:flex;flex-direction:column;max-height:85vh">
            <div id="annModalHeader" style="padding:1.75rem 2rem 1.5rem;position:relative;flex-shrink:0;border-radius:16px 16px 0 0">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i id="annModalIconI" class="bi text-white" style="font-size:1.4rem"></i>
                    </div>
                    <div>
                        <div id="annModalCategory" style="font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.7);margin-bottom:.2rem"></div>
                        <div id="annModalDate" style="font-size:.82rem;color:rgba(255,255,255,.85)"></div>
                    </div>
                </div>
                <h4 id="annModalTitle" class="fw-bold text-white mb-0" style="line-height:1.35;padding-right:2rem"></h4>
                <button type="button" class="btn-close btn-close-white position-absolute" style="top:1.1rem;right:1.1rem" data-bs-dismiss="modal"></button>
            </div>
            <div id="annModalBody" style="flex:1;overflow-y:auto;padding:1.75rem 2rem;font-size:.97rem;line-height:1.85;color:#374151"></div>
            <div id="annModalAttachments" style="flex-shrink:0;display:none;padding:.85rem 2rem 1rem;border-top:1px solid #f3f4f6;background:#fafafa">
                <div style="font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#6b7280;margin-bottom:.6rem">
                    <i class="bi bi-paperclip me-1"></i>Attachments
                </div>
                <div id="annModalAttachmentList" class="d-flex flex-wrap gap-2"></div>
            </div>
            <div style="flex-shrink:0;padding:1rem 2rem 1.25rem;border-top:1px solid #f3f4f6;text-align:right">
                <button type="button" class="btn rounded-pill px-4 fw-semibold"
                        style="background:var(--navy);color:#fff" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Footer --}}
<div class="footer-strip">
    <div class="container d-flex justify-content-between flex-wrap gap-2">
        <span>&copy; {{ date('Y') }} {{ $uni['university_name'] ?? config('app.name') }}. All rights reserved.</span>
        <span><a href="{{ route('home') }}">Home</a> &middot; <a href="{{ route('programs') }}">Programmes</a> &middot; <a href="{{ route('news.public') }}">Announcements</a> &middot; <a href="{{ route('apply') }}">Apply</a> &middot; <a href="{{ route('login') }}">Staff Portal</a></span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const items      = document.querySelectorAll('.ann-item');
    const noResults  = document.getElementById('noResults');
    const searchInput = document.getElementById('annSearch');

    let currentCat = 'all';
    let searchTerm = '';

    function applyFilters() {
        let visible = 0;
        items.forEach(item => {
            const catMatch    = currentCat === 'all' || item.dataset.cat === currentCat;
            const searchMatch = !searchTerm || item.dataset.text.includes(searchTerm);
            const show = catMatch && searchMatch;
            item.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        noResults.style.display = visible === 0 ? 'block' : 'none';
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCat = btn.dataset.cat;
            applyFilters();
        });
    });

    searchInput.addEventListener('input', () => {
        searchTerm = searchInput.value.toLowerCase().trim();
        applyFilters();
    });
})();

/* Announcement read-more modal */
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

        const strip = document.getElementById('annModalAttachments');
        const list  = document.getElementById('annModalAttachmentList');
        list.innerHTML = '';
        const files = (typeof annAttachMap !== 'undefined' ? annAttachMap[d.annId] : null) || [];

        if (files.length) {
            files.forEach(f => {
                const ext = f.name.split('.').pop().toLowerCase();
                const icon = ['jpg','jpeg','png','gif','webp'].includes(ext)
                    ? 'bi-file-earmark-image'
                    : ext === 'pdf' ? 'bi-file-earmark-pdf'
                    : ['doc','docx'].includes(ext) ? 'bi-file-earmark-word'
                    : 'bi-file-earmark';
                const iconColor = ext === 'pdf' ? '#dc2626'
                    : ['jpg','jpeg','png','gif'].includes(ext) ? '#7c3aed'
                    : ['doc','docx'].includes(ext) ? '#1d4ed8' : '#6b7280';
                const item = document.createElement('div');
                item.style.cssText = 'background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;padding:.55rem .9rem;display:flex;align-items:center;gap:.6rem;max-width:260px';
                item.innerHTML = `
                    <i class="bi ${icon}" style="font-size:1.25rem;color:${iconColor};flex-shrink:0"></i>
                    <span style="font-size:.8rem;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1" title="${f.name}">${f.name}</span>
                    <a href="${f.url}" target="_blank" style="width:28px;height:28px;border-radius:6px;background:rgba(11,31,58,.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--navy);text-decoration:none">
                        <i class="bi bi-eye" style="font-size:.85rem"></i>
                    </a>
                    <a href="${f.url}" download="${f.name}" style="width:28px;height:28px;border-radius:6px;background:rgba(201,168,76,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--gold);text-decoration:none">
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
</script>
</body>
</html>
