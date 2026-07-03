<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model {
    use HasFactory;

    protected $fillable = ['title', 'description', 'file_path', 'file_name', 'file_type', 'file_size', 'category', 'version', 'uploaded_by', 'is_public', 'access_roles', 'download_count', 'status'];
    protected $casts = ['is_public' => 'boolean', 'access_roles' => 'array'];

    public function uploadedBy() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getFileSizeFormattedAttribute() {
        if ($this->file_size < 1024) return $this->file_size . ' B';
        if ($this->file_size < 1048576) return round($this->file_size / 1024, 1) . ' KB';
        return round($this->file_size / 1048576, 1) . ' MB';
    }

    public function getDownloadUrlAttribute() { return route('documents.download', $this->id); }
}
