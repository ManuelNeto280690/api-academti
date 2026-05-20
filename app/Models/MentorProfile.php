<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MentorProfile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'expertise',
        'bio',
        'price_per_session',
        'rating'
    ];

    protected $casts = [
        'expertise' => 'array',
        'price_per_session' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
