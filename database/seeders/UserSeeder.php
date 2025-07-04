<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin', 'position' => ''],
            ['name' => 'Aefine Rumedi, S.T., M.IP', 'position' => 'Kepala UPTD SDA Wilayah V'],
            ['name' => 'Mia Fitita Devi, S.T', 'position' => 'Kasub TU UPTD SDA Wilayah V'],
            ['name' => 'Heldi Apriadi', 'position' => 'Peng. ADM UMUM'],
            ['name' => 'Nana Suryana', 'position' => 'Teknisi Air'],
            
            // Tim Administrasi
            ['name' => 'Niar Arfidaniah, S.T', 'position' => 'Tim Administrasi'],
            ['name' => 'Siti Rosiah', 'position' => 'Tim Administrasi'],
            ['name' => 'Nurshea, S.Kom', 'position' => 'Tim Administrasi'],
            ['name' => 'Misja', 'position' => 'Tim Administrasi'],
            ['name' => 'Dedi Suryadi', 'position' => 'Tim Administrasi'],
            
            // Humas & Media
            ['name' => 'Jemmy Susilo, S.E', 'position' => 'Humas & Media'],
            ['name' => 'Aldi Setiawan, S.H', 'position' => 'Humas & Media'],
            ['name' => 'Teguh Akbar', 'position' => 'Humas & Media'],
            ['name' => 'Ghassan Austin B., S.I.Kom', 'position' => 'Humas & Media'],
            
            // Tim Teknis
            ['name' => 'Asep Mulyadi, S.IP', 'position' => 'Tim Teknis'],
            ['name' => 'Purnomo Satria, N.S., Kom', 'position' => 'Tim Teknis'],
            ['name' => 'Dede Sandi', 'position' => 'Tim Teknis'],
            ['name' => 'Nurul Yustika, S.T', 'position' => 'Tim Teknis'],
            ['name' => 'Darto', 'position' => 'Tim Teknis'],
            
            // Koordinator Lapangan Wilayah Kec. Sepatan Timur
            ['name' => 'Mangsur', 'position' => 'Koordinator Lapangan Wilayah Kec. Sepatan Timur'],
            ['name' => 'Amid Dayut', 'position' => 'Anggota Wilayah Kec. Sepatan Timur'],
            ['name' => 'Hendrik', 'position' => 'Anggota Wilayah Kec. Sepatan Timur'],
            ['name' => 'Resin', 'position' => 'Anggota Wilayah Kec. Sepatan Timur'],
            
            // Koordinator Lapangan Wilayah Kec. Sepatan
            ['name' => 'Aswi Suprianto', 'position' => 'Koordinator Lapangan Wilayah Kec. Sepatan'],
            ['name' => 'Rusdi', 'position' => 'Anggota Wilayah Kec. Sepatan'],
            ['name' => 'Sudin', 'position' => 'Anggota Wilayah Kec. Sepatan'],
            ['name' => 'M. Arif Soleh', 'position' => 'Anggota Wilayah Kec. Sepatan'],
            
            // Koordinator Lapangan Wilayah Kec. Sukadiri
            ['name' => 'Dulhadi', 'position' => 'Koordinator Lapangan Wilayah Kec. Sukadiri'],
            ['name' => 'Sartamin', 'position' => 'Anggota Wilayah Kec. Sukadiri'],
            ['name' => 'Suparman', 'position' => 'Anggota Wilayah Kec. Sukadiri'],
            ['name' => 'Amsin', 'position' => 'Anggota Wilayah Kec. Sukadiri'],
            
            // Koordinator Lapangan Mauk Bagian Timur
            ['name' => 'Basri', 'position' => 'Koordinator Lapangan Mauk Bagian Timur'],
            ['name' => 'Abdurohman', 'position' => 'Anggota Mauk Bagian Timur'],
            ['name' => 'Nurhadi', 'position' => 'Anggota Mauk Bagian Timur'],
            ['name' => 'Badri', 'position' => 'Anggota Mauk Bagian Timur'],
            ['name' => 'Yahya', 'position' => 'Anggota Mauk Bagian Timur'],
            ['name' => 'Andri Salosa', 'position' => 'Anggota Mauk Bagian Timur'],
            
            // Koordinator Lapangan Wilayah Kecamatan Mauk
            ['name' => 'Aswan', 'position' => 'Koordinator Lapangan Wilayah Kecamatan Mauk'],
            ['name' => 'Thomas', 'position' => 'Anggota Wilayah Kecamatan Mauk'],
            ['name' => 'Abdul Mazid', 'position' => 'Anggota Wilayah Kecamatan Mauk'],
            ['name' => 'Sulha', 'position' => 'Anggota Wilayah Kecamatan Mauk'],
            ['name' => 'Asmana', 'position' => 'Anggota Wilayah Kecamatan Mauk'],
            ['name' => 'Sarban', 'position' => 'Anggota Wilayah Kecamatan Mauk'],
        ];

        foreach ($users as $index => $user) {
            // Find position, handle empty position for Admin
            $position = null;
            if (!empty($user['position'])) {
                $position = Position::where('name', $user['position'])->first();
            }

            User::create([
                'name' => $user['name'],
                'email' => $user['name'] == 'Admin' ? 'admin@example.com' : 'user' . ($index + 1) . '@example.com',
                'password' => Hash::make('password'),
                'position_id' => $position?->id,
                'role' => $user['name'] == 'Admin' ? 'admin' : 'user'
            ]);
        }
    }
}