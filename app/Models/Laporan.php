<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laporan extends Model
{
    use HasFactory;
    protected $table = 'laporan';
    protected $guarded = [];

    public function surveyor_name(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveyor', 'id');
    }

    public function photo_saluran(): HasMany
    {
        return $this->hasMany(PhotoSaluran::class, 'laporan_id', 'id');
    }
}

