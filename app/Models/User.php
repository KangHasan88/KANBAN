<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'avatar',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && file_exists(public_path($this->avatar))) {
            return asset($this->avatar);
        }
        return 'https://ui-avatars.com/api/?background=1e3a5f&color=fff&name=' . urlencode($this->name);
    }

    // ==============================================
    // RELATIONSHIPS
    // ==============================================

    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    // Multiple assignees - tasks assigned to this user (MANY-TO-MANY)
    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    public function activities()
    {
        return $this->hasMany(TaskActivity::class);
    }
    
    public function sharedBoards()
    {
        return $this->belongsToMany(Board::class, 'board_user')
                    ->withPivot('permission')
                    ->withTimestamps();
    }
    
    public function findForPassport($username)
    {
        return $this->where('username', $username)->orWhere('email', $username)->first();
    }
}