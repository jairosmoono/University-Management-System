@extends('layouts.app')
@section('title', 'Library Fines')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-cash me-2" style="color:var(--secondary)"></i>Library Fines</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('library.borrowings.index') }}">Borrowings</a></li>
            <li class="breadcrumb-item active">Fines</li>
        </ol></nav>
    </div>
</div>

{{-- ── STAT CARDS ─────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-danger">{{ formatCurrency($stats['total_unpaid']) }}</div>
            <small class="text-muted">Total Unpaid</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-success">{{ formatCurrency($stats['total_collected']) }}</div>
            <small class="text-muted">Total Collected</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-warning">{{ $stats['count_unpaid'] }}</div>
            <small class="text-muted">Unpaid Fines</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-secondary">{{ $stats['count_waived'] }}</div>
            <small class="text-muted">Waived</small>
        </div>
    </div>
</div>

{{-- ── FILTERS ─────────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search borrower or book..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="fine_status" class="form-select form-select-sm">
                    <option value="">All Fines</option>
                    <option value="unpaid"  {{ request('fine_status') === 'unpaid'  ? 'selected' : '' }}>Unpaid</option>
                    <option value="paid"    {{ request('fine_status') === 'paid'    ? 'selected' : '' }}>Collected</option>
                    <option value="waived"  {{ request('fine_status') === 'waived'  ? 'selected' : '' }}>Waived</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('library.borrowings.fines') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- ── FINES TABLE ─────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Borrower</th>
                        <th>Book</th>
                        <th>Due Date</th>
                        <th class="text-center">Days Late</th>
                        <th>Fine Amount</th>
                        <th>Status</th>
                        <th>Settled By / When</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $b)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $b->borrower_name }}</td>
                        <td class="text-truncate" style="max-width:180px">{{ optional($b->book)->title ?? '—' }}</td>
                        <td>{{ $b->due_date?->format('d M Y') ?? '—' }}</td>
                        <td class="text-center">
                            @if($b->return_date && $b->due_date)
                                @php $late = max(0, $b->due_date->diffInDays($b->return_date, false) * -1) @endphp
                                {{ $late > 0 ? $late : '—' }}
                            @elseif(!$b->return_date && $b->due_date?->lt(today()))
                                <span class="text-danger fw-semibold">{{ $b->due_date->diffInDays(today()) }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="fw-semibold">{{ formatCurrency($b->fine_amount) }}</td>
                        <td>
                            @if($b->fine_waived)
                                <span class="badge bg-secondary">Waived</span>
                            @elseif($b->fine_paid)
                                <span class="badge bg-success">Collected</span>
                            @else
                                <span class="badge bg-danger">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            @if($b->fine_waived || $b->fine_paid)
                                <div class="small">{{ optional($b->fineCollectedBy)->name ?? '—' }}</div>
                                <div class="text-muted small">{{ $b->fine_paid_at?->format('d M Y H:i') ?? '—' }}</div>
                                @if($b->fine_waived && $b->fine_waive_reason)
                                <div class="text-muted small fst-italic">"{{ $b->fine_waive_reason }}"</div>
                                @endif
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            @if(!$b->fine_paid && !$b->fine_waived && $b->fine_amount > 0)
                            <button class="btn btn-sm btn-success me-1"
                                onclick="collectFine({{ $b->id }}, '{{ addslashes($b->borrower_name) }}', {{ $b->fine_amount }})"
                                title="Collect Fine">
                                <i class="bi bi-cash-coin me-1"></i>Collect
                            </button>
                            <button class="btn btn-sm btn-outline-secondary me-1"
                                onclick="waiveFine({{ $b->id }}, '{{ addslashes($b->borrower_name) }}')"
                                title="Waive Fine">
                                <i class="bi bi-x-circle me-1"></i>Waive
                            </button>
                            <button class="btn btn-sm btn-outline-warning"
                                onclick="adjustFine({{ $b->id }}, {{ $b->fine_amount }})"
                                title="Adjust Fine">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>
                            No fines found
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

{{-- ── COLLECT FINE MODAL ──────────────────────────────────────────────────── --}}
<div class="modal fade" id="collectModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="collectForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-cash-coin me-2 text-success"></i>Collect Fine</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Borrower: <strong id="collectName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Fine Amount Due</label>
                        <div class="input-group">
                            <span class="input-group-text">ZMW</span>
                            <input type="text" id="fineDue" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Amount Received <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">ZMW</span>
                            <input type="number" name="amount_paid" id="amountPaid" class="form-control"
                                step="0.01" min="0.01" required>
                        </div>
                        <div class="form-text">Enter less than the full amount for a partial payment.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-cash-coin me-1"></i>Confirm Collection
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── WAIVE FINE MODAL ────────────────────────────────────────────────────── --}}
<div class="modal fade" id="waiveModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="waiveForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-x-circle me-2 text-secondary"></i>Waive Fine</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Borrower: <strong id="waiveName"></strong></p>
                    <div class="mb-1">
                        <label class="form-label">Reason for Waiver <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3"
                            placeholder="e.g. Medical emergency, first-time offence..." required minlength="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Waive Fine
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── ADJUST FINE MODAL ───────────────────────────────────────────────────── --}}
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="adjustForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-pencil me-2 text-warning"></i>Adjust Fine</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-1">
                        <label class="form-label">New Fine Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">ZMW</span>
                            <input type="number" name="fine_amount" id="adjustAmount" class="form-control"
                                step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="bi bi-check me-1"></i>Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function collectFine(id, name, amount) {
    document.getElementById('collectForm').action = `/library/borrowings/${id}/collect-fine`;
    document.getElementById('collectName').textContent = name;
    document.getElementById('fineDue').value = parseFloat(amount).toFixed(2);
    document.getElementById('amountPaid').value = parseFloat(amount).toFixed(2);
    document.getElementById('amountPaid').max = amount;
    new bootstrap.Modal(document.getElementById('collectModal')).show();
}

function waiveFine(id, name) {
    document.getElementById('waiveForm').action = `/library/borrowings/${id}/waive-fine`;
    document.getElementById('waiveName').textContent = name;
    new bootstrap.Modal(document.getElementById('waiveModal')).show();
}

function adjustFine(id, amount) {
    document.getElementById('adjustForm').action = `/library/borrowings/${id}/adjust-fine`;
    document.getElementById('adjustAmount').value = parseFloat(amount).toFixed(2);
    new bootstrap.Modal(document.getElementById('adjustModal')).show();
}
</script>
@endpush
@endsection
