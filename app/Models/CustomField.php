<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = [
        'name', 'label', 'type', 'options', 'required', 'order', 'board_id', 'user_id'
    ];
    
    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];
    
    public function board()
    {
        return $this->belongsTo(Board::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function taskValues()
    {
        return $this->hasMany(TaskCustomFieldValue::class);
    }
    
    public function renderInput($value = null)
    {
        $name = "custom_fields[{$this->id}]";
        $required = $this->required ? 'required' : '';
        
        switch ($this->type) {
            case 'text':
                return "<input type=\"text\" name=\"{$name}\" value=\"" . htmlspecialchars($value) . "\" class=\"w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500\" {$required}>";
                
            case 'number':
                return "<input type=\"number\" name=\"{$name}\" value=\"" . htmlspecialchars($value) . "\" class=\"w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500\" {$required}>";
                
            case 'date':
                return "<input type=\"date\" name=\"{$name}\" value=\"" . htmlspecialchars($value) . "\" class=\"w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500\" {$required}>";
                
            case 'textarea':
                return "<textarea name=\"{$name}\" rows=\"3\" class=\"w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500\" {$required}>" . htmlspecialchars($value) . "</textarea>";
                
            case 'checkbox':
                $checked = $value ? 'checked' : '';
                return "<input type=\"hidden\" name=\"{$name}\" value=\"0\"><input type=\"checkbox\" name=\"{$name}\" value=\"1\" class=\"w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500\" {$checked} {$required}>";
                
            case 'dropdown':
                $options = $this->options ?? [];
                $html = "<select name=\"{$name}\" class=\"w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500\" {$required}>";
                $html .= "<option value=\"\">-- Select --</option>";
                foreach ($options as $option) {
                    $selected = ($value == $option) ? 'selected' : '';
                    $html .= "<option value=\"" . htmlspecialchars($option) . "\" {$selected}>" . htmlspecialchars($option) . "</option>";
                }
                $html .= "</select>";
                return $html;
                
            default:
                return "<input type=\"text\" name=\"{$name}\" value=\"" . htmlspecialchars($value) . "\" class=\"w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500\" {$required}>";
        }
    }
    
    public function formatValue($value)
    {
        if ($value === null || $value === '') return '-';
        
        if ($this->type === 'checkbox') {
            return $value ? '✓ Yes' : '✗ No';
        }
        
        return htmlspecialchars($value);
    }
}