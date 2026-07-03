<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollItemType extends Model
{
    protected $fillable = ['name', 'slug', 'category', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function allowances(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('category', 'allowance')->where('is_active', true)->orderBy('name')->get();
    }

    public static function deductions(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('category', 'deduction')->where('is_active', true)->orderBy('name')->get();
    }
}
