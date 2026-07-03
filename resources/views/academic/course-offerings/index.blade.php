@extends('layouts.app')
@section('title', isset($isStudentView) ? 'Course Offerings' : 'Course Offerings')
@section('content')

@if(isset($isStudentView))
{{-- ══════════════════════════════════════════════════════════════════════════
     STUDENT VIEW
══════════════════════════════════════════════════════════════════════════ --}}

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>Course Offerings</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Course Offerings</li>
        </ol></nav>
    </div>
    <form method="GET" class="d-flex align-items-center gap-2">
        <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:180px">
            @foreach($semesters as $s)
            <option value="{{ $s->id }}" {{ ($semester?->id == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </form>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ request()->fullUrlWithQuery(['filter' => '']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 {{ !request('filter') || request('filter') === '' ? 'border-primary border-2' : '' }}">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-collection-fill"></i></div>
                <div>
                    <div class="stat-value text-primary">{{ $stats['total'] }}</div>
                    <div class="stat-label text-muted">Total Offerings</div>
                </div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'registered']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 {{ request('filter') === 'registered' ? 'border-success border-2' : '' }}">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-value text-success">{{ $stats['registered'] }}</div>
                    <div class="stat-label text-muted">My Registered</div>
                </div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'available']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 {{ request('filter') === 'available' ? 'border-warning border-2' : '' }}">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-plus-circle-fill"></i></div>
                <div>
                    <div class="stat-value text-warning">{{ $stats['available'] }}</div>
                    <div class="stat-label text-muted">Available to Register</div>
                </div>
            </div>
        </div>
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="semester_id" value="{{ $semester?->id }}">
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search by course name or code…" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary">Search</button>
                @if(request()->hasAny(['search','department_id']))
                <a href="{{ route('academic.course-offerings.index', array_filter(['semester_id' => $semester?->id, 'filter' => request('filter')])) }}"
                   class="btn btn-sm btn-outline-secondary ms-1">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Offerings grid --}}
<div class="row g-3">
    @forelse($offerings as $offering)
    @php
        $reg      = $myRegistrations->get($offering->id);
        $enrolled = $offering->approved_registrations_count;
        $max      = $offering->max_students;
        $isFull   = $enrolled >= $max;
        $pct      = $max > 0 ? round(($enrolled / $max) * 100) : 0;
        $barColor = $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warning' : 'success');
        $regStatus = $reg?->status;
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 {{ $regStatus && $regStatus !== 'dropped' ? 'border-start border-success border-3' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold" style="font-size:0.8rem">
                        {{ $offering->course?->code }}
                    </span>
                    @if($regStatus && $regStatus !== 'dropped')
                        <span class="badge bg-{{ $regStatus === 'approved' ? 'success' : ($regStatus === 'registered' ? 'success' : 'secondary') }}">
                            <i class="bi bi-check-circle me-1"></i>{{ ucfirst($regStatus) }}
                        </span>
                    @elseif($isFull)
                        <span class="badge bg-danger">Full</span>
                    @else
                        <span class="badge bg-light text-muted">Available</span>
                    @endif
                </div>

                <h6 class="fw-semibold mb-1" style="font-size:0.9rem">{{ $offering->course?->name }}</h6>
                <div class="text-muted mb-2" style="font-size:0.78rem">
                    <i class="bi bi-person me-1"></i>{{ optional(optional($offering->lecturer)->user)->name ?? 'TBA' }}
                    @if($offering->course?->credit_hours)
                        &bull; {{ $offering->course->credit_hours }} credit hrs
                    @endif
                </div>
                @if($offering->venue)
                <div class="text-muted mb-2" style="font-size:0.78rem">
                    <i class="bi bi-geo-alt me-1"></i>{{ $offering->venue }}
                </div>
                @endif

                {{-- Enrollment bar --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.75rem">
                        <span class="text-muted">Enrollment</span>
                        <span class="text-muted">{{ $enrolled }}/{{ $max }}</span>
                    </div>
                    <div class="progress" style="height:5px;border-radius:4px">
                        <div class="progress-bar bg-{{ $barColor }}" style="width:{{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Action --}}
                @if($regStatus && $regStatus !== 'dropped')
                    <div class="d-flex gap-2">
                        <a href="{{ route('academic.course-offerings.show', $offering) }}"
                           class="btn btn-sm btn-outline-primary flex-grow-1">
                            <i class="bi bi-eye me-1"></i> View
                        </a>
                        @if($regStatus === 'registered')
                        <form method="POST" action="{{ route('academic.registrations.drop', $reg->id) }}"
                              onsubmit="return confirm('Drop this course?')" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Drop
                            </button>
                        </form>
                        @endif
                    </div>
                @elseif(!$isFull)
                    <form method="POST" action="{{ route('academic.registrations.register') }}">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student?->id }}">
                        <input type="hidden" name="course_offering_ids[]" value="{{ $offering->id }}">
                        <button class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-plus-circle me-1"></i> Register
                        </button>
                    </form>
                @else
                    <button class="btn btn-sm btn-secondary w-100" disabled>Class Full</button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-50"></i>
                No course offerings found for {{ $semester?->name ?? 'this semester/term' }}.
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($offerings->hasPages())
<div class="mt-3">{{ $offerings->withQueryString()->links() }}</div>
@endif


@else
{{-- ══════════════════════════════════════════════════════════════════════════
     ADMIN / STAFF VIEW (original)
══════════════════════════════════════════════════════════════════════════ --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Course Offerings</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Course Offerings</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <a href="{{ route('academic.course-offerings.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Offering</a>
    @endcan
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <select name="semester_id" class="form-select form-select-sm">
                    <option value="">All Semesters/Terms</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Course</th><th>Code</th><th>Semester/Term</th><th>Lecturer</th><th>Enrolled</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($offerings as $offering)
                    <tr>
                        <td>{{ optional($offering->course)->name }}</td>
                        <td><code>{{ optional($offering->course)->code }}</code></td>
                        <td>{{ optional($offering->semester)->name }}</td>
                        <td>{{ optional(optional($offering->lecturer)->user)->name ?? '—' }}</td>
                        <td>
                            <span class="badge bg-{{ $offering->enrolled_students >= $offering->max_students ? 'danger' : 'secondary' }}">
                                {{ $offering->approved_registrations_count }} / {{ $offering->max_students }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('academic.course-offerings.show', $offering) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                @can('manage-academic')
                                <a href="{{ route('academic.course-offerings.edit', $offering) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('academic.course-offerings.destroy', $offering) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this offering?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No course offerings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $offerings->links() }}
@endif

@endsection
