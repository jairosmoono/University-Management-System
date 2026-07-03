@extends('layouts.app')
@section('title', 'Database Backup')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Database Backup</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Backup</li>
        </ol></nav>
    </div>
    <form action="{{ route('admin.backup.create') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" onclick="return confirm('Create a new database backup?')">
            <i class="bi bi-download me-1"></i>Create Backup
        </button>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Filename</th><th>Size</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($backups as $backup)
                    <tr>
                        <td><i class="bi bi-file-earmark-zip me-2 text-secondary"></i>{{ $backup['name'] }}</td>
                        <td>{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</td>
                        <td>{{ date('d M Y H:i', $backup['date']) }}</td>
                        <td>
                            <a href="{{ route('admin.backup.download', $backup['name']) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i>Download
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No backups found. Create one now.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
