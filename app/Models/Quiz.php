<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Quiz extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['course_id', 'module_id', 'certification_id', 'title', 'description', 'min_score'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'quiz_user')
                    ->withPivot('score', 'status', 'is_locked', 'completed_at')
                    ->withTimestamps();
    }
}
