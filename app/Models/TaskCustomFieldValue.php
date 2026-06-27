<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskCustomFieldValue extends Model
{
    protected $fillable = ['task_id', 'custom_field_id', 'value'];
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }
}