@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-bell me-2" style="color:var(--secondary)"></i>Notifications</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Notifications</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('notifications.preferences') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-envelope-gear me-1"></i>Email Preferences
        </a>
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-check2-all me-1"></i>Mark All Read
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @forelse($notifications as $notif)
        <div class="d-flex align-items-start gap-3 p-3 border-bottom {{ $notif->is_read ? '' : 'bg-light' }}"
             style="{{ !$notif->is_read ? 'background: rgba(11,31,58,0.03) !important;' : '' }}">
            <div class="mt-1 flex-shrink-0" style="width:10px">
                @if(!$notif->is_read)
                <div style="width:8px;height:8px;background:#8B0000;border-radius:50%;margin-top:5px"></div>
                @endif
            </div>
            <div class="flex-shrink-0 mt-1">
                @php
                    $iconMap = [
                        'payment' => ['bi-credit-card', 'text-success'],
                        'admission' => ['bi-person-plus', 'text-primary'],
                        'result' => ['bi-bar-chart', 'text-info'],
                        'announcement' => ['bi-megaphone', 'text-warning'],
                        'support' => ['bi-headset', 'text-danger'],
                    ];
                    $notifType = $notif->type ?? 'general';
                    $icon = $iconMap[$notifType] ?? ['bi-bell', 'text-secondary'];
                @endphp
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:38px;height:38px;background:rgba(11,31,58,0.08)">
                    <i class="bi {{ $icon[0] }} {{ $icon[1] }}"></i>
                </div>
            </div>
            <div class="flex-1">
                <div class="fw-semibold" style="font-size:0.9rem">{{ $notif->title }}</div>
                <div class="text-muted mt-1" style="font-size:0.85rem">{{ $notif->message }}</div>
                <div class="text-muted mt-1" style="font-size:0.78rem">
                    <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                    &mdash; {{ $notif->created_at->format('d M Y, H:i') }}
                </div>
            </div>
            @if(!$notif->is_read)
            <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="flex-shrink-0">
                @csrf
                <button type="submit" class="btn btn-link btn-sm text-muted p-0" title="Mark as read">
                    <i class="bi bi-check2"></i> Mark read
                </button>
            </form>
            @else
            <div class="flex-shrink-0">
                <span class="text-muted" style="font-size:0.75rem"><i class="bi bi-check2-all"></i> Read</span>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-bell-slash fs-1 d-block mb-3 opacity-25"></i>
            <h5 class="fw-semibold">No Notifications</h5>
            <p class="mb-0">You're all caught up! No notifications at this time.</p>
        </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-between py-2">
        <div class="text-muted" style="font-size:0.85rem">
            Showing {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} of {{ $notifications->total() }}
        </div>
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
