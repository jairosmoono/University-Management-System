<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryBook extends Model {
    use HasFactory, SoftDeletes;

    // Actual DB columns: book_category_id, isbn, title, author, publisher, publication_year,
    // edition, copies_total, copies_available, shelf_location, description
    protected $fillable = [
        'book_category_id', 'isbn', 'title', 'author', 'publisher', 'publication_year',
        'edition', 'copies_total', 'copies_available', 'shelf_location', 'description',
    ];

    public function category() { return $this->belongsTo(BookCategory::class, 'book_category_id'); }
    public function borrowings() { return $this->hasMany(BookBorrowing::class, 'library_book_id'); }
    public function activeBorrowings() { return $this->hasMany(BookBorrowing::class, 'library_book_id')->where('status', 'borrowed'); }
    public function getIsAvailableAttribute() { return $this->copies_available > 0; }
    public function scopeActive($query) { return $query->where('copies_available', '>', 0); }
}
