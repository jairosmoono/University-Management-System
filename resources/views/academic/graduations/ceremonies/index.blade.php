@extends('layouts.app')
@section('title', 'Graduation Ceremonies')

@section('content')
<div class="mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h4 class="mb-1">Graduation Ceremonies</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('graduation.index') }}">Graduation</a></li>
            <li class="breadcrumb-item active">Ceremonies</li>
        </ol></nav>
    </div>
    @hasrole('super-admin|registrar')
    <a href="{{ route('graduation.ceremonies.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>New Ceremony
    </a>
    @endhasrole
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['planned','confirmed','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="academic_year_id" class="form-select form-select-sm">
                    <option value="">All Academic Years</option>
                    @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('graduation.ceremonies.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Ceremonies list --}}
<div class="row g-4">
    @forelse($ceremonies as $ceremony)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $ceremony->name }}</h6>
                        <div class="text-muted small">{{ $ceremony->academicYear?->name }}</div>
                    </div>
                    <span class="badge bg-{{ \App\Models\GraduationCeremony::statusColor($ceremony->status) }}">
                        {{ \App\Models\GraduationCeremony::statusLabel($ceremony->status) }}
                    </span>
                </div>

                <div class="d-flex flex-column gap-1 mb-3">
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <i class="bi bi-calendar3"></i>
                        {{ $ceremony->ceremony_date->format('d F Y') }}
                    </div>
                    @if($ceremony->venue)
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <i class="bi bi-geo-alt"></i>
                        {{ $ceremony->venue }}
                    </div>
                    @endif
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <i class="bi bi-people"></i>
                        {{ number_format($ceremony->graduates_count) }} graduates
                        @if($ceremony->max_graduates)
                        / {{ number_format($ceremony->max_graduates) }} max
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('graduation.ceremonies.show', $ceremony) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        View Details
                    </a>
                    @hasrole('super-admin')
                    <form method="POST" action="{{ route('graduation.ceremonies.destroy', $ceremony) }}"
                          onsubmit="return confirm('Delete this ceremony? Applications linked to it will be unassigned.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    @endhasrole
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-event fs-1 text-muted mb-3 d-block"></i>
                <h6 class="fw-semibold">No ceremonies yet</h6>
                @hasrole('super-admin|registrar')
                <a href="{{ route('graduation.ceremonies.create') }}" class="btn btn-primary btn-sm mt-2">Create First Ceremony</a>
                @endhasrole
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($ceremonies->hasPages())
<div class="mt-4">{{ $ceremonies->withQueryString()->links() }}</div>
@endif
@endsection
