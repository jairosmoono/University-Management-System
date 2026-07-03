@extends('layouts.app')
@section('title', 'Asset Depreciation Report')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="mb-1">Asset Depreciation Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
            <li class="breadcrumb-item active">Depreciation Report</li>
        </ol></nav>
    </div>
</div>

{{-- Totals --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-4 fw-bold text-dark">{{ formatCurrency($totals['purchase']) }}</div>
            <small class="text-muted">Total Original Cost</small>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-4 fw-bold text-danger">{{ formatCurrency($totals['accumulated']) }}</div>
            <small class="text-muted">Accumulated Depreciation</small>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-4 fw-bold text-primary">{{ formatCurrency($totals['book_value']) }}</div>
            <small class="text-muted">Total Book Value</small>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-4 fw-bold text-warning">{{ formatCurrency($totals['annual_dep']) }}</div>
            <small class="text-muted">Annual Depreciation (est.)</small>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="method" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Methods</option>
                    <option value="straight_line"    {{ request('method') === 'straight_line'    ? 'selected' : '' }}>Straight-Line</option>
                    <option value="declining_balance" {{ request('method') === 'declining_balance' ? 'selected' : '' }}>Declining Balance</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach(['Electronics','Furniture','Vehicle','Machinery','Lab Equipment','Office Equipment','Building','Land','Other'] as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <a href="{{ route('assets.depreciation-report') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Report Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-graph-down-arrow me-2 text-warning"></i>Depreciable Assets</h6>
        <span class="badge bg-secondary">{{ $assets->count() }} assets</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0 table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Asset</th>
                        <th>Category</th>
                        <th>Department</th>
                        <th>Method</th>
                        <th class="text-end">Original Cost</th>
                        <th class="text-end">Accum. Dep.</th>
                        <th class="text-end">Book Value</th>
                        <th class="text-end">Annual Dep.</th>
                        <th class="text-center">% Depreciated</th>
                        <th>Purchase Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    @php
                        $pct     = $asset->depreciationPercent();
                        $barCol  = $pct >= 80 ? 'danger' : ($pct >= 50 ? 'warning' : 'success');
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold small">{{ $asset->name }}</div>
                            <code class="small text-muted">{{ $asset->asset_code }}</code>
                        </td>
                        <td><span class="badge bg-light text-dark border small">{{ $asset->category }}</span></td>
                        <td class="text-muted small">{{ $asset->department?->name ?? '—' }}</td>
                        <td>
                            @if($asset->depreciation_method === 'straight_line')
                                <span class="badge bg-info bg-opacity-75 text-dark">Straight-Line</span>
                                @if($asset->useful_life_years)
                                <small class="text-muted d-block">{{ $asset->useful_life_years }}yr life</small>
                                @endif
                            @else
                                <span class="badge bg-purple bg-opacity-75" style="background:#6f42c1;color:#fff">Declining Bal.</span>
                                @if($asset->depreciation_rate)
                                <small class="text-muted d-block">{{ $asset->depreciation_rate }}%/yr</small>
                                @endif
                            @endif
                        </td>
                        <td class="text-end small">{{ formatCurrency($asset->purchase_price ?? 0) }}</td>
                        <td class="text-end small text-danger">{{ formatCurrency($asset->accumulatedDepreciation()) }}</td>
                        <td class="text-end small fw-semibold text-primary">{{ formatCurrency($asset->current_value ?? 0) }}</td>
                        <td class="text-end small text-warning">{{ formatCurrency($asset->annualDepreciation()) }}</td>
                        <td class="text-center" style="min-width:110px">
                            <div class="progress mb-1" style="height:5px">
                                <div class="progress-bar bg-{{ $barCol }}" style="width:{{ $pct }}%"></div>
                            </div>
                            <small class="{{ $pct >= 80 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ $pct }}%</small>
                        </td>
                        <td class="small text-muted">{{ $asset->purchase_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-5">
                            <i class="bi bi-graph-down-arrow d-block fs-2 mb-2 opacity-25"></i>
                            No assets with depreciation configured match the selected filters.
                            <br><small>Set a depreciation method on an asset to see it here.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($assets->isNotEmpty())
                <tfoot class="table-light fw-semibold">
                    <tr>
                        <td colspan="4">Totals ({{ $assets->count() }} assets)</td>
                        <td class="text-end">{{ formatCurrency($totals['purchase']) }}</td>
                        <td class="text-end text-danger">{{ formatCurrency($totals['accumulated']) }}</td>
                        <td class="text-end text-primary">{{ formatCurrency($totals['book_value']) }}</td>
                        <td class="text-end text-warning">{{ formatCurrency($totals['annual_dep']) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
