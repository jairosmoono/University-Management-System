@extends('layouts.app')
@section('title', 'Assignments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Assignments</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Assignments</li>
        </ol></nav>
    </div>
    @hasanyrole('lecturer|registrar|super-admin')
    <a href="{{ route('academic.assignments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> New Assignment
    </a>
    @endhasanyrole
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            @if($programs->isNotEmpty())
            <div class="col-md-3">
                <select name="program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                        {{ $prog->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-4">
                <select name="offering_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Course Offerings</option>
                    @foreach($offerings as $off)
                    <option value="{{ $off->id }}" {{ request('offering_id') == $off->id ? 'selected' : '' }}>
                        {{ optional($off->course)->code }} — {{ optional($off->course)->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('academic.assignments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table datatable table-hover mb-0">
            <thead class="table-light">
                <tr><th>Title</th><th>Course</th><th>Due Date</th><th>Total Marks</th><th>Status</th><th>Submissions</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($assignments as $a)
                <tr>
                    <td class="fw-semibold">{{ $a->title }}</td>
                    <td>{{ optional(optional($a->courseOffering)->course)->code }}</td>
                    <td>
                        {{ $a->due_date?->format('d M Y H:i') }}
                        @if($a->is_overdue && $a->status === 'published')
                            <span class="badge bg-danger ms-1">Overdue</span>
                        @endif
                    </td>
                    <td>{{ $a->total_marks }}</td>
                    <td>
                        @php $sc = ['draft'=>'secondary','published'=>'success','closed'=>'dark'] @endphp
                        <span class="badge bg-{{ $sc[$a->status] ?? 'secondary' }}">{{ ucfirst($a->status) }}</span>
                    </td>
                    <td><span class="badge bg-info">{{ $a->submissions_count }}</span></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('academic.assignments.show', $a) }}"><i class="bi bi-eye me-2"></i>View Submissions</a></li>
                                @if($a->status === 'draft')
                                <li><a class="dropdown-item" href="{{ route('academic.assignments.edit', $a) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li>
                                    <form method="POST" action="{{ route('academic.assignments.publish', $a) }}">
                                        @csrf
                                        <button class="dropdown-item text-success"><i class="bi bi-send me-2"></i>Publish</button>
                                    </form>
                                </li>
                                @endif
                                @if($a->status === 'published')
                                <li>
                                    <form method="POST" action="{{ route('academic.assignments.close', $a) }}">
                                        @csrf
                                        <button class="dropdown-item text-warning"><i class="bi bi-lock me-2"></i>Close</button>
                                    </form>
                                </li>
                                @endif
                                @hasanyrole('lecturer|super-admin')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('academic.assignments.destroy', $a) }}" onsubmit="return confirm('Delete this assignment and all its submissions?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </form>
                                </li>
                                @endhasanyrole
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No assignments found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $assignments->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
