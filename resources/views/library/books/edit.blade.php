@extends('layouts.app')
@section('title', 'Edit Book')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Book</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('library.books.index') }}">Books</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-body">
        <form action="{{ route('library.books.update', $book) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $book->title) }}" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Author</label>
                    <input type="text" name="author" class="form-control" value="{{ old('author', $book->author) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" value="{{ old('isbn', $book->isbn) }}">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        @foreach(['book','journal','magazine','reference','thesis','ebook'] as $t)
                            <option value="{{ $t }}" {{ old('type', $book->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Copies</label>
                    <input type="number" name="total_copies" class="form-control" value="{{ old('total_copies', $book->total_copies) }}" min="1">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Publisher</label>
                    <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $book->publisher) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Publication Year</label>
                    <input type="number" name="publication_year" class="form-control" value="{{ old('publication_year', $book->publication_year) }}">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Book</button>
                <a href="{{ route('library.books.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
