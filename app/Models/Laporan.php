<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    use HasFactory;
    protected $table = 'laporan';
    protected $guarded = [];

    public function surveyor_name(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveyor', 'id');
    }

}

