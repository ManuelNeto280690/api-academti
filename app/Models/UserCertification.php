<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCertification extends Model
{
    protected $fillable = [
        'user_id',
        'certification_id',
        'issue_date',
        'certificate_code',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class);
    }
}
