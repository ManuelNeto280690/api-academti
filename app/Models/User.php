<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasRoles, HasApiTokens;

    protected $guard_name = 'api';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'bi_id',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'trainer_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function profile()
    {
        return $this->hasOne(TrainerProfile::class);
    }

    public function mentorProfile()
    {
        return $this->hasOne(MentorProfile::class);
    }

    public function companyProfile()
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function company()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_profile_id');
    }

    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
                    ->withPivot('completed_at')
                    ->withTimestamps();
    }

    public function completedQuizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_user')
                    ->withPivot('score', 'status', 'is_locked', 'completed_at')
                    ->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
