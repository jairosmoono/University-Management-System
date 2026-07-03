@extends('layouts.app')
@section('title', 'Application - ' . $admission->application_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Application: <code>{{ $admission->application_number }}</code></h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admissions.index') }}">Admissions</a></li>
            <li class="breadcrumb-item active">{{ $admission->application_number }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        @if($admission->status === 'approved')
        <a href="{{ route('admissions.letter', $admission) }}" class="btn btn-outline-danger">
            <i class="bi bi-file-pdf me-1"></i> Admission Letter
        </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Personal Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2 text-primary"></i>Personal Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><strong>Full Name</strong><br>{{ $admission->full_name }}</div>
                    <div class="col-md-4"><strong>Date of Birth</strong><br>{{ \Carbon\Carbon::parse($admission->date_of_birth)->format('d M Y') }}</div>
                    <div class="col-md-4"><strong>Gender</strong><br>{{ ucfirst($admission->gender) }}</div>
                    <div class="col-md-4"><strong>Nationality</strong><br>{{ $admission->nationality }}</div>
                    <div class="col-md-4"><strong>Phone</strong><br>{{ $admission->phone }}</div>
                    <div class="col-md-4"><strong>Email</strong><br>{{ $admission->email }}</div>
                    <div class="col-12"><strong>Address</strong><br>{{ $admission->address }}</div>
                </div>
            </div>
        </div>

        <!-- Academic Background -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-mortarboard me-2 text-primary"></i>Academic Background</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Previous Institution</strong><br>{{ $admission->previous_school }}</div>
                    <div class="col-md-3"><strong>Qualification</strong><br>{{ $admission->qualification_type }}</div>
                    <div class="col-md-3"><strong>Year Completed</strong><br>{{ $admission->year_completed }}</div>
                    <div class="col-md-4"><strong>Grade/GPA</strong><br>{{ $admission->grade }}</div>
                    <div class="col-md-4"><strong>Program Applied</strong><br>{{ optional($admission->program)->name }}</div>
                    <div class="col-md-4"><strong>Semester/Term</strong><br>{{ optional($admission->semester)->name }}</div>
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-folder me-2 text-primary"></i>Documents Submitted</h6>
            </div>
            <div class="card-body">
                @php
                    $docLabels = ['certificates' => 'Academic Certificates', 'national_id' => 'National ID / Passport', 'photo' => 'Passport Photo', 'other' => 'Other Documents'];
                @endphp
                @if($admission->documents && count($admission->documents) > 0)
                <ul class="list-group list-group-flush">
                    @foreach($admission->documents as $type => $path)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-earmark me-2 text-muted"></i>{{ $docLabels[$type] ?? ucfirst($type) }}</span>
                        <a href="{{ asset($path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i>View
                        </a>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted">No documents uploaded.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Application Status</h6>
                @php $sc = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','waitlisted'=>'info'] @endphp
                <div class="text-center mb-4">
                    <span class="badge bg-{{ $sc[$admission->status] ?? 'secondary' }} fs-5 px-4 py-2">{{ strtoupper($admission->status) }}</span>
                </div>

                @can('manage-admissions')
                @if($admission->status === 'pending')
                <div class="d-grid gap-2">
                    <form method="POST" action="{{ route('admissions.approve', $admission) }}">
                        @csrf
                        <button class="btn btn-success w-100"><i class="bi bi-check-circle me-2"></i>Approve Application</button>
                    </form>
                    <button class="btn btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#waitlistForm">Waitlist</button>
                    <button class="btn btn-danger" data-bs-toggle="collapse" data-bs-target="#rejectForm">Reject</button>

                    <div class="collapse" id="rejectForm">
                        <form method="POST" action="{{ route('admissions.reject', $admission) }}" class="mt-2">
                            @csrf
                            <textarea name="rejection_reason" class="form-control mb-2" rows="3" placeholder="Reason for rejection..." required></textarea>
                            <button class="btn btn-danger w-100">Confirm Rejection</button>
                        </form>
                    </div>
                </div>
                @endif
                @endcan

                <hr>
                <div class="small text-muted">
                    <p class="mb-1"><strong>Applied:</strong> {{ \Carbon\Carbon::parse($admission->created_at)->format('d M Y H:i') }}</p>
                    @if($admission->reviewed_at)
                    <p class="mb-1"><strong>Reviewed:</strong> {{ \Carbon\Carbon::parse($admission->reviewed_at)->format('d M Y H:i') }}</p>
                    <p class="mb-0"><strong>By:</strong> {{ optional($admission->reviewedBy)->name ?? '—' }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Activity Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="d-flex gap-3 mb-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:32px;height:32px;font-size:12px">
                            <i class="bi bi-send"></i>
                        </div>
                        <div>
                            <strong class="small">Application Submitted</strong><br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($admission->created_at)->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                    @if($admission->reviewed_at)
                    <div class="d-flex gap-3 mb-3">
                        <div class="rounded-circle bg-{{ $sc[$admission->status] ?? 'secondary' }} d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:32px;height:32px;font-size:12px">
                            <i class="bi bi-{{ $admission->status === 'approved' ? 'check' : 'x' }}"></i>
                        </div>
                        <div>
                            <strong class="small">Application {{ ucfirst($admission->status) }}</strong><br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($admission->reviewed_at)->format('d M Y H:i') }} by {{ optional($admission->reviewedBy)->name }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
