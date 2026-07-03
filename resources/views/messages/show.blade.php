@extends('layouts.app')
@section('title', $message->subject)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $message->subject }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('messages.index') }}">Messages</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
        <form action="{{ route('messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Delete this message?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <strong>From:</strong> {{ optional($message->sender)->name }}
                <span class="text-muted mx-2">&rarr;</span>
                <strong>To:</strong> {{ optional($message->receiver)->name }}
            </div>
            <small class="text-muted">{{ $message->created_at->format('d M Y, H:i') }}</small>
        </div>
        <div class="border-top pt-3">{!! nl2br(e($message->content)) !!}</div>
    </div>
</div>

@if($replies->count())
<h6 class="mb-2 text-muted">Replies ({{ $replies->count() }})</h6>
@foreach($replies as $reply)
<div class="card border-0 shadow-sm mb-2 ms-4">
    <div class="card-body py-2">
        <div class="d-flex justify-content-between">
            <strong>{{ optional($reply->sender)->name }}</strong>
            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
        </div>
        <p class="mb-0 mt-1">{{ $reply->content }}</p>
    </div>
</div>
@endforeach
@endif

<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-transparent"><h6 class="mb-0">Reply</h6></div>
    <div class="card-body">
        <form action="{{ route('messages.send') }}" method="POST">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id }}">
            <input type="hidden" name="subject" value="Re: {{ $message->subject }}">
            <input type="hidden" name="parent_id" value="{{ $message->id }}">
            <div class="mb-3">
                <textarea name="content" class="form-control" rows="4" placeholder="Write your reply..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-reply me-1"></i>Send Reply</button>
        </form>
    </div>
</div>
@endsection
