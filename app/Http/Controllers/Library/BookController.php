<?php
namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\BookCategory;
use App\Models\BookBorrowing;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = LibraryBook::with('category');
        if ($request->category_id) $query->where('book_category_id', $request->category_id);
        if ($request->availability === 'available') $query->where('copies_available', '>', 0);
        if ($request->availability === 'borrowed')  $query->where('copies_available', 0);
        if ($request->search) {
            $q = $request->search;
            $query->where(fn($q2) => $q2->where('title', 'like', "%$q%")
                ->orWhere('author', 'like', "%$q%")
                ->orWhere('isbn', 'like', "%$q%"));
        }
        $books = $query->orderBy('title')->paginate(20);
        $categories = BookCategory::all();
        $stats = [
            'total'     => LibraryBook::count(),
            'available' => LibraryBook::where('copies_available', '>', 0)->count(),
            'borrowed'  => BookBorrowing::where('status', 'borrowed')->count(),
            'overdue'   => BookBorrowing::where('status', 'overdue')->count(),
        ];
        return view('library.books.index', compact('books', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = BookCategory::all();
        return view('library.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'copies_total' => 'required|integer|min:1',
        ]);
        $data = $request->except('_token');
        $data['copies_available'] = $data['copies_total'];
        LibraryBook::create($data);
        return redirect()->route('library.books.index')->with('success', 'Book added to library.');
    }

    public function show(LibraryBook $book)
    {
        $book->load(['category', 'borrowings' => fn($q) => $q->latest()->take(10)]);
        return view('library.books.show', compact('book'));
    }

    public function edit(LibraryBook $book)
    {
        $categories = BookCategory::all();
        return view('library.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, LibraryBook $book)
    {
        $book->update($request->except('_token', '_method'));
        return redirect()->route('library.books.index')->with('success', 'Book updated.');
    }

    public function destroy(LibraryBook $book)
    {
        $book->delete();
        return redirect()->route('library.books.index')->with('success', 'Book deleted.');
    }
}
