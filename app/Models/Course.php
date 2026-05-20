<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Course extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id', 'trainer_id', 'title', 'slug', 'description', 'about',
        'modalidade', 'location', 'preco_normal', 'preco_promocional', 'imagem', 
        'url_video_destaque', 'tags', 'numero_alunos', 'pagamento_vezes', 
        'tipo_destaque', 'tipo_acesso', 'rating', 'status',
        'meeting_platform', 'meeting_link', 'meeting_id', 'meeting_password',
        'sequential_unlock', 'min_pass_score', 'access_duration_days', 'certificate_enabled',
        'certificate_template_id', 'show_learning_outcomes', 'show_requirements',
        'duration_hours', 'level'
    ];

    public function materials()
    {
        return $this->morphMany(Material::class, 'materialable');
    }

    protected $casts = [
        'tags' => 'json',
        'preco_normal' => 'decimal:2',
        'preco_promocional' => 'decimal:2',
        'certificate_enabled' => 'boolean',
        'show_learning_outcomes' => 'boolean',
        'show_requirements' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function requirements()
    {
        return $this->hasMany(CourseRequirement::class)->orderBy('order');
    }

    public function learningOutcomes()
    {
        return $this->hasMany(CourseLearningOutcome::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class)->whereNull('module_id');
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }

    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }
}
