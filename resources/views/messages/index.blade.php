@extends('layouts.app')
@section('title', 'Messages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Messages</h4>
    </div>
    <a href="{{ route('messages.compose') }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i> Compose
    </a>
</div>

<div class="row g-0" style="height:calc(100vh - 200px); min-height:500px">
    <!-- Left panel: message list -->
    <div class="col-md-4 border-end">
        <div class="card border-0 h-100">
            <!-- Tabs -->
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs border-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request('folder', 'inbox') == 'inbox' ? 'active' : '' }}" href="{{ route('messages.index', ['folder' => 'inbox']) }}">
                            <i class="bi bi-inbox me-1"></i> Inbox
                            @if($unreadCount > 0)
                            <span class="badge bg-danger">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('folder') == 'sent' ? 'active' : '' }}" href="{{ route('messages.index', ['folder' => 'sent']) }}">
                            <i class="bi bi-send me-1"></i> Sent
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Message list -->
            <div class="list-group list-group-flush overflow-auto" style="max-height:600px">
                @forelse($messages as $msg)
                <a href="{{ route('messages.index', ['folder' => request('folder','inbox'), 'message' => $msg->id]) }}" class="list-group-item list-group-item-action py-3 {{ !$msg->read_at && $msg->receiver_id == auth()->id() ? 'bg-light fw-semibold' : '' }}">
                    <div class="d-flex align-items-start gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-size:13px">
                            {{ strtoupper(substr(optional($msg->sender)->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between">
                                <span class="small">{{ optional($msg->sender)->name }}</span>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($msg->created_at)->format('d M') }}</small>
                            </div>
                            <div class="text-truncate small fw-{{ !$msg->read_at && $msg->receiver_id == auth()->id() ? 'bold' : 'normal' }}">{{ $msg->subject }}</div>
                            <div class="text-muted small text-truncate">{{ Str::limit(strip_tags($msg->content), 60) }}</div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-envelope display-4"></i>
                    <p class="mt-2">No messages</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right panel: message content -->
    <div class="col-md-8">
        <div class="card border-0 h-100 d-flex align-items-center justify-content-center">
            @if(isset($selectedMessage))
                <div class="w-100 p-4">
                    <h5 class="fw-bold">{{ $selectedMessage->subject }}</h5>
                    <div class="d-flex justify-content-between text-muted small mb-3">
                        <span>From: <strong>{{ optional($selectedMessage->sender)->name }}</strong></span>
                        <span>{{ \Carbon\Carbon::parse($selectedMessage->created_at)->format('d M Y H:i') }}</span>
                    </div>
                    <hr>
                    <div style="line-height:1.8">{!! nl2br(e($selectedMessage->content)) !!}</div>
                    <hr>
                    <form method="POST" action="{{ route('messages.reply', $selectedMessage) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="4" placeholder="Write your reply..." required></textarea>
                        </div>
                        <button class="btn btn-primary"><i class="bi bi-reply me-1"></i>Reply</button>
                    </form>
                </div>
            @else
                <div class="text-center text-muted">
                    <i class="bi bi-chat-left-text display-3"></i>
                    <p class="mt-3">Select a message to read</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
