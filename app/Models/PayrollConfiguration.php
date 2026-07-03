<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollConfiguration extends Model
{
    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = ['key', 'label', 'value', 'description', 'group'];

    /**
     * Return all configs as a keyed array: ['napsa_rate' => '5', ...]
     */
    public static function asArray(): array
    {
        return static::pluck('value', 'key')->toArray();
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }
}
