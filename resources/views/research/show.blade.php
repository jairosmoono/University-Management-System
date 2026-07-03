@extends('layouts.app')
@section('title', 'Research Project')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Research Project</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('research.index') }}">Research</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        @can('manage-research')
        <a href="{{ route('research.edit', $project) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @endcan
        <a href="{{ route('research.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Project Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                @php
                    $statusColors = ['proposal'=>'info','ongoing'=>'success','completed'=>'primary','suspended'=>'warning'];
                @endphp
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="fw-bold mb-0">{{ $project->title }}</h5>
                    <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }} ms-2">{{ ucfirst($project->status) }}</span>
                </div>
                @if($project->keywords)
                <div class="mb-3">
                    @foreach(explode(',', $project->keywords) as $kw)
                    <span class="badge bg-light text-dark border me-1">{{ trim($kw) }}</span>
                    @endforeach
                </div>
                @endif
                <h6 class="fw-semibold text-muted mb-2">Abstract</h6>
                <p>{{ $project->abstract }}</p>
            </div>
        </div>

        <!-- Publications -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Related Publications</h6>
                @can('manage-research')
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPublicationModal">
                    <i class="bi bi-plus me-1"></i> Add Publication
                </button>
                @endcan
            </div>
            <div class="card-body p-0">
                @if($project->publications->count())
                <div class="list-group list-group-flush">
                    @foreach($project->publications as $pub)
                    <div class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fw-semibold mb-1">{{ $pub->title }}</div>
                                <div class="text-muted small">
                                    {{ $pub->staff->user->name }} &bull;
                                    {{ $pub->publisher }} &bull;
                                    {{ $pub->publication_year }}
                                </div>
                                @if($pub->doi)
                                <div class="small mt-1"><span class="text-muted">DOI:</span> <code>{{ $pub->doi }}</code></div>
                                @endif
                            </div>
                            <span class="badge bg-secondary ms-2 align-self-start">{{ ucfirst($pub->type) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                    No publications linked yet
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Project Details</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Status</dt>
                    <dd class="col-7"><span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">{{ ucfirst($project->status) }}</span></dd>

                    <dt class="col-5 text-muted small">Start Date</dt>
                    <dd class="col-7 small">{{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}</dd>

                    @if($project->end_date)
                    <dt class="col-5 text-muted small">End Date</dt>
                    <dd class="col-7 small">{{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</dd>
                    @endif

                    @if($project->budget)
                    <dt class="col-5 text-muted small">Budget</dt>
                    <dd class="col-7 small fw-semibold">K {{ number_format($project->budget, 2) }}</dd>
                    @endif

                    @if($project->funding_source)
                    <dt class="col-5 text-muted small">Funding</dt>
                    <dd class="col-7 small">{{ $project->funding_source }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Investigators</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:38px;height:38px;font-size:14px">
                        {{ strtoupper(substr($project->principalInvestigator->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold small">{{ $project->principalInvestigator->user->name }}</div>
                        <small class="text-muted">Principal Investigator</small>
                    </div>
                </div>
                @if($project->co_investigators)
                <div class="border-top pt-3">
                    <small class="text-muted fw-semibold d-block mb-2">Co-Investigators</small>
                    <p class="small mb-0">{{ $project->co_investigators }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <small class="text-muted d-block">Created: {{ $project->created_at->format('d M Y') }}</small>
                <small class="text-muted d-block">Updated: {{ $project->updated_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Add Publication Modal -->
@can('manage-research')
<div class="modal fade" id="addPublicationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('research.publications.store', $project) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Publication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Staff Author *</label>
                            <select name="staff_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach($staff as $s)
                                <option value="{{ $s->id }}">{{ $s->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                @foreach(['journal','conference','book','thesis','report'] as $t)
                                <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Publisher</label>
                            <input type="text" name="publisher" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Year *</label>
                            <input type="number" name="publication_year" class="form-control" value="{{ date('Y') }}" min="1990" max="{{ date('Y') + 1 }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">DOI</label>
                            <input type="text" name="doi" class="form-control" placeholder="10.xxxx/...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Abstract</label>
                            <textarea name="abstract" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Publication</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
