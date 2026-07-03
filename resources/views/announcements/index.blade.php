@extends('layouts.app')
@section('title', 'Announcements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Announcements</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Announcements</li>
        </ol></nav>
    </div>
    @can('create-announcement')
    <a href="{{ route('announcements.create') }}" class="btn btn-primary">
        <i class="bi bi-megaphone me-1"></i> New Announcement
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <option value="academic" {{ request('category') == 'academic' ? 'selected' : '' }}>Academic</option>
                    <option value="event" {{ request('category') == 'event' ? 'selected' : '' }}>Event</option>
                    <option value="emergency" {{ request('category') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search announcements..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Search</button>
                <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse($announcements as $announcement)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    @php $catColors = ['academic'=>'primary','event'=>'success','emergency'=>'danger','general'=>'secondary'] @endphp
                    <span class="badge bg-{{ $catColors[$announcement->category] ?? 'secondary' }}">{{ ucfirst($announcement->category) }}</span>
                    @if($announcement->priority === 'urgent')
                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Urgent</span>
                    @elseif($announcement->priority === 'high')
                    <span class="badge bg-warning">High Priority</span>
                    @endif
                </div>
                <h6 class="fw-bold mb-2">{{ $announcement->title }}</h6>
                <p class="text-muted small mb-3">{{ Str::limit($announcement->content, 100) }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }}</small>
                    <small class="text-muted"><i class="bi bi-eye me-1"></i>{{ $announcement->views_count ?? 0 }} views</small>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex gap-2 pt-0">
                <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-sm btn-outline-primary flex-grow-1">Read More</a>
                @can('manage-announcements')
                <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 text-muted">
            <i class="bi bi-megaphone display-3"></i>
            <p class="mt-2">No announcements found.</p>
        </div>
    </div>
    @endforelse
</div>
<div class="mt-4">
    {{ $announcements->withQueryString()->links() }}
</div>
@endsection
