<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'category_id',
        'type',
        'duration_hours',
        'level',
        'price',
        'validity_months',
        'status',
        'certificate_template_id',
        'prerequisites',
        'objectives',
        'exam_format',
        'salary_range',
        'partner_companies',
    ];

    protected $casts = [
        'duration_hours' => 'integer',
        'price' => 'decimal:2',
        'validity_months' => 'integer',
        'prerequisites' => 'array',
        'objectives' => 'array',
        'exam_format' => 'array',
        'salary_range' => 'array',
        'partner_companies' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_certifications')
            ->withPivot('issue_date', 'certificate_code')
            ->withTimestamps();
    }

    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
