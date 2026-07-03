@extends('layouts.app')
@section('title', $isSelfView ? 'My Results' : 'Results — ' . $student->full_name)
@section('page-title', $isSelfView ? 'My Results' : 'Student Results')

@section('content')

{{-- ── HEADER ─────────────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">
            <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
            {{ $isSelfView ? 'My Academic Results' : $student->full_name . ' — Results' }}
        </h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @if(!$isSelfView)
            <li class="breadcrumb-item"><a href="{{ route('academic.results.index') }}">Results</a></li>
            @endif
            <li class="breadcrumb-item active">
                {{ $isSelfView ? 'My Results' : $student->full_name }}
            </li>
        </ol></nav>
        @if(!$isSelfView)
        <small class="text-muted">{{ $student->student_id }} &bull; {{ $student->program?->name }}</small>
        @endif
    </div>
    @if(!$isSelfView)
    <div class="d-flex gap-2">
        <a href="{{ route('academic.results.gpa', $student) }}" class="btn btn-outline-info btn-sm">
            <i class="bi bi-graph-up me-1"></i> GPA Report
        </a>
        <a href="{{ route('academic.results.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
    @endif
</div>

{{-- ── STAT CARDS ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-journal-text"></i></div>
                <div>
                    <div class="stat-value text-primary">{{ $stats['total'] }}</div>
                    <div class="stat-label text-muted">Total Courses</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-value text-success">{{ $stats['passed'] }}</div>
                    <div class="stat-label text-muted">Passed</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle-fill"></i></div>
                <div>
                    <div class="stat-value text-danger">{{ $stats['failed'] }}</div>
                    <div class="stat-label text-muted">
                        Failed
                        @if($stats['pending'] > 0)
                            <span class="text-secondary fw-normal">/ {{ $stats['pending'] }} pending</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                @php
                    $cgpa      = (float) ($stats['cgpa'] ?? 0);
                    $cgpaColor = $cgpa >= 3.5 ? 'success' : ($cgpa >= 2.0 ? 'primary' : ($cgpa >= 1.0 ? 'warning' : 'danger'));
                    $standing  = $stats['standing'] ?? null;
                    $standingColors = ["Dean's List" => 'success', 'Good Standing' => 'primary', 'Probation' => 'warning', 'Academic Dismissal' => 'danger'];
                @endphp
                <div class="stat-icon bg-{{ $cgpaColor }} bg-opacity-10 text-{{ $cgpaColor }}"><i class="bi bi-award-fill"></i></div>
                <div>
                    <div class="stat-value text-{{ $cgpaColor }}">
                        {{ $stats['cgpa'] !== null ? number_format($cgpa, 2) : '—' }}
                    </div>
                    <div class="stat-label text-muted">CGPA</div>
                    @if($standing)
                    <span class="badge bg-{{ $standingColors[$standing] ?? 'secondary' }}" style="font-size:0.65rem">
                        {{ $standing }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── RESULTS BY INTAKE ─────────────────────────────────────────────────── --}}
@if($resultsBySemester->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-bar-chart fs-2 d-block mb-2 opacity-50"></i>
        No results available yet.
        @if($isSelfView)
        <div class="mt-2 small">Results will appear here once your lecturers enter and approve them.</div>
        @endif
    </div>
</div>
@else
@foreach($resultsBySemester as $semesterId => $semResults)
@php
    $semName   = optional(optional($semResults->first())->courseOffering?->semester)->name ?? 'Unknown Semester/Term';
    $ayName    = optional(optional($semResults->first())->courseOffering?->semester?->academicYear)->name ?? '';
    $gpaRecord = $gpaRecords->get($semesterId);
    $semPassed = $semResults->where('status', 'pass')->count();
    $semFailed = $semResults->where('status', 'fail')->count();
    $semPending = $semResults->where('status', 'pending')->count();
@endphp
<div class="card border-0 shadow-sm mb-4">
    {{-- Semester/Term header --}}
    <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3 flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-0">{{ $semName }}</h6>
            @if($ayName)<small class="text-muted">{{ $ayName }}</small>@endif
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($semPassed > 0)<span class="badge bg-success">{{ $semPassed }} passed</span>@endif
            @if($semFailed > 0)<span class="badge bg-danger">{{ $semFailed }} failed</span>@endif
            @if($semPending > 0)<span class="badge bg-secondary">{{ $semPending }} pending</span>@endif
            @if($gpaRecord)
            <span class="badge bg-primary px-3 py-2" style="font-size:0.8rem">
                GPA: {{ number_format($gpaRecord->gpa, 2) }}
            </span>
            @php
                $cgpaVal   = (float) $gpaRecord->cgpa;
                $cgpaClr   = $cgpaVal >= 3.5 ? 'success' : ($cgpaVal >= 2.0 ? 'info' : ($cgpaVal >= 1.0 ? 'warning' : 'danger'));
            @endphp
            <span class="badge bg-{{ $cgpaClr }} px-3 py-2" style="font-size:0.8rem">
                CGPA: {{ number_format($cgpaVal, 2) }}
            </span>
            @if($gpaRecord->academic_standing)
            <span class="badge bg-{{ $standingColors[$gpaRecord->academic_standing] ?? 'secondary' }}">
                {{ $gpaRecord->academic_standing }}
            </span>
            @endif
            @endif
        </div>
    </div>

    {{-- Results table --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Course</th>
                        <th>Code</th>
                        <th class="text-center">Credits</th>
                        <th class="text-center">CA <small class="text-muted fw-normal">/40</small></th>
                        <th class="text-center">Exam <small class="text-muted fw-normal">/60</small></th>
                        <th class="text-center">Total <small class="text-muted fw-normal">/100</small></th>
                        <th class="text-center">Grade</th>
                        <th class="text-center">Points</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($semResults as $result)
                    @php
                        $gradeColors = ['A+'=>'success','A'=>'success','A-'=>'success','B+'=>'primary','B'=>'primary','B-'=>'primary','C+'=>'warning','C'=>'warning','C-'=>'warning','D'=>'danger','F'=>'danger'];
                        $rowOpacity  = $result->status === 'pending' ? 'opacity-75' : '';
                    @endphp
                    <tr class="{{ $rowOpacity }}">
                        <td class="ps-3" style="font-size:0.875rem">
                            {{ optional(optional($result->courseOffering)->course)->name ?? '—' }}
                        </td>
                        <td><code>{{ optional(optional($result->courseOffering)->course)->code ?? '—' }}</code></td>
                        <td class="text-center">
                            <small>{{ optional(optional($result->courseOffering)->course)->credit_hours ?? '—' }}</small>
                        </td>
                        <td class="text-center">{{ $result->ca_score ?? '—' }}</td>
                        <td class="text-center">{{ $result->exam_score ?? '—' }}</td>
                        <td class="text-center fw-bold">{{ $result->total_score ?? '—' }}</td>
                        <td class="text-center">
                            @if($result->grade)
                            <span class="badge bg-{{ $gradeColors[$result->grade] ?? 'secondary' }} px-2">
                                {{ $result->grade }}
                            </span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <small class="fw-semibold">{{ $result->grade_points ?? '—' }}</small>
                        </td>
                        <td class="text-center">
                            @if($result->status === 'pass')
                                <span class="badge bg-success">Pass</span>
                            @elseif($result->status === 'fail')
                                <span class="badge bg-danger">Fail</span>
                            @else
                                <span class="badge bg-secondary">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @if($gpaRecord && $semResults->whereNotIn('status', ['pending'])->count() > 0)
                <tfoot class="table-light">
                    <tr>
                        <td colspan="2" class="ps-3 fw-semibold text-muted small">Semester/Term Summary</td>
                        <td class="text-center fw-semibold small">
                            {{ $gpaRecord->credits_earned ?? '—' }} cr
                        </td>
                        <td colspan="4"></td>
                        <td colspan="2" class="text-center">
                            <span class="fw-bold text-primary">GPA {{ number_format($gpaRecord->gpa, 2) }}</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endforeach
@endif

@endsection
