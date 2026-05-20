<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'slug', 'color', 'icon'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
