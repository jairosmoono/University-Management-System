@extends('layouts.app')
@section('title', 'Research Papers')
@section('page-title', 'Research Papers')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Research Papers</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('research.index') }}">Research</a></li>
            <li class="breadcrumb-item active">Papers</li>
        </ol></nav>
    </div>
    @can('manage-research')
    <a href="{{ route('research.papers.upload') }}" class="btn btn-primary">
        <i class="bi bi-cloud-upload me-1"></i> Upload Paper
    </a>
    @endcan
</div>

{{-- ── STATS ───────────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                <div>
                    <div class="stat-value text-danger">{{ number_format($totalPapers) }}</div>
                    <div class="stat-label text-muted">Total Papers</div>
                </div>
            </div>
        </div>
    </div>
    @foreach([['journal_article','Journal Articles','primary'],['conference_paper','Conference Papers','info'],['thesis','Theses','warning']] as [$cat, $label, $color])
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-{{ $color }} bg-opacity-10 text-{{ $color }}"><i class="bi bi-journal-text"></i></div>
                <div>
                    <div class="stat-value text-{{ $color }}">{{ $byCategory[$cat] ?? 0 }}</div>
                    <div class="stat-label text-muted">{{ $label }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── FILTERS ─────────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search title, authors, keywords…"
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach(['journal_article'=>'Journal Article','conference_paper'=>'Conference Paper','thesis'=>'Thesis','technical_report'=>'Technical Report','book_chapter'=>'Book Chapter','other'=>'Other'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('category') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $proj)
                    <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                        {{ Str::limit($proj->title, 50) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary px-3">Filter</button>
                @if(request()->hasAny(['search','category','project_id']))
                <a href="{{ route('research.papers.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── PAPERS LIST ─────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="min-width:260px">Paper</th>
                        <th>Authors</th>
                        <th>Category</th>
                        <th>Project</th>
                        <th class="text-center">Year</th>
                        <th class="text-center">Size</th>
                        <th class="text-center">Visibility</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($papers as $paper)
                    @php
                        $catColors = [
                            'journal_article'  => 'primary',
                            'conference_paper' => 'info',
                            'thesis'           => 'warning',
                            'technical_report' => 'secondary',
                            'book_chapter'     => 'success',
                            'other'            => 'dark',
                        ];
                        $color = $catColors[$paper->category] ?? 'secondary';
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-file-earmark-{{ str_ends_with($paper->file_original_name, '.pdf') ? 'pdf text-danger' : 'word text-primary' }} fs-4 flex-shrink-0 mt-1"></i>
                                <div>
                                    <div class="fw-semibold" style="font-size:0.9rem">{{ Str::limit($paper->title, 70) }}</div>
                                    @if($paper->doi)
                                    <a href="https://doi.org/{{ $paper->doi }}" target="_blank" class="text-muted" style="font-size:0.75rem">
                                        <i class="bi bi-link-45deg"></i> {{ $paper->doi }}
                                    </a>
                                    @endif
                                    @if($paper->keywords)
                                    <div class="mt-1">
                                        @foreach(array_slice(explode(',', $paper->keywords), 0, 3) as $kw)
                                        <span class="badge bg-light text-dark border" style="font-size:0.65rem">{{ trim($kw) }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit($paper->authors ?? '—', 40) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $color }} bg-opacity-15 text-{{ $color }} border border-{{ $color }} border-opacity-25" style="font-size:0.72rem">
                                {{ \App\Models\ResearchPaper::categoryLabel($paper->category) }}
                            </span>
                        </td>
                        <td>
                            @if($paper->researchProject)
                            <a href="{{ route('research.show', $paper->researchProject) }}" class="text-decoration-none" style="font-size:0.8rem">
                                {{ Str::limit($paper->researchProject->title, 35) }}
                            </a>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <small>{{ $paper->publication_year ?? '—' }}</small>
                        </td>
                        <td class="text-center">
                            <small class="text-muted">{{ $paper->file_size_formatted }}</small>
                        </td>
                        <td class="text-center">
                            @if($paper->is_public)
                            <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25" style="font-size:0.7rem">
                                <i class="bi bi-globe2 me-1"></i>Public
                            </span>
                            @else
                            <span class="badge bg-secondary bg-opacity-15 text-secondary border" style="font-size:0.7rem">
                                <i class="bi bi-lock me-1"></i>Restricted
                            </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex gap-1 justify-content-end align-items-center">
                                <a href="{{ route('research.papers.download', $paper) }}"
                                   class="btn btn-sm btn-outline-primary" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                                @if($paper->abstract)
                                <button class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#abstract-{{ $paper->id }}"
                                        title="View Abstract">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @endif
                                @can('manage-research')
                                <form method="POST" action="{{ route('research.papers.destroy', $paper) }}"
                                      onsubmit="return confirm('Delete this paper? The file will be permanently removed.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    {{-- Abstract modal --}}
                    @if($paper->abstract)
                    <div class="modal fade" id="abstract-{{ $paper->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title fw-bold">{{ $paper->title }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    @if($paper->authors)
                                    <p class="text-muted mb-2"><strong>Authors:</strong> {{ $paper->authors }}</p>
                                    @endif
                                    @if($paper->publication_year)
                                    <p class="text-muted mb-2"><strong>Year:</strong> {{ $paper->publication_year }}</p>
                                    @endif
                                    <hr>
                                    <p style="white-space:pre-line;font-size:0.9rem">{{ $paper->abstract }}</p>
                                </div>
                                <div class="modal-footer">
                                    <a href="{{ route('research.papers.download', $paper) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-download me-1"></i> Download Paper
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-file-earmark-x fs-2 d-block mb-2 opacity-50"></i>
                            No research papers found.
                            @can('manage-research')
                            <div class="mt-2">
                                <a href="{{ route('research.papers.upload') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-cloud-upload me-1"></i> Upload the first paper
                                </a>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($papers->hasPages())
    <div class="card-footer bg-transparent">
        {{ $papers->links() }}
    </div>
    @endif
</div>

@endsection
