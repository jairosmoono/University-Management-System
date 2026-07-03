<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Now — {{ $uni['university_name'] ?? config('app.name') }}</title>
    @if(!empty($uni['favicon_path']))
    <link rel="icon" href="{{ asset('storage/' . $uni['favicon_path']) }}" type="image/x-icon">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root { --navy:#0B1F3A; --gold:#C9A84C; --gold2:#e8c66d; }
        body { background:#F4F6FA; font-family:'Segoe UI',system-ui,sans-serif; }

        /* Topbar */
        .topbar { background:var(--navy); padding:.75rem 0; }
        .topbar .brand-name { color:#fff; font-weight:700; font-size:1.05rem; }
        .topbar .brand-tag  { color:var(--gold); font-size:.7rem; letter-spacing:.08em; text-transform:uppercase; }

        /* Progress steps */
        .step-bar { background:#fff; border-bottom:1px solid #e5e7eb; padding:.85rem 0; position:sticky; top:0; z-index:100; }
        .step { display:flex; align-items:center; gap:.5rem; font-size:.82rem; font-weight:600; color:#9ca3af; white-space:nowrap; }
        .step.active { color:var(--navy); }
        .step.done   { color:#10b981; }
        .step-circle { width:28px; height:28px; border-radius:50%; border:2px solid currentColor; display:flex; align-items:center; justify-content:center; font-size:.75rem; flex-shrink:0; }
        .step.active .step-circle { background:var(--navy); border-color:var(--navy); color:#fff; }
        .step.done   .step-circle { background:#10b981; border-color:#10b981; color:#fff; }
        .step-connector { flex:1; height:2px; background:#e5e7eb; min-width:20px; max-width:60px; }
        .step-connector.done { background:#10b981; }

        /* Cards */
        .form-section { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); padding:2rem; margin-bottom:1.5rem; display:none; }
        .form-section.active { display:block; }
        .section-icon { width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,var(--navy),#1e3a60); display:flex; align-items:center; justify-content:center; }
        .section-icon i { color:var(--gold); font-size:1.15rem; }
        .section-title { font-size:1.1rem; font-weight:700; color:var(--navy); }
        .section-sub { font-size:.85rem; color:#6b7280; }

        /* Form controls */
        .form-label { font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.3rem; }
        .form-control, .form-select { border-radius:8px; border:1.5px solid #d1d5db; font-size:.9rem; padding:.55rem .9rem; }
        .form-control:focus, .form-select:focus { border-color:var(--navy); box-shadow:0 0 0 3px rgba(11,31,58,.1); }
        .required-star { color:#ef4444; }

        /* Upload zone */
        .upload-zone { border:2px dashed #d1d5db; border-radius:10px; padding:1.5rem; text-align:center; cursor:pointer; transition:border-color .2s,background .2s; }
        .upload-zone:hover, .upload-zone.dragover { border-color:var(--navy); background:rgba(11,31,58,.03); }
        .upload-zone i { font-size:1.6rem; color:#9ca3af; }
        .upload-zone.has-file { border-color:#10b981; background:rgba(16,185,129,.04); }
        .upload-zone.has-file i { color:#10b981; }

        /* Buttons */
        .btn-prev { background:#fff; border:2px solid var(--navy); color:var(--navy); font-weight:700; border-radius:50px; padding:.65rem 1.8rem; }
        .btn-next { background:var(--navy); border:none; color:#fff; font-weight:700; border-radius:50px; padding:.65rem 1.8rem; }
        .btn-submit { background:var(--gold); border:none; color:var(--navy); font-weight:800; border-radius:50px; padding:.75rem 2.5rem; font-size:1rem; }
        .btn-prev:hover  { background:var(--navy); color:#fff; }
        .btn-next:hover  { background:#1a3a6b; }
        .btn-submit:hover { background:var(--gold2); }

        /* Review summary */
        .review-row { display:flex; gap:.5rem; padding:.5rem 0; border-bottom:1px solid #f3f4f6; font-size:.88rem; }
        .review-row .lbl { color:#6b7280; min-width:160px; flex-shrink:0; }
        .review-row .val { color:var(--navy); font-weight:600; }
    </style>
</head>
<body>

{{-- Topbar --}}
<div class="topbar">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            @if(!empty($uni['logo_path']))
            <img src="{{ asset('storage/' . $uni['logo_path']) }}" alt="Logo"
                 style="height:36px;max-width:40px;object-fit:contain;border-radius:6px;background:#fff;padding:2px">
            @else
            <div style="width:34px;height:34px;border-radius:8px;background:var(--gold);display:flex;align-items:center;justify-content:center">
                <i class="bi bi-mortarboard-fill" style="color:var(--navy)"></i>
            </div>
            @endif
            <div>
                <div class="brand-name">{{ $uni['university_name'] ?? config('app.name') }}</div>
                <div class="brand-tag">Online Admissions Portal</div>
            </div>
        </a>
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="bi bi-box-arrow-in-right me-1"></i>Staff Login
        </a>
    </div>
</div>

{{-- Step progress bar --}}
<div class="step-bar">
    <div class="container">
        <div class="d-flex align-items-center gap-1 overflow-auto pb-1">
            <div class="step active" id="step-ind-1">
                <div class="step-circle">1</div>
                <span class="d-none d-sm-inline">Personal</span>
            </div>
            <div class="step-connector" id="conn-1"></div>
            <div class="step" id="step-ind-2">
                <div class="step-circle">2</div>
                <span class="d-none d-sm-inline">Contact</span>
            </div>
            <div class="step-connector" id="conn-2"></div>
            <div class="step" id="step-ind-3">
                <div class="step-circle">3</div>
                <span class="d-none d-sm-inline">Programme</span>
            </div>
            <div class="step-connector" id="conn-3"></div>
            <div class="step" id="step-ind-4">
                <div class="step-circle">4</div>
                <span class="d-none d-sm-inline">Education</span>
            </div>
            <div class="step-connector" id="conn-4"></div>
            <div class="step" id="step-ind-5">
                <div class="step-circle">5</div>
                <span class="d-none d-sm-inline">Documents</span>
            </div>
            <div class="step-connector" id="conn-5"></div>
            <div class="step" id="step-ind-6">
                <div class="step-circle">6</div>
                <span class="d-none d-sm-inline">Review</span>
            </div>
        </div>
    </div>
</div>

<div class="container py-4" style="max-width:780px">

    {{-- Errors --}}
    @if($errors->any())
    <div class="alert alert-danger rounded-3 mb-4">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2 ps-3">
            @foreach($errors->all() as $error)
            <li class="small">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('apply.submit') }}" enctype="multipart/form-data" id="applyForm" novalidate>
        @csrf

        {{-- ── Step 1: Personal Info ── --}}
        <div class="form-section active" id="section-1">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="section-icon"><i class="bi bi-person-fill"></i></div>
                <div>
                    <div class="section-title">Personal Information</div>
                    <div class="section-sub">Tell us about yourself</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">First Name <span class="required-star">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="e.g. Jane" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}" placeholder="Optional">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name <span class="required-star">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="e.g. Mwangi" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth <span class="required-star">*</span></label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender <span class="required-star">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select gender</option>
                        <option value="male"   {{ old('gender')=='male'   ?'selected':'' }}>Male</option>
                        <option value="female" {{ old('gender')=='female' ?'selected':'' }}>Female</option>
                        <option value="other"  {{ old('gender')=='other'  ?'selected':'' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nationality</label>
                    <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $uni['university_country'] ?? '') }}" placeholder="e.g. Zambian">
                </div>
            </div>
        </div>

        {{-- ── Step 2: Contact ── --}}
        <div class="form-section" id="section-2">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="section-icon"><i class="bi bi-telephone-fill"></i></div>
                <div>
                    <div class="section-title">Contact Details</div>
                    <div class="section-sub">How can we reach you?</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="required-star">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="you@example.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number <span class="required-star">*</span></label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+260 97 000 0000" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Residential Address</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Street, City, Province">{{ old('address') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Step 3: Programme Selection ── --}}
        <div class="form-section" id="section-3">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="section-icon"><i class="bi bi-mortarboard-fill"></i></div>
                <div>
                    <div class="section-title">Programme Selection</div>
                    <div class="section-sub">Choose your desired programme of study</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Filter by Faculty</label>
                    <select id="facultyFilter" class="form-select">
                        <option value="">All Faculties</option>
                        @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Programme <span class="required-star">*</span></label>
                    <select name="program_id" id="programSelect" class="form-select" required>
                        <option value="">Select programme</option>
                        @foreach($programs as $program)
                        <option value="{{ $program->id }}"
                                data-faculty="{{ $program->department?->faculty?->id }}"
                                {{ old('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->name }}
                            ({{ ucfirst($program->level) }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Semester/Term Period</label>
                    <select name="semester_id" class="form-select">
                        <option value="">Not specified</option>
                        @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── Step 4: Educational Background ── --}}
        <div class="form-section" id="section-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="section-icon"><i class="bi bi-book-fill"></i></div>
                <div>
                    <div class="section-title">Educational Background</div>
                    <div class="section-sub">Your most recent qualification</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Previous School / Institution <span class="required-star">*</span></label>
                    <input type="text" name="previous_school" class="form-control" value="{{ old('previous_school') }}" placeholder="Name of your last school or institution" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Qualification Type <span class="required-star">*</span></label>
                    <select name="qualification_type" class="form-select" required>
                        <option value="">Select qualification</option>
                        @foreach(['Grade 12 Certificate','IGCSE','A-Level','Diploma','Advanced Diploma','Bachelor\'s Degree','Other'] as $q)
                        <option value="{{ $q }}" {{ old('qualification_type') == $q ? 'selected' : '' }}>{{ $q }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year Completed <span class="required-star">*</span></label>
                    <input type="number" name="year_completed" class="form-control" value="{{ old('year_completed') }}"
                           min="1990" max="{{ date('Y') }}" placeholder="{{ date('Y') - 1 }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grade / GPA <span class="required-star">*</span></label>
                    <input type="text" name="grade" class="form-control" value="{{ old('grade') }}" placeholder="e.g. A, Merit, 3.8" required>
                </div>
            </div>
        </div>

        {{-- ── Step 5: Documents ── --}}
        <div class="form-section" id="section-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="section-icon"><i class="bi bi-paperclip"></i></div>
                <div>
                    <div class="section-title">Supporting Documents</div>
                    <div class="section-sub">Upload scanned copies (PDF, JPG, PNG — max 5 MB each). All optional.</div>
                </div>
            </div>
            <div class="row g-3">
                @foreach([
                    ['key'=>'certificates', 'label'=>'Academic Certificates', 'icon'=>'bi-file-earmark-text', 'accept'=>'.pdf,.jpg,.jpeg,.png'],
                    ['key'=>'national_id',  'label'=>'National ID / Passport', 'icon'=>'bi-person-badge',     'accept'=>'.pdf,.jpg,.jpeg,.png'],
                    ['key'=>'photo',        'label'=>'Passport Photo',         'icon'=>'bi-camera',           'accept'=>'.jpg,.jpeg,.png'],
                ] as $doc)
                <div class="col-md-4">
                    <label class="form-label">{{ $doc['label'] }}</label>
                    <label class="upload-zone d-block" id="zone-{{ $doc['key'] }}" for="file-{{ $doc['key'] }}">
                        <i class="bi {{ $doc['icon'] }} d-block mb-2"></i>
                        <div class="fw-semibold small" style="color:#374151" id="zone-text-{{ $doc['key'] }}">Click to upload</div>
                        <div class="text-muted" style="font-size:.75rem">{{ strtoupper(str_replace(['.','jpeg'], ['','JPG'], $doc['accept'])) }}</div>
                        <input type="file" id="file-{{ $doc['key'] }}" name="documents[{{ $doc['key'] }}]"
                               accept="{{ $doc['accept'] }}" class="d-none"
                               onchange="markUploaded('{{ $doc['key'] }}', this)">
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Step 6: Review & Submit ── --}}
        <div class="form-section" id="section-6">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="section-icon"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="section-title">Review & Submit</div>
                    <div class="section-sub">Confirm your details before submitting</div>
                </div>
            </div>

            <div id="review-content">
                {{-- Populated by JS --}}
            </div>

            <div class="alert alert-warning border-0 rounded-3 mt-3 small">
                <i class="bi bi-info-circle me-2"></i>
                By submitting this form you confirm that all information provided is accurate. Your application number will be shown after submission — please save it.
            </div>
        </div>

        {{-- Navigation buttons --}}
        <div class="d-flex justify-content-between align-items-center mt-3 mb-5">
            <button type="button" class="btn-prev" id="btnPrev" onclick="changeStep(-1)" style="display:none">
                <i class="bi bi-arrow-left me-1"></i> Previous
            </button>
            <div class="ms-auto d-flex gap-3">
                <button type="button" class="btn-next" id="btnNext" onclick="changeStep(1)">
                    Next <i class="bi bi-arrow-right ms-1"></i>
                </button>
                <button type="submit" class="btn-submit" id="btnSubmit" style="display:none">
                    <i class="bi bi-send-fill me-2"></i>Submit Application
                </button>
            </div>
        </div>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentStep = 1;
const totalSteps = 6;

// Programs data for faculty filter
@php
$programsJson = $programs->map(fn($p) => [
    'id'         => $p->id,
    'name'       => $p->name,
    'level'      => $p->level,
    'faculty_id' => $p->department?->faculty?->id,
])->toJson();
@endphp
const programs = {!! $programsJson !!};

/* ── Faculty filter ─────────────────────────────────────────── */
document.getElementById('facultyFilter').addEventListener('change', function () {
    const fid = this.value ? parseInt(this.value) : null;
    const sel = document.getElementById('programSelect');
    const cur = sel.value;
    sel.innerHTML = '<option value="">Select programme</option>';
    programs.forEach(p => {
        if (fid && p.faculty_id !== fid) return;
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.name + ' (' + p.level.charAt(0).toUpperCase() + p.level.slice(1) + ')';
        opt.dataset.faculty = p.faculty_id;
        if (String(p.id) === cur) opt.selected = true;
        sel.appendChild(opt);
    });
});

/* ── File upload visual feedback ────────────────────────────── */
function markUploaded(key, input) {
    const zone = document.getElementById('zone-' + key);
    const text = document.getElementById('zone-text-' + key);
    if (input.files.length) {
        zone.classList.add('has-file');
        text.textContent = input.files[0].name;
    } else {
        zone.classList.remove('has-file');
        text.textContent = 'Click to upload';
    }
}

/* ── Step validation ────────────────────────────────────────── */
function validateStep(step) {
    const section = document.getElementById('section-' + step);
    const required = section.querySelectorAll('[required]');
    let ok = true;
    required.forEach(el => {
        el.classList.remove('is-invalid');
        if (!el.value.trim()) {
            el.classList.add('is-invalid');
            ok = false;
        }
    });
    return ok;
}

/* ── Build review summary ────────────────────────────────────── */
function buildReview() {
    const f = document.getElementById('applyForm');
    const g = n => f.querySelector('[name="' + n + '"]')?.value ?? '—';
    const gtext = n => { const el = f.querySelector('[name="' + n + '"]'); return el?.options?.[el.selectedIndex]?.text ?? el?.value ?? '—'; };

    const rows = [
        ['Full Name',       [g('first_name'), g('middle_name'), g('last_name')].filter(Boolean).join(' ')],
        ['Date of Birth',   g('date_of_birth')],
        ['Gender',          gtext('gender')],
        ['Nationality',     g('nationality')],
        ['Email',           g('email')],
        ['Phone',           g('phone')],
        ['Address',         g('address') || '—'],
        ['Programme',       gtext('program_id')],
        ['Semester/Term',        gtext('semester_id')],
        ['Previous School', g('previous_school')],
        ['Qualification',   gtext('qualification_type')],
        ['Year Completed',  g('year_completed')],
        ['Grade',           g('grade')],
    ];

    let html = '';
    rows.forEach(([l, v]) => {
        html += `<div class="review-row"><span class="lbl">${l}</span><span class="val">${v || '—'}</span></div>`;
    });

    // Uploaded files
    ['certificates','national_id','photo'].forEach(key => {
        const inp = document.getElementById('file-' + key);
        const label = {'certificates':'Certificates','national_id':'National ID','photo':'Photo'}[key];
        const val = inp.files.length ? inp.files[0].name : '<span class="text-muted fw-normal">Not uploaded</span>';
        html += `<div class="review-row"><span class="lbl">${label}</span><span class="val">${val}</span></div>`;
    });

    document.getElementById('review-content').innerHTML = html;
}

/* ── Step navigation ────────────────────────────────────────── */
function changeStep(dir) {
    if (dir === 1 && !validateStep(currentStep)) return;

    // Mark step done
    if (dir === 1) {
        document.getElementById('step-ind-' + currentStep).classList.remove('active');
        document.getElementById('step-ind-' + currentStep).classList.add('done');
        document.getElementById('step-ind-' + currentStep).querySelector('.step-circle').innerHTML = '<i class="bi bi-check-lg"></i>';
        if (currentStep < totalSteps) document.getElementById('conn-' + currentStep).classList.add('done');
    }

    document.getElementById('section-' + currentStep).classList.remove('active');
    currentStep += dir;

    document.getElementById('section-' + currentStep).classList.add('active');
    document.getElementById('step-ind-' + currentStep).classList.remove('done');
    document.getElementById('step-ind-' + currentStep).classList.add('active');

    // Build review on last step
    if (currentStep === totalSteps) buildReview();

    // Buttons
    document.getElementById('btnPrev').style.display   = currentStep > 1 ? '' : 'none';
    document.getElementById('btnNext').style.display   = currentStep < totalSteps ? '' : 'none';
    document.getElementById('btnSubmit').style.display = currentStep === totalSteps ? '' : 'none';

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Init button state
document.getElementById('btnPrev').style.display = 'none';
</script>
</body>
</html>
