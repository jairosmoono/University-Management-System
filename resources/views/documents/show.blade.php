@extends('layouts.app')
@section('title', $document->title)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $document->title }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
            <li class="breadcrumb-item active">{{ $document->title }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('documents.download', $document) }}" class="btn btn-primary btn-sm"><i class="bi bi-download me-1"></i>Download</a>
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-4 text-muted fw-normal">Category</dt><dd class="col-8"><span class="badge bg-secondary">{{ ucfirst($document->category) }}</span></dd>
            <dt class="col-4 text-muted fw-normal">File</dt><dd class="col-8">{{ $document->file_name }}</dd>
            <dt class="col-4 text-muted fw-normal">Size</dt><dd class="col-8">{{ number_format($document->file_size / 1024, 1) }} KB</dd>
            <dt class="col-4 text-muted fw-normal">Type</dt><dd class="col-8">{{ $document->file_type }}</dd>
            <dt class="col-4 text-muted fw-normal">Uploaded By</dt><dd class="col-8">{{ optional($document->uploadedBy)->name }}</dd>
            <dt class="col-4 text-muted fw-normal">Date</dt><dd class="col-8">{{ $document->created_at->format('d M Y') }}</dd>
            <dt class="col-4 text-muted fw-normal">Downloads</dt><dd class="col-8">{{ $document->download_count }}</dd>
            <dt class="col-4 text-muted fw-normal">Visibility</dt><dd class="col-8"><span class="badge bg-{{ $document->is_public ? 'success' : 'warning text-dark' }}">{{ $document->is_public ? 'Public' : 'Private' }}</span></dd>
        </dl>
        @if($document->description)
        <hr>
        <p class="text-muted mb-0">{{ $document->description }}</p>
        @endif
    </div>
</div>
@endsection
