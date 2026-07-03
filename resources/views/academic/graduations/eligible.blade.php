@extends('layouts.app')
@section('title', 'Eligible Students')

@section('content')
<div class="mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h4 class="mb-1">Students Eligible for Graduation</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('graduation.index') }}">Graduation</a></li>
            <li class="breadcrumb-item active">Eligible Students</li>
        </ol></nav>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name…" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="program_id" class="form-select form-select-sm">
                    <option value="">All Programs</option>
                    @foreach($programs as $p)
                    <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('graduation.eligible') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">Active Students Without a Graduation Application</h6>
        <span class="badge bg-secondary">{{ $students->count() }} students</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Student</th>
                    <th>Program</th>
                    <th>CGPA</th>
                    <th>Credits</th>
                    <th class="text-center">Finance</th>
                    <th class="text-center">Library</th>
                    <th class="text-center">Academic</th>
                    <th class="text-center">Eligible</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $row)
                @php $s = $row['student']; $e = $row['eligibility']; @endphp
                <tr class="{{ $e['eligible'] ? '' : 'table-warning' }}">
                    <td>
                        <div class="fw-semibold">{{ $s->full_name }}</div>
                        <div class="text-muted small">{{ $s->student_id }}</div>
                    </td>
                    <td class="small">{{ $s->program?->name }}</td>
                    <td>
                        <span class="fw-semibold {{ $e['cgpa_ok'] ? 'text-success' : 'text-danger' }}">
                            {{ $e['cgpa'] }}
                        </span>
                        @if(!$e['cgpa_ok'])<small class="text-muted d-block">Min 1.5 req.</small>@endif
                    </td>
                    <td>
                        <span class="{{ $e['credits_ok'] ? 'text-success' : 'text-danger' }} fw-semibold">
                            {{ $e['credits_earned'] }}
                        </span>
                        <span class="text-muted small"> / {{ $e['required_credits'] }}</span>
                    </td>
                    <td class="text-center">
                        @if($e['finance_ok'])
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-danger fs-5" title="Outstanding: {{ number_format($e['outstanding_bal'], 2) }}"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($e['library_ok'])
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-danger fs-5" title="{{ $e['active_loans'] }} loans, KES {{ number_format($e['unpaid_fines'], 2) }} fines"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($e['academic_ok'])
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-danger fs-5" title="{{ $e['failed_count'] }} failed, {{ $e['pending_results'] }} pending results"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($e['eligible'])
                            <span class="badge bg-success">Eligible</span>
                        @else
                            <span class="badge bg-warning text-dark">Not Yet</span>
                        @endif
                    </td>
                    <td>
                        @if($e['eligible'])
                        <a href="{{ route('graduation.apply') }}?student_id={{ $s->id }}" class="btn btn-sm btn-primary">
                            Apply
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
