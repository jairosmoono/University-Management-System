@extends('layouts.app')
@section('title', 'Research & Publications')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">Research & Publications</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Research</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('research.papers.index') }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Research Papers
        </a>
        @can('manage-research')
        <a href="{{ route('research.papers.upload') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-cloud-upload me-1"></i> Upload Paper
        </a>
        <a href="{{ route('research.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Project
        </a>
        @endcan
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-primary fw-bold">{{ $stats['total_projects'] }}</h4><small class="text-muted">Total Projects</small></div></div>
    <div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-warning fw-bold">{{ $stats['ongoing'] }}</h4><small class="text-muted">Ongoing</small></div></div>
    <div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-success fw-bold">{{ $stats['completed'] }}</h4><small class="text-muted">Completed</small></div></div>
    <div class="col-6 col-md-3">
        <a href="{{ route('research.papers.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm text-center p-3 h-100">
            <h4 class="text-danger fw-bold">{{ $stats['total_papers'] }}</h4>
            <small class="text-muted">Uploaded Papers <i class="bi bi-file-earmark-pdf ms-1 text-danger"></i></small>
        </div>
        </a>
    </div>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#projects-tab">Research Projects</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pub-tab">Publications</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="projects-tab">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table datatable table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th><th>Principal Investigator</th><th>Start Date</th><th>End Date</th><th>Budget</th><th>Funding</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr>
                            <td class="fw-semibold">{{ Str::limit($project->title, 60) }}</td>
                            <td>{{ optional(optional($project->principalInvestigator)->user)->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}</td>
                            <td>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d M Y') : 'Ongoing' }}</td>
                            <td>{{ $project->budget ? formatCurrency($project->budget) : '—' }}</td>
                            <td>{{ $project->funding_source ?? '—' }}</td>
                            <td>
                                @php $sc = ['proposal'=>'info','ongoing'=>'warning','completed'=>'success','suspended'=>'danger'] @endphp
                                <span class="badge bg-{{ $sc[$project->status] ?? 'secondary' }}">{{ ucfirst($project->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('research.show', $project) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                @can('manage-research')
                                <a href="{{ route('research.edit', $project) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pub-tab">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table datatable table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th><th>Author</th><th>Type</th><th>Publisher</th><th>Year</th><th>DOI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($publications as $pub)
                        <tr>
                            <td class="fw-semibold">{{ Str::limit($pub->title, 60) }}</td>
                            <td>{{ optional(optional($pub->staff)->user)->name }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($pub->type) }}</span></td>
                            <td>{{ $pub->publisher ?? '—' }}</td>
                            <td>{{ $pub->publication_year }}</td>
                            <td>{{ $pub->doi ? '<a href="https://doi.org/'.$pub->doi.'" target="_blank"><code>'.$pub->doi.'</code></a>' : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
