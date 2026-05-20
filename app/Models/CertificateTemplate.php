<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_default',
        'primary_color',
        'secondary_color',
        'background_image',
        'signature_image',
        'signature_name',
        'signature_title',
        'show_logo',
        'font_family',
        'layout',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'show_logo' => 'boolean',
        'layout' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function ($template) {
            if ($template->is_default) {
                // Remove default status from all other templates
                static::where('id', '!=', $template->id)->update(['is_default' => false]);
            }
        });
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
