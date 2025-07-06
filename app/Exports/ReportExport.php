<?php

namespace App\Exports;

use App\Models\Laporan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReportExport implements FromCollection, WithHeadings, WithDrawings, WithEvents
{
    protected $laporans;
    protected $section, $startDate, $endDate;
    

    public function __construct($section = null, $startDate, $endDate)
    {
        $this->section = $section;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $query = Laporan::with('surveyor_name')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('date');

        if ($section) {
            $query->where('section', $section);
        }

        $this->laporans = $query->get();
    }

    public function collection()
    {
        if ($this->section == 'Tanggap Darurat Bencana') {
            return $this->laporans->map(function ($item) {
                return [
                    $item->date,
                    $item->title,
                    $item->type,
                    $item->description,
                    $item->address,
                    $item->latitude . ' - ' . $item->longitude,
                    $item->surveyor_name ? $item->surveyor_name->name : '-',
                    '', '', '', '', '', // untuk Foto 1–5 (kosong, karena pakai Drawing)
                    url('storage/' . $item->video), // atau sesuaikan jika URL lain
                ];
            });
        } else if ($this->section == 'Laporan Pekerjaan Rutin') {
            return $this->laporans->map(function ($item) {
                return [
                    $item->date,
                    $item->title,
                    $item->description,
                    $item->latitude . ' - ' . $item->longitude,
                    $item->surveyor_name ? $item->surveyor_name->name : '-',
                    '', '', '', '', '', // untuk Foto 1–4 (kosong, karena pakai Drawing)
                ];
            });
        } else {
            return $this->laporans->map(function ($item) {
                return [
                    $item->date,
                    $item->title,
                    $item->description,
                    $item->latitude . ' - ' . $item->longitude,
                    $item->surveyor_name ? $item->surveyor_name->name : '-',
                    '', '', // untuk Foto 1–4 (kosong, karena pakai Drawing)
                ];
            });
        }
    }

    public function headings(): array
    {
        if ($this->section == 'Tanggap Darurat Bencana') {
            return [
                'Tanggal',
                'Judul',
                'Tipe',
                'Deskripsi',
                'Alamat',
                'Koordinat',
                'Surveyor',
                'Foto 1',
                'Foto 2',
                'Foto 3',
                'Foto 4',
                'Foto 5',
                'Vidio Link',

            ];
        } else if ($this->section == 'Laporan Pekerjaan Rutin') {
            return [
                'Tanggal',
                'Judul',
                'Deskripsi',
                'Koordinat',
                'Surveyor',
                'Foto 1',
                'Foto 2',
                'Foto 3',
                'Foto 4',
            ];
        } else {
            return [
                'Tanggal',
                'Nama Saluran',
                'Deskripsi',
                'Koordinat',
                'Surveyor',
                'Foto Form Survey',
                'Foto',
            ];
        }
    }

    public function drawings()
    {
        $drawings = [];
        $row = 2; // mulai dari baris kedua (data pertama)
        if ($this->section == 'Tanggap Darurat Bencana') {
            foreach ($this->laporans as $laporan) {
                $fotoPaths = [
                    'H' => $laporan->photo_1,
                    'I' => $laporan->photo_2,
                    'J' => $laporan->photo_3,
                    'K' => $laporan->photo_4,
                    'L' => $laporan->photo_5,
                ];

                foreach ($fotoPaths as $col => $path) {
                    if ($path) {
                        $fullPath = storage_path('app/public/' . $path);
                        if (file_exists($fullPath)) {
                            $drawing = new Drawing();
                            $drawing->setName('Photo');
                            $drawing->setDescription('Photo');
                            $drawing->setPath($fullPath);
                            $drawing->setHeight(80); // kamu bisa sesuaikan tinggi
                            $drawing->setCoordinates($col . $row);
                            $drawings[] = $drawing;
                        }
                    }
                }

                $row++;
            }
        } else if ($this->section == 'Laporan Pekerjaan Rutin') {
            foreach ($this->laporans as $laporan) {
                $fotoPaths = [
                    'F' => $laporan->photo_1,
                    'G' => $laporan->photo_2,
                    'H' => $laporan->photo_3,
                    'I' => $laporan->photo_4,
                ];

                foreach ($fotoPaths as $col => $path) {
                    if ($path) {
                        $fullPath = storage_path('app/public/' . $path);
                        if (file_exists($fullPath)) {
                            $drawing = new Drawing();
                            $drawing->setName('Photo');
                            $drawing->setDescription('Photo');
                            $drawing->setPath($fullPath);
                            $drawing->setHeight(80); // kamu bisa sesuaikan tinggi
                            $drawing->setCoordinates($col . $row);
                            $drawings[] = $drawing;
                        }
                    }
                }

                $row++;
            }
        } else if ($this->section == 'Data Saluran') {
            foreach ($this->laporans as $laporan) {
                // Foto tunggal
                $foto1Path = $laporan->photo_1 ? storage_path('app/public/' . $laporan->photo_1) : null;
                if ($foto1Path && file_exists($foto1Path)) {
                    $drawing = new Drawing();
                    $drawing->setName('Photo 1');
                    $drawing->setDescription('Photo 1');
                    $drawing->setPath($foto1Path);
                    $drawing->setHeight(80);
                    $drawing->setCoordinates('F' . $row);
                    $drawings[] = $drawing;
                }

                // Foto saluran (banyak)
                $offset = 0; // untuk memindahkan gambar secara vertikal
                foreach ($laporan->photo_saluran as $photo) {
                    $photoPath = $photo->photo ? storage_path('app/public/' . $photo->photo) : null;
                    if ($photoPath && file_exists($photoPath)) {
                        $drawing = new Drawing();
                        $drawing->setName('Saluran');
                        $drawing->setDescription('Foto Saluran');
                        $drawing->setPath($photoPath);
                        $drawing->setHeight(60);
                        $drawing->setCoordinates('G' . $row);
                        $drawing->setOffsetY($offset); // Geser ke bawah per gambar
                        $drawings[] = $drawing;

                        $offset += 65; // Tambahkan offset untuk gambar berikutnya
                    }
                }

                $row++;
            }
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        if ($this->section == 'Tanggap Darurat Bencana') {
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $row = 2;
                    foreach ($this->laporans as $laporan) {
                        $hasImage = false;
                        foreach (['photo_1', 'photo_2', 'photo_3', 'photo_4', 'photo_5'] as $field) {
                            $path = $laporan->$field ? storage_path('app/public/' . $laporan->$field) : null;
                            if ($path && file_exists($path)) {
                                $hasImage = true;
                                break;
                            }
                        }

                        if ($hasImage) {
                            $event->sheet->getDelegate()->getRowDimension($row)->setRowHeight(60);
                        }

                        $row++;
                    }
                },
            ];
        } else if ($this->section == 'Laporan Pekerjaan Rutin') {
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $row = 2;
                    foreach ($this->laporans as $laporan) {
                        $hasImage = false;
                        foreach (['photo_1', 'photo_2', 'photo_3', 'photo_4'] as $field) {
                            $path = $laporan->$field ? storage_path('app/public/' . $laporan->$field) : null;
                            if ($path && file_exists($path)) {
                                $hasImage = true;
                                break;
                            }
                        }

                        if ($hasImage) {
                            $event->sheet->getDelegate()->getRowDimension($row)->setRowHeight(60);
                        }

                        $row++;
                    }
                },
            ];
        } else {
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $row = 2;

                    foreach ($this->laporans as $laporan) {
                        // Hitung jumlah gambar dari photo_1 dan relasi photo_saluran
                        $jumlahFoto = 0;

                        // Cek photo_1 (satu gambar)
                        $photo1Path = $laporan->photo_1 ? storage_path('app/public/' . $laporan->photo_1) : null;
                        if ($photo1Path && file_exists($photo1Path)) {
                            $jumlahFoto++;
                        }

                        // Cek semua gambar dari relasi photo_saluran (banyak)
                        if ($laporan->relationLoaded('photo_saluran') || method_exists($laporan, 'photo_saluran')) {
                            foreach ($laporan->photo_saluran as $photo) {
                                $path = $photo->photo ? storage_path('app/public/' . $photo->photo) : null;
                                if ($path && file_exists($path)) {
                                    $jumlahFoto++;
                                }
                            }
                        }

                        // Hitung tinggi baris
                        if ($jumlahFoto > 0) {
                            $tinggiPerGambar = 65; // satuan point (Excel)
                            $tinggiMinimal = 60;
                            $totalTinggi = max($tinggiMinimal, $jumlahFoto * $tinggiPerGambar);

                            $event->sheet->getDelegate()->getRowDimension($row)->setRowHeight($totalTinggi);
                        }

                        $row++;
                    }
                },
            ];
        }
    }
}

