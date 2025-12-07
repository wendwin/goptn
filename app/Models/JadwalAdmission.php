<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalAdmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'campus_id',
        'status', 
        'description', 
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function items()
    {
        return $this->hasMany(AdmissionItem::class, 'admission_id');
    }
}
