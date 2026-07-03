@extends('layouts.app')
@section('title', 'Student Holds')
@section('page-title', 'Student Holds')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-slash-circle me-2" style="color:var(--secondary)"></i>Student Holds</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Student Holds</li>
        </ol></nav>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="text-danger fs-2 fw-bold">{{ $stats['active'] }}</div>
            <div class="text-muted" style="font-size:0.82rem"><i class="bi bi-slash-circle me-1"></i>Active Holds</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="text-warning fs-2 fw-bold">{{ $stats['blocking'] }}</div>
            <div class="text-muted" style="font-size:0.82rem"><i class="bi bi-lock me-1"></i>Blocking Registration</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="text-success fs-2 fw-bold">{{ $stats['released'] }}</div>
            <div class="text-muted" style="font-size:0.82rem"><i class="bi bi-check-circle me-1"></i>Released</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3 px-2">
            @foreach($stats['by_type'] as $type => $count)
            <div class="d-flex justify-content-between" style="font-size:0.78rem">
                <span>{{ \App\Models\StudentHold::typeLabel($type) }}</span>
                <span class="fw-semibold">{{ $count }}</span>
            </div>
            @endforeach
            @if($stats['by_type']->isEmpty())<div class="text-muted" style="font-size:0.8rem">No active holds</div>@endif
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Place Hold Form --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3"><h5 class="mb-0 fw-semibold"><i class="bi bi-plus-lg me-2"></i>Place Hold</h5></div>
            <div class="card-body">
                <form action="{{ route('academic.student-holds.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Student ID</label>
                        <input type="number" name="student_id" class="form-control @error('student_id') is-invalid @enderror"
                               placeholder="Enter student database ID" value="{{ old('student_id') }}" required>
                        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hold Type</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">— Select type —</option>
                            @foreach($types as $t)
                            <option value="{{ $t }}" {{ old('type') == $t ? 'selected' : '' }}>{{ \App\Models\StudentHold::typeLabel($t) }}</option>
                            @endforeach
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason</label>
                        <textarea name="reason" rows="3" class="form-control @error('reason') is-invalid @enderror"
                                  placeholder="Reason for the hold…" required>{{ old('reason') }}</textarea>
                        @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="blocks_registration" value="1" id="blocksReg" {{ old('blocks_registration', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="blocksReg">Block Course Registration</label>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-slash-circle me-1"></i>Place Hold
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Holds List --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3 d-flex align-items-center gap-3">
                <h5 class="mb-0 fw-semibold flex-1">All Holds</h5>
                <form class="d-flex gap-2">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()" style="width:150px">
                        <option value="">All Types</option>
                        @foreach($types as $t)
                        <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ \App\Models\StudentHold::typeLabel($t) }}</option>
                        @endforeach
                    </select>
                    <select name="is_active" class="form-select form-select-sm" onchange="this.form.submit()" style="width:130px">
                        <option value="">All</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Released</option>
                    </select>
                    <input name="search" class="form-control form-control-sm" placeholder="Search…" value="{{ request('search') }}" style="width:130px">
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Student</th><th>Type</th><th>Reason</th><th>Placed By</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($holds as $hold)
                        <tr>
                            <td>
                                <div class="fw-semibold" style="font-size:0.88rem">{{ $hold->student->full_name }}</div>
                                <div class="text-muted" style="font-size:0.78rem">{{ $hold->student->student_id }}</div>
                            </td>
                            <td><span class="badge {{ \App\Models\StudentHold::typeBadgeClass($hold->type) }}">{{ \App\Models\StudentHold::typeLabel($hold->type) }}</span></td>
                            <td style="font-size:0.83rem;max-width:200px" class="text-truncate" title="{{ $hold->reason }}">{{ $hold->reason }}</td>
                            <td style="font-size:0.82rem">{{ $hold->placedBy?->name ?? '—' }}<br><span class="text-muted" style="font-size:0.75rem">{{ $hold->created_at->format('d M Y') }}</span></td>
                            <td>
                                @if($hold->is_active)
                                    <span class="badge bg-danger">Active</span>
                                    @if($hold->blocks_registration)<br><span class="badge bg-warning text-dark mt-1" style="font-size:0.7rem">Blocks Reg.</span>@endif
                                @else
                                    <span class="badge bg-success">Released</span>
                                    <div class="text-muted" style="font-size:0.75rem">{{ $hold->released_at?->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td>
                                @if($hold->is_active)
                                <form action="{{ route('academic.student-holds.release', $hold) }}" method="POST" onsubmit="return confirm('Release this hold?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">Release</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No holds found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($holds->hasPages())
            <div class="card-footer py-2">{{ $holds->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
