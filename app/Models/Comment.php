<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['content', 'user_id', 'task_id', 'parent_id', 'mentions', 'likes_count'];
    
    protected $casts = [
        'mentions' => 'array',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at', 'asc');
    }
    
    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }
    
    public function isLikedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
    
    public function getLikeCountAttribute()
    {
        return $this->likes()->count();
    }
}