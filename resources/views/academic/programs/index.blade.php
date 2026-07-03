@extends('layouts.app')
@section('title', 'Programs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Programs / Courses of Study</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Programs</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProgramModal">
        <i class="bi bi-plus-circle me-1"></i> Add Program
    </button>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="faculty_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Faculties</option>
                    @foreach($faculties as $f)
                    <option value="{{ $f->id }}" {{ request('faculty_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="level" class="form-select form-select-sm">
                    <option value="">All Levels</option>
                    @foreach(\App\Models\Program::levelLabels() as $val => $lbl)
                    <option value="{{ $val }}" {{ request('level') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('academic.programs.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Program Name</th><th>Code</th><th>Department</th><th>Level</th><th>Duration</th><th>Min Credits</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programs as $i => $program)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $program->name }}</td>
                    <td><code>{{ $program->code }}</code></td>
                    <td>{{ optional($program->department)->name }}</td>
                    <td>
                        @php $levelColors = ['degree'=>'primary','diploma'=>'success','certificate'=>'info','craft_certificate'=>'warning','trade_test_certificate'=>'dark'] @endphp
                        <span class="badge bg-{{ $levelColors[$program->level] ?? 'secondary' }}">{{ $program->level_label }}</span>
                    </td>
                    <td>{{ $program->duration_label }}</td>
                    <td>{{ $program->credit_hours_required }} hrs</td>
                    <td><span class="badge bg-{{ $program->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($program->status) }}</span></td>
                    <td>
                        @can('manage-academic')
                        <a href="{{ route('academic.programs.edit', $program) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('academic.programs.destroy', $program) }}" class="d-inline" onsubmit="return confirm('Delete this program?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@can('manage-academic')
<div class="modal fade" id="createProgramModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('academic.programs.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Program</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Program Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Code *</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department *</label>
                            <select name="department_id" class="form-select" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Level *</label>
                            <select name="level" class="form-select" required>
                                @foreach(\App\Models\Program::levelLabels() as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Duration *</label>
                            <div class="input-group">
                                <input type="number" name="duration_years" class="form-control" min="1" required>
                                <select name="duration_unit" class="form-select" style="max-width:110px">
                                    <option value="years">Years</option>
                                    <option value="months">Months</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Credit Hours Required *</label>
                            <input type="number" name="credit_hours_required" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Program</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
