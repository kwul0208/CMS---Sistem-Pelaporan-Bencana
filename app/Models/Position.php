<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';
    protected $guarded = [];

    
    public function parent()
    {
        return $this->belongsTo(Position::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasOne(Position::class, 'parent_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'position_id', 'id');
    }
}

