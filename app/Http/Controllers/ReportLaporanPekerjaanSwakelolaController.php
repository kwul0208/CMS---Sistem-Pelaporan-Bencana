<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportLaporanPekerjaanSwakelolaController extends Controller
{
    public function index() {
        return view('pages.report.laporan_pekerjaan_swakelola.index');
    }

    public function data(Request $request) {
        $query = Laporan::with('surveyor_name')
            ->where('section', 'Laporan Pekerjaan Swakelola')
            ->orderBy('date', 'desc');

        // Default: 1 bulan terakhir
        $startDate = $request->start_date ?? now()->subMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        $query->whereBetween('date', [$startDate, $endDate]);

        return DataTables::of($query->get())            
            ->addColumn('date', function ($row) {
                return $row->date;
            })
            ->addColumn('title', function ($row) {
                return $row->title;
            })
            ->addColumn('description', function ($row) {
                return $row->description;
            })
            ->addColumn('photo_pengukuran', function ($row) {
                $html = '<ul>';
                foreach ($row->photo_swakelola_pengukuran as $photo) {
                    $html .= '<li><a href="' . asset('storage/' . $photo->photo) . '" target="_blank">'. asset('storage/' . $photo->photo) .'</a></li>';
                }
                $html .= '</ul>';
                return $html;
            })
            ->addColumn('photo_hasil', function ($row) {
                $html = '<ul>';
                foreach ($row->photo_swakelola_hasil as $photo) {
                    $html .= '<li><a href="' . asset('storage/' . $photo->photo) . '" target="_blank">'. asset('storage/' . $photo->photo) .'</a></li>';
                }
                $html .= '</ul>';
                return $html;
            })
            ->addColumn('address', function ($row) {
                return $row->address;
            })
            ->addColumn('location', function ($row) {
                return $row->latitude.' - '. $row->longitude;
            })
            ->addColumn('surveyor', function ($row) {
                return $row->surveyor_name ? $row->surveyor_name->name : '-';
            })
            ->rawColumns(['photo_pengukuran', 'photo_hasil'])
            ->addIndexColumn() 

            ->make(true);
    }
    
    public function export(Request $request)
    {
        $startDate = $request->start_date ?? now()->subMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        return Excel::download(new ReportExport('Laporan Pekerjaan Swakelola',  $startDate, $endDate), 'report_laporan_pekerjaan_swakelola_' . now()->format('Y-m-d') . '.xlsx');
    }

}
