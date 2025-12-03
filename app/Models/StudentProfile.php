<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_name',
        'city',
        'average_grade'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
