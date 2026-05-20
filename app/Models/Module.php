<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Module extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['course_id', 'certification_id', 'title', 'description', 'order'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function materials()
    {
        return $this->morphMany(Material::class, 'materialable');
    }
}
