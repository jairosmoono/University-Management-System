@extends('layouts.app')
@section('title', $assignment->title)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $assignment->title }}</h4>
        <p class="text-muted mb-0">
            {{ optional(optional($assignment->courseOffering)->course)->code }} &bull;
            Due: {{ $assignment->due_date?->format('d M Y H:i') }}
            @if($assignment->is_overdue)<span class="badge bg-danger ms-1">Overdue</span>@endif
        </p>
    </div>
    <div class="d-flex gap-2">
        @if($assignment->status === 'draft')
            <a href="{{ route('academic.assignments.edit', $assignment) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
            <form method="POST" action="{{ route('academic.assignments.publish', $assignment) }}">
                @csrf
                <button class="btn btn-success btn-sm"><i class="bi bi-send me-1"></i>Publish</button>
            </form>
        @elseif($assignment->status === 'published')
            <form method="POST" action="{{ route('academic.assignments.close', $assignment) }}">
                @csrf
                <button class="btn btn-warning btn-sm"><i class="bi bi-lock me-1"></i>Close</button>
            </form>
        @endif
        <a href="{{ route('academic.assignments.performance-sheet', $assignment) }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="bi bi-printer me-1"></i>Performance Sheet</a>
        <a href="{{ route('academic.assignments.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3">
        <h4 class="text-primary fw-bold">{{ $submissions->count() }}</h4><small class="text-muted">Submitted</small>
    </div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3">
        <h4 class="text-secondary fw-bold">{{ $enrolledCount - $submissions->count() }}</h4><small class="text-muted">Pending</small>
    </div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3">
        <h4 class="text-success fw-bold">{{ $submissions->where('status','graded')->count() }}</h4><small class="text-muted">Graded</small>
    </div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3">
        <h4 class="text-danger fw-bold">{{ $submissions->where('status','late')->count() }}</h4><small class="text-muted">Late</small>
    </div></div>
</div>

@if($assignment->description)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent"><h6 class="mb-0">Instructions</h6></div>
    <div class="card-body">{{ $assignment->description }}</div>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Submissions</h6>
        <span class="text-muted small">Total marks: {{ $assignment->total_marks }}</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Student</th><th>Submitted At</th><th>Status</th><th>Marks</th><th>Feedback</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ optional(optional($sub->student)->user)->name }}</div>
                        <small class="text-muted">{{ optional($sub->student)->student_id }}</small>
                    </td>
                    <td><small>{{ $sub->submitted_at?->format('d M Y H:i') }}</small></td>
                    <td>
                        @php $sc2 = ['submitted'=>'primary','graded'=>'success','late'=>'danger','pending'=>'secondary'] @endphp
                        <span class="badge bg-{{ $sc2[$sub->status] ?? 'secondary' }}">{{ ucfirst($sub->status) }}</span>
                    </td>
                    <td>
                        @if($sub->marks_obtained !== null)
                            <span class="fw-semibold">{{ $sub->marks_obtained }}</span><span class="text-muted">/{{ $assignment->total_marks }}</span>
                            <small class="d-block text-muted">{{ $sub->percentage }}%</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td style="max-width:180px"><small class="text-muted">{{ Str::limit($sub->feedback, 60) ?: '—' }}</small></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#gradeModal{{ $sub->id }}">
                            <i class="bi bi-{{ $sub->status === 'graded' ? 'pencil' : 'star' }}"></i>
                            {{ $sub->status === 'graded' ? 'Re-grade' : 'Grade' }}
                        </button>
                        @if($sub->file_path)
                        <a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary ms-1">
                            <i class="bi bi-download"></i>
                        </a>
                        @endif
                    </td>
                </tr>

                {{-- Grade Modal --}}
                <div class="modal fade" id="gradeModal{{ $sub->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('academic.assignments.grade', [$assignment, $sub]) }}">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">Grade — {{ optional(optional($sub->student)->user)->name }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    @if($sub->submission_text)
                                    <div class="mb-3 p-2 bg-light rounded" style="font-size:0.88rem;max-height:120px;overflow-y:auto">
                                        {{ $sub->submission_text }}
                                    </div>
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label">Marks Obtained <span class="text-danger">*</span></label>
                                        <input type="number" name="marks_obtained" class="form-control"
                                            value="{{ $sub->marks_obtained }}" min="0" max="{{ $assignment->total_marks }}" step="0.5" required>
                                        <small class="text-muted">Out of {{ $assignment->total_marks }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Feedback</label>
                                        <textarea name="feedback" class="form-control" rows="3">{{ $sub->feedback }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Grade</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No submissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
