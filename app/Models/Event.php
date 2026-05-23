<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'type', 'instructor', 'date', 'is_live'];
    
    protected $casts = [
        'date' => 'datetime',
        'is_live' => 'boolean',
    ];
}
