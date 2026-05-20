<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CompanyProfile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'company_name',
        'nif',
        'sector',
        'address',
        'employees_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(User::class, 'company_profile_id');
    }
}
