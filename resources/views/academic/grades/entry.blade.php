@extends('layouts.app')
@section('title', 'Enter Grades — ' . $examination->name)
@section('page-title', 'Enter Grades')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">
            <i class="bi bi-pencil-square me-2 text-warning"></i>
            {{ $examination->name }}
        </h4>
        <p class="text-muted small mb-0">
            <strong>{{ optional(optional($examination->courseOffering)->course)->code }}</strong>
            &bull; {{ optional(optional($examination->courseOffering)->course)->title ?? optional(optional($examination->courseOffering)->course)->name }}
            &bull; {{ optional(optional($examination->courseOffering)->semester)->name }}
            @if($examination->venue) &bull; <i class="bi bi-geo-alt"></i> {{ $examination->venue }} @endif
        </p>
    </div>
    <a href="{{ route('academic.grades.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Grades
    </a>
</div>

{{-- Info Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="text-muted small">Exam Date</div>
                <div class="fw-semibold">{{ $examination->exam_date?->format('d M Y') ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="text-muted small">Max Marks</div>
                <div class="fw-semibold">{{ number_format($examination->max_marks, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="text-muted small">Passing Marks</div>
                <div class="fw-semibold">{{ $examination->passing_marks ? number_format($examination->passing_marks, 0) : number_format($examination->max_marks * 0.4, 0) . ' (40%)' }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="text-muted small">Students / Avg</div>
                <div class="fw-semibold">
                    {{ $students->count() }}
                    @if($avgMark !== null)
                        <span class="text-muted fw-normal small">&bull; avg {{ number_format($avgMark, 1) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Grade Distribution (if marks already entered) --}}
@if($distribution->count())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0 py-2 px-3">
        <span class="fw-semibold small text-muted">GRADE DISTRIBUTION</span>
    </div>
    <div class="card-body pt-0 pb-2 px-3 d-flex flex-wrap gap-2">
        @php
            $gc = ['A+'=>'success','A'=>'success','A-'=>'success','B+'=>'primary','B'=>'primary','B-'=>'primary','C+'=>'warning','C'=>'warning','C-'=>'warning','D'=>'danger','F'=>'danger'];
            foreach (['A+','A','A-','B+','B','B-','C+','C','C-','D','F'] as $g) {
                if ($distribution->has($g)) echo '<span class="badge bg-' . ($gc[$g] ?? 'secondary') . ' px-2 py-1" style="font-size:.8rem">' . $g . ': ' . $distribution[$g] . '</span> ';
            }
        @endphp
    </div>
</div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Marks Entry Form --}}
<form action="{{ route('academic.grades.save') }}" method="POST" id="gradesForm">
    @csrf
    <input type="hidden" name="examination_id" value="{{ $examination->id }}">

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-2 px-3">
            <span class="fw-semibold">Student Mark Sheet</span>
            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="markAllPresent()">
                    <i class="bi bi-person-check me-1"></i>All Present
                </button>
                <span class="text-muted small" id="enteredCount">0 entered</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:36px">#</th>
                            <th>Student</th>
                            <th class="text-center" style="width:80px">Absent</th>
                            <th style="width:130px">
                                Marks
                                <div class="fw-normal text-muted" style="font-size:.72rem">/{{ number_format($examination->max_marks, 0) }}</div>
                            </th>
                            <th class="text-center" style="width:80px">Grade</th>
                            <th class="text-center" style="width:80px">%</th>
                            <th style="width:160px">Remarks</th>
                            <th class="text-center pe-3" style="width:90px">Current</th>
                        </tr>
                    </thead>
                    <tbody id="marksheetBody">
                        @forelse($students as $i => $student)
                        @php $result = $student->examResults->first(); @endphp
                        <tr class="mark-row" data-idx="{{ $i }}">
                            <td class="ps-3 text-muted small">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                         style="width:32px;height:32px;font-size:.72rem">
                                        {{ strtoupper(substr($student->full_name ?? $student->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $student->full_name ?? $student->user?->name }}</div>
                                        <code class="text-muted" style="font-size:.72rem">{{ $student->student_id }}</code>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input absent-cb" type="checkbox"
                                           name="grades[{{ $student->id }}][is_absent]"
                                           value="1"
                                           {{ $result?->is_absent ? 'checked' : '' }}
                                           onchange="toggleAbsent(this)">
                                </div>
                            </td>
                            <td>
                                <input type="number"
                                       name="grades[{{ $student->id }}][marks]"
                                       class="form-control form-control-sm marks-input"
                                       value="{{ $result && !$result->is_absent ? $result->marks_obtained : '' }}"
                                       min="0" max="{{ $examination->max_marks }}" step="0.5"
                                       placeholder="0"
                                       {{ $result?->is_absent ? 'disabled' : '' }}
                                       oninput="calcRow(this)">
                            </td>
                            <td class="text-center">
                                @php
                                    $g  = $result?->grade ?? '—';
                                    $gc = ['A+'=>'success','A'=>'success','A-'=>'success','B+'=>'primary','B'=>'primary','B-'=>'primary','C+'=>'warning','C'=>'warning','C-'=>'warning','D'=>'secondary','F'=>'danger'];
                                @endphp
                                <span class="badge bg-{{ $gc[$g] ?? 'secondary' }} grade-badge">{{ $g }}</span>
                            </td>
                            <td class="text-center pct-cell text-muted small">
                                @if($result && !$result->is_absent && $result->marks_obtained !== null)
                                    {{ number_format($result->marks_obtained / $examination->max_marks * 100, 1) }}%
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <input type="text"
                                       name="grades[{{ $student->id }}][remarks]"
                                       class="form-control form-control-sm"
                                       value="{{ $result?->remarks }}"
                                       placeholder="Optional"
                                       maxlength="200">
                            </td>
                            <td class="text-center pe-3">
                                @if($result)
                                    @if($result->is_absent)
                                        <span class="badge bg-secondary">Absent</span>
                                    @else
                                        <span class="badge bg-{{ $gc[$result->grade] ?? 'secondary' }}">
                                            {{ $result->grade }}
                                            <span class="fw-normal">({{ number_format($result->marks_obtained, 1) }})</span>
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-3 d-block mb-2 opacity-25"></i>
                                No students are registered for this examination's course offering.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($students->count() > 0)
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Grades are auto-calculated from marks. Absent students are excluded from grade calculation.
        </small>
        <div class="d-flex gap-2">
            <a href="{{ route('academic.grades.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-warning px-4">
                <i class="bi bi-save me-1"></i> Save All Grades
            </button>
        </div>
    </div>
    @endif
</form>

{{-- Grade Scale Reference --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0 py-2 px-3">
        <span class="fw-semibold small text-muted">GRADE SCALE (based on % of max marks)</span>
    </div>
    <div class="card-body pt-0 pb-2 px-3 d-flex flex-wrap gap-2">
        @foreach([['A+','≥90%','success'],['A','≥80%','success'],['A-','≥75%','success'],['B+','≥70%','primary'],['B','≥65%','primary'],['B-','≥60%','primary'],['C+','≥55%','warning'],['C','≥50%','warning'],['C-','≥45%','warning'],['D','≥40%','secondary'],['F','<40%','danger']] as [$g,$range,$col])
            <span class="badge bg-{{ $col }} px-2 py-1" style="font-size:.78rem">{{ $g }}: {{ $range }}</span>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
const MAX_MARKS = {{ (float)$examination->max_marks }};
const gradeScale = [[90,'A+'],[80,'A'],[75,'A-'],[70,'B+'],[65,'B'],[60,'B-'],[55,'C+'],[50,'C'],[45,'C-'],[40,'D'],[0,'F']];
const gradeColors = {'A+':'success','A':'success','A-':'success','B+':'primary','B':'primary','B-':'primary','C+':'warning','C':'warning','C-':'warning','D':'secondary','F':'danger'};

function calcRow(input) {
    const row   = input.closest('tr');
    const marks = parseFloat(input.value);
    const pctEl = row.querySelector('.pct-cell');
    const gBadge= row.querySelector('.grade-badge');

    if (isNaN(marks) || input.value === '') {
        pctEl.textContent = '—';
        gBadge.textContent= '—';
        gBadge.className  = 'badge bg-secondary grade-badge';
    } else {
        const pct   = MAX_MARKS > 0 ? (marks / MAX_MARKS) * 100 : 0;
        const grade = gradeScale.find(([min]) => pct >= min)?.[1] || 'F';
        const col   = gradeColors[grade] || 'secondary';
        pctEl.textContent  = pct.toFixed(1) + '%';
        gBadge.textContent = grade;
        gBadge.className   = 'badge bg-' + col + ' grade-badge';
    }
    updateEnteredCount();
}

function toggleAbsent(cb) {
    const row   = cb.closest('tr');
    const input = row.querySelector('.marks-input');
    const pctEl = row.querySelector('.pct-cell');
    const gBadge= row.querySelector('.grade-badge');

    if (cb.checked) {
        input.disabled = true;
        input.value    = '';
        pctEl.textContent  = 'Absent';
        gBadge.textContent = '—';
        gBadge.className   = 'badge bg-secondary grade-badge';
    } else {
        input.disabled = false;
        pctEl.textContent  = '—';
        gBadge.textContent = '—';
        gBadge.className   = 'badge bg-secondary grade-badge';
        input.focus();
    }
    updateEnteredCount();
}

function markAllPresent() {
    document.querySelectorAll('.absent-cb').forEach(cb => {
        if (cb.checked) { cb.checked = false; toggleAbsent(cb); }
    });
}

function updateEnteredCount() {
    let filled = 0, total = 0;
    document.querySelectorAll('.mark-row').forEach(row => {
        total++;
        const cb    = row.querySelector('.absent-cb');
        const input = row.querySelector('.marks-input');
        if (cb.checked || (input.value !== '' && !isNaN(parseFloat(input.value)))) filled++;
    });
    document.getElementById('enteredCount').textContent = filled + '/' + total + ' entered';
}

// Init on load
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.marks-input').forEach(inp => { if (inp.value) calcRow(inp); });
    updateEnteredCount();
});
</script>
@endpush
@endsection
