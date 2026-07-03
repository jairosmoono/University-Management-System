@extends('layouts.app')
@section('title', 'Documents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Documents</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Documents</li>
        </ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
        <i class="bi bi-upload me-1"></i> Upload Document
    </button>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="transcript" {{ request('type') == 'transcript' ? 'selected' : '' }}>Transcript</option>
                    <option value="certificate" {{ request('type') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                    <option value="id_card" {{ request('type') == 'id_card' ? 'selected' : '' }}>ID Card</option>
                    <option value="admission_letter" {{ request('type') == 'admission_letter' ? 'selected' : '' }}>Admission Letter</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search documents..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Search</button>
                <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Title</th><th>Type</th><th>Student/User</th><th>Uploaded By</th><th>Size</th><th>Date</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                <tr>
                    <td class="fw-semibold"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>{{ $doc->title }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_',' ',$doc->type)) }}</span></td>
                    <td>{{ optional($doc->student)->student_id ?? '—' }}</td>
                    <td>{{ optional($doc->uploadedBy)->name }}</td>
                    <td>{{ $doc->file_size ? number_format($doc->file_size / 1024, 1) . ' KB' : '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('documents.download', $doc) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                        @can('manage-documents')
                        <form method="POST" action="{{ route('documents.destroy', $doc) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $documents->withQueryString()->links() }}
    </div>
</div>

<div class="modal fade" id="uploadDocModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Upload Document</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Document Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select name="type" class="form-select" required>
                            <option value="transcript">Transcript</option>
                            <option value="certificate">Certificate</option>
                            <option value="id_card">ID Card</option>
                            <option value="admission_letter">Admission Letter</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student (optional)</label>
                        <select name="student_id" class="form-select">
                            <option value="">General Document</option>
                            @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ optional($s->user)->name }} ({{ $s->student_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File *</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
