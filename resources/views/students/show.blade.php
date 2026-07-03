@extends('layouts.app')
@section('title', $student->full_name)
@section('page-title', 'Student Profile')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1>Student Profile</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
            <li class="breadcrumb-item active">{{ $student->full_name }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('students.card', $student) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-person-vcard me-1"></i>ID Card</a>
        <a href="{{ route('students.transcript', $student) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i>Transcript</a>

        {{-- Status action dropdown --}}
        @php $st = $student->status; @endphp
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-gear me-1"></i>Status
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @if($st !== 'active')
                <li>
                    <form method="POST" action="{{ route('students.status', $student) }}"
                          onsubmit="return confirm('Reinstate {{ addslashes($student->full_name) }} as active?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="active">
                        <button class="dropdown-item text-success"><i class="bi bi-person-check me-2"></i>Reinstate</button>
                    </form>
                </li>
                @endif
                @if($st === 'active')
                <li>
                    <form method="POST" action="{{ route('students.status', $student) }}"
                          onsubmit="return confirm('Suspend {{ addslashes($student->full_name) }}?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="suspended">
                        <button class="dropdown-item text-warning"><i class="bi bi-slash-circle me-2"></i>Suspend</button>
                    </form>
                </li>
                <li>
                    <form method="POST" action="{{ route('students.status', $student) }}"
                          onsubmit="return confirm('Defer enrollment for {{ addslashes($student->full_name) }}?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="deferred">
                        <button class="dropdown-item text-info"><i class="bi bi-pause-circle me-2"></i>Defer Enrollment</button>
                    </form>
                </li>
                <li>
                    <form method="POST" action="{{ route('students.status', $student) }}"
                          onsubmit="return confirm('Mark {{ addslashes($student->full_name) }} as graduated?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="graduated">
                        <button class="dropdown-item text-primary"><i class="bi bi-mortarboard me-2"></i>Graduate</button>
                    </form>
                </li>
                <li>
                    <form method="POST" action="{{ route('students.status', $student) }}"
                          onsubmit="return confirm('Mark {{ addslashes($student->full_name) }} as inactive?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="inactive">
                        <button class="dropdown-item text-secondary"><i class="bi bi-person-dash me-2"></i>Mark Inactive</button>
                    </form>
                </li>
                @endif
                @if(in_array($st, ['suspended']) )
                <li>
                    <form method="POST" action="{{ route('students.status', $student) }}"
                          onsubmit="return confirm('Defer enrollment for {{ addslashes($student->full_name) }}?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="deferred">
                        <button class="dropdown-item text-info"><i class="bi bi-pause-circle me-2"></i>Defer Enrollment</button>
                    </form>
                </li>
                @endif
                @if($st !== 'dropped_out')
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('students.status', $student) }}"
                          onsubmit="return confirm('Mark {{ addslashes($student->full_name) }} as Dropped Out?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="dropped_out">
                        <button class="dropdown-item text-danger"><i class="bi bi-person-x me-2"></i>Drop Out</button>
                    </form>
                </li>
                @endif
                @if($st === 'dropped_out' || $st === 'graduated')
                <li><span class="dropdown-item text-muted disabled" style="font-size:0.82rem">
                    <i class="bi bi-{{ $st === 'graduated' ? 'mortarboard' : 'person-x' }} me-2"></i>
                    {{ $st === 'dropped_out' ? 'Dropped Out' : ucfirst($st) }}
                </span></li>
                @endif
            </ul>
        </div>

        <a href="{{ route('students.edit', $student) }}" class="btn btn-primary btn-sm text-white"><i class="bi bi-pencil me-1"></i>Edit</a>
        @hasrole('super-admin')
        <form method="POST" action="{{ route('students.destroy', $student) }}"
              onsubmit="return confirm('Permanently delete {{ addslashes($student->full_name) }}? All records will be erased and this cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
        @endhasrole
    </div>
</div>

<div class="row g-3">
    <!-- Profile Card -->
    <div class="col-lg-3">
        <div class="card text-center p-4">
            <img src="{{ $student->photo_url }}" class="rounded-circle mx-auto mb-3" style="width:100px;height:100px;object-fit:cover;border:4px solid #dee2e6" alt="">
            <h5 class="fw-bold mb-1">{{ $student->full_name }}</h5>
            <div class="text-muted mb-2" style="font-size:0.85rem">{{ $student->student_id }}</div>
            {!! statusBadge($student->status) !!}
            <hr>
            <div class="text-start" style="font-size:0.85rem">
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Program</span><strong class="text-end" style="max-width:60%">{{ $student->program?->name }}</strong></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Faculty</span><strong>{{ $student->program?->department?->faculty?->code }}</strong></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Level</span><strong>Level {{ $student->year_of_study }}</strong></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Admitted</span><strong>{{ $student->enrollment_date?->format('M Y') }}</strong></div>
                <div class="d-flex justify-content-between"><span class="text-muted">GPA</span><strong class="text-success">{{ number_format($student->latest_cgpa, 2) }}</strong></div>
            </div>
        </div>

        <!-- Bill Summary -->
        @if($student->bills->isNotEmpty())
        <div class="card mt-3 p-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-receipt me-2 text-success"></i>Fee Status</h6>
            @php $latestBill = $student->bills->first(); @endphp
            <div class="d-flex justify-content-between mb-1" style="font-size:0.85rem"><span class="text-muted">Total</span><strong>{{ formatCurrency($latestBill->total_amount) }}</strong></div>
            <div class="d-flex justify-content-between mb-1" style="font-size:0.85rem"><span class="text-muted">Paid</span><strong class="text-success">{{ formatCurrency($latestBill->amount_paid) }}</strong></div>
            <div class="d-flex justify-content-between" style="font-size:0.85rem"><span class="text-muted">Balance</span><strong class="text-danger">{{ formatCurrency($latestBill->balance) }}</strong></div>
            <div class="progress mt-2" style="height:6px">
                @php $pct = $latestBill->total_amount > 0 ? min(100, ($latestBill->amount_paid / $latestBill->total_amount) * 100) : 0; @endphp
                <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
            </div>
        </div>
        @endif
    </div>

    <!-- Details -->
    <div class="col-lg-9">
        <ul class="nav nav-tabs border-bottom-0 mb-3" id="profileTab">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal">Personal</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#courses">Courses</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#results">Results</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#guardian">Guardian</button></li>
            @hasanyrole('admin|registrar|finance')
            <li class="nav-item">
                <button class="nav-link {{ $student->activeHolds->isNotEmpty() ? 'text-danger' : '' }}" data-bs-toggle="tab" data-bs-target="#holds">
                    Holds
                    @if($student->activeHolds->isNotEmpty())
                    <span class="badge bg-danger ms-1">{{ $student->activeHolds->count() }}</span>
                    @endif
                </button>
            </li>
            @endhasanyrole
        </ul>

        <div class="tab-content">
            <!-- Personal Info -->
            <div class="tab-pane fade show active" id="personal">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach([
                                ['Email', $student->user?->email],
                                ['Phone', $student->phone],
                                ['Gender', ucfirst($student->gender ?? '')],
                                ['Date of Birth', $student->date_of_birth?->format('d M Y')],
                                ['Age', $student->age ? $student->age . ' years' : null],
                                ['Nationality', $student->nationality],
                                ['National ID', $student->national_id],
                                ['Address', $student->address],
                                ['Sponsor / Funding', $student->sponsor],
                                ['Admission Type', ucfirst(str_replace('-', ' ', $student->admission_type ?? ''))],
                                ['Year of Study', $student->year_of_study ? 'Year ' . $student->year_of_study : null],
                                ['Emergency Contact', $student->guardians->where('is_emergency_contact', true)->first()?->name]
                            ] as [$label, $value])
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="text-muted" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em">{{ $label }}</div>
                                    <div class="fw-semibold">{{ $value ?? '&mdash;' }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses -->
            <div class="tab-pane fade" id="courses">
                <div class="card">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr><th>Course</th><th>Code</th><th>Credits</th><th>Lecturer</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse($student->courseRegistrations as $reg)
                                <tr>
                                    <td>{{ $reg->courseOffering?->course?->name }}</td>
                                    <td><code>{{ $reg->courseOffering?->course?->code }}</code></td>
                                    <td>{{ $reg->courseOffering?->course?->credits }}</td>
                                    <td>{{ $reg->courseOffering?->lecturer?->full_name ?? '&mdash;' }}</td>
                                    <td>{!! statusBadge($reg->status) !!}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No courses registered</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="tab-pane fade" id="results">
            @php
                $gradeColor = fn($g) => match(true) {
                    in_array($g, ['A+','A','A-'])       => 'success',
                    in_array($g, ['B+','B','B-'])       => 'primary',
                    in_array($g, ['C+','C','C-'])       => 'info',
                    in_array($g, ['D+','D','D-'])       => 'warning',
                    default                              => 'danger',
                };
                $latestGpa   = $student->gpaRecords->sortByDesc('created_at')->first();
                $totalEarned = $student->gpaRecords->sum('credits_earned');
                $totalAttempt= $student->gpaRecords->sum('total_credits_earned');
                $passed      = $student->finalResults->where('grade','!=','F')->count();
                $total       = $student->finalResults->count();
                $passRate    = $total > 0 ? round($passed / $total * 100) : 0;
            @endphp

            {{-- Academic summary strip --}}
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <div class="card border-0 bg-primary bg-opacity-10 text-center p-2">
                        <div class="fw-bold fs-4 text-primary">{{ number_format($latestGpa?->cgpa ?? 0, 2) }}</div>
                        <div class="text-muted" style="font-size:0.72rem;text-transform:uppercase">CGPA</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 bg-success bg-opacity-10 text-center p-2">
                        <div class="fw-bold fs-4 text-success">{{ $totalEarned }}</div>
                        <div class="text-muted" style="font-size:0.72rem;text-transform:uppercase">Credits Earned</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 bg-info bg-opacity-10 text-center p-2">
                        <div class="fw-bold fs-4 text-info">{{ $passRate }}%</div>
                        <div class="text-muted" style="font-size:0.72rem;text-transform:uppercase">Pass Rate</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 text-center p-2"
                         style="background:{{ ($latestGpa?->cgpa ?? 0) >= 3.5 ? '#fff8e1' : (($latestGpa?->cgpa ?? 0) < 2.0 ? '#ffeaea' : '#f0faf0') }}">
                        <div class="fw-bold" style="font-size:0.85rem;color:{{ ($latestGpa?->cgpa ?? 0) >= 3.5 ? '#b45309' : (($latestGpa?->cgpa ?? 0) < 2.0 ? '#dc2626' : '#15803d') }}">
                            {{ $latestGpa?->academic_standing ?? 'N/A' }}
                        </div>
                        <div class="text-muted" style="font-size:0.72rem;text-transform:uppercase">Standing</div>
                    </div>
                </div>
            </div>

            {{-- Toolbar --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted" style="font-size:0.85rem">{{ $total }} result(s) across {{ $resultsBySemester->count() }} semester(s)/term(s)</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addResultModal">
                    <i class="bi bi-plus-lg me-1"></i>Add Result
                </button>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible py-2 mb-3">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Results by semester/term --}}
            @if($resultsBySemester->isEmpty())
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-25"></i>
                        No results recorded yet.
                    </div>
                </div>
            @else
            <div class="accordion" id="resultsAccordion">
            @foreach($resultsBySemester as $semesterId => $semResults)
            @php
                $semester   = $semResults->first()->courseOffering->semester;
                $semGpa     = $gpaMap->get($semesterId);
                $semCredits = $semResults->sum(fn($r) => $r->courseOffering->course->credits ?? 0);
                $loopIdx    = $loop->index;
            @endphp
            <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $loopIdx > 0 ? 'collapsed' : '' }} py-2"
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#sem{{ $semesterId }}">
                        <div class="d-flex align-items-center gap-3 w-100 me-3">
                            <div>
                                <span class="fw-semibold">{{ $semester?->name ?? 'Semester/Term '.$semesterId }}</span>
                                <span class="text-muted ms-2" style="font-size:0.8rem">
                                    {{ $semester?->academicYear?->name }}
                                </span>
                            </div>
                            <div class="ms-auto d-flex gap-2">
                                @if($semGpa)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fw-normal">
                                    GPA {{ number_format($semGpa->gpa, 2) }}
                                </span>
                                @endif
                                <span class="badge bg-light text-dark border fw-normal">
                                    {{ $semResults->count() }} courses
                                </span>
                                <span class="badge bg-light text-dark border fw-normal">
                                    {{ $semCredits }} cr
                                </span>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="sem{{ $semesterId }}" class="accordion-collapse collapse {{ $loopIdx === 0 ? 'show' : '' }}">
                    <div class="accordion-body p-0">
                        <table class="table table-hover mb-0 align-middle" style="font-size:0.86rem">
                            <thead class="table-light">
                                <tr>
                                    <th>Course</th>
                                    <th class="text-center" style="width:50px">Cr</th>
                                    <th class="text-center" style="width:70px">CA</th>
                                    <th class="text-center" style="width:70px">Exam</th>
                                    <th class="text-center" style="width:70px">Total</th>
                                    <th class="text-center" style="width:60px">Grade</th>
                                    <th class="text-center" style="width:60px">GP</th>
                                    <th class="text-center" style="width:90px">Status</th>
                                    <th class="text-end" style="width:100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($semResults as $result)
                            <tr>
                                <td>
                                    <div class="fw-semibold" style="line-height:1.2">
                                        {{ $result->courseOffering?->course?->name }}
                                    </div>
                                    <code class="text-muted" style="font-size:0.75rem">
                                        {{ $result->courseOffering?->course?->code }}
                                    </code>
                                    @if($result->remarks)
                                        <div class="text-muted" style="font-size:0.75rem">
                                            <i class="bi bi-chat-left-text me-1"></i>{{ $result->remarks }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center text-muted">
                                    {{ $result->courseOffering?->course?->credits ?? '—' }}
                                </td>
                                <td class="text-center">{{ number_format($result->ca_score, 1) }}</td>
                                <td class="text-center">{{ number_format($result->exam_score, 1) }}</td>
                                <td class="text-center fw-bold">{{ number_format($result->total_score, 1) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $gradeColor($result->grade) }}">
                                        {{ $result->grade }}
                                    </span>
                                </td>
                                <td class="text-center text-muted">{{ number_format($result->grade_points, 1) }}</td>
                                <td class="text-center">{!! statusBadge($result->status) !!}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button class="btn btn-sm btn-outline-secondary py-0 px-2"
                                                title="Edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editResultModal"
                                                data-id="{{ $result->id }}"
                                                data-ca="{{ $result->ca_score }}"
                                                data-exam="{{ $result->exam_score }}"
                                                data-remarks="{{ $result->remarks }}"
                                                data-url="{{ route('students.results.update', [$student, $result]) }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if($result->status === 'pending')
                                        <form method="POST"
                                              action="{{ route('students.results.approve', [$student, $result]) }}"
                                              onsubmit="return confirm('Approve this result?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success py-0 px-2" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                            @if($semGpa)
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end text-muted fw-semibold" style="font-size:0.8rem">
                                        Semester/Term Summary
                                    </td>
                                    <td class="text-center fw-bold">—</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($semGpa->gpa, 2) }}</span>
                                    </td>
                                    <td class="text-center text-muted" style="font-size:0.8rem">GPA</td>
                                    <td class="text-center text-muted" style="font-size:0.8rem">
                                        {{ $semGpa->credits_earned }} cr earned
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
            @endif

            {{-- GPA progression --}}
            @if($student->gpaRecords->isNotEmpty())
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-graph-up me-2 text-primary"></i>GPA Progression</h6>
                    <div class="row g-2">
                        @foreach($student->gpaRecords->sortBy('created_at') as $gpaRec)
                        @php $cgpaColor = $gpaRec->cgpa >= 3.5 ? 'success' : ($gpaRec->cgpa >= 2.0 ? 'primary' : 'danger'); @endphp
                        <div class="col-6 col-md-3">
                            <div class="border rounded-3 p-2 text-center">
                                <div class="text-muted" style="font-size:0.72rem">
                                    {{ $gpaRec->semester?->name }}
                                    <span class="d-block">{{ $gpaRec->semester?->academicYear?->name }}</span>
                                </div>
                                <div class="fw-bold fs-5 text-{{ $cgpaColor }} mt-1">{{ number_format($gpaRec->gpa, 2) }}</div>
                                <div class="text-muted" style="font-size:0.7rem">CGPA {{ number_format($gpaRec->cgpa, 2) }}</div>
                                <div class="text-muted" style="font-size:0.68rem">{{ $gpaRec->credits_earned }} cr earned</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ═══ Add Result Modal ═══ --}}
            <div class="modal fade" id="addResultModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add / Update Result</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('students.results.store', $student) }}">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Course Offering <span class="text-danger">*</span></label>
                                    <select name="course_offering_id" class="form-select" required>
                                        <option value="">— Select Course —</option>
                                        @foreach($registeredOfferings as $offering)
                                        <option value="{{ $offering->id }}">
                                            {{ $offering->course?->name }}
                                            ({{ $offering->course?->code }})
                                            — {{ $offering->semester?->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">CA Score <span class="text-danger">*</span></label>
                                        <input type="number" name="ca_score" class="form-control"
                                               step="0.5" min="0" max="40" required placeholder="0 – 40">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Exam Score <span class="text-danger">*</span></label>
                                        <input type="number" name="exam_score" class="form-control"
                                               step="0.5" min="0" max="60" required placeholder="0 – 60">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label fw-semibold">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes…"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Save Result
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ═══ Edit Result Modal ═══ --}}
            <div class="modal fade" id="editResultModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Result</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" id="editResultForm">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">CA Score <span class="text-danger">*</span></label>
                                        <input type="number" name="ca_score" id="editCa" class="form-control"
                                               step="0.5" min="0" max="40" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Exam Score <span class="text-danger">*</span></label>
                                        <input type="number" name="exam_score" id="editExam" class="form-control"
                                               step="0.5" min="0" max="60" required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label fw-semibold">Remarks</label>
                                    <textarea name="remarks" id="editRemarks" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Update Result
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            </div>{{-- /results tab-pane --}}

            <!-- Guardian -->
            <div class="tab-pane fade" id="guardian">

                {{-- Toolbar --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted" style="font-size:0.85rem">
                        {{ $student->guardians->count() }} guardian(s) on record
                    </span>
                    <button class="btn btn-primary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#addGuardianModal">
                        <i class="bi bi-plus-lg me-1"></i>Add Guardian
                    </button>
                </div>

                @if($student->guardians->isEmpty())
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                        No guardian information recorded yet.
                    </div>
                </div>
                @else
                <div class="row g-3">
                @foreach($student->guardians as $guardian)
                <div class="col-md-6">
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body">

                            {{-- Header row --}}
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:40px;height:40px">
                                        <i class="bi bi-person-heart text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $guardian->name }}</div>
                                        <div class="d-flex gap-1 mt-1 flex-wrap">
                                            <span class="badge bg-light text-dark border" style="font-size:0.72rem">
                                                {{ ucfirst($guardian->relationship) }}
                                            </span>
                                            @if($guardian->is_emergency_contact)
                                            <span class="badge bg-danger" style="font-size:0.72rem">
                                                <i class="bi bi-exclamation-circle me-1"></i>Emergency Contact
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Action buttons --}}
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-secondary py-0 px-2"
                                            title="Edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editGuardianModal"
                                            data-id="{{ $guardian->id }}"
                                            data-name="{{ $guardian->name }}"
                                            data-relationship="{{ $guardian->relationship }}"
                                            data-phone="{{ $guardian->phone }}"
                                            data-email="{{ $guardian->email }}"
                                            data-address="{{ $guardian->address }}"
                                            data-emergency="{{ $guardian->is_emergency_contact ? '1' : '0' }}"
                                            data-url="{{ route('students.guardians.update', [$student, $guardian]) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST"
                                          action="{{ route('students.guardians.destroy', [$student, $guardian]) }}"
                                          onsubmit="return confirm('Remove this guardian?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <hr class="my-2">

                            {{-- Contact details --}}
                            <div class="row g-1" style="font-size:0.83rem">
                                <div class="col-12">
                                    <i class="bi bi-telephone text-muted me-1"></i>
                                    <span>{{ $guardian->phone }}</span>
                                </div>
                                @if($guardian->email)
                                <div class="col-12">
                                    <i class="bi bi-envelope text-muted me-1"></i>
                                    <a href="mailto:{{ $guardian->email }}" class="text-decoration-none">
                                        {{ $guardian->email }}
                                    </a>
                                </div>
                                @endif
                                @if($guardian->address)
                                <div class="col-12 text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $guardian->address }}
                                </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
                @endforeach
                </div>
                @endif

                {{-- ═══ Add Guardian Modal ═══ --}}
                <div class="modal fade" id="addGuardianModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add Guardian</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('students.guardians.store', $student) }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" required
                                                   placeholder="Guardian's full name">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-semibold">Relationship <span class="text-danger">*</span></label>
                                            <select name="relationship" class="form-select" required>
                                                <option value="">— Select —</option>
                                                @foreach(['mother','father','guardian','uncle','aunt','grandparent','sibling','spouse','other'] as $rel)
                                                <option value="{{ $rel }}">{{ ucfirst($rel) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                                            <input type="text" name="phone" class="form-control" required
                                                   placeholder="+260 XXX XXXXXX">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Email</label>
                                            <input type="email" name="email" class="form-control"
                                                   placeholder="Optional">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Address</label>
                                            <textarea name="address" class="form-control" rows="2"
                                                      placeholder="Optional"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="is_emergency_contact" value="1" id="addEmergency">
                                                <label class="form-check-label" for="addEmergency">
                                                    Mark as emergency contact
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Save Guardian
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ═══ Edit Guardian Modal ═══ --}}
                <div class="modal fade" id="editGuardianModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Guardian</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" id="editGuardianForm">
                                @csrf @method('PUT')
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="editGuardianName"
                                                   class="form-control" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-semibold">Relationship <span class="text-danger">*</span></label>
                                            <select name="relationship" id="editGuardianRel" class="form-select" required>
                                                @foreach(['mother','father','guardian','uncle','aunt','grandparent','sibling','spouse','other'] as $rel)
                                                <option value="{{ $rel }}">{{ ucfirst($rel) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                                            <input type="text" name="phone" id="editGuardianPhone"
                                                   class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Email</label>
                                            <input type="email" name="email" id="editGuardianEmail"
                                                   class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Address</label>
                                            <textarea name="address" id="editGuardianAddress"
                                                      class="form-control" rows="2"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="is_emergency_contact" value="1"
                                                       id="editEmergency">
                                                <label class="form-check-label" for="editEmergency">
                                                    Mark as emergency contact
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Update Guardian
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>{{-- /guardian tab-pane --}}

            @hasanyrole('admin|registrar|finance')
            <div class="tab-pane fade" id="holds">
                <div class="row g-3">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header py-3"><h6 class="mb-0 fw-semibold">Place New Hold</h6></div>
                            <div class="card-body">
                                <form action="{{ route('academic.student-holds.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Hold Type</label>
                                        <select name="type" class="form-select" required>
                                            @foreach(['financial','academic','disciplinary','library','hostel','administrative'] as $t)
                                            <option value="{{ $t }}">{{ \App\Models\StudentHold::typeLabel($t) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Reason</label>
                                        <textarea name="reason" rows="3" class="form-control" required></textarea>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input class="form-check-input" type="checkbox" name="blocks_registration" value="1" id="blocksRegProfile" checked>
                                        <label class="form-check-label" for="blocksRegProfile">Block Course Registration</label>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100"><i class="bi bi-slash-circle me-1"></i>Place Hold</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header py-3"><h6 class="mb-0 fw-semibold">Hold History</h6></div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size:0.85rem">
                                    <thead class="table-light"><tr><th>Type</th><th>Reason</th><th>Placed</th><th>Status</th><th></th></tr></thead>
                                    <tbody>
                                        @forelse($student->holds()->with('placedBy','releasedBy')->latest()->get() as $hold)
                                        <tr>
                                            <td><span class="badge {{ \App\Models\StudentHold::typeBadgeClass($hold->type) }}">{{ \App\Models\StudentHold::typeLabel($hold->type) }}</span></td>
                                            <td style="max-width:160px" class="text-truncate" title="{{ $hold->reason }}">{{ $hold->reason }}</td>
                                            <td class="text-muted">{{ $hold->created_at->format('d M Y') }}</td>
                                            <td>
                                                @if($hold->is_active)
                                                    <span class="badge bg-danger">Active</span>
                                                @else
                                                    <span class="badge bg-success">Released</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hold->is_active)
                                                <form action="{{ route('academic.student-holds.release', $hold) }}" method="POST" onsubmit="return confirm('Release hold?')">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success">Release</button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">No holds on this student.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- /holds tab-pane --}}
            @endhasanyrole

        </div>
    </div>
</div>
@push('scripts')
<script>
// Populate edit result modal
document.getElementById('editResultModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('editCa').value      = btn.dataset.ca;
    document.getElementById('editExam').value    = btn.dataset.exam;
    document.getElementById('editRemarks').value = btn.dataset.remarks ?? '';
    document.getElementById('editResultForm').action = btn.dataset.url;
});

// Populate edit guardian modal
document.getElementById('editGuardianModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('editGuardianName').value    = btn.dataset.name;
    document.getElementById('editGuardianPhone').value   = btn.dataset.phone;
    document.getElementById('editGuardianEmail').value   = btn.dataset.email ?? '';
    document.getElementById('editGuardianAddress').value = btn.dataset.address ?? '';
    document.getElementById('editEmergency').checked     = btn.dataset.emergency === '1';
    document.getElementById('editGuardianForm').action   = btn.dataset.url;

    // Set relationship select
    const rel = document.getElementById('editGuardianRel');
    for (let opt of rel.options) {
        opt.selected = opt.value === btn.dataset.relationship;
    }
});
</script>
@endpush
@endsection
