<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    protected $fillable = [
        'user_id', 'title', 'content', 'category', 'status', 'pinned', 'reported_count', 'likes_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(CommunityReply::class);
    }
}
