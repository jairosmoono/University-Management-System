<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookCategory;
use App\Models\LibraryBook;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Computer Science', 'code' => 'CS'],
            ['name' => 'Mathematics', 'code' => 'MTH'],
            ['name' => 'Business & Management', 'code' => 'BUS'],
            ['name' => 'Law', 'code' => 'LAW'],
            ['name' => 'Science & Technology', 'code' => 'SCI'],
            ['name' => 'Social Sciences', 'code' => 'SOC'],
            ['name' => 'Medicine & Health', 'code' => 'MED'],
            ['name' => 'Literature & Arts', 'code' => 'LIT'],
            ['name' => 'Reference', 'code' => 'REF'],
            ['name' => 'Journals & Periodicals', 'code' => 'JNL'],
        ];

        foreach ($categories as $cat) {
            BookCategory::firstOrCreate(['code' => $cat['code']], $cat);
        }

        $csCategory = BookCategory::where('code', 'CS')->first();
        $busCategory = BookCategory::where('code', 'BUS')->first();
        $lawCategory = BookCategory::where('code', 'LAW')->first();

        $books = [
            ['title' => 'Introduction to Algorithms', 'author' => 'Cormen, Leiserson, Rivest, Stein', 'isbn' => '9780262033848', 'category_id' => $csCategory?->id, 'publisher' => 'MIT Press', 'publication_year' => 2022, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'CS-A1', 'type' => 'physical'],
            ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'isbn' => '9780132350884', 'category_id' => $csCategory?->id, 'publisher' => 'Prentice Hall', 'publication_year' => 2008, 'total_copies' => 3, 'available_copies' => 3, 'shelf_location' => 'CS-A2', 'type' => 'physical'],
            ['title' => 'Design Patterns', 'author' => 'Gang of Four', 'isbn' => '9780201633610', 'category_id' => $csCategory?->id, 'publisher' => 'Addison-Wesley', 'publication_year' => 1994, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'CS-A3', 'type' => 'physical'],
            ['title' => 'Principles of Management', 'author' => 'Harold Koontz', 'isbn' => '9780070527560', 'category_id' => $busCategory?->id, 'publisher' => 'McGraw-Hill', 'publication_year' => 2020, 'total_copies' => 6, 'available_copies' => 6, 'shelf_location' => 'BUS-B1', 'type' => 'physical'],
            ['title' => 'Financial Accounting', 'author' => 'Jerry Weygandt', 'isbn' => '9781119334545', 'category_id' => $busCategory?->id, 'publisher' => 'Wiley', 'publication_year' => 2021, 'total_copies' => 8, 'available_copies' => 8, 'shelf_location' => 'BUS-B2', 'type' => 'physical'],
            ['title' => 'Contract Law', 'author' => 'Ewan McKendrick', 'isbn' => '9781352009774', 'category_id' => $lawCategory?->id, 'publisher' => 'Palgrave', 'publication_year' => 2020, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'LAW-C1', 'type' => 'physical'],
            ['title' => 'Database System Concepts', 'author' => 'Silberschatz, Korth, Sudarshan', 'isbn' => '9780078022159', 'category_id' => $csCategory?->id, 'publisher' => 'McGraw-Hill', 'publication_year' => 2019, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'CS-A4', 'type' => 'physical'],
        ];

        foreach ($books as $book) {
            LibraryBook::firstOrCreate(['isbn' => $book['isbn']], array_merge($book, ['status' => 'available']));
        }

        $this->command->info('Library data seeded successfully.');
    }
}
