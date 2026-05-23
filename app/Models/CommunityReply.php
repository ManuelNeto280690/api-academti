<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityReply extends Model
{
    protected $fillable = [
        'community_post_id', 'user_id', 'content', 'is_solution', 'likes_count', 'reported_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'community_post_id');
    }
}
