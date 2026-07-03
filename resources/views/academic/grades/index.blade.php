@extends('layouts.app')
@section('title', 'Grades Management')
@section('page-title', 'Grades Management')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-star-half me-2 text-warning"></i>Grades Management</h4>
        <p class="text-muted small mb-0">Enter and manage examination marks for all examinations.</p>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:42px;height:42px">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total Exams</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width:42px;height:42px">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5">{{ $stats['graded'] }}</div>
                        <div class="text-muted small">Graded</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width:42px;height:42px">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5">{{ $stats['pending'] }}</div>
                        <div class="text-muted small">Pending</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width:42px;height:42px">
                        <i class="bi bi-calendar-event fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5">{{ $stats['this_month'] }}</div>
                        <div class="text-muted small">This Month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('academic.grades.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-4 col-md-3">
                <label class="form-label small fw-semibold mb-1">Semester/Term</label>
                <select name="semester_id" class="form-select form-select-sm">
                    <option value="">All Semesters/Terms</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" @selected(request('semester_id') == $sem->id)>{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <label class="form-label small fw-semibold mb-1">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="midterm" @selected(request('type') == 'midterm')>Midterm</option>
                    <option value="final" @selected(request('type') == 'final')>Final</option>
                    <option value="quiz" @selected(request('type') == 'quiz')>Quiz</option>
                    <option value="assignment" @selected(request('type') == 'assignment')>Assignment</option>
                    <option value="practical" @selected(request('type') == 'practical')>Practical</option>
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <label class="form-label small fw-semibold mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="scheduled" @selected(request('status') == 'scheduled')>Scheduled</option>
                    <option value="ongoing" @selected(request('status') == 'ongoing')>Ongoing</option>
                    <option value="completed" @selected(request('status') == 'completed')>Completed</option>
                    <option value="cancelled" @selected(request('status') == 'cancelled')>Cancelled</option>
                </select>
            </div>
            <div class="col-sm-12 col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('academic.grades.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Examinations Table --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Examination</th>
                        <th>Course</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Max Marks</th>
                        <th class="text-center">Enrolled</th>
                        <th class="text-center">Graded</th>
                        <th class="text-center">Avg Marks</th>
                        <th class="text-center">Pass Rate</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($examinations as $exam)
                    @php
                        $graded    = $exam->results_count;
                        $enrolled  = $exam->enrolled_count ?? 0;
                        $pct       = $enrolled > 0 ? round($graded / $enrolled * 100) : 0;
                        $passRate  = ($graded > 0 && $exam->enrolled_count > 0) ? round($exam->pass_count / $graded * 100) : 0;
                        $statusColors = [
                            'scheduled' => 'info',
                            'ongoing'   => 'primary',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                        ];
                        $typeColors = [
                            'midterm'    => 'primary',
                            'final'      => 'danger',
                            'quiz'       => 'warning',
                            'assignment' => 'info',
                            'practical'  => 'success',
                        ];
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">{{ $exam->name }}</div>
                            <span class="badge bg-{{ $typeColors[$exam->type] ?? 'secondary' }} bg-opacity-10 text-{{ $typeColors[$exam->type] ?? 'secondary' }} border border-{{ $typeColors[$exam->type] ?? 'secondary' }}" style="font-size:.7rem">
                                {{ ucfirst($exam->type) }}
                            </span>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ optional(optional($exam->courseOffering)->course)->code }}</div>
                            <div class="text-muted" style="font-size:.78rem">{{ optional(optional($exam->courseOffering)->semester)->name }}</div>
                        </td>
                        <td class="text-center">
                            <div class="small fw-semibold">{{ $exam->exam_date?->format('d M Y') ?? '—' }}</div>
                            @if($exam->start_time)
                                <div class="text-muted" style="font-size:.75rem">{{ substr($exam->start_time,0,5) }}–{{ substr($exam->end_time,0,5) }}</div>
                            @endif
                        </td>
                        <td class="text-center fw-semibold">{{ number_format($exam->max_marks, 0) }}</td>
                        <td class="text-center">{{ $enrolled }}</td>
                        <td class="text-center">
                            <div>{{ $graded }}/{{ $enrolled }}</div>
                            <div class="progress mt-1" style="height:4px;width:60px;margin:0 auto">
                                <div class="progress-bar {{ $pct == 100 ? 'bg-success' : 'bg-primary' }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($exam->avg_marks !== null)
                                <span class="fw-semibold">{{ number_format($exam->avg_marks, 1) }}</span>
                                <span class="text-muted" style="font-size:.75rem">/{{ number_format($exam->max_marks, 0) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($graded > 0)
                                <span class="fw-semibold {{ $passRate >= 60 ? 'text-success' : 'text-danger' }}">{{ $passRate }}%</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $statusColors[$exam->status] ?? 'secondary' }}">
                                {{ ucfirst($exam->status ?? 'scheduled') }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('academic.grades.entry', $exam) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil-square me-1"></i>
                                {{ $graded > 0 ? 'Edit Marks' : 'Enter Marks' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="bi bi-file-earmark-x fs-3 d-block mb-2 opacity-25"></i>
                            No examinations found{{ request()->hasAny(['semester_id','type','status']) ? ' for the selected filters.' : '.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($examinations->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">Showing {{ $examinations->firstItem() }}–{{ $examinations->lastItem() }} of {{ $examinations->total() }} examinations</small>
        {{ $examinations->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
