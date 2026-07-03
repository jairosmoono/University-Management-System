@extends('layouts.app')
@section('title', 'Inbox')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Inbox</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('messages.index') }}">Messages</a></li>
            <li class="breadcrumb-item active">Inbox</li>
        </ol></nav>
    </div>
    <a href="{{ route('messages.compose') }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square me-1"></i>Compose</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @forelse($messages as $msg)
        <a href="{{ route('messages.show', $msg) }}" class="d-block text-decoration-none border-bottom px-4 py-3 {{ !$msg->is_read ? 'bg-light fw-semibold' : '' }} hover-bg">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    @if(!$msg->is_read)<span class="badge bg-primary me-2">New</span>@endif
                    <span class="text-dark">{{ optional($msg->sender)->name }}</span>
                    <span class="text-muted mx-2">&bull;</span>
                    <span class="{{ !$msg->is_read ? 'text-dark' : 'text-muted' }}">{{ $msg->subject }}</span>
                </div>
                <small class="text-muted text-nowrap">{{ $msg->created_at->diffForHumans() }}</small>
            </div>
            <p class="text-muted small mb-0 mt-1">{{ Str::limit(strip_tags($msg->content), 100) }}</p>
        </a>
        @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            Your inbox is empty.
        </div>
        @endforelse
    </div>
</div>
{{ $messages->links() }}
@endsection
