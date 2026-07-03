@extends('layouts.app')
@section('title', $asset->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $asset->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
            <li class="breadcrumb-item active">{{ $asset->asset_code }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">

    {{-- ── Left: Asset Info ──────────────────────────────────────── --}}
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-box-seam me-2 text-primary"></i>Asset Details</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted fw-normal">Code</dt>
                    <dd class="col-7"><code>{{ $asset->asset_code }}</code></dd>

                    <dt class="col-5 text-muted fw-normal">Category</dt>
                    <dd class="col-7"><span class="badge bg-secondary">{{ $asset->category }}</span></dd>

                    <dt class="col-5 text-muted fw-normal">Status</dt>
                    <dd class="col-7">
                        @php $sc = ['active'=>'success','maintenance'=>'warning','disposed'=>'secondary','lost'=>'danger'] @endphp
                        <span class="badge bg-{{ $sc[$asset->status] ?? 'secondary' }}">{{ ucfirst($asset->status) }}</span>
                    </dd>

                    <dt class="col-5 text-muted fw-normal">Department</dt>
                    <dd class="col-7">{{ $asset->department?->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Location</dt>
                    <dd class="col-7">{{ $asset->location ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Serial No.</dt>
                    <dd class="col-7">{{ $asset->serial_number ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Purchase Date</dt>
                    <dd class="col-7">{{ $asset->purchase_date?->format('d M Y') ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Warranty</dt>
                    <dd class="col-7">
                        @if($asset->warranty_expiry)
                            <span class="{{ $asset->warranty_expiry->isPast() ? 'text-danger' : 'text-success' }}">
                                {{ $asset->warranty_expiry->format('d M Y') }}
                                {{ $asset->warranty_expiry->isPast() ? '(Expired)' : '' }}
                            </span>
                        @else —
                        @endif
                    </dd>
                </dl>
                @if($asset->description)
                <hr class="my-2">
                <p class="text-muted small mb-0">{{ $asset->description }}</p>
                @endif
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-cash-stack me-2 text-success"></i>Financial Summary</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Purchase Price</span>
                    <span class="fw-semibold">{{ formatCurrency($asset->purchase_price ?? 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Accumulated Depreciation</span>
                    <span class="fw-semibold text-danger">− {{ formatCurrency($asset->accumulatedDepreciation()) }}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small fw-semibold">Current Book Value</span>
                    <span class="fw-bold fs-6 text-primary">{{ formatCurrency($asset->current_value ?? 0) }}</span>
                </div>

                @php $pct = $asset->depreciationPercent(); @endphp
                <div class="mb-1 d-flex justify-content-between small">
                    <span class="text-muted">Value Retained</span>
                    <span>{{ 100 - $pct }}%</span>
                </div>
                <div class="progress mb-3" style="height:8px">
                    <div class="progress-bar bg-success" style="width:{{ 100 - $pct }}%"></div>
                    <div class="progress-bar bg-danger bg-opacity-50" style="width:{{ $pct }}%"></div>
                </div>

                <div class="d-flex justify-content-between small text-muted">
                    <span>Salvage Value</span>
                    <span>{{ formatCurrency($asset->salvage_value ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Right: Depreciation ────────────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Depreciation Config --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-graph-down-arrow me-2 text-warning"></i>Depreciation Settings</h6>
                <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i>Edit Settings
                </a>
            </div>
            <div class="card-body">
                @if($asset->depreciation_method)
                <div class="row g-3">
                    <div class="col-sm-3 text-center">
                        <div class="text-muted small mb-1">Method</div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                            {{ $asset->depreciation_method === 'straight_line' ? 'Straight-Line' : 'Declining Balance' }}
                        </span>
                    </div>
                    <div class="col-sm-3 text-center">
                        <div class="text-muted small mb-1">Annual Rate</div>
                        <div class="fw-bold fs-5">
                            @if($asset->depreciation_method === 'straight_line' && $asset->useful_life_years)
                                {{ round(100 / $asset->useful_life_years, 1) }}%
                            @elseif($asset->depreciation_rate)
                                {{ $asset->depreciation_rate }}%
                            @else —
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-3 text-center">
                        <div class="text-muted small mb-1">Annual Amount</div>
                        <div class="fw-bold fs-5 text-warning">{{ formatCurrency($asset->annualDepreciation()) }}</div>
                    </div>
                    <div class="col-sm-3 text-center">
                        <div class="text-muted small mb-1">Monthly Amount</div>
                        <div class="fw-bold fs-5 text-muted">{{ formatCurrency($asset->monthlyDepreciation()) }}</div>
                    </div>

                    @if($asset->depreciation_method === 'straight_line' && $asset->useful_life_years)
                    <div class="col-sm-3 text-center">
                        <div class="text-muted small mb-1">Useful Life</div>
                        <div class="fw-bold">{{ $asset->useful_life_years }} yrs</div>
                    </div>
                    <div class="col-sm-3 text-center">
                        <div class="text-muted small mb-1">Remaining Life</div>
                        <div class="fw-bold {{ ($asset->remainingLife() ?? 0) <= 1 ? 'text-danger' : '' }}">
                            {{ $asset->remainingLife() !== null ? round($asset->remainingLife(), 1) . ' yrs' : '—' }}
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-muted mb-0 small">No depreciation method configured.
                    <a href="{{ route('assets.edit', $asset) }}">Edit the asset</a> to set one up.
                </p>
                @endif
            </div>
        </div>

        {{-- Record Depreciation Form --}}
        @can('manage-assets')
        @if($asset->status !== 'disposed' && $asset->status !== 'lost')
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-2 text-success"></i>Record Depreciation Entry</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('assets.depreciate', $asset) }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label form-label-sm">Period <span class="text-danger">*</span></label>
                            <input type="text" name="period_label" class="form-control form-control-sm @error('period_label') is-invalid @enderror"
                                placeholder="e.g. 2025-Q1" value="{{ old('period_label', now()->format('Y') . '-Q' . ceil(now()->month / 3)) }}" required>
                            @error('period_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label form-label-sm">Depreciation Amount (K) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">K</span>
                                <input type="number" name="depreciation_amount"
                                    class="form-control @error('depreciation_amount') is-invalid @enderror"
                                    value="{{ old('depreciation_amount', $asset->depreciation_method ? round($asset->annualDepreciation(), 2) : '') }}"
                                    min="0.01" step="0.01" required>
                            </div>
                            @if($asset->annualDepreciation() > 0)
                            <div class="form-text">Suggested annual: {{ formatCurrency($asset->annualDepreciation()) }}</div>
                            @endif
                            @error('depreciation_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label form-label-sm">Notes</label>
                            <input type="text" name="notes" class="form-control form-control-sm"
                                placeholder="Optional note" value="{{ old('notes') }}">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-sm btn-success w-100">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @endcan

        {{-- Depreciation Log --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-secondary"></i>Depreciation Log</h6>
                <span class="badge bg-secondary">{{ $asset->depreciationLogs->count() }} entries</span>
            </div>
            <div class="card-body p-0">
                @if($asset->depreciationLogs->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Period</th>
                                <th>Method</th>
                                <th class="text-end">Opening Value</th>
                                <th class="text-end">Depreciation</th>
                                <th class="text-end">Closing Value</th>
                                <th>Recorded By</th>
                                <th>Date</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asset->depreciationLogs as $log)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $log->period_label }}</span></td>
                                <td><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $log->method)) }}</small></td>
                                <td class="text-end small">{{ formatCurrency($log->opening_value) }}</td>
                                <td class="text-end small text-danger fw-semibold">− {{ formatCurrency($log->depreciation_amount) }}</td>
                                <td class="text-end small fw-semibold text-primary">{{ formatCurrency($log->closing_value) }}</td>
                                <td class="small text-muted">{{ $log->recordedBy?->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $log->created_at->format('d M Y') }}</td>
                                <td class="small text-muted">{{ $log->notes ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="fw-semibold text-end small">Total Depreciated</td>
                                <td class="text-end fw-bold text-danger small">− {{ formatCurrency($asset->totalDepreciationPosted()) }}</td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4 small">
                    <i class="bi bi-journal-x d-block fs-3 mb-2 opacity-25"></i>
                    No depreciation entries recorded yet.
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
