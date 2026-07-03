@extends('layouts.app')
@section('title', 'Grade Appeals')
@section('page-title', 'Grade Appeals')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-flag me-2" style="color:var(--secondary)"></i>Grade Appeals</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Grade Appeals</li>
        </ol></nav>
    </div>
    @role('student')
    <a href="{{ route('academic.grade-appeals.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Submit Appeal
    </a>
    @endrole
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Stats (admin only) --}}
@unlessrole('student')
<div class="row g-3 mb-4">
    @foreach(['pending' => ['warning','clock','Pending'], 'under_review' => ['info','search','Under Review'], 'approved' => ['success','check-circle','Approved'], 'rejected' => ['danger','x-circle','Rejected']] as $key => [$color, $icon, $label])
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="text-{{ $color }} fs-2 fw-bold">{{ $stats[$key] }}</div>
            <div class="text-muted" style="font-size:0.82rem"><i class="bi bi-{{ $icon }} me-1"></i>{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>
@endunlessrole

<div class="card">
    <div class="card-header py-3 d-flex align-items-center gap-3">
        <h5 class="mb-0 fw-semibold flex-1">Appeals</h5>
        <form class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:160px">
                <option value="">All Statuses</option>
                @foreach(['pending','under_review','approved','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    @unlessrole('student')<th>Student</th>@endunlessrole
                    <th>Course</th>
                    <th>Semester/Term</th>
                    <th>Original Grade</th>
                    <th>Revised Grade</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($appeals as $appeal)
                <tr>
                    <td class="text-muted" style="font-size:0.82rem">{{ $appeal->id }}</td>
                    @unlessrole('student')
                    <td>
                        <div class="fw-semibold" style="font-size:0.88rem">{{ $appeal->student->full_name }}</div>
                        <div class="text-muted" style="font-size:0.78rem">{{ $appeal->student->student_id }}</div>
                    </td>
                    @endunlessrole
                    <td style="font-size:0.88rem">{{ $appeal->courseOffering->course->name }}</td>
                    <td style="font-size:0.82rem">{{ $appeal->courseOffering->semester->name ?? '—' }}</td>
                    <td><span class="fw-semibold">{{ $appeal->original_grade ?? '—' }}</span> <span class="text-muted">({{ $appeal->original_total ?? '—' }}%)</span></td>
                    <td>
                        @if($appeal->revised_grade)
                            <span class="fw-semibold text-success">{{ $appeal->revised_grade }}</span> <span class="text-muted">({{ $appeal->revised_total }}%)</span>
                        @else —
                        @endif
                    </td>
                    <td>{!! $appeal->status_badge !!}</td>
                    <td class="text-muted" style="font-size:0.78rem">{{ $appeal->created_at->format('d M Y') }}</td>
                    <td><a href="{{ route('academic.grade-appeals.show', $appeal) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No grade appeals found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($appeals->hasPages())
    <div class="card-footer py-2">{{ $appeals->links() }}</div>
    @endif
</div>
@endsection
