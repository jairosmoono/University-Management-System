@extends('layouts.app')
@section('title', 'Asset Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Asset Management</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Assets</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('assets.depreciation-report') }}" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-graph-down-arrow me-1"></i> Depreciation Report
        </a>
        @can('manage-assets')
        <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Asset
        </a>
        @endcan
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-primary fw-bold mb-1">{{ $stats['total'] }}</h4>
            <small class="text-muted">Total Assets</small>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-success fw-bold mb-1">{{ $stats['active'] }}</h4>
            <small class="text-muted">Active</small>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-warning fw-bold mb-1">{{ $stats['maintenance'] }}</h4>
            <small class="text-muted">In Maintenance</small>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-secondary fw-bold mb-1">{{ $stats['disposed'] }}</h4>
            <small class="text-muted">Disposed</small>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-dark fw-bold mb-1" style="font-size:1rem">{{ formatCurrency($stats['total_purchase']) }}</h4>
            <small class="text-muted">Total Cost</small>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-primary fw-bold mb-1" style="font-size:1rem">{{ formatCurrency($stats['total_book_value']) }}</h4>
            <small class="text-muted">Total Book Value</small>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
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
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active"      {{ request('status') == 'active'      ? 'selected' : '' }}>Active</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="disposed"    {{ request('status') == 'disposed'    ? 'selected' : '' }}>Disposed</option>
                    <option value="lost"        {{ request('status') == 'lost'        ? 'selected' : '' }}>Lost</option>
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
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search name, code…" value="{{ request('search') }}">
            </div>
            <div class="col-auto d-flex gap-1">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table datatable table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Asset Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Department</th>
                    <th class="text-end">Purchase Price</th>
                    <th class="text-end">Book Value</th>
                    <th class="text-center">Depreciation</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                @php
                    $pct = $asset->depreciationPercent();
                @endphp
                <tr>
                    <td><code class="small">{{ $asset->asset_code }}</code></td>
                    <td class="fw-semibold">{{ $asset->name }}</td>
                    <td><span class="badge bg-light text-dark border small">{{ $asset->category }}</span></td>
                    <td class="text-muted small">{{ optional($asset->department)->name ?? '—' }}</td>
                    <td class="text-end small">{{ $asset->purchase_price ? formatCurrency($asset->purchase_price) : '—' }}</td>
                    <td class="text-end small fw-semibold text-primary">{{ $asset->current_value ? formatCurrency($asset->current_value) : '—' }}</td>
                    <td class="text-center" style="min-width:100px">
                        @if($asset->depreciation_method)
                            <div class="progress mb-1" style="height:5px">
                                <div class="progress-bar bg-danger" style="width:{{ $pct }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pct }}% depreciated</small>
                        @else
                            <small class="text-muted">—</small>
                        @endif
                    </td>
                    <td>
                        @php $sc = ['active'=>'success','maintenance'=>'warning','disposed'=>'secondary','lost'=>'danger'] @endphp
                        <span class="badge bg-{{ $sc[$asset->status] ?? 'secondary' }}">{{ ucfirst($asset->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-secondary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can('manage-assets')
                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" class="d-inline" onsubmit="return confirm('Delete asset?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">{{ $assets->links() }}</div>
    </div>
</div>
@endsection
