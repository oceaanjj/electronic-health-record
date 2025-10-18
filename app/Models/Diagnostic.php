<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Diagnostic extends Model
{
    use HasFactory;

    protected $table = 'diagnostic';

    protected $fillable = [
        'patient_id',
        'uploader_id',
        'diagnostic_type',
        'path',
        'filename',
    ];

    /**
     * Get the patient that this diagnostic image belongs to.
     * We specify the foreign and owner keys because your patient model uses 'patient_id'.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    /**
     * Get the user (nurse) who uploaded the image.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    /**
     * Accessor to get the full public URL of the image.
     */
    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }
}