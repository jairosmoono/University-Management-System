@extends('layouts.app')
@section('title', 'Alumni Profile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Alumni Profile</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('alumni.index') }}">Alumni</a></li>
            <li class="breadcrumb-item active">{{ $alumnus->student->student_id }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        @can('manage-alumni')
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editAlumniModal">
            <i class="bi bi-pencil me-1"></i> Edit
        </button>
        @endcan
        <a href="{{ route('alumni.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center mb-4">
            <div class="card-body py-4">
                <img src="{{ $alumnus->student->photo_url }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover" alt="Photo">
                <h5 class="fw-bold mb-1">{{ $alumnus->student->user->name }}</h5>
                <p class="text-muted mb-1">{{ $alumnus->student->student_id }}</p>
                <p class="text-muted mb-3">Class of {{ $alumnus->graduation_year }}</p>
                @php
                    $badge = ['employed'=>'success','self_employed'=>'info','unemployed'=>'danger','further_studies'=>'warning'];
                    $labels = ['employed'=>'Employed','self_employed'=>'Self-Employed','unemployed'=>'Unemployed','further_studies'=>'Further Studies'];
                @endphp
                <span class="badge bg-{{ $badge[$alumnus->employment_status] ?? 'secondary' }} fs-6 px-3 py-2">
                    {{ $labels[$alumnus->employment_status] ?? ucfirst($alumnus->employment_status) }}
                </span>
                @if($alumnus->linkedin_url)
                <div class="mt-3">
                    <a href="{{ $alumnus->linkedin_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-linkedin me-1"></i> LinkedIn
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Academic Record</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Program</dt>
                    <dd class="col-7 small">{{ optional($alumnus->student->program)->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted small">Graduated</dt>
                    <dd class="col-7 small">{{ $alumnus->graduation_year }}</dd>
                    <dt class="col-5 text-muted small">Enrolled</dt>
                    <dd class="col-7 small">{{ $alumnus->student->enrollment_date ? \Carbon\Carbon::parse($alumnus->student->enrollment_date)->format('M Y') : '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Details -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Employment Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small d-block mb-1">Current Employer</label>
                        <strong>{{ $alumnus->current_employer ?: '—' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block mb-1">Job Title / Position</label>
                        <strong>{{ $alumnus->job_title ?: '—' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block mb-1">City</label>
                        <span>{{ $alumnus->city ?: '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block mb-1">Country</label>
                        <span>{{ $alumnus->country ?: 'Zambia' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($alumnus->biography)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Biography</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $alumnus->biography }}</p>
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small d-block mb-1"><i class="bi bi-envelope me-1"></i>Email</label>
                        <a href="mailto:{{ $alumnus->student->user->email }}" class="text-decoration-none">{{ $alumnus->student->user->email }}</a>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block mb-1"><i class="bi bi-telephone me-1"></i>Phone</label>
                        <span>{{ $alumnus->student->phone ?: $alumnus->student->user->phone ?: '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
@can('manage-alumni')
<div class="modal fade" id="editAlumniModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('alumni.update', $alumnus) }}">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Alumni Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employment Status</label>
                            <select name="employment_status" class="form-select">
                                <option value="employed" {{ $alumnus->employment_status == 'employed' ? 'selected' : '' }}>Employed</option>
                                <option value="self_employed" {{ $alumnus->employment_status == 'self_employed' ? 'selected' : '' }}>Self-Employed</option>
                                <option value="unemployed" {{ $alumnus->employment_status == 'unemployed' ? 'selected' : '' }}>Unemployed</option>
                                <option value="further_studies" {{ $alumnus->employment_status == 'further_studies' ? 'selected' : '' }}>Further Studies</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Graduation Year</label>
                            <input type="number" name="graduation_year" class="form-control" value="{{ $alumnus->graduation_year }}" min="1990" max="{{ date('Y') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Current Employer</label>
                            <input type="text" name="current_employer" class="form-control" value="{{ $alumnus->current_employer }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Job Title</label>
                            <input type="text" name="job_title" class="form-control" value="{{ $alumnus->job_title }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ $alumnus->city }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ $alumnus->country }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">LinkedIn URL</label>
                            <input type="url" name="linkedin_url" class="form-control" value="{{ $alumnus->linkedin_url }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Biography</label>
                            <textarea name="biography" class="form-control" rows="3">{{ $alumnus->biography }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
