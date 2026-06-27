<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GanttSetting extends Model
{
    protected $fillable = [
        'board_id',
        'view_mode',
        'zoom_level',
        'show_weekends',
        'show_progress',
        'show_dependencies',
        'filters'
    ];

    protected $casts = [
        'zoom_level' => 'integer',
        'show_weekends' => 'boolean',
        'show_progress' => 'boolean',
        'show_dependencies' => 'boolean',
        'filters' => 'array',
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public static function getForBoard($boardId)
    {
        return self::firstOrCreate(
            ['board_id' => $boardId],
            [
                'view_mode' => 'day',
                'zoom_level' => 1,
                'show_weekends' => true,
                'show_progress' => true,
                'show_dependencies' => false,
            ]
        );
    }
}