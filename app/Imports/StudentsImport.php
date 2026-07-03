<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class StudentsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public function collection(Collection $rows) {}
}
