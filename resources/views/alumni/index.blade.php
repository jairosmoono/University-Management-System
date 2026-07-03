@extends('layouts.app')
@section('title', 'Alumni')

@push('styles')
<style>
.searchable-select { position: relative; }
.searchable-select .ss-input-wrap {
    display: flex; align-items: center; gap: 6px;
    border: 1px solid #dee2e6; border-radius: 0.375rem;
    padding: 0.375rem 0.75rem; background: #fff; cursor: text;
}
.searchable-select .ss-input-wrap.is-invalid { border-color: #dc3545; }
.searchable-select .ss-input-wrap input {
    border: none; outline: none; flex: 1; font-size: 0.875rem; background: transparent; padding: 0; min-width: 0;
}
.searchable-select .ss-badge {
    display: flex; align-items: center; gap: 4px;
    background: #e9ecef; border-radius: 0.25rem; padding: 2px 6px; font-size: 0.8rem; white-space: nowrap;
}
.searchable-select .ss-badge button { background: none; border: none; padding: 0; line-height: 1; color: #6c757d; cursor: pointer; font-size: 1rem; }
.searchable-select .ss-badge button:hover { color: #dc3545; }
.searchable-select .ss-dropdown {
    position: absolute; top: 100%; left: 0; right: 0; z-index: 9999;
    background: #fff; border: 1px solid #dee2e6; border-radius: 0.375rem;
    box-shadow: 0 4px 16px rgba(0,0,0,.12); max-height: 220px; overflow-y: auto; display: none; margin-top: 2px;
}
.searchable-select .ss-dropdown.open { display: block; }
.searchable-select .ss-option { padding: 7px 12px; cursor: pointer; font-size: 0.875rem; border-bottom: 1px solid #f0f0f0; }
.searchable-select .ss-option:last-child { border-bottom: none; }
.searchable-select .ss-option:hover, .searchable-select .ss-option.focused { background: #e8f0fe; }
.searchable-select .ss-option .ss-id { font-size: 0.75rem; color: #6c757d; margin-left: 4px; }
.searchable-select .ss-empty { padding: 10px 12px; font-size: 0.85rem; color: #6c757d; text-align: center; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Alumni Directory</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Alumni</li>
        </ol></nav>
    </div>
    @can('manage-alumni')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerAlumniModal">
        <i class="bi bi-plus-circle me-1"></i> Register Alumni
    </button>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-primary fw-bold">{{ $stats['total'] }}</h4><small class="text-muted">Total Alumni</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-success fw-bold">{{ $stats['employed'] }}</h4><small class="text-muted">Employed</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-warning fw-bold">{{ $stats['in_business'] }}</h4><small class="text-muted">In Business</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-info fw-bold">{{ $stats['further_studies'] }}</h4><small class="text-muted">Further Studies</small></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="graduation_year" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    @for($y = date('Y'); $y >= 2000; $y--)
                    <option value="{{ $y }}" {{ request('graduation_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search alumni..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Search</button>
                <a href="{{ route('alumni.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Alumni ID</th><th>Name</th><th>Program</th><th>Grad. Year</th><th>Employer</th><th>City</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumni as $a)
                <tr>
                    <td><code>{{ optional($a->student)->student_id }}</code></td>
                    <td class="fw-semibold">{{ optional(optional($a->student)->user)->name }}</td>
                    <td>{{ optional(optional($a->student)->program)->name }}</td>
                    <td>{{ $a->graduation_year }}</td>
                    <td>{{ $a->current_employer ?? '—' }}</td>
                    <td>{{ $a->city ?? '—' }}</td>
                    <td>
                        @php $ec = ['employed'=>'success','self_employed'=>'primary','unemployed'=>'danger','further_studies'=>'info'] @endphp
                        <span class="badge bg-{{ $ec[$a->employment_status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$a->employment_status)) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('alumni.show', $a) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        @can('manage-alumni')
                        <a href="{{ route('alumni.edit', $a) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $alumni->withQueryString()->links() }}
    </div>
</div>
@can('manage-alumni')
<div class="modal fade" id="registerAlumniModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('alumni.store') }}" id="alumniForm">
            @csrf
            <input type="hidden" name="student_id" id="alumni_student_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register Alumni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Student <span class="text-danger">*</span></label>
                        <div class="searchable-select" id="alumniStudentPicker">
                            <div class="ss-input-wrap" id="alumniStudentWrap">
                                <input type="text" id="alumniStudentSearch" placeholder="Type name or student ID…" autocomplete="off">
                            </div>
                            <div class="ss-dropdown" id="alumniStudentDropdown">
                                @forelse($students as $s)
                                <div class="ss-option" data-value="{{ $s->id }}" data-label="{{ optional($s->user)->name }}">
                                    {{ optional($s->user)->name }}
                                    <span class="ss-id">{{ $s->student_id }} — {{ optional($s->program)->name }}</span>
                                </div>
                                @empty
                                <div class="ss-empty">All students are already registered as alumni.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="invalid-feedback d-none" id="alumniStudentError">Please select a student.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Graduation Year <span class="text-danger">*</span></label>
                            <select name="graduation_year" class="form-select" required>
                                @for($y = date('Y'); $y >= 2000; $y--)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Employment Status</label>
                            <select name="employment_status" class="form-select">
                                <option value="employed">Employed</option>
                                <option value="self_employed">Self Employed</option>
                                <option value="unemployed">Unemployed</option>
                                <option value="further_studies">Further Studies</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Job Title</label>
                            <input type="text" name="job_title" class="form-control" placeholder="e.g. Software Engineer">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Current Employer</label>
                            <input type="text" name="current_employer" class="form-control" placeholder="Company / Organisation">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="Zambia">
                        </div>
                        <div class="col-12">
                            <label class="form-label">LinkedIn URL</label>
                            <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/…">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
(function () {
    const search   = document.getElementById('alumniStudentSearch');
    const dropdown = document.getElementById('alumniStudentDropdown');
    const wrap     = document.getElementById('alumniStudentWrap');
    const hidden   = document.getElementById('alumni_student_id');
    const errEl    = document.getElementById('alumniStudentError');
    if (!search) return;

    const allOptions = Array.from(dropdown.querySelectorAll('.ss-option'));

    function filter(q) {
        const t = q.toLowerCase();
        let vis = 0;
        allOptions.forEach(o => { const m = !t || o.textContent.toLowerCase().includes(t); o.style.display = m ? '' : 'none'; if (m) vis++; });
        let empty = dropdown.querySelector('.ss-empty');
        if (vis === 0 && !empty) { empty = document.createElement('div'); empty.className='ss-empty'; empty.textContent='No results.'; dropdown.appendChild(empty); }
        else if (empty) empty.style.display = vis === 0 ? '' : 'none';
    }

    function open() { dropdown.classList.add('open'); filter(search.value); }
    function close() { dropdown.classList.remove('open'); }

    function select(opt) {
        hidden.value = opt.dataset.value;
        const badge = document.createElement('span');
        badge.className = 'ss-badge';
        badge.innerHTML = `${opt.dataset.label} <button type="button">&times;</button>`;
        badge.querySelector('button').onclick = e => { e.stopPropagation(); hidden.value=''; badge.remove(); search.placeholder='Type name or student ID…'; search.focus(); open(); wrap.classList.remove('is-invalid'); errEl.classList.add('d-none'); };
        wrap.querySelector('.ss-badge')?.remove();
        wrap.insertBefore(badge, search);
        search.value = ''; search.placeholder = '';
        wrap.classList.remove('is-invalid'); errEl.classList.add('d-none');
        close();
    }

    search.addEventListener('focus', open);
    search.addEventListener('input', () => { if (!dropdown.classList.contains('open')) open(); filter(search.value); });
    dropdown.addEventListener('mousedown', e => { const o = e.target.closest('.ss-option'); if (o) { e.preventDefault(); select(o); } });
    document.addEventListener('mousedown', e => { if (!wrap.closest('.searchable-select').contains(e.target)) close(); });

    search.addEventListener('keydown', e => {
        const vis = allOptions.filter(o => o.style.display !== 'none');
        const fi  = dropdown.querySelector('.ss-option.focused');
        let idx   = vis.indexOf(fi);
        if (e.key === 'ArrowDown') { e.preventDefault(); fi?.classList.remove('focused'); vis[Math.min(idx+1,vis.length-1)]?.classList.add('focused'); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); fi?.classList.remove('focused'); vis[Math.max(idx-1,0)]?.classList.add('focused'); }
        else if (e.key === 'Enter' && fi) { e.preventDefault(); select(fi); }
        else if (e.key === 'Escape') close();
    });

    document.getElementById('alumniForm')?.addEventListener('submit', e => {
        if (!hidden.value) { e.preventDefault(); wrap.classList.add('is-invalid'); errEl.classList.remove('d-none'); }
    });

    document.getElementById('registerAlumniModal')?.addEventListener('hidden.bs.modal', () => {
        hidden.value = ''; search.value = ''; search.placeholder = 'Type name or student ID…';
        wrap.querySelector('.ss-badge')?.remove(); wrap.classList.remove('is-invalid'); errEl.classList.add('d-none');
    });
})();
</script>
@endpush
