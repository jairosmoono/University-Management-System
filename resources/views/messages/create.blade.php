@extends('layouts.app')
@section('title', 'Compose Message')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Compose Message</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('messages.index') }}">Messages</a></li>
            <li class="breadcrumb-item active">Compose</li>
        </ol></nav>
    </div>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-body">
        <form method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">To *</label>
                <select name="receiver_id" class="form-select" required>
                    <option value="">Select Recipient</option>
                    @foreach($users as $user)
                    @if($user->id !== auth()->id())
                    <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Subject *</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Message *</label>
                <textarea name="body" class="form-control" rows="8" required>{{ old('body') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Attachment</label>
                <input type="file" name="attachment" class="form-control">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Send Message</button>
                <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
