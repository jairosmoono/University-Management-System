@extends('layouts.app')
@section('title', 'Examinations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Examinations</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Examinations</li>
        </ol></nav>
    </div>
    @can('manage-exams')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createExamModal">
        <i class="bi bi-plus-circle me-1"></i> Schedule Exam
    </button>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Exam Name</th><th>Course</th><th>Date</th><th>Time</th><th>Venue</th><th>Invigilator</th><th>Max Marks</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($examinations as $exam)
                <tr>
                    <td class="fw-semibold">{{ $exam->name }}</td>
                    <td>{{ optional(optional($exam->courseOffering)->course)->code }}</td>
                    <td>{{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($exam->end_time)->format('H:i') }}</td>
                    <td>{{ $exam->venue ?? '—' }}</td>
                    <td>{{ optional(optional($exam->invigilator)->user)->name ?? '—' }}</td>
                    <td>{{ $exam->max_marks }}</td>
                    <td>
                        @php $statusColors = ['scheduled'=>'primary','ongoing'=>'warning','completed'=>'success','cancelled'=>'danger'] @endphp
                        <span class="badge bg-{{ $statusColors[$exam->status] ?? 'secondary' }}">{{ ucfirst($exam->status) }}</span>
                    </td>
                    <td>
                        @can('manage-exams')
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('academic.examinations.edit', $exam) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item" href="{{ route('academic.examinations.seating', $exam) }}"><i class="bi bi-grid me-2"></i>Seating Plan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('academic.examinations.destroy', $exam) }}" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@can('manage-exams')
<div class="modal fade" id="createExamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('academic.examinations.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Schedule Examination</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Exam Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. CS101 Final Examination" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="">— Select Type —</option>
                                @foreach($examTypes as $et)
                                    <option value="{{ $et->code }}">{{ $et->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Course Offering *</label>
                            <select name="course_offering_id" class="form-select" required>
                                <option value="">— Select Course —</option>
                                @foreach($offerings as $o)
                                <option value="{{ $o->id }}">{{ optional($o->course)->code }} — {{ optional($o->course)->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Exam Date *</label>
                            <input type="date" name="exam_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Time *</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Time *</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Venue</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g. Main Exam Hall">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Invigilator</label>
                            <select name="invigilator_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($staff as $s)
                                <option value="{{ $s->id }}">{{ optional($s->user)->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total Marks *</label>
                            <input type="number" name="max_marks" class="form-control" value="100" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pass Mark</label>
                            <input type="number" name="passing_marks" class="form-control" value="40" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
