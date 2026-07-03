@extends('layouts.app')
@section('title', 'Library Books')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Library Books</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Library</li>
        </ol></nav>
    </div>
    @can('manage-library')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
        <i class="bi bi-plus-circle me-1"></i> Add Book
    </button>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-primary fw-bold">{{ $stats['total'] }}</h4><small class="text-muted">Total Titles</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-success fw-bold">{{ $stats['available'] }}</h4><small class="text-muted">Available</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-warning fw-bold">{{ $stats['borrowed'] }}</h4><small class="text-muted">Borrowed</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-danger fw-bold">{{ $stats['overdue'] }}</h4><small class="text-muted">Overdue</small></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="availability" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="borrowed" {{ request('availability') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search title, author, ISBN..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Search</button>
                <a href="{{ route('library.books.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>ISBN</th><th>Title</th><th>Author</th><th>Category</th><th>Publisher</th><th>Year</th><th>Total</th><th>Available</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
                <tr>
                    <td><code>{{ $book->isbn }}</code></td>
                    <td class="fw-semibold">{{ $book->title }}</td>
                    <td>{{ $book->author }}</td>
                    <td>{{ optional($book->category)->name }}</td>
                    <td>{{ $book->publisher }}</td>
                    <td>{{ $book->publication_year }}</td>
                    <td>{{ $book->copies_total }}</td>
                    <td>
                        @php $avail = $book->copies_available ?? ($book->copies_total - ($book->borrowed_count ?? 0)); @endphp
                        <span class="badge bg-{{ $avail > 0 ? 'success' : 'danger' }}">{{ $avail }}</span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('library.books.show', $book) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                                @can('manage-library')
                                <li><a class="dropdown-item" href="#" onclick="issueBook({{ $book->id }}, '{{ addslashes($book->title) }}')"><i class="bi bi-book me-2"></i>Issue Book</a></li>
                                <li><a class="dropdown-item" href="{{ route('library.books.edit', $book) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('library.books.destroy', $book) }}" onsubmit="return confirm('Delete this book? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@can('manage-library')
<div class="modal fade" id="addBookModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('library.books.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Book</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Author *</label>
                            <input type="text" name="author" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Publisher</label>
                            <input type="text" name="publisher" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category *</label>
                            <select name="book_category_id" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Year Published</label>
                            <input type="number" name="publication_year" class="form-control" min="1900" max="{{ date('Y') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Copies *</label>
                            <input type="number" name="copies_total" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Edition</label>
                            <input type="text" name="edition" class="form-control" placeholder="1st">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Shelf Location</label>
                            <input type="text" name="shelf_location" class="form-control" placeholder="e.g. A-12">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Book</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
