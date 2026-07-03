@extends('layouts.app')
@section('title', 'Course Catalog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Course Catalog</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Courses</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
        <i class="bi bi-plus-circle me-1"></i> Add Course
    </button>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Code</th><th>Course Name</th><th>Credits</th><th>Department</th><th>Level</th><th>Type</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td><code class="fw-bold">{{ $course->code }}</code></td>
                    <td>{{ $course->name }}</td>
                    <td class="text-center">{{ $course->credits }}</td>
                    <td>{{ optional($course->department)->name ?? '—' }}</td>
                    <td>{{ ucfirst($course->level ?? '—') }}</td>
                    <td>
                        @php $typeColors = ['theory'=>'warning','practical'=>'primary'] @endphp
                        <span class="badge bg-{{ $typeColors[$course->course_type] ?? 'secondary' }}">{{ ucfirst($course->course_type ?? '—') }}</span>
                    </td>
                    <td><span class="badge bg-{{ $course->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($course->status) }}</span></td>
                    <td>
                        @can('manage-academic')
                        <button class="btn btn-sm btn-outline-primary" onclick="editCourse({{ $course->id }}, '{{ addslashes($course->name) }}', '{{ $course->code }}', {{ $course->credits }}, {{ $course->department_id ?? 'null' }}, '{{ $course->level }}', '{{ $course->course_type }}', '{{ $course->status }}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('academic.courses.destroy', $course) }}" class="d-inline" onsubmit="return confirm('Delete course?')">
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
<div class="modal fade" id="createCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('academic.courses.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Course</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Course Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Code *</label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. CS101" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Credits *</label>
                            <input type="number" name="credits" class="form-control" min="1" max="10" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">Select Department</option>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Level</label>
                            <select name="level" class="form-select">
                                <option value="100">100 Level</option>
                                <option value="200">200 Level</option>
                                <option value="300">300 Level</option>
                                <option value="400">400 Level</option>
                                <option value="500">500 Level (Postgrad)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Course Type</label>
                            <select name="course_type" class="form-select">
                                @foreach($courseTypes as $ct)
                                <option value="{{ $ct }}">{{ ucfirst($ct) }}</option>
                                @endforeach
                            </select>
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
                        <div class="col-12">
                            <label class="form-label">Prerequisites</label>
                            <input type="text" name="prerequisites" class="form-control" placeholder="e.g. CS100, MATH101">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Course</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editCourseForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Course</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Course Name *</label>
                            <input type="text" name="name" id="cEditName" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Code *</label>
                            <input type="text" name="code" id="cEditCode" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Credits *</label>
                            <input type="number" name="credits" id="cEditCredits" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <select name="department_id" id="cEditDept" class="form-select">
                                <option value="">Select Department</option>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Course Type</label>
                            <select name="course_type" id="cEditType" class="form-select">
                                @foreach($courseTypes as $ct)
                                <option value="{{ $ct }}">{{ ucfirst($ct) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" id="cEditStatus" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editCourse(id, name, code, credits, deptId, level, type, status) {
    document.getElementById('cEditName').value = name;
    document.getElementById('cEditCode').value = code;
    document.getElementById('cEditCredits').value = credits;
    if (deptId) document.getElementById('cEditDept').value = deptId;
    document.getElementById('cEditType').value = type;
    document.getElementById('cEditStatus').value = status;
    document.getElementById('editCourseForm').action = '/academic/courses/' + id;
    new bootstrap.Modal(document.getElementById('editCourseModal')).show();
}
</script>
@endpush
@endcan
@endsection
