<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = ['name', 'code', 'employee_id', 'punched_at', 'device', 'type', 'notes', 'is_active', 'meta'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'enabled' => 'boolean',
            'meta' => 'array',
            'issued_at' => 'datetime',
            'joined_at' => 'date',
            'punched_at' => 'datetime',
            'due_at' => 'datetime',
            'entry_date' => 'date'
        ];
    }
}
