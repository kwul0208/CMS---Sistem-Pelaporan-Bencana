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
    public function pengawas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengawas_id', 'id');
    }
    public function korwil(): BelongsTo
    {
        return $this->belongsTo(User::class, 'korwil_id', 'id');
    }

    public function photo_saluran(): HasMany
    {
        return $this->hasMany(PhotoSaluran::class, 'laporan_id', 'id');
    }

    public function photo_swakelola_pengukuran(): HasMany
    {
        return $this->hasMany(PhotoSwakelola::class, 'laporan_id', 'id')
                    ->where('type', 'Pengukuran');
    }

    public function photo_swakelola_hasil(): HasMany
    {
        return $this->hasMany(PhotoSwakelola::class, 'laporan_id', 'id')
                    ->where('type', 'Hasil');
    }
}

