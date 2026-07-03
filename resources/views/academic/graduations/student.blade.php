@extends('layouts.app')
@section('title', 'My Graduation Status')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">My Graduation Status</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Graduation</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    {{-- Eligibility card --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                <h6 class="fw-semibold mb-0">Graduation Eligibility</h6>
            </div>
            <div class="card-body px-4 pb-4">
                @php
                    $checks = [
                        ['icon' => 'journal-bookmark-fill', 'label' => 'Credits Earned',
                         'ok' => $eligibility['credits_ok'],
                         'value' => $eligibility['credits_earned'].' / '.$eligibility['required_credits'].' credits',
                         'hint' => $eligibility['credits_ok'] ? null : 'Need '.($eligibility['required_credits'] - $eligibility['credits_earned']).' more credits'],
                        ['icon' => 'graph-up', 'label' => 'Minimum CGPA (1.5)',
                         'ok' => $eligibility['cgpa_ok'],
                         'value' => 'CGPA: '.$eligibility['cgpa'],
                         'hint' => $eligibility['cgpa_ok'] ? null : 'Current CGPA below minimum requirement'],
                        ['icon' => 'cash-coin', 'label' => 'Finance Clearance',
                         'ok' => $eligibility['finance_ok'],
                         'value' => $eligibility['finance_ok'] ? 'No outstanding balance' : 'Balance due: KES '.number_format($eligibility['outstanding_bal'],2),
                         'hint' => $eligibility['finance_ok'] ? null : 'Please clear all outstanding fees'],
                        ['icon' => 'book', 'label' => 'Library Clearance',
                         'ok' => $eligibility['library_ok'],
                         'value' => $eligibility['library_ok'] ? 'No active loans or fines' :
                                    ($eligibility['active_loans'] > 0 ? $eligibility['active_loans'].' book(s) to return' : 'Fines: KES '.number_format($eligibility['unpaid_fines'],2)),
                         'hint' => $eligibility['library_ok'] ? null : 'Return borrowed books and clear fines'],
                        ['icon' => 'mortarboard', 'label' => 'Academic Clearance',
                         'ok' => $eligibility['academic_ok'],
                         'value' => $eligibility['academic_ok'] ? 'All results cleared' :
                                    ($eligibility['failed_count'] > 0 ? $eligibility['failed_count'].' failed course(s)' : $eligibility['pending_results'].' pending result(s)'),
                         'hint' => $eligibility['academic_ok'] ? null : 'Resolve outstanding academic issues'],
                    ];
                @endphp

                @foreach($checks as $chk)
                <div class="d-flex gap-3 py-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0
                        {{ $chk['ok'] ? 'bg-success' : 'bg-danger' }} bg-opacity-10"
                        style="width:40px;height:40px">
                        <i class="bi bi-{{ $chk['icon'] }} {{ $chk['ok'] ? 'text-success' : 'text-danger' }}"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small">{{ $chk['label'] }}</div>
                        <div class="text-muted" style="font-size:0.8rem">{{ $chk['value'] }}</div>
                        @if(!$chk['ok'] && $chk['hint'])
                        <div class="text-danger" style="font-size:0.75rem">{{ $chk['hint'] }}</div>
                        @endif
                    </div>
                    <div class="ms-auto">
                        <i class="bi bi-{{ $chk['ok'] ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }} fs-5"></i>
                    </div>
                </div>
                @endforeach

                <div class="mt-4 p-3 rounded-3 text-center
                    {{ $eligibility['eligible'] ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' }}">
                    <i class="bi bi-{{ $eligibility['eligible'] ? 'patch-check-fill text-success' : 'hourglass-split text-warning' }} fs-3 mb-2 d-block"></i>
                    <div class="fw-bold {{ $eligibility['eligible'] ? 'text-success' : 'text-warning' }}">
                        {{ $eligibility['eligible'] ? 'You are eligible for graduation!' : 'Not yet eligible' }}
                    </div>
                    @if(!$eligibility['eligible'])
                    <div class="text-muted small mt-1">Complete all requirements above to become eligible.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Application status --}}
    <div class="col-lg-7">
        @if($application)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Your Application</h6>
                <span class="badge bg-{{ \App\Models\GraduationApplication::statusColor($application->status) }} px-3 py-2">
                    {{ \App\Models\GraduationApplication::statusLabel($application->status) }}
                </span>
            </div>
            <div class="card-body px-4 pb-4">

                @if($application->status === 'rejected')
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle me-2"></i><strong>Application Rejected</strong><br>
                    {{ $application->rejection_reason }}
                </div>
                @elseif($application->status === 'graduated')
                <div class="alert alert-dark text-center py-4">
                    <i class="bi bi-mortarboard-fill fs-2 d-block mb-2"></i>
                    <strong class="fs-5">Congratulations, {{ $student->full_name }}!</strong><br>
                    <span class="text-muted">You graduated on {{ $application->graduation_date?->format('d F Y') }}.</span>
                    <div class="mt-3">
                        <a href="{{ route('graduation.certificate', $application) }}" class="btn btn-outline-dark btn-sm" target="_blank">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Download Certificate
                        </a>
                    </div>
                </div>
                @elseif($application->status === 'approved')
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i><strong>Your application has been approved!</strong>
                    @if($application->graduation_date)
                    <br>Graduation Date: <strong>{{ $application->graduation_date->format('d F Y') }}</strong>
                    @endif
                </div>
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="text-muted small">Program</div>
                        <div class="fw-semibold">{{ $application->program?->name }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Academic Year</div>
                        <div class="fw-semibold">{{ $application->academicYear?->name }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">CGPA at Application</div>
                        <div class="fw-semibold">{{ number_format($application->cgpa, 2) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Credits Earned</div>
                        <div class="fw-semibold">{{ $application->credits_earned }}</div>
                    </div>
                    @if($application->ceremony)
                    <div class="col-12">
                        <div class="text-muted small">Assigned Ceremony</div>
                        <div class="fw-semibold">{{ $application->ceremony->name }} — {{ $application->ceremony->ceremony_date->format('d M Y') }}</div>
                        @if($application->ceremony->venue)
                        <div class="text-muted small">{{ $application->ceremony->venue }}</div>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Clearance status --}}
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="text-center p-2 rounded-3 border {{ $application->finance_cleared ? 'border-success bg-success bg-opacity-10' : '' }}">
                            <i class="bi bi-cash-coin {{ $application->finance_cleared ? 'text-success' : 'text-muted' }}"></i>
                            <div style="font-size:0.75rem" class="{{ $application->finance_cleared ? 'text-success' : 'text-muted' }}">Finance</div>
                            <div style="font-size:0.7rem">{{ $application->finance_cleared ? 'Cleared' : 'Pending' }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-2 rounded-3 border {{ $application->library_cleared ? 'border-success bg-success bg-opacity-10' : '' }}">
                            <i class="bi bi-book {{ $application->library_cleared ? 'text-success' : 'text-muted' }}"></i>
                            <div style="font-size:0.75rem" class="{{ $application->library_cleared ? 'text-success' : 'text-muted' }}">Library</div>
                            <div style="font-size:0.7rem">{{ $application->library_cleared ? 'Cleared' : 'Pending' }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-2 rounded-3 border {{ $application->academic_cleared ? 'border-success bg-success bg-opacity-10' : '' }}">
                            <i class="bi bi-mortarboard {{ $application->academic_cleared ? 'text-success' : 'text-muted' }}"></i>
                            <div style="font-size:0.75rem" class="{{ $application->academic_cleared ? 'text-success' : 'text-muted' }}">Academic</div>
                            <div style="font-size:0.7rem">{{ $application->academic_cleared ? 'Cleared' : 'Pending' }}</div>
                        </div>
                    </div>
                </div>

                <div class="text-muted small">Applied on {{ $application->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-mortarboard fs-1 text-muted mb-3 d-block"></i>
                <h6 class="fw-semibold">No graduation application yet</h6>
                <p class="text-muted small mb-4">You have not submitted a graduation application. Once you meet all eligibility requirements, contact the Registrar's office to apply.</p>
                @if($eligibility['eligible'])
                <div class="alert alert-success d-inline-block small">
                    <i class="bi bi-check-circle me-1"></i>You are eligible! Please visit the Registrar's office to submit your application.
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
