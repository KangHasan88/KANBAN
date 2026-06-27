<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $fillable = ['name', 'is_checked', 'checklist_id', 'order'];
    
    protected $casts = [
        'is_checked' => 'boolean',
    ];
    
    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}