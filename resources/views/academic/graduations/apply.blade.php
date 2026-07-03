@extends('layouts.app')
@section('title', 'New Graduation Application')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">New Graduation Application</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('graduation.index') }}">Graduation</a></li>
        <li class="breadcrumb-item active">New Application</li>
    </ol></nav>
</div>

<div class="row g-4">
    {{-- Main form --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                <h6 class="fw-semibold mb-0">Application Details</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST" action="{{ route('graduation.store') }}">
                    @csrf

                    @if(!$student)
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <label class="form-label fw-semibold">Select Student</label>
                        <p class="text-muted small mb-2">Type a student name or ID to search, then select from the results.</p>
                        <input type="hidden" name="student_id" id="selectedStudentId" value="{{ old('student_id') }}" required>
                        <div class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="studentSearch" class="form-control @error('student_id') is-invalid @enderror"
                                       placeholder="Search by name or student number…" autocomplete="off">
                            </div>
                            <div id="searchResults" class="position-absolute w-100 bg-white border rounded-3 shadow-sm mt-1 d-none" style="z-index:1050;max-height:260px;overflow-y:auto"></div>
                        </div>
                        <div id="selectedStudent" class="d-none mt-2 p-2 bg-white border rounded-3 d-flex align-items-center gap-2">
                            <i class="bi bi-person-circle text-primary fs-4"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small" id="selName"></div>
                                <div class="text-muted" style="font-size:.75rem" id="selMeta"></div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSelection()"><i class="bi bi-x"></i></button>
                        </div>
                        @error('student_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    @else
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <div class="mb-4 p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                        <img src="{{ $student->photo_url }}" class="rounded-circle" width="52" height="52" style="object-fit:cover">
                        <div>
                            <div class="fw-bold">{{ $student->full_name }}</div>
                            <div class="text-muted small">{{ $student->student_id }} &bull; {{ $student->program?->name }}</div>
                        </div>
                        <a href="{{ route('graduation.eligible') }}" class="ms-auto btn btn-sm btn-outline-secondary">Change</a>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                        <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            <option value="">Select academic year…</option>
                            @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ old('academic_year_id') == $ay->id ? 'selected' : '' }}>
                                {{ $ay->name }}{{ $ay->is_current ? ' (Current)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign to Ceremony <span class="text-muted fw-normal">(optional)</span></label>
                        <select name="ceremony_id" class="form-select">
                            <option value="">No ceremony assigned yet</option>
                            @foreach($ceremonies as $c)
                            <option value="{{ $c->id }}" {{ old('ceremony_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} — {{ $c->ceremony_date->format('d M Y') }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Notes <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Any additional notes…">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Submit Application
                        </button>
                        <a href="{{ route('graduation.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Eligibility sidebar --}}
    @if($student && $eligibility)
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                <h6 class="fw-semibold mb-0">Eligibility Check</h6>
            </div>
            <div class="card-body px-4 pb-4">
                @php
                    $checks = [
                        ['label' => 'Credits Earned', 'ok' => $eligibility['credits_ok'],
                         'note' => $eligibility['credits_earned'].' / '.$eligibility['required_credits'].' required'],
                        ['label' => 'Min CGPA (1.5)', 'ok' => $eligibility['cgpa_ok'],
                         'note' => 'Current CGPA: '.$eligibility['cgpa']],
                        ['label' => 'Finance Cleared', 'ok' => $eligibility['finance_ok'],
                         'note' => $eligibility['finance_ok'] ? 'No outstanding balance' : 'Outstanding: '.number_format($eligibility['outstanding_bal'],2)],
                        ['label' => 'Library Cleared', 'ok' => $eligibility['library_ok'],
                         'note' => $eligibility['library_ok'] ? 'No active loans/fines' : $eligibility['active_loans'].' loans, KES '.number_format($eligibility['unpaid_fines'],2).' fines'],
                        ['label' => 'Academic Cleared', 'ok' => $eligibility['academic_ok'],
                         'note' => $eligibility['academic_ok'] ? 'No failed / pending results' : $eligibility['failed_count'].' failed, '.$eligibility['pending_results'].' pending'],
                    ];
                @endphp

                @foreach($checks as $chk)
                <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                    <i class="bi bi-{{ $chk['ok'] ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }} fs-5"></i>
                    <div>
                        <div class="fw-semibold small">{{ $chk['label'] }}</div>
                        <div class="text-muted" style="font-size:0.78rem">{{ $chk['note'] }}</div>
                    </div>
                </div>
                @endforeach

                <div class="mt-3 p-3 rounded-3 {{ $eligibility['eligible'] ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }}">
                    <i class="bi bi-{{ $eligibility['eligible'] ? 'patch-check-fill' : 'exclamation-triangle-fill' }} me-2"></i>
                    <strong>{{ $eligibility['eligible'] ? 'Student is fully eligible for graduation.' : 'Student does not meet all eligibility criteria.' }}</strong>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@push('scripts')
<script>
(function() {
    const searchInput  = document.getElementById('studentSearch');
    const resultsBox   = document.getElementById('searchResults');
    const hiddenInput  = document.getElementById('selectedStudentId');
    const selectedBox  = document.getElementById('selectedStudent');
    if (!searchInput) return;

    let timer = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { resultsBox.classList.add('d-none'); return; }
        timer = setTimeout(() => fetchStudents(q), 300);
    });

    function fetchStudents(q) {
        fetch(`{{ route('graduation.eligible') }}?search=${encodeURIComponent(q)}&_ajax=1`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                resultsBox.innerHTML = '<div class="p-3 text-muted text-center small">No graduated students found.</div>';
                resultsBox.classList.remove('d-none');
                return;
            }
            resultsBox.innerHTML = data.map(s =>
                `<div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom student-result" style="cursor:pointer"
                      data-id="${s.id}" data-name="${s.name}" data-sid="${s.student_id}" data-program="${s.program}">
                    <i class="bi bi-person-circle text-primary"></i>
                    <div>
                        <div class="fw-semibold small">${s.name}</div>
                        <div class="text-muted" style="font-size:.72rem">${s.student_id} &bull; ${s.program}</div>
                    </div>
                </div>`
            ).join('');
            resultsBox.classList.remove('d-none');

            resultsBox.querySelectorAll('.student-result').forEach(el => {
                el.addEventListener('click', () => selectStudent(el.dataset));
                el.addEventListener('mouseenter', () => el.style.background = '#f0f4ff');
                el.addEventListener('mouseleave', () => el.style.background = '');
            });
        })
        .catch(() => {});
    }

    function selectStudent(data) {
        hiddenInput.value = data.id;
        document.getElementById('selName').textContent = data.name;
        document.getElementById('selMeta').textContent = data.sid + ' — ' + data.program;
        selectedBox.classList.remove('d-none');
        selectedBox.classList.add('d-flex');
        searchInput.value = '';
        resultsBox.classList.add('d-none');

        // Reload page with student_id to show eligibility sidebar
        window.location.href = `{{ route('graduation.apply') }}?student_id=${data.id}`;
    }

    window.clearSelection = function() {
        hiddenInput.value = '';
        selectedBox.classList.add('d-none');
        searchInput.value = '';
        searchInput.focus();
    };

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.classList.add('d-none');
        }
    });
})();
</script>
@endpush
@endsection
