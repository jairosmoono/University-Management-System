@extends('layouts.app')
@section('title', $ceremony->name)

@section('content')
<div class="mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h4 class="mb-1">{{ $ceremony->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('graduation.index') }}">Graduation</a></li>
            <li class="breadcrumb-item"><a href="{{ route('graduation.ceremonies.index') }}">Ceremonies</a></li>
            <li class="breadcrumb-item active">{{ $ceremony->name }}</li>
        </ol></nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-{{ \App\Models\GraduationCeremony::statusColor($ceremony->status) }} fs-6 px-3 py-2">
            {{ \App\Models\GraduationCeremony::statusLabel($ceremony->status) }}
        </span>
        @hasrole('super-admin')
        <form method="POST" action="{{ route('graduation.ceremonies.destroy', $ceremony) }}"
              onsubmit="return confirm('Delete this ceremony? Applications linked to it will be unassigned.')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
        @endhasrole
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <i class="bi bi-calendar3 fs-4 text-primary mb-2 d-block"></i>
                <div class="fw-bold">{{ $ceremony->ceremony_date->format('d M Y') }}</div>
                <div class="text-muted small">Ceremony Date</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <i class="bi bi-geo-alt fs-4 text-info mb-2 d-block"></i>
                <div class="fw-bold">{{ $ceremony->venue ?? 'TBD' }}</div>
                <div class="text-muted small">Venue</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <i class="bi bi-people fs-4 text-success mb-2 d-block"></i>
                <div class="fw-bold">{{ $graduates->count() }}{{ $ceremony->max_graduates ? ' / '.$ceremony->max_graduates : '' }}</div>
                <div class="text-muted small">Graduates</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <i class="bi bi-mortarboard fs-4 text-warning mb-2 d-block"></i>
                <div class="fw-bold">{{ $ceremony->academicYear?->name }}</div>
                <div class="text-muted small">Academic Year</div>
            </div>
        </div>
    </div>
</div>

@if($ceremony->dress_code || $ceremony->notes)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body px-4 py-3">
        @if($ceremony->dress_code)
        <div class="d-flex gap-2 align-items-center mb-1">
            <i class="bi bi-suit-heart text-muted"></i>
            <span class="small"><strong>Dress Code:</strong> {{ $ceremony->dress_code }}</span>
        </div>
        @endif
        @if($ceremony->notes)
        <div class="d-flex gap-2 align-items-start">
            <i class="bi bi-info-circle text-muted mt-1"></i>
            <span class="small">{{ $ceremony->notes }}</span>
        </div>
        @endif
    </div>
</div>
@endif

{{-- Graduates list --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">Graduates List</h6>
        <span class="badge bg-secondary">{{ $graduates->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Graduate</th>
                    <th>Program</th>
                    <th>CGPA</th>
                    <th>Credits</th>
                    <th>Status</th>
                    <th>Graduation Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($graduates as $i => $app)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>
                        <div class="fw-semibold">{{ $app->student?->full_name }}</div>
                        <div class="text-muted small">{{ $app->student?->student_id }}</div>
                    </td>
                    <td class="small">{{ $app->program?->name }}</td>
                    <td>
                        <span class="fw-semibold {{ $app->cgpa >= 1.5 ? 'text-success' : 'text-muted' }}">
                            {{ number_format($app->cgpa, 2) }}
                        </span>
                    </td>
                    <td>{{ $app->credits_earned }}</td>
                    <td>
                        <span class="badge bg-{{ \App\Models\GraduationApplication::statusColor($app->status) }}">
                            {{ \App\Models\GraduationApplication::statusLabel($app->status) }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $app->graduation_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @if(in_array($app->status, ['approved','graduated']))
                        <a href="{{ route('graduation.certificate', $app) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Certificate">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No graduates assigned to this ceremony yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
