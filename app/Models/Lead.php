<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = ['name', 'code', 'email', 'phone', 'stage', 'value', 'owner', 'is_active', 'meta'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'enabled' => 'boolean',
            'meta' => 'array'
        ];
    }
}
