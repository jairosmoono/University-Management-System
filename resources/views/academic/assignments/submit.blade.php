@extends('layouts.app')
@section('title', 'Submit Assignment')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $assignment->title }}</h4>
        <p class="text-muted mb-0">
            {{ optional(optional($assignment->courseOffering)->course)->code }} &bull;
            Due: <span class="text-{{ $assignment->is_overdue ? 'danger' : 'muted' }}">{{ $assignment->due_date?->format('d M Y H:i') }}</span>
        </p>
    </div>
    <a href="{{ route('academic.assignments.my') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    {{-- Assignment details --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent"><h6 class="mb-0">Instructions</h6></div>
            <div class="card-body" style="font-size:0.9rem">
                {{ $assignment->description ?: 'No instructions provided.' }}
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <dl class="row mb-0" style="font-size:0.88rem">
                    <dt class="col-6 text-muted fw-normal">Total Marks</dt><dd class="col-6">{{ $assignment->total_marks }}</dd>
                    <dt class="col-6 text-muted fw-normal">Due Date</dt><dd class="col-6">{{ $assignment->due_date?->format('d M Y H:i') }}</dd>
                    <dt class="col-6 text-muted fw-normal">Status</dt>
                    <dd class="col-6">
                        @if($assignment->is_overdue)<span class="text-danger fw-semibold">Overdue</span>
                        @else<span class="text-success">Open</span>@endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Submission form / result --}}
    <div class="col-md-8">
        @if($submission && $submission->status === 'graded')
        {{-- Show grade & feedback --}}
        <div class="card border-0 shadow-sm mb-3 border-start border-success border-3">
            <div class="card-body">
                <h6 class="text-success mb-3"><i class="bi bi-patch-check me-2"></i>Graded</h6>
                <div class="row g-3 text-center mb-3">
                    <div class="col-4">
                        <div class="fs-2 fw-bold text-success">{{ $submission->marks_obtained }}</div>
                        <small class="text-muted">Marks obtained</small>
                    </div>
                    <div class="col-4">
                        <div class="fs-2 fw-bold">{{ $assignment->total_marks }}</div>
                        <small class="text-muted">Total marks</small>
                    </div>
                    <div class="col-4">
                        <div class="fs-2 fw-bold text-{{ $submission->percentage >= 50 ? 'success' : 'danger' }}">{{ $submission->percentage }}%</div>
                        <small class="text-muted">Percentage</small>
                    </div>
                </div>
                @if($submission->feedback)
                <div class="p-3 bg-light rounded">
                    <p class="mb-1 fw-semibold small">Lecturer Feedback:</p>
                    <p class="mb-0" style="font-size:0.9rem">{{ $submission->feedback }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if(!$submission || $submission->status !== 'graded')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">
                    @if($submission) Edit Submission @else Submit Assignment @endif
                </h6>
            </div>
            <div class="card-body">
                @if($submission)
                <div class="alert alert-info py-2 mb-3" style="font-size:0.88rem">
                    <i class="bi bi-info-circle me-1"></i>
                    Already submitted on {{ $submission->submitted_at?->format('d M Y H:i') }}.
                    You can update your submission below.
                </div>
                @endif

                @if($assignment->is_overdue && !$submission)
                <div class="alert alert-danger">This assignment is past its due date. Submissions are no longer accepted.</div>
                @else
                <form method="POST" action="{{ route('academic.assignments.submit', $assignment) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Your Answer / Response</label>
                        <textarea name="submission_text" class="form-control" rows="6"
                            placeholder="Type your answer here...">{{ old('submission_text', $submission?->submission_text) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload File <small class="text-muted">(PDF, Word, ZIP, image — max 10MB)</small></label>
                        @if($submission?->file_path)
                        <div class="mb-2">
                            <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-paperclip me-1"></i>Current file
                            </a>
                            <small class="text-muted ms-2">Upload a new file to replace it</small>
                        </div>
                        @endif
                        <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.txt,.zip,.jpg,.jpeg,.png">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>
                            @if($submission) Update Submission @else Submit @endif
                        </button>
                        <a href="{{ route('academic.assignments.my') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
