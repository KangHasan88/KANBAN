<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    protected $fillable = [
        'file_name', 'file_path', 'file_type', 'file_size', 
        'mime_type', 'task_id', 'user_id', 'is_cover'
    ];
    
    protected $casts = [
        'is_cover' => 'boolean',
    ];
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getFileUrlAttribute()
    {
        return asset($this->file_path);
    }
    
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
    
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }
    
    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }
    
    public function isDocument()
    {
        $docTypes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ];
        return in_array($this->mime_type, $docTypes);
    }
}