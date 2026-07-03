@extends('layouts.app')
@section('title', 'Overdue Books')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-clock-history me-2" style="color:var(--secondary)"></i>Overdue Books</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('library.borrowings.index') }}">Borrowings</a></li>
            <li class="breadcrumb-item active">Overdue</li>
        </ol></nav>
    </div>
    <a href="{{ route('library.borrowings.fines') }}" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-cash me-1"></i> Manage Fines
    </a>
</div>

{{-- ── STAT CARDS ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-danger">{{ number_format($stats['total']) }}</div>
            <small class="text-muted">Overdue Books</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-warning">{{ formatCurrency($stats['fines_due']) }}</div>
            <small class="text-muted">Fines Accrued</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-secondary">{{ number_format($stats['oldest_days']) }}</div>
            <small class="text-muted">Most Days Overdue</small>
        </div>
    </div>
</div>
<div class="alert alert-info d-flex align-items-center gap-2 mb-3 py-2">
    <i class="bi bi-info-circle-fill"></i>
    <span>Overdue fine rate: <strong>K{{ number_format($stats['daily_rate'], 2) }} per day</strong>.
    Change this in <a href="{{ route('settings.index') }}#general">System Settings → Library Fine per Day</a>.</span>
</div>

{{-- ── SEARCH ───────────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm"
                placeholder="Search by borrower or book title..." value="{{ request('search') }}" style="max-width:320px">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-search me-1"></i>Search
            </button>
            @if(request('search'))
            <a href="{{ route('library.borrowings.overdue') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- ── TABLE ───────────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Borrower</th>
                        <th>Book</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th class="text-center">Days Overdue</th>
                        <th>Accrued Fine</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $b)
                    @php
                        $daysOverdue  = $b->due_date->diffInDays(today());
                        $accruedFine  = round($daysOverdue * $stats['daily_rate'], 2);
                    @endphp
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $b->borrower_name }}</td>
                        <td class="text-truncate" style="max-width:200px">
                            {{ optional($b->book)->title ?? '—' }}
                        </td>
                        <td>{{ $b->issue_date?->format('d M Y') }}</td>
                        <td class="text-danger fw-semibold">{{ $b->due_date?->format('d M Y') }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $daysOverdue > 14 ? 'danger' : 'warning' }} text-{{ $daysOverdue > 14 ? 'white' : 'dark' }}">
                                {{ $daysOverdue }} day{{ $daysOverdue != 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td class="fw-semibold text-danger">
                            {{ formatCurrency($accruedFine) }}
                            <small class="text-muted d-block" style="font-size:0.75rem">
                                {{ $daysOverdue }} × K{{ number_format($stats['daily_rate'], 2) }}
                            </small>
                        </td>
                        <td class="text-end pe-3">
                            <form action="{{ route('library.borrowings.return', $b) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Return this book?\n\n{{ $daysOverdue }} day(s) overdue — fine of K{{ number_format($accruedFine, 2) }} will be applied.')">
                                @csrf
                                <button class="btn btn-sm btn-outline-success me-1">
                                    <i class="bi bi-box-arrow-in-left me-1"></i>Return
                                </button>
                            </form>
                            <form action="{{ route('library.borrowings.renew', $b) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-repeat me-1"></i>Renew
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>
                            No overdue books — all borrowings are on time!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-transparent">
        {{ $borrowings->withQueryString()->links() }}
    </div>
</div>
@endsection
