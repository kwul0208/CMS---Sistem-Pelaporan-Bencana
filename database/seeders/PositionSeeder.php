<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        // Struktur Induk
        $head = Position::create(['name' => 'Kepala UPTD SDA Wilayah V', 'parent_id' => 0]);
        $kasubtu = Position::create(['name' => 'Kasub TU UPTD SDA Wilayah V', 'parent_id' => $head->id]);

        // Sub-divisi
        $peng_admin = Position::create(['name' => 'Peng. ADM UMUM', 'parent_id' => $kasubtu->id]);
        $teknisi_air = Position::create(['name' => 'Teknisi Air', 'parent_id' => $head->id]);

        $tim_admin = Position::create(['name' => 'Tim Administrasi', 'parent_id' => $kasubtu->id]);
        $tim_teknis = Position::create(['name' => 'Tim Teknis', 'parent_id' => $head->id]);
        $humas_media = Position::create(['name' => 'Humas & Media', 'parent_id' => $kasubtu->id]);

        // Koordinator & Wilayah
        $wilayah = [
            'Wilayah Kec. Sepatan Timur',
            'Wilayah Kec. Sepatan',
            'Wilayah Kec. Sukadiri',
            'Mauk Bagian Timur',
            'Wilayah Kecamatan Mauk',
            'Mauk Bagian Barat',
        ];

        foreach ($wilayah as $w) {
            Position::create(['name' => "Koordinator Lapangan $w", 'parent_id' => $head->id]);
            Position::create(['name' => "Anggota $w", 'parent_id' => $head->id]);
        }
    }
}
