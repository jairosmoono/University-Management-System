@extends('layouts.app')
@section('title', isset($project) ? 'Edit Research Project' : 'New Research Project')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ isset($project) ? 'Edit Research Project' : 'New Research Project' }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('research.index') }}">Research</a></li>
            <li class="breadcrumb-item active">{{ isset($project) ? 'Edit' : 'New' }}</li>
        </ol></nav>
    </div>
</div>

<form method="POST" action="{{ isset($project) ? route('research.update', $project) : route('research.store') }}">
    @csrf
    @if(isset($project)) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Project Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Project Title *</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $project->title ?? '') }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Abstract / Summary *</label>
                            <textarea name="abstract" class="form-control @error('abstract') is-invalid @enderror" rows="5" required>{{ old('abstract', $project->abstract ?? '') }}</textarea>
                            @error('abstract') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Principal Investigator *</label>
                            <select name="principal_investigator_id" class="form-select @error('principal_investigator_id') is-invalid @enderror" required>
                                <option value="">-- Select Staff Member --</option>
                                @foreach($staff as $s)
                                <option value="{{ $s->id }}" {{ old('principal_investigator_id', $project->principal_investigator_id ?? '') == $s->id ? 'selected' : '' }}>
                                    {{ $s->user->name }} ({{ $s->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('principal_investigator_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Keywords</label>
                            <input type="text" name="keywords" class="form-control"
                                value="{{ old('keywords', $project->keywords ?? '') }}" placeholder="e.g., machine learning, AI, data science">
                            <small class="text-muted">Comma-separated keywords</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Co-Investigators</label>
                            <textarea name="co_investigators" class="form-control" rows="2" placeholder="Names and affiliations of co-investigators">{{ old('co_investigators', $project->co_investigators ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Timeline & Funding</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date', $project->start_date ?? '') }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Expected End Date</label>
                            <input type="date" name="end_date" class="form-control"
                                value="{{ old('end_date', $project->end_date ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Budget (K)</label>
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" name="budget" class="form-control" step="0.01" min="0"
                                    value="{{ old('budget', $project->budget ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Funding Source</label>
                            <input type="text" name="funding_source" class="form-control"
                                value="{{ old('funding_source', $project->funding_source ?? '') }}"
                                placeholder="e.g., Government Grant, EU Horizon, Internal">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Project Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        @php $statuses = ['proposal'=>'Proposal','ongoing'=>'Ongoing','completed'=>'Completed','suspended'=>'Suspended'] @endphp
                        @foreach($statuses as $val => $label)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="status" value="{{ $val }}" id="status_{{ $val }}"
                                {{ old('status', $project->status ?? 'proposal') == $val ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_{{ $val }}">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> {{ isset($project) ? 'Update Project' : 'Submit Project' }}
                        </button>
                        <a href="{{ route('research.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
