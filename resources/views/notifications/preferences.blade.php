@extends('layouts.app')
@section('title', 'Notification Preferences')
@section('page-title', 'Notification Preferences')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-bell-slash me-2" style="color:var(--secondary)"></i>Notification Preferences</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('notifications.index') }}">Notifications</a></li>
            <li class="breadcrumb-item active">Preferences</li>
        </ol></nav>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('notifications.preferences.update') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header py-3">
            <h5 class="mb-0 fw-semibold">Choose how you want to receive each type of notification</h5>
        </div>

        {{-- Column headers --}}
        <div class="d-flex align-items-center px-3 py-2 border-bottom bg-light">
            <div style="flex:1"></div>
            <div class="d-flex gap-4 me-1">
                <div class="text-center" style="width:90px">
                    <i class="bi bi-envelope text-primary me-1"></i>
                    <span class="fw-semibold" style="font-size:0.82rem">Email</span>
                </div>
                <div class="text-center" style="width:90px">
                    <i class="bi bi-phone text-success me-1"></i>
                    <span class="fw-semibold" style="font-size:0.82rem">SMS</span>
                </div>
            </div>
        </div>

        @foreach($types as $key => $info)
        @php
            $emailOn = isset($existing[$key]) ? (bool)$existing[$key]['email'] : true;
            $smsOn   = isset($existing[$key]) ? (bool)$existing[$key]['sms']   : true;
        @endphp
        <div class="d-flex align-items-center p-3 border-bottom">
            <div class="d-flex align-items-center gap-3" style="flex:1">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;background:rgba(11,31,58,0.07)">
                    <i class="bi {{ $info['icon'] }} fs-5" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:0.9rem">{{ $info['label'] }}</div>
                    <div class="text-muted" style="font-size:0.82rem">{{ $info['desc'] }}</div>
                </div>
            </div>
            <div class="d-flex gap-4 me-1">
                {{-- Email toggle --}}
                <div class="text-center" style="width:90px">
                    <div class="form-check form-switch d-flex justify-content-center m-0">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="email_{{ $key }}" id="email_{{ $key }}"
                               value="1" {{ $emailOn ? 'checked' : '' }}
                               style="width:2.4rem;height:1.2rem;cursor:pointer">
                    </div>
                </div>
                {{-- SMS toggle --}}
                <div class="text-center" style="width:90px">
                    <div class="form-check form-switch d-flex justify-content-center m-0">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="sms_{{ $key }}" id="sms_{{ $key }}"
                               value="1" {{ $smsOn ? 'checked' : '' }}
                               style="width:2.4rem;height:1.2rem;cursor:pointer">
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="card-footer text-end py-3">
            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Save Preferences
            </button>
        </div>
    </div>
</form>
@endsection
