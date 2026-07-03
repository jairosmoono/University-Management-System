@extends('layouts.app')
@section('title', 'Take Attendance')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Take Attendance</h4>
        <p class="text-muted mb-0">
            {{ optional($offering->course)->code }} — {{ optional($offering->course)->name }}
            @isset($byProgram)
                &bull; <span class="badge bg-success">{{ $program->name }}</span>
            @endisset
            &bull; {{ $today }}
        </p>
    </div>
    <a href="{{ route('academic.attendance.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

@if($existingSession)
    <div class="alert alert-warning"><i class="bi bi-info-circle me-2"></i>Attendance already taken for this session today. Saving will update existing records.</div>
@endif

<form action="{{ route('academic.attendance.save') }}" method="POST">
    @csrf
    <input type="hidden" name="course_offering_id" value="{{ $offering->id }}">
    <input type="hidden" name="date" value="{{ $today }}">
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Session Type</label>
            <select name="type" class="form-select">
                @foreach(['lecture','tutorial','lab','seminar'] as $t)
                    <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label">Topic</label>
            <input type="text" name="topic" class="form-control" placeholder="Topic covered today">
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Students ({{ $students->count() }})</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('present')">All Present</button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">All Absent</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>#</th><th>Student</th><th>ID</th><th>Status</th><th>Notes</th></tr></thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ optional($student->user)->name ?: $student->student_id }}
                            </td>
                            <td><code>{{ $student->student_id }}</code></td>
                            <td>
                                <div class="btn-group" role="group">
                                    @foreach(['present','absent','late','excused'] as $status)
                                    <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" value="{{ $status }}" id="att_{{ $student->id }}_{{ $status }}" {{ $loop->first ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-{{ $status === 'present' ? 'success' : ($status === 'absent' ? 'danger' : ($status === 'late' ? 'warning' : 'info')) }}" for="att_{{ $student->id }}_{{ $status }}">
                                        {{ ucfirst($status) }}
                                    </label>
                                    @endforeach
                                </div>
                            </td>
                            <td><input type="text" name="remarks[{{ $student->id }}]" class="form-control form-control-sm" placeholder="Remarks" style="min-width:120px"></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No students registered for this course.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if($students->count() > 0)
    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save Attendance</button>
    @endif
</form>

@push('scripts')
<script>
function markAll(status) {
    document.querySelectorAll('input[type="radio"][value="' + status + '"]').forEach(r => r.checked = true);
}
</script>
@endpush
@endsection
