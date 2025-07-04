<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        // Level 1 - Top Leadership
        $kepala = Position::create([
            'name' => 'Kepala UPTD SDA Wilayah V',
            'parent_id' => null
        ]);

        // Level 2 - Direct Reports
        $kasub_tu = Position::create([
            'name' => 'Kasub TU UPTD SDA Wilayah V',
            'parent_id' => $kepala->id
        ]);

        $peng_admin = Position::create([
            'name' => 'Peng. ADM UMUM',
            'parent_id' => $kepala->id
        ]);

        $teknisi_air = Position::create([
            'name' => 'Teknisi Air',
            'parent_id' => $kepala->id
        ]);

        // Level 3 - Teams
        $tim_admin = Position::create([
            'name' => 'Tim Administrasi',
            'parent_id' => $kasub_tu->id
        ]);

        $humas_media = Position::create([
            'name' => 'Humas & Media',
            'parent_id' => $kasub_tu->id
        ]);

        $tim_teknis = Position::create([
            'name' => 'Tim Teknis',
            'parent_id' => $kepala->id
        ]);

        // Level 4 - Regional Coordinators
        $koordinator_sepatan_timur = Position::create([
            'name' => 'Koordinator Lapangan Wilayah Kec. Sepatan Timur',
            'parent_id' => $kepala->id
        ]);

        $koordinator_sepatan = Position::create([
            'name' => 'Koordinator Lapangan Wilayah Kec. Sepatan',
            'parent_id' => $kepala->id
        ]);

        $koordinator_sukadiri = Position::create([
            'name' => 'Koordinator Lapangan Wilayah Kec. Sukadiri',
            'parent_id' => $kepala->id
        ]);

        $koordinator_mauk_timur = Position::create([
            'name' => 'Koordinator Lapangan Mauk Bagian Timur',
            'parent_id' => $kepala->id
        ]);

        $koordinator_mauk = Position::create([
            'name' => 'Koordinator Lapangan Wilayah Kecamatan Mauk',
            'parent_id' => $kepala->id
        ]);

        // Level 5 - Field Staff Members
        $field_positions = [
            // Sepatan Timur
            ['name' => 'Anggota Wilayah Kec. Sepatan Timur', 'parent' => $koordinator_sepatan_timur->id],
            
            // Sepatan
            ['name' => 'Anggota Wilayah Kec. Sepatan', 'parent' => $koordinator_sepatan->id],
            
            // Sukadiri
            ['name' => 'Anggota Wilayah Kec. Sukadiri', 'parent' => $koordinator_sukadiri->id],
            
            // Mauk Timur
            ['name' => 'Anggota Mauk Bagian Timur', 'parent' => $koordinator_mauk_timur->id],
            
            // Mauk
            ['name' => 'Anggota Wilayah Kecamatan Mauk', 'parent' => $koordinator_mauk->id],
        ];

        foreach ($field_positions as $position) {
            Position::create([
                'name' => $position['name'],
                'parent_id' => $position['parent']
            ]);
        }
    }
}