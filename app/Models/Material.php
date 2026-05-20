<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Material extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'materialable_id', 'materialable_type', 'title', 
        'file_path', 'file_size', 'type'
    ];

    /**
     * Get the parent materialable model (Course or Lesson).
     */
    public function materialable()
    {
        return $this->morphTo();
    }
}
