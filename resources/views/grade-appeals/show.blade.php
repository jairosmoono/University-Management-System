@extends('layouts.app')
@section('title', 'Grade Appeal #' . $gradeAppeal->id)
@section('page-title', 'Grade Appeal Detail')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-flag me-2" style="color:var(--secondary)"></i>Appeal #{{ $gradeAppeal->id }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.grade-appeals.index') }}">Grade Appeals</a></li>
            <li class="breadcrumb-item active">#{{ $gradeAppeal->id }}</li>
        </ol></nav>
    </div>
    {!! $gradeAppeal->status_badge !!}
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        {{-- Appeal Details --}}
        <div class="card mb-4">
            <div class="card-header py-3"><h5 class="mb-0 fw-semibold">Appeal Information</h5></div>
            <div class="card-body">
                <table class="table table-borderless mb-0" style="font-size:0.9rem">
                    <tr><th width="160" class="text-muted">Student</th><td>{{ $gradeAppeal->student->full_name }} <span class="text-muted">({{ $gradeAppeal->student->student_id }})</span></td></tr>
                    <tr><th class="text-muted">Course</th><td>{{ $gradeAppeal->courseOffering->course->name }} <span class="text-muted">({{ $gradeAppeal->courseOffering->course->code }})</span></td></tr>
                    <tr><th class="text-muted">Semester/Term</th><td>{{ $gradeAppeal->courseOffering->semester->name ?? '—' }}</td></tr>
                    <tr><th class="text-muted">Submitted</th><td>{{ $gradeAppeal->created_at->format('d M Y, H:i') }}</td></tr>
                    <tr>
                        <th class="text-muted">Original Grade</th>
                        <td><span class="fw-bold fs-5">{{ $gradeAppeal->original_grade ?? '—' }}</span>
                            @if($gradeAppeal->original_total) <span class="text-muted ms-2">({{ $gradeAppeal->original_total }}%)</span>@endif</td>
                    </tr>
                    @if($gradeAppeal->revised_grade)
                    <tr>
                        <th class="text-muted">Revised Grade</th>
                        <td><span class="fw-bold fs-5 text-success">{{ $gradeAppeal->revised_grade }}</span>
                            <span class="text-muted ms-2">({{ $gradeAppeal->revised_total }}%)</span></td>
                    </tr>
                    @endif
                </table>
                <hr>
                <div class="fw-semibold mb-2">Reason for Appeal</div>
                <div class="text-secondary" style="font-size:0.9rem;white-space:pre-line">{{ $gradeAppeal->reason }}</div>

                @if($gradeAppeal->supporting_document)
                <div class="mt-3">
                    <a href="{{ asset('storage/' . $gradeAppeal->supporting_document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-paperclip me-1"></i>View Supporting Document
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Admin Notes --}}
        @if($gradeAppeal->admin_notes)
        <div class="card">
            <div class="card-header py-3"><h5 class="mb-0 fw-semibold">Review Notes</h5></div>
            <div class="card-body">
                <div class="text-muted" style="font-size:0.82rem">Reviewed by {{ $gradeAppeal->reviewedBy?->name ?? '—' }} on {{ $gradeAppeal->reviewed_at?->format('d M Y, H:i') }}</div>
                <p class="mt-2 mb-0" style="white-space:pre-line">{{ $gradeAppeal->admin_notes }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Review Panel (admin only) --}}
    @hasanyrole('super-admin|registrar')
    @if(in_array($gradeAppeal->status, ['pending', 'under_review']))
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header py-3"><h5 class="mb-0 fw-semibold">Review This Appeal</h5></div>
            <div class="card-body">
                <form action="{{ route('academic.grade-appeals.review', $gradeAppeal) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Decision <span class="text-danger">*</span></label>
                        <select name="status" id="appealStatus" class="form-select" onchange="toggleRevision(this.value)" required>
                            <option value="">— Select outcome —</option>
                            <option value="under_review">Mark as Under Review</option>
                            <option value="approved">Approve (revise grade)</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>

                    <div id="revisionFields" class="d-none">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Revised Grade</label>
                            <input type="text" name="revised_grade" class="form-control" placeholder="e.g. B+" maxlength="5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Revised Total Score (%)</label>
                            <input type="number" name="revised_total" class="form-control" placeholder="0–100" min="0" max="100" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Review Notes <span class="text-danger">*</span></label>
                        <textarea name="admin_notes" rows="5" class="form-control" placeholder="Explain your decision clearly…" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check2-circle me-1"></i>Submit Review
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endhasanyrole
</div>

<script>
function toggleRevision(val) {
    const f = document.getElementById('revisionFields');
    if (val === 'approved') { f.classList.remove('d-none'); }
    else { f.classList.add('d-none'); }
}
</script>
@endsection
