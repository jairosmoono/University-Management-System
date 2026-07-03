<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model {
    use HasFactory;

    protected $fillable = ['name', 'code', 'description'];

    public function books() { return $this->hasMany(LibraryBook::class, 'book_category_id'); }
}
