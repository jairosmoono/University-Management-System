@extends('layouts.app')
@section('title', 'Upload Research Paper')
@section('page-title', 'Upload Research Paper')

@section('content')

<div class="mb-4">
    <h4 class="mb-1"><i class="bi bi-cloud-upload me-2 text-primary"></i>Upload Research Paper</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('research.index') }}">Research</a></li>
        <li class="breadcrumb-item"><a href="{{ route('research.papers.index') }}">Papers</a></li>
        <li class="breadcrumb-item active">Upload</li>
    </ol></nav>
</div>

<form method="POST" action="{{ route('research.papers.store') }}" enctype="multipart/form-data">
@csrf

<div class="row g-4">
    {{-- ── MAIN FORM ─────────────────────────────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- File drop zone --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-paperclip me-2"></i>Paper File</h6>
            </div>
            <div class="card-body">
                <div id="drop-zone"
                     class="border border-2 border-dashed rounded-3 p-5 text-center position-relative"
                     style="border-color:#dee2e6 !important; cursor:pointer; transition:background 0.2s"
                     ondragover="event.preventDefault(); this.style.background='#f0f8ff'"
                     ondragleave="this.style.background=''"
                     ondrop="handleDrop(event)">
                    <i class="bi bi-file-earmark-arrow-up fs-1 text-muted mb-3 d-block"></i>
                    <div class="fw-semibold mb-1">Drag & drop your file here</div>
                    <div class="text-muted small mb-3">or click to browse</div>
                    <div class="text-muted" style="font-size:0.75rem">
                        Accepted: <strong>PDF, DOC, DOCX</strong> &nbsp;|&nbsp; Max size: <strong>20 MB</strong>
                    </div>
                    <input type="file" id="paper_file" name="paper_file"
                           accept=".pdf,.doc,.docx"
                           class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                           style="cursor:pointer"
                           onchange="showFileName(this)">
                </div>
                <div id="file-name-display" class="mt-2 text-success fw-semibold d-none">
                    <i class="bi bi-check-circle me-1"></i><span id="file-name-text"></span>
                    <small class="text-muted ms-2" id="file-size-text"></small>
                </div>
                @error('paper_file')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Paper details --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2"></i>Paper Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="Full title of the paper" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Authors</label>
                    <input type="text" name="authors" class="form-control @error('authors') is-invalid @enderror"
                           value="{{ old('authors') }}" placeholder="e.g. John Doe, Jane Smith">
                    <div class="form-text">Separate multiple authors with commas.</div>
                    @error('authors')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Abstract</label>
                    <textarea name="abstract" rows="5" class="form-control @error('abstract') is-invalid @enderror"
                              placeholder="Brief summary of the paper's content and findings…">{{ old('abstract') }}</textarea>
                    @error('abstract')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Keywords</label>
                    <input type="text" name="keywords" class="form-control @error('keywords') is-invalid @enderror"
                           value="{{ old('keywords') }}" placeholder="e.g. machine learning, data science, neural networks">
                    <div class="form-text">Comma-separated keywords to aid searchability.</div>
                    @error('keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

    </div>

    {{-- ── SIDEBAR ───────────────────────────────────────────────────────── --}}
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm mb-4 position-sticky" style="top:80px">
            <div class="card-header bg-transparent py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-tags me-2"></i>Classification</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                        <option value="">— Select —</option>
                        @foreach([
                            'journal_article'  => 'Journal Article',
                            'conference_paper' => 'Conference Paper',
                            'thesis'           => 'Thesis',
                            'technical_report' => 'Technical Report',
                            'book_chapter'     => 'Book Chapter',
                            'other'            => 'Other',
                        ] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('category') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Publication Year</label>
                    <input type="number" name="publication_year" class="form-control @error('publication_year') is-invalid @enderror"
                           value="{{ old('publication_year') }}" min="1900" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                    @error('publication_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">DOI</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted" style="font-size:0.8rem">10.</span>
                        <input type="text" name="doi" class="form-control @error('doi') is-invalid @enderror"
                               value="{{ old('doi') }}" placeholder="xxxx/xxxxx">
                    </div>
                    <div class="form-text">Digital Object Identifier (optional).</div>
                    @error('doi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Linked Research Project</label>
                    <select name="research_project_id" class="form-select @error('research_project_id') is-invalid @enderror">
                        <option value="">— None —</option>
                        @foreach($projects as $proj)
                        <option value="{{ $proj->id }}"
                            {{ old('research_project_id', optional($preselected)->id) == $proj->id ? 'selected' : '' }}>
                            {{ Str::limit($proj->title, 55) }}
                        </option>
                        @endforeach
                    </select>
                    @error('research_project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Visibility</label>
                    <div class="form-check">
                        <input type="radio" name="is_public" id="vis-public" value="1" class="form-check-input"
                               {{ old('is_public', '1') == '1' ? 'checked' : '' }}>
                        <label for="vis-public" class="form-check-label">
                            <i class="bi bi-globe2 text-success me-1"></i>
                            <strong>Public</strong>
                            <div class="text-muted" style="font-size:0.78rem">Anyone with access can download</div>
                        </label>
                    </div>
                    <div class="form-check mt-2">
                        <input type="radio" name="is_public" id="vis-restricted" value="0" class="form-check-input"
                               {{ old('is_public') === '0' ? 'checked' : '' }}>
                        <label for="vis-restricted" class="form-check-label">
                            <i class="bi bi-lock text-warning me-1"></i>
                            <strong>Restricted</strong>
                            <div class="text-muted" style="font-size:0.78rem">Only authorized staff can download</div>
                        </label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-2"></i>Upload Paper
                    </button>
                    <a href="{{ route('research.papers.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

</form>

@push('scripts')
<script>
function showFileName(input) {
    if (input.files && input.files[0]) {
        const file  = input.files[0];
        const mb    = (file.size / 1048576).toFixed(1);
        const kb    = (file.size / 1024).toFixed(0);
        const sizeStr = file.size >= 1048576 ? mb + ' MB' : kb + ' KB';
        document.getElementById('file-name-text').textContent = file.name;
        document.getElementById('file-size-text').textContent = '(' + sizeStr + ')';
        document.getElementById('file-name-display').classList.remove('d-none');
        document.getElementById('drop-zone').style.background = '#f0fff4';
    }
}

function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').style.background = '';
    const dt = e.dataTransfer;
    if (dt.files && dt.files[0]) {
        const input = document.getElementById('paper_file');
        const dT    = new DataTransfer();
        dT.items.add(dt.files[0]);
        input.files = dT.files;
        showFileName(input);
    }
}
</script>
@endpush

@endsection
