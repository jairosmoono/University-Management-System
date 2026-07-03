@extends('layouts.app')
@section('title', 'Graduation Application')

@section('content')
<div class="mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h4 class="mb-1">Graduation Application #{{ $application->id }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('graduation.index') }}">Graduation</a></li>
            <li class="breadcrumb-item active">Application #{{ $application->id }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if(!auth()->user()->hasRole('student'))
        <a href="{{ route('graduation.certificate.preview', $application) }}" class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="bi bi-eye me-1"></i>Preview Certificate
        </a>
        @endif
        @if(in_array($application->status, ['approved', 'graduated']))
        <a href="{{ route('graduation.certificate', $application) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i>Certificate PDF
        </a>
        @endif
        <span class="badge bg-{{ \App\Models\GraduationApplication::statusColor($application->status) }} fs-6 px-3 py-2">
            {{ \App\Models\GraduationApplication::statusLabel($application->status) }}
        </span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    {{-- Left: Student info + eligibility --}}
    <div class="col-lg-4">
        {{-- Student card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <img src="{{ $application->student?->photo_url }}" class="rounded-circle mb-3" width="80" height="80" style="object-fit:cover">
                <h6 class="fw-bold mb-0">{{ $application->student?->full_name }}</h6>
                <div class="text-muted small mb-2">{{ $application->student?->student_id }}</div>
                <div class="small text-muted">{{ $application->program?->name }}</div>
                <div class="small text-muted">{{ $application->program?->department?->name }}</div>
            </div>
            <div class="card-footer bg-white border-0 pb-3">
                <div class="row text-center g-0">
                    <div class="col border-end">
                        <div class="fw-bold {{ $application->cgpa >= 1.5 ? 'text-success' : 'text-danger' }}">{{ number_format($application->cgpa, 2) }}</div>
                        <div class="text-muted" style="font-size:0.75rem">CGPA</div>
                    </div>
                    <div class="col">
                        <div class="fw-bold">{{ $application->credits_earned }}</div>
                        <div class="text-muted" style="font-size:0.75rem">Credits</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live eligibility snapshot --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 pb-1 px-3">
                <h6 class="fw-semibold small mb-0 text-uppercase text-muted">Live Eligibility Check</h6>
            </div>
            <div class="card-body px-3 pb-3">
                @php
                    $checks = [
                        ['label' => 'Credits', 'ok' => $eligibility['credits_ok'],
                         'note' => $eligibility['credits_earned'].' / '.$eligibility['required_credits']],
                        ['label' => 'CGPA ≥ 1.5', 'ok' => $eligibility['cgpa_ok'],
                         'note' => $eligibility['cgpa']],
                        ['label' => 'Finance', 'ok' => $eligibility['finance_ok'],
                         'note' => $eligibility['finance_ok'] ? 'Cleared' : 'KES '.number_format($eligibility['outstanding_bal'],2).' due'],
                        ['label' => 'Library', 'ok' => $eligibility['library_ok'],
                         'note' => $eligibility['library_ok'] ? 'Cleared' : $eligibility['active_loans'].' loans'],
                        ['label' => 'Academic', 'ok' => $eligibility['academic_ok'],
                         'note' => $eligibility['academic_ok'] ? 'All passed' : $eligibility['failed_count'].' failed'],
                    ];
                @endphp
                @foreach($checks as $chk)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-{{ $chk['ok'] ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }}"></i>
                        <span class="small fw-semibold">{{ $chk['label'] }}</span>
                    </div>
                    <span class="small text-muted">{{ $chk['note'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Application metadata --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body px-3 py-3">
                <div class="small text-muted mb-1">Academic Year</div>
                <div class="fw-semibold mb-3">{{ $application->academicYear?->name }}</div>
                <div class="small text-muted mb-1">Ceremony</div>
                <div class="fw-semibold mb-3">{{ $application->ceremony?->name ?? '—' }}</div>
                <div class="small text-muted mb-1">Graduation Date</div>
                <div class="fw-semibold mb-3">{{ $application->graduation_date?->format('d M Y') ?? '—' }}</div>
                @if($application->approved_at)
                <div class="small text-muted mb-1">Approved By</div>
                <div class="fw-semibold mb-3">{{ $application->approvedBy?->name ?? '—' }}</div>
                @endif
                @if($application->notes)
                <div class="small text-muted mb-1">Notes</div>
                <div class="small">{{ $application->notes }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Clearance + Actions --}}
    <div class="col-lg-8">

        @if($application->status === 'rejected')
        <div class="alert alert-danger">
            <i class="bi bi-x-circle me-2"></i><strong>Rejected:</strong> {{ $application->rejection_reason }}
        </div>
        @endif

        {{-- Clearance panel --}}
        @hasrole('super-admin|registrar|finance-officer|librarian')
        @if(!in_array($application->status, ['graduated', 'rejected']))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 pb-1 px-4">
                <h6 class="fw-semibold mb-0">Clearance Management</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST" action="{{ route('graduation.clearance', $application) }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 text-center {{ $application->finance_cleared ? 'border-success bg-success bg-opacity-10' : '' }}">
                                <i class="bi bi-cash-coin fs-4 {{ $application->finance_cleared ? 'text-success' : 'text-muted' }}"></i>
                                <div class="fw-semibold mt-2 mb-2">Finance</div>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" name="finance_cleared" value="1" id="fin"
                                        {{ $application->finance_cleared ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2" for="fin">
                                        {{ $application->finance_cleared ? 'Cleared' : 'Not cleared' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 text-center {{ $application->library_cleared ? 'border-success bg-success bg-opacity-10' : '' }}">
                                <i class="bi bi-book fs-4 {{ $application->library_cleared ? 'text-success' : 'text-muted' }}"></i>
                                <div class="fw-semibold mt-2 mb-2">Library</div>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" name="library_cleared" value="1" id="lib"
                                        {{ $application->library_cleared ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2" for="lib">
                                        {{ $application->library_cleared ? 'Cleared' : 'Not cleared' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 text-center {{ $application->academic_cleared ? 'border-success bg-success bg-opacity-10' : '' }}">
                                <i class="bi bi-mortarboard fs-4 {{ $application->academic_cleared ? 'text-success' : 'text-muted' }}"></i>
                                <div class="fw-semibold mt-2 mb-2">Academic</div>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" name="academic_cleared" value="1" id="acad"
                                        {{ $application->academic_cleared ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2" for="acad">
                                        {{ $application->academic_cleared ? 'Cleared' : 'Not cleared' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Assign Ceremony</label>
                            <select name="ceremony_id" class="form-select form-select-sm">
                                <option value="">None</option>
                                @foreach($ceremonies as $c)
                                <option value="{{ $c->id }}" {{ $application->ceremony_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} — {{ $c->ceremony_date->format('d M Y') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Notes</label>
                            <input type="text" name="notes" class="form-control form-control-sm" value="{{ $application->notes }}" placeholder="Optional note…">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save me-1"></i>Save Clearance
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endhasrole

        {{-- Action buttons --}}
        @hasrole('super-admin|registrar')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 pb-1 px-4">
                <h6 class="fw-semibold mb-0">Actions</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">

                    {{-- Move to review --}}
                    @if($application->status === 'pending')
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('graduation.review', $application) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-info w-100">
                                <i class="bi bi-eye me-1"></i>Mark as Under Review
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Approve --}}
                    @if(in_array($application->status, ['pending', 'under_review', 'cleared']))
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('graduation.approve', $application) }}" id="approveForm">
                            @csrf
                            <input type="hidden" name="graduation_date" id="approveDate">
                            <input type="hidden" name="ceremony_id" id="approveCeremony">
                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-1"></i>Approve Application
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Mark Graduated --}}
                    @if($application->status === 'approved')
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('graduation.graduate', $application) }}" id="graduateForm">
                            @csrf
                            <input type="hidden" name="graduation_date" id="gradDate">
                            <button type="button" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#graduateModal">
                                <i class="bi bi-mortarboard me-1"></i>Mark as Graduated
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Reject --}}
                    @if(!in_array($application->status, ['graduated', 'rejected']))
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i>Reject Application
                        </button>
                    </div>
                    @endif

                    {{-- Delete --}}
                    @if($application->status !== 'graduated')
                    <div class="col-12">
                        <form method="POST" action="{{ route('graduation.destroy', $application) }}"
                              onsubmit="return confirm('Delete this application? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger btn-sm p-0">
                                <i class="bi bi-trash me-1"></i>Delete Application
                            </button>
                        </form>
                    </div>
                    @endif

                </div>
            </div>
        </div>
        @endhasrole

        {{-- Clearance timeline --}}
        @if($application->cleared_at || $application->approved_at || $application->graduation_date)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-1 px-4">
                <h6 class="fw-semibold mb-0">Timeline</h6>
            </div>
            <div class="card-body px-4 pb-3">
                <div class="timeline-list">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;flex-shrink:0">
                            <i class="bi bi-file-earmark-text small"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Application Submitted</div>
                            <div class="text-muted" style="font-size:0.78rem">{{ $application->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    @if($application->cleared_at)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;flex-shrink:0">
                            <i class="bi bi-patch-check small"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">All Clearances Passed</div>
                            <div class="text-muted" style="font-size:0.78rem">{{ $application->cleared_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    @endif
                    @if($application->approved_at)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;flex-shrink:0">
                            <i class="bi bi-check-circle small"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Application Approved</div>
                            <div class="text-muted" style="font-size:0.78rem">{{ $application->approved_at->format('d M Y, H:i') }} by {{ $application->approvedBy?->name }}</div>
                        </div>
                    </div>
                    @endif
                    @if($application->status === 'graduated')
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;flex-shrink:0">
                            <i class="bi bi-mortarboard small"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Graduated</div>
                            <div class="text-muted" style="font-size:0.78rem">{{ $application->graduation_date?->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0"><h6 class="modal-title fw-semibold">Approve Application</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label class="form-label small fw-semibold">Graduation Date (optional)</label>
                <input type="date" id="approveDateInput" class="form-control form-control-sm mb-3">
                <label class="form-label small fw-semibold">Ceremony (optional)</label>
                <select id="approveCeremonyInput" class="form-select form-select-sm">
                    <option value="">None</option>
                    @foreach($ceremonies as $c)
                    <option value="{{ $c->id }}" {{ $application->ceremony_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-success btn-sm" onclick="submitApprove()">Approve</button>
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

{{-- Graduate Modal --}}
<div class="modal fade" id="graduateModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0"><h6 class="modal-title fw-semibold">Mark as Graduated</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p class="small text-muted">This will update the student's status to <strong>graduated</strong> and create an alumni record.</p>
                <label class="form-label small fw-semibold">Graduation Date</label>
                <input type="date" id="gradDateInput" class="form-control form-control-sm" value="{{ $application->graduation_date?->format('Y-m-d') ?? now()->toDateString() }}">
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-dark btn-sm" onclick="submitGraduate()">Confirm Graduate</button>
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{ route('graduation.reject', $application) }}">
                @csrf
                <div class="modal-header border-0"><h6 class="modal-title fw-semibold">Reject Application</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label class="form-label small fw-semibold">Reason for rejection <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control form-control-sm" rows="3" required placeholder="Explain why this application is rejected…"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function submitApprove() {
    document.getElementById('approveDate').value = document.getElementById('approveDateInput').value;
    document.getElementById('approveCeremony').value = document.getElementById('approveCeremonyInput').value;
    document.getElementById('approveForm').submit();
}
function submitGraduate() {
    document.getElementById('gradDate').value = document.getElementById('gradDateInput').value;
    document.getElementById('graduateForm').submit();
}
</script>
@endpush
@endsection
