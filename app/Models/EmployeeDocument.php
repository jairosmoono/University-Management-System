<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'employee_id', 'document_type', 'title',
        'file_path', 'file_name', 'file_size', 'mime_type', 'uploaded_by',
    ];

    public function employee()   { return $this->belongsTo(Employee::class); }
    public function uploadedBy() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'nrc'           => 'NRC',
            'cv'            => 'CV / Resume',
            'qualification' => 'Professional Qualification',
            'accreditation' => 'Accreditation / Licence',
            default         => ucfirst($this->document_type),
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->document_type) {
            'nrc'           => 'bi-person-vcard',
            'cv'            => 'bi-file-person',
            'qualification' => 'bi-award',
            'accreditation' => 'bi-patch-check',
            default         => 'bi-file-earmark',
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match($this->document_type) {
            'nrc'           => 'bg-info',
            'cv'            => 'bg-primary',
            'qualification' => 'bg-success',
            'accreditation' => 'bg-warning text-dark',
            default         => 'bg-secondary',
        };
    }

    public function getFileSizeHumanAttribute(): string
    {
        $kb = $this->file_size / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 1) . ' MB';
    }
}
