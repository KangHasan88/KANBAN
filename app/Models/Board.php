<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'user_id',
        'auto_archive_enabled',
        'auto_archive_days',
        'auto_archive_list_name',
        'card_aging_enabled',
        'card_aging_days',
        'cover_enabled'
    ];
    
    protected $casts = [
        'auto_archive_enabled' => 'boolean',
        'auto_archive_days' => 'integer',
        'card_aging_enabled' => 'boolean',
        'card_aging_days' => 'integer',
        'cover_enabled' => 'boolean',
    ];
    
    // ==============================================
    // RELATIONSHIPS
    // ==============================================
    
    public function lists()
    {
        return $this->hasMany(TaskList::class)->orderBy('order');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function sharedUsers()
    {
        return $this->belongsToMany(User::class, 'board_user')
                    ->withPivot('permission')
                    ->withTimestamps();
    }
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function hasAccess($userId)
    {
        return $this->user_id == $userId || 
               $this->sharedUsers()->where('user_id', $userId)->exists();
    }
    
    public function labels()
    {
        return $this->hasMany(Label::class);
    }
    
    // ==============================================
    // TASK TEMPLATES RELATION
    // ==============================================
    
    public function taskTemplates()
    {
        return $this->hasMany(TaskTemplate::class);
    }
    
    // ==============================================
    // CUSTOM FIELDS
    // ==============================================
    
    public function customFields()
    {
        return $this->hasMany(CustomField::class)->orderBy('order');
    }
    
    // ==============================================
    // AUTO ARCHIVE HELPERS
    // ==============================================
    
    public function isAutoArchiveEnabled()
    {
        return $this->auto_archive_enabled;
    }
    
    public function getAutoArchiveDays()
    {
        return $this->auto_archive_days ?? 7;
    }
    
    public function getAutoArchiveListName()
    {
        return $this->auto_archive_list_name ?? 'Done';
    }
    
    // ==============================================
    // CARD AGING HELPERS
    // ==============================================
    
    public function isCardAgingEnabled()
    {
        return $this->card_aging_enabled;
    }
    
    public function getCardAgingDays()
    {
        return $this->card_aging_days ?? 7;
    }
    
    // ==============================================
    // COVER SETTINGS (ADDED)
    // ==============================================
    
    public function isCoverEnabled()
    {
        return $this->cover_enabled ?? true;
    }
}