<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Enrollment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'course_id',
        'certification_id',
        'progress',
        'status',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class);
    }
}
