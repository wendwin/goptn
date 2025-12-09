<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionItem extends Model
{
    protected $fillable = [
        'admission_id',
        'name',
        'start_date',
        'end_date',
        'batch',
        'status',
        'description'
    ];

    public function admission()
    {
        return $this->belongsTo(JadwalAdmission::class);
    }

}
