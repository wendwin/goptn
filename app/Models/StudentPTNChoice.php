<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPTNChoice extends Model
{
    use HasFactory;

    protected $table = 'student_ptn_choices'; 

    protected $fillable = [
        'user_id',
        'university_name',
        'major'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
