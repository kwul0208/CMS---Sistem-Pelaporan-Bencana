<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
