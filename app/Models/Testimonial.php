<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'company',
        'image',
        'content',
        'likes',
        'comments',
        'date_string',
        'verified',
        'is_active',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'is_active' => 'boolean',
        'likes' => 'integer',
        'comments' => 'integer',
    ];
}
