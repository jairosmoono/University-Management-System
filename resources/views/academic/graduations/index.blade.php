@extends('layouts.app')
@section('title', 'Graduation Management')

@section('content')
<div class="mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h4 class="mb-1">Graduation Management</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Graduation</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('graduation.eligible') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-check me-1"></i>Eligible Students
        </a>
        <a href="{{ route('graduation.ceremonies.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-calendar-event me-1"></i>Ceremonies
        </a>
        <a href="{{ route('graduation.certificate.sample') }}" class="btn btn-outline-info btn-sm" target="_blank">
            <i class="bi bi-eye me-1"></i>Certificate Preview
        </a>
        @hasrole('super-admin|registrar')
        <a href="{{ route('graduation.apply') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New Application
        </a>
        @endhasrole
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
                <div class="text-muted small">Total Applications</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-warning">{{ number_format($stats['pending']) }}</div>
                <div class="text-muted small">Pending / Review</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-info">{{ number_format($stats['cleared']) }}</div>
                <div class="text-muted small">Cleared</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-success">{{ number_format($stats['approved']) }}</div>
                <div class="text-muted small">Approved</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold" style="color:#212529">{{ number_format($stats['graduated']) }}</div>
                <div class="text-muted small">Graduated</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-danger">{{ number_format($stats['rejected']) }}</div>
                <div class="text-muted small">Rejected</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending'=>'Pending','under_review'=>'Under Review','cleared'=>'Cleared','approved'=>'Approved','rejected'=>'Rejected','graduated'=>'Graduated'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="program_id" class="form-select form-select-sm">
                    <option value="">All Programs</option>
                    @foreach($programs as $p)
                    <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="academic_year_id" class="form-select form-select-sm">
                    <option value="">All Academic Years</option>
                    @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('graduation.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Applications table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">Graduation Applications</h6>
        <span class="badge bg-secondary">{{ $applications->total() }} records</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Program</th>
                    <th>CGPA</th>
                    <th>Credits</th>
                    <th>Clearance</th>
                    <th>Ceremony</th>
                    <th>Status</th>
                    <th>Applied</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td class="text-muted small">{{ $app->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $app->student?->full_name }}</div>
                        <div class="text-muted small">{{ $app->student?->student_id }}</div>
                    </td>
                    <td class="small">{{ $app->program?->name }}</td>
                    <td>
                        <span class="fw-semibold {{ $app->cgpa >= 1.5 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($app->cgpa, 2) }}
                        </span>
                    </td>
                    <td class="small">{{ $app->credits_earned }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <span title="Finance" class="badge rounded-pill bg-{{ $app->finance_cleared ? 'success' : 'secondary' }}">F</span>
                            <span title="Library" class="badge rounded-pill bg-{{ $app->library_cleared ? 'success' : 'secondary' }}">L</span>
                            <span title="Academic" class="badge rounded-pill bg-{{ $app->academic_cleared ? 'success' : 'secondary' }}">A</span>
                        </div>
                    </td>
                    <td class="small">{{ $app->ceremony?->name ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ \App\Models\GraduationApplication::statusColor($app->status) }}">
                            {{ \App\Models\GraduationApplication::statusLabel($app->status) }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $app->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('graduation.show', $app) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">No graduation applications found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
    <div class="card-footer bg-white border-0">
        {{ $applications->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
