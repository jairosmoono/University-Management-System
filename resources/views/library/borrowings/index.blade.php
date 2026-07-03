@extends('layouts.app')
@section('title', 'Book Borrowings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Book Borrowings</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Borrowings</li>
        </ol></nav>
    </div>
    @can('manage-library')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issueBookModal">
        <i class="bi bi-book me-1"></i> Issue Book
    </button>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-primary fw-bold">{{ $stats['active'] }}</h4>
            <small class="text-muted">Active Borrowings</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-danger fw-bold">{{ $stats['overdue'] }}</h4>
            <small class="text-muted">Overdue</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-success fw-bold">{{ $stats['returned_today'] }}</h4>
            <small class="text-muted">Returned Today</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold text-dark" style="font-size:.85rem">
                    <i class="bi bi-cash-stack me-1 text-warning"></i>Fines Summary
                </span>
                <a href="{{ route('library.borrowings.fines') }}" class="text-decoration-none" style="font-size:.75rem">
                    Manage <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                <small class="text-muted"><i class="bi bi-check-circle text-success me-1"></i>Collected</small>
                <span class="fw-semibold text-success">{{ formatCurrency($stats['fines_collected']) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                <small class="text-muted"><i class="bi bi-hourglass-split text-danger me-1"></i>Pending</small>
                <span class="fw-semibold text-danger">{{ formatCurrency($stats['fines_pending']) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                <small class="text-muted"><i class="bi bi-clock text-warning me-1"></i>Accruing</small>
                <span class="fw-semibold text-warning">{{ formatCurrency($stats['fines_accruing']) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-1">
                <small class="text-muted"><i class="bi bi-slash-circle text-secondary me-1"></i>Waived</small>
                <span class="fw-semibold text-secondary">{{ $stats['fines_waived'] }} case{{ $stats['fines_waived'] != 1 ? 's' : '' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search student or book..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('library.borrowings.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Student</th><th>Book Title</th><th>Issue Date</th><th>Due Date</th><th>Return Date</th><th>Days Overdue</th><th>Fine</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($borrowings as $b)
                @php
                    $daysOverdue = 0;
                    if (!$b->return_date && $b->due_date < now()) {
                        $daysOverdue = (int) $b->due_date->diffInDays(today());
                    }
                @endphp
                <tr class="{{ $daysOverdue > 0 ? 'table-danger' : '' }}">
                    <td>{{ $b->id }}</td>
                    <td>
                        <strong>{{ $b->borrower_name }}</strong><br>
                        <small class="text-muted">{{ optional($b->student)->student_id }}</small>
                    </td>
                    <td>{{ optional($b->book)->title }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->issue_date)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->due_date)->format('d M Y') }}</td>
                    <td>{{ $b->return_date ? \Carbon\Carbon::parse($b->return_date)->format('d M Y') : '—' }}</td>
                    <td>{{ $daysOverdue > 0 ? $daysOverdue . ' days' : '—' }}</td>
                    <td>{{ $b->fine_amount ? formatCurrency($b->fine_amount) : '—' }}</td>
                    <td>
                        @php $sc = ['borrowed'=>'warning','returned'=>'success','overdue'=>'danger','lost'=>'dark'] @endphp
                        <span class="badge bg-{{ $sc[$b->status] ?? 'secondary' }}">{{ ucfirst($b->status) }}</span>
                    </td>
                    <td>
                        @can('manage-library')
                        @if($b->status === 'borrowed' || $b->status === 'overdue')
                        <form method="POST" action="{{ route('library.borrowings.return', $b) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Return</button>
                        </form>
                        <form method="POST" action="{{ route('library.borrowings.renew', $b) }}" class="d-inline" onsubmit="return confirm('Renew for 14 more days?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-primary">Renew</button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $borrowings->withQueryString()->links() }}
    </div>
</div>

@can('manage-library')
<div class="modal fade" id="issueBookModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('library.borrowings.issue') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Issue Book</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Student *</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select Student</option>
                            @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ optional($s->user)->name }} ({{ $s->student_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Book *</label>
                        <select name="library_book_id" class="form-select" required>
                            <option value="">Select Book</option>
                            @foreach($availableBooks as $book)
                            <option value="{{ $book->id }}">{{ $book->title }} - {{ $book->author }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Issue Date *</label>
                            <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Due Date *</label>
                            <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Issue Book</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
