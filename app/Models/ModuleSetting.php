<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleSetting extends Model
{
    protected $fillable = ['key', 'enabled', 'meta'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'enabled' => 'boolean',
            'meta' => 'array'
        ];
    }
}
