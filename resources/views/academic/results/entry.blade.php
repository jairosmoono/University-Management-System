@extends('layouts.app')
@section('title', 'Enter Results')
@section('page-title', 'Enter Results')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <i class="bi bi-pencil-square me-2 text-primary"></i>
            Enter Results — {{ optional($offering->course)->code }}
        </h4>
        <p class="text-muted mb-0 small">
            {{ optional($offering->course)->title ?? optional($offering->course)->name }}
            &bull; {{ optional($offering->semester)->name }}
            @if($offering->venue) &bull; {{ $offering->venue }} @endif
        </p>
    </div>
    <a href="{{ route('academic.results.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Results
    </a>
</div>

{{-- Score formula banner --}}
<div class="alert alert-info d-flex gap-3 align-items-start mb-4">
    <i class="bi bi-info-circle-fill fs-5 mt-1 flex-shrink-0"></i>
    <div class="small">
        <strong>Score Formula:</strong>
        CA&nbsp;=&nbsp;(Supplementary&nbsp;1&nbsp;+&nbsp;Supplementary&nbsp;2&nbsp;+&nbsp;Midterm)&nbsp;/&nbsp;300&nbsp;×&nbsp;40
        &nbsp;&bull;&nbsp;
        Exam&nbsp;=&nbsp;Final&nbsp;/&nbsp;100&nbsp;×&nbsp;60
        &nbsp;&bull;&nbsp;
        Total&nbsp;=&nbsp;CA&nbsp;+&nbsp;Exam
        <br>
        @if($suppExams->isEmpty() && $midtermExams->isEmpty() && $finalExams->isEmpty())
            <span class="text-warning fw-semibold">
                <i class="bi bi-exclamation-triangle me-1"></i>
                No examination records found for this course offering — enter grades in the Grades section first.
            </span>
        @else
            Scores are <strong>auto-calculated</strong> from Grades entered for this offering. You may override them manually.
        @endif
    </div>
</div>

{{-- Exam component legend --}}
@if($suppExams->isNotEmpty() || $midtermExams->isNotEmpty() || $finalExams->isNotEmpty())
<div class="row g-2 mb-4">
    @foreach($suppExams as $exam)
    <div class="col-auto">
        <div class="card border-0 shadow-sm px-3 py-2 d-flex flex-row align-items-center gap-2">
            <span class="badge bg-warning text-dark">S{{ $loop->iteration }}</span>
            <span class="small">{{ $exam->name }} <span class="text-muted">/{{ number_format($exam->max_marks,0) }}</span></span>
        </div>
    </div>
    @endforeach
    @foreach($midtermExams as $exam)
    <div class="col-auto">
        <div class="card border-0 shadow-sm px-3 py-2 d-flex flex-row align-items-center gap-2">
            <span class="badge bg-primary">MT</span>
            <span class="small">{{ $exam->name }} <span class="text-muted">/{{ number_format($exam->max_marks,0) }}</span></span>
        </div>
    </div>
    @endforeach
    @foreach($finalExams as $exam)
    <div class="col-auto">
        <div class="card border-0 shadow-sm px-3 py-2 d-flex flex-row align-items-center gap-2">
            <span class="badge bg-danger">FE</span>
            <span class="small">{{ $exam->name }} <span class="text-muted">/{{ number_format($exam->max_marks,0) }}</span></span>
        </div>
    </div>
    @endforeach
</div>
@endif

<form action="{{ route('academic.results.save') }}" method="POST">
    @csrf
    <input type="hidden" name="course_offering_id" value="{{ $offering->id }}">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="resultsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:36px">#</th>
                            <th>Student</th>
                            {{-- Component score columns --}}
                            @foreach($suppExams as $exam)
                            <th class="text-center" style="width:70px">
                                <span class="badge bg-warning text-dark">S{{ $loop->iteration }}</span>
                                <div class="fw-normal text-muted" style="font-size:.7rem">/100</div>
                            </th>
                            @endforeach
                            @foreach($midtermExams as $exam)
                            <th class="text-center" style="width:70px">
                                <span class="badge bg-primary">MT</span>
                                <div class="fw-normal text-muted" style="font-size:.7rem">/100</div>
                            </th>
                            @endforeach
                            @foreach($finalExams as $exam)
                            <th class="text-center" style="width:70px">
                                <span class="badge bg-danger">FE</span>
                                <div class="fw-normal text-muted" style="font-size:.7rem">/100</div>
                            </th>
                            @endforeach
                            {{-- Final scored inputs --}}
                            <th style="width:110px">
                                CA Score
                                <div class="fw-normal text-muted" style="font-size:.72rem">/40</div>
                            </th>
                            <th style="width:110px">
                                Exam Score
                                <div class="fw-normal text-muted" style="font-size:.72rem">/60</div>
                            </th>
                            <th style="width:90px" class="text-center">Total</th>
                            <th style="width:80px" class="text-center">Grade</th>
                            <th style="width:100px" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $i => $student)
                        @php
                            $existing  = $student->finalResults->first();
                            $scores    = $calcScores[$student->id] ?? ['calc_ca' => null, 'calc_exam' => null];
                            $calcCa    = $scores['calc_ca'];
                            $calcExam  = $scores['calc_exam'];
                            $fillCa    = $existing?->ca_score   ?? $calcCa;
                            $fillExam  = $existing?->exam_score ?? $calcExam;
                            $fillTotal = ($fillCa !== null && $fillExam !== null) ? $fillCa + $fillExam : null;
                        @endphp
                        <tr>
                            <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                         style="width:34px;height:34px;font-size:.72rem">
                                        {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $student->full_name }}</div>
                                        <code class="text-muted" style="font-size:.75rem">{{ $student->student_id }}</code>
                                    </div>
                                </div>
                            </td>

                            {{-- Supplementary component cells (read-only) --}}
                            @foreach($suppExams as $exam)
                            @php $er = $erMap[$student->id][$exam->id] ?? null; @endphp
                            <td class="text-center">
                                @if($er && !$er->is_absent && $er->marks_obtained !== null)
                                    <span class="fw-semibold small text-{{ $er->marks_obtained >= 50 ? 'success' : 'danger' }}">
                                        {{ number_format($er->marks_obtained, 1) }}
                                    </span>
                                @elseif($er && $er->is_absent)
                                    <span class="badge bg-secondary" style="font-size:.7rem">Abs</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            @endforeach

                            {{-- Midterm component cells (read-only) --}}
                            @foreach($midtermExams as $exam)
                            @php $er = $erMap[$student->id][$exam->id] ?? null; @endphp
                            <td class="text-center">
                                @if($er && !$er->is_absent && $er->marks_obtained !== null)
                                    <span class="fw-semibold small text-{{ $er->marks_obtained >= 50 ? 'success' : 'danger' }}">
                                        {{ number_format($er->marks_obtained, 1) }}
                                    </span>
                                @elseif($er && $er->is_absent)
                                    <span class="badge bg-secondary" style="font-size:.7rem">Abs</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            @endforeach

                            {{-- Final exam component cells (read-only) --}}
                            @foreach($finalExams as $exam)
                            @php $er = $erMap[$student->id][$exam->id] ?? null; @endphp
                            <td class="text-center">
                                @if($er && !$er->is_absent && $er->marks_obtained !== null)
                                    <span class="fw-semibold small text-{{ $er->marks_obtained >= 50 ? 'success' : 'danger' }}">
                                        {{ number_format($er->marks_obtained, 1) }}
                                    </span>
                                @elseif($er && $er->is_absent)
                                    <span class="badge bg-secondary" style="font-size:.7rem">Abs</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            @endforeach

                            {{-- CA Score input --}}
                            <td>
                                <input type="number"
                                       name="results[{{ $student->id }}][ca_total]"
                                       class="form-control form-control-sm ca-input"
                                       value="{{ $fillCa }}"
                                       min="0" max="40" step="0.01"
                                       placeholder="0"
                                       oninput="calcRow(this)"
                                       @if($calcCa !== null && !$existing) title="Auto-calculated: {{ $calcCa }}" @endif>
                            </td>

                            {{-- Exam Score input --}}
                            <td>
                                <input type="number"
                                       name="results[{{ $student->id }}][exam_score]"
                                       class="form-control form-control-sm exam-input"
                                       value="{{ $fillExam }}"
                                       min="0" max="60" step="0.01"
                                       placeholder="0"
                                       oninput="calcRow(this)"
                                       @if($calcExam !== null && !$existing) title="Auto-calculated: {{ $calcExam }}" @endif>
                            </td>

                            <td class="text-center">
                                <span class="total-cell fw-bold">
                                    {{ $fillTotal !== null ? number_format($fillTotal, 1) : '—' }}
                                </span>
                            </td>
                            <td class="text-center grade-cell">
                                @php
                                    $gc = ['A+'=>'success','A'=>'success','A-'=>'success','B+'=>'primary','B'=>'primary','B-'=>'primary','C+'=>'warning','C'=>'warning','C-'=>'warning','D'=>'danger','F'=>'danger'];
                                    $g  = $existing?->grade ?? ($fillTotal !== null ? scoreToGrade($fillTotal) : '—');
                                @endphp
                                <span class="badge bg-{{ $gc[$g] ?? 'secondary' }}">{{ $g }}</span>
                            </td>
                            <td class="text-center">
                                @if($existing)
                                    @php $sc = ['pending'=>'secondary','pass'=>'success','fail'=>'danger']; @endphp
                                    <span class="badge bg-{{ $sc[$existing->status] ?? 'secondary' }}">
                                        {{ ucfirst($existing->status) }}
                                    </span>
                                @else
                                    <span class="text-muted small">Not entered</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="20" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-3 d-block mb-2 opacity-25"></i>
                                No students are registered in this course offering.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($students->count() > 0)
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            {{ $students->count() }} student{{ $students->count() != 1 ? 's' : '' }} enrolled
            &bull; Saved results will be marked <strong>Pending</strong> until approved
        </small>
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i> Save Results
        </button>
    </div>
    @endif
</form>

@push('scripts')
<script>
const gradeScale = [[90,'A+'],[80,'A'],[75,'A-'],[70,'B+'],[65,'B'],[60,'B-'],[55,'C+'],[50,'C'],[45,'C-'],[40,'D'],[0,'F']];
const gradeColors = {'A+':'success','A':'success','A-':'success','B+':'primary','B':'primary','B-':'primary','C+':'warning','C':'warning','C-':'warning','D':'danger','F':'danger'};

function calcRow(input) {
    const row  = input.closest('tr');
    const ca   = parseFloat(row.querySelector('.ca-input').value) || 0;
    const exam = parseFloat(row.querySelector('.exam-input').value) || 0;
    const tot  = ca + exam;

    row.querySelector('.total-cell').textContent = tot > 0 ? tot.toFixed(1) : '—';

    const grade   = gradeScale.find(([min]) => tot >= min)?.[1] || 'F';
    const color   = gradeColors[grade] || 'secondary';
    const gradeEl = row.querySelector('.grade-cell span');
    gradeEl.textContent = tot > 0 ? grade : '—';
    gradeEl.className   = tot > 0 ? `badge bg-${color}` : 'badge bg-secondary';
}
</script>
@endpush
@endsection
