<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Lesson extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'module_id', 'title', 'type', 'content', 'video_url', 'is_preview', 'order',
        'meeting_platform', 'meeting_link', 'meeting_id', 'meeting_password'
    ];

    protected $casts = [
        'is_preview' => 'boolean',
    ];

    public function materials()
    {
        return $this->morphMany(Material::class, 'materialable');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
