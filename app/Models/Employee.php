<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'code', 'email', 'phone', 'department', 'designation', 'salary', 'joined_at', 'is_active', 'meta'];

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
