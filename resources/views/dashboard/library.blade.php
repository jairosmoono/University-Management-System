@extends('layouts.app')
@section('title', 'Library Dashboard')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1><i class="bi bi-book me-2" style="color:var(--secondary)"></i>Library Dashboard</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('library.borrowings.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-arrow-left-right me-1"></i> Issue / Return
        </a>
        <a href="{{ route('library.books.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-journal-plus me-1"></i> Manage Books
        </a>
    </div>
</div>

{{-- ── STAT CARDS ─────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(11,31,58,0.1);color:#0B1F3A"><i class="bi bi-journals"></i></div>
            <div class="stat-value">{{ number_format($totalTitles) }}</div>
            <div class="stat-label text-muted">Total Titles</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(108,117,125,0.1);color:#6c757d"><i class="bi bi-stack"></i></div>
            <div class="stat-value">{{ number_format($totalCopies) }}</div>
            <div class="stat-label text-muted">Total Copies</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(25,135,84,0.1);color:#198754"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value text-success">{{ number_format($availableCopies) }}</div>
            <div class="stat-label text-muted">Available</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(13,110,253,0.1);color:#0d6efd"><i class="bi bi-arrow-left-right"></i></div>
            <div class="stat-value text-primary">{{ number_format($borrowed) }}</div>
            <div class="stat-label text-muted">Borrowed</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(220,53,69,0.1);color:#dc3545"><i class="bi bi-clock-history"></i></div>
            <div class="stat-value {{ $overdue > 0 ? 'text-danger' : '' }}">{{ number_format($overdue) }}</div>
            <div class="stat-label text-muted">Overdue</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(255,193,7,0.15);color:#e6a817"><i class="bi bi-cash"></i></div>
            <div class="stat-value {{ $unpaidFines > 0 ? 'text-warning' : '' }}">{{ formatCurrency($unpaidFines) }}</div>
            <div class="stat-label text-muted">Unpaid Fines</div>
        </div>
    </div>
</div>

{{-- ── OVERDUE ALERT ───────────────────────────────────────────────────────── --}}
@if($overdue > 0)
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
    <div>
        <strong>{{ $overdue }} overdue borrowing{{ $overdue != 1 ? 's' : '' }}</strong> — books are past their due date and may be accruing fines.
        <a href="{{ route('library.borrowings.overdue') }}" class="alert-link ms-1">View overdue list</a>
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    {{-- ── BOOKS BY CATEGORY ──────────────────────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-tags me-2 text-primary"></i>Books by Category</h6>
                <a href="{{ route('library.books.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body">
                @php $maxBooks = $byCategory->max('books_count') ?: 1; @endphp
                @forelse($byCategory as $cat)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold">{{ $cat->name }}</span>
                        <span class="small text-muted">{{ $cat->books_count }} title{{ $cat->books_count != 1 ? 's' : '' }}</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar" style="width:{{ round(($cat->books_count / $maxBooks) * 100) }}%;background:#6f42c1"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center small py-3">No categories found</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── MOST BORROWED + QUICK ACTIONS ─────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-trophy me-2 text-warning"></i>Most Borrowed</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($mostBorrowed as $i => $book)
                    <div class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <span class="fw-bold text-muted" style="width:20px;font-size:0.85rem">{{ $i + 1 }}</span>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate small">{{ $book->title }}</div>
                            <small class="text-muted">{{ $book->author }}</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $book->borrowings_count }}</span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No borrowing data yet</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body p-3">
                <a href="{{ route('library.borrowings.index') }}" class="btn btn-outline-primary w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Issue a Book
                </a>
                <a href="{{ route('library.borrowings.overdue') }}" class="btn btn-outline-danger w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Overdue Books
                    @if($overdue > 0)
                    <span class="badge bg-danger ms-auto">{{ $overdue }}</span>
                    @endif
                </a>
                <a href="{{ route('library.borrowings.fines') }}" class="btn btn-outline-warning w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-cash"></i> Manage Fines
                </a>
                <a href="{{ route('library.books.index') }}" class="btn btn-outline-secondary w-100 text-start d-flex align-items-center gap-2">
                    <i class="bi bi-journal-plus"></i> Add New Book
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── MONTHLY TREND CHART ─────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header py-3 border-bottom">
        <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-graph-up me-2 text-primary"></i>Borrowing Activity (Last 6 Months)</h6>
    </div>
    <div class="card-body">
        <canvas id="trendChart" height="70"></canvas>
    </div>
</div>

<div class="row g-3">
    {{-- ── OVERDUE BORROWINGS ──────────────────────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-2 text-danger"></i>Overdue Borrowings
                    @if($overdue > 0)
                    <span class="badge bg-danger ms-1">{{ $overdue }}</span>
                    @endif
                </h6>
                <a href="{{ route('library.borrowings.overdue') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($overdueBorrowings as $b)
                    @php $daysOverdue = $b->due_date->diffInDays(today()) @endphp
                    <div class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:36px;height:36px;font-size:13px">
                            {{ strtoupper(substr($b->borrower_name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate small">{{ $b->borrower_name }}</div>
                            <small class="text-muted text-truncate d-block">{{ optional($b->book)->title ?? '—' }}</small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge bg-danger">{{ $daysOverdue }}d overdue</span>
                            <div class="text-muted small mt-1">Due {{ $b->due_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
                        No overdue borrowings
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ── RECENT BORROWINGS ───────────────────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Recent Borrowings</h6>
                <a href="{{ route('library.borrowings.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($recentBorrowings as $b)
                    <div class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:34px;height:34px;font-size:12px">
                            {{ strtoupper(substr($b->borrower_name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate small">{{ $b->borrower_name }}</div>
                            <small class="text-muted text-truncate d-block">{{ optional($b->book)->title ?? '—' }}</small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            @if($b->status === 'returned')
                            <span class="badge bg-success">Returned</span>
                            @elseif($b->is_overdue)
                            <span class="badge bg-danger">Overdue</span>
                            @else
                            <span class="badge bg-primary">Active</span>
                            @endif
                            <div class="text-muted small mt-1">{{ $b->issue_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">No borrowings yet</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
@php
    $labels   = $monthlyTrend->map(fn($r) => \Carbon\Carbon::createFromFormat('Y-m', $r->month)->format('M Y'));
    $issued   = $monthlyTrend->pluck('issued');
    $returned = $monthlyTrend->pluck('returned');
@endphp
new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: @json($labels),
        datasets: [
            {
                label: 'Issued',
                data: @json($issued),
                backgroundColor: 'rgba(13,110,253,0.7)',
                borderColor: 'rgba(13,110,253,1)',
                borderWidth: 1,
                borderRadius: 4,
            },
            {
                label: 'Returned',
                data: @json($returned),
                backgroundColor: 'rgba(25,135,84,0.7)',
                borderColor: 'rgba(25,135,84,1)',
                borderWidth: 1,
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
@endsection
