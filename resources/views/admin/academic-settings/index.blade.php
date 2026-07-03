@extends('layouts.app')
@section('title', 'Academic Settings')
@section('page-title', 'Academic Settings')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-sliders me-2 text-primary"></i>Academic Settings</h4>
        <p class="text-muted small mb-0">Manage grade scales and examination types used across the system.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="settingTabs">
    <li class="nav-item">
        <a class="nav-link {{ request('tab','grades') == 'grades' ? 'active' : '' }}"
           href="?tab=grades">
            <i class="bi bi-star-half me-1"></i> Grade Scales
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('tab') == 'types' ? 'active' : '' }}"
           href="?tab=types">
            <i class="bi bi-tag me-1"></i> Exam Types
        </a>
    </li>
</ul>

{{-- ══════════════════ GRADE SCALES TAB ══════════════════ --}}
@if(request('tab','grades') == 'grades')

<div class="row g-4">
    {{-- Table --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                <span class="fw-semibold">Current Grade Scale</span>
                <small class="text-muted">{{ $gradeScales->count() }} grades defined</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width:70px">Grade</th>
                                <th style="width:130px">Min Score</th>
                                <th style="width:110px">Grade Points</th>
                                <th>Label</th>
                                <th class="text-center" style="width:80px">Pass?</th>
                                <th class="text-center" style="width:70px">Order</th>
                                <th class="text-end pe-3" style="width:90px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gradeScales as $gs)
                            @php
                                $gc = ['A+'=>'success','A'=>'success','A-'=>'success','B+'=>'primary','B'=>'primary','B-'=>'primary','C+'=>'warning','C'=>'warning','C-'=>'warning','D+'=>'secondary','D'=>'secondary','D-'=>'secondary','F'=>'danger'];
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-{{ $gc[$gs->grade] ?? 'secondary' }} fs-6 px-3">{{ $gs->grade }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">≥ {{ number_format($gs->min_score, 1) }}</span>
                                    <span class="text-muted small">%</span>
                                </td>
                                <td>{{ number_format($gs->grade_points, 2) }}</td>
                                <td class="text-muted small">{{ $gs->label ?? '—' }}</td>
                                <td class="text-center">
                                    @if($gs->is_pass)
                                        <span class="badge bg-success-subtle text-success border border-success">Pass</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger">Fail</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted small">{{ $gs->sort_order }}</td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-secondary"
                                            onclick="editGrade({{ $gs->id }}, '{{ $gs->grade }}', {{ $gs->min_score }}, {{ $gs->grade_points }}, '{{ $gs->label }}', {{ $gs->is_pass ? 1 : 0 }}, {{ $gs->sort_order }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.academic-settings.grade.destroy', $gs) }}" class="d-inline"
                                          onsubmit="return confirm('Delete grade {{ $gs->grade }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No grade scales defined yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add / Edit form --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" id="gradeFormCard">
            <div class="card-header bg-transparent border-0 pt-3">
                <span class="fw-semibold" id="gradeFormTitle">Add Grade</span>
            </div>
            <div class="card-body">
                <form method="POST" id="gradeForm" action="{{ route('admin.academic-settings.grade.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="gradeMethod" value="POST">
                    <input type="hidden" name="grade_id" id="gradeId">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Grade Letter <span class="text-danger">*</span></label>
                        <input type="text" name="grade" id="gradeInput" class="form-control @error('grade') is-invalid @enderror"
                               placeholder="e.g. A+" maxlength="5" required>
                        @error('grade')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Minimum Score (%) <span class="text-danger">*</span></label>
                        <input type="number" name="min_score" id="minScoreInput" class="form-control @error('min_score') is-invalid @enderror"
                               placeholder="e.g. 80" min="0" max="100" step="0.01" required>
                        @error('min_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Student's percentage score must be ≥ this value to earn this grade.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Grade Points <span class="text-danger">*</span></label>
                        <input type="number" name="grade_points" id="gradePointsInput" class="form-control @error('grade_points') is-invalid @enderror"
                               placeholder="e.g. 4.0" min="0" max="4" step="0.01" required>
                        @error('grade_points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Label</label>
                        <input type="text" name="label" id="gradeLabelInput" class="form-control" placeholder="e.g. Excellent" maxlength="60">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Sort Order</label>
                            <input type="number" name="sort_order" id="gradeSortInput" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Result</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_pass" id="isPassInput" value="1" checked>
                                <label class="form-check-label small" for="isPassInput">Counts as Pass</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1" id="gradeSubmitBtn">
                            <i class="bi bi-plus-circle me-1"></i> Add Grade
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="gradeCancelBtn" onclick="resetGradeForm()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════ EXAM TYPES TAB ══════════════════ --}}
@elseif(request('tab') == 'types')

<div class="row g-4">
    {{-- Table --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                <span class="fw-semibold">Examination Types</span>
                <small class="text-muted">{{ $examTypes->count() }} types defined</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Name</th>
                                <th style="width:140px">Code</th>
                                <th style="width:110px">Category</th>
                                <th>Description</th>
                                <th class="text-center" style="width:80px">Active</th>
                                <th class="text-center" style="width:70px">Order</th>
                                <th class="text-end pe-3" style="width:90px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($examTypes as $et)
                            @php
                                $catColor = ['ca'=>'warning','exam'=>'danger','other'=>'secondary'];
                                $catLabel = ['ca'=>'CA','exam'=>'Exam','other'=>'Other'];
                            @endphp
                            <tr>
                                <td class="ps-3 fw-semibold small">{{ $et->name }}</td>
                                <td><code class="small">{{ $et->code }}</code></td>
                                <td>
                                    <span class="badge bg-{{ $catColor[$et->category] ?? 'secondary' }}">
                                        {{ $catLabel[$et->category] ?? $et->category }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $et->description ?? '—' }}</td>
                                <td class="text-center">
                                    @if($et->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted small">{{ $et->sort_order }}</td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-secondary"
                                            onclick="editType({{ $et->id }}, '{{ addslashes($et->name) }}', '{{ $et->code }}', '{{ $et->category }}', '{{ addslashes($et->description ?? '') }}', {{ $et->is_active ? 1 : 0 }}, {{ $et->sort_order }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.academic-settings.type.destroy', $et) }}" class="d-inline"
                                          onsubmit="return confirm('Delete exam type {{ $et->code }}? This will not remove existing examinations using this type.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No exam types defined yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add / Edit form --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <span class="fw-semibold" id="typeFormTitle">Add Exam Type</span>
            </div>
            <div class="card-body">
                <form method="POST" id="typeForm" action="{{ route('admin.academic-settings.type.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="typeMethod" value="POST">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="typeNameInput" class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Midterm Examination" maxlength="100" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="typeCodeInput" class="form-control @error('code') is-invalid @enderror"
                               placeholder="e.g. mid_term" maxlength="50" required
                               pattern="[a-z0-9_]+" title="Lowercase letters, numbers and underscores only">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Used internally — lowercase, underscores only. Cannot be changed after exams use it.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                        <select name="category" id="typeCategoryInput" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="ca">CA (Continuous Assessment — counts toward CA score)</option>
                            <option value="exam">Exam (Final — counts toward Exam score)</option>
                            <option value="other">Other (Informational only)</option>
                        </select>
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <input type="text" name="description" id="typeDescInput" class="form-control" maxlength="255">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Sort Order</label>
                            <input type="number" name="sort_order" id="typeSortInput" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Status</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="typeActiveInput" value="1" checked>
                                <label class="form-check-label small" for="typeActiveInput">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1" id="typeSubmitBtn">
                            <i class="bi bi-plus-circle me-1"></i> Add Type
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="typeCancelBtn" onclick="resetTypeForm()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Category legend --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body py-2 px-3">
                <div class="fw-semibold small text-muted mb-2">SCORE FORMULA</div>
                <div class="small">
                    <span class="badge bg-warning text-dark me-1">CA</span> types → summed / 300 × 40<br>
                    <span class="badge bg-danger me-1">Exam</span> types → summed / 100 × 60<br>
                    <span class="badge bg-secondary me-1">Other</span> types → not counted in score
                </div>
            </div>
        </div>
    </div>
</div>

@endif

@push('scripts')
<script>
// ── Grade Scale form helpers ──────────────────────────────────────────────────
function editGrade(id, grade, minScore, gradePoints, label, isPass, sortOrder) {
    document.getElementById('gradeFormTitle').textContent   = 'Edit Grade';
    document.getElementById('gradeSubmitBtn').innerHTML     = '<i class="bi bi-save me-1"></i> Update Grade';
    document.getElementById('gradeCancelBtn').classList.remove('d-none');
    document.getElementById('gradeMethod').value            = 'PUT';

    // Change form action to update route
    document.getElementById('gradeForm').action = '{{ url("admin/academic-settings/grade") }}/' + id;

    document.getElementById('gradeInput').value       = grade;
    document.getElementById('minScoreInput').value    = minScore;
    document.getElementById('gradePointsInput').value = gradePoints;
    document.getElementById('gradeLabelInput').value  = label || '';
    document.getElementById('gradeSortInput').value   = sortOrder;
    document.getElementById('isPassInput').checked    = isPass == 1;

    document.getElementById('gradeFormCard').scrollIntoView({ behavior: 'smooth' });
}

function resetGradeForm() {
    document.getElementById('gradeFormTitle').textContent   = 'Add Grade';
    document.getElementById('gradeSubmitBtn').innerHTML     = '<i class="bi bi-plus-circle me-1"></i> Add Grade';
    document.getElementById('gradeCancelBtn').classList.add('d-none');
    document.getElementById('gradeMethod').value            = 'POST';
    document.getElementById('gradeForm').action             = '{{ route("admin.academic-settings.grade.store") }}';
    document.getElementById('gradeForm').reset();
}

// ── Exam Type form helpers ────────────────────────────────────────────────────
function editType(id, name, code, category, desc, isActive, sortOrder) {
    document.getElementById('typeFormTitle').textContent  = 'Edit Exam Type';
    document.getElementById('typeSubmitBtn').innerHTML    = '<i class="bi bi-save me-1"></i> Update Type';
    document.getElementById('typeCancelBtn').classList.remove('d-none');
    document.getElementById('typeMethod').value           = 'PUT';
    document.getElementById('typeForm').action = '{{ url("admin/academic-settings/type") }}/' + id;

    document.getElementById('typeNameInput').value      = name;
    document.getElementById('typeCodeInput').value      = code;
    document.getElementById('typeCategoryInput').value  = category;
    document.getElementById('typeDescInput').value      = desc;
    document.getElementById('typeSortInput').value      = sortOrder;
    document.getElementById('typeActiveInput').checked  = isActive == 1;

    document.querySelector('.col-lg-4').scrollIntoView({ behavior: 'smooth' });
}

function resetTypeForm() {
    document.getElementById('typeFormTitle').textContent  = 'Add Exam Type';
    document.getElementById('typeSubmitBtn').innerHTML    = '<i class="bi bi-plus-circle me-1"></i> Add Type';
    document.getElementById('typeCancelBtn').classList.add('d-none');
    document.getElementById('typeMethod').value           = 'POST';
    document.getElementById('typeForm').action            = '{{ route("admin.academic-settings.type.store") }}';
    document.getElementById('typeForm').reset();
}

// Auto-generate code from name
@if(request('tab') == 'types')
document.getElementById('typeNameInput').addEventListener('input', function() {
    const codeField = document.getElementById('typeCodeInput');
    if (codeField.dataset.manual) return;
    codeField.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g,'');
});
document.getElementById('typeCodeInput').addEventListener('input', function() {
    this.dataset.manual = '1';
});
@endif
</script>
@endpush
@endsection
