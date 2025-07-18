<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportLaporanPekerjaanRutinController extends Controller
{
    public function index() {
        return view('pages.report.laporan_pekerjaan_rutin.index');
    }

    public function data(Request $request) {
        $query = Laporan::with('surveyor_name')
            ->where('section', 'Laporan Pekerjaan Rutin')
            ->orderBy('date', 'desc');

        // Default: 1 bulan terakhir
        // $startDate = $request->start_date ?? now()->subMonth()->toDateString();
        // $endDate = $request->end_date ?? now()->toDateString();
        // $query->whereBetween('date', [$startDate, $endDate]);

        return DataTables::of($query->get())
            ->addColumn('date', function ($row) {
                return $row->date;
            })
            ->addColumn('title', function ($row) {
                return $row->title;
            })
            ->addColumn('period', function ($row) {
                return $row->period;
            })
            ->addColumn('description', function ($row) {
                return $row->description;
            })
            ->addColumn('photo_1', function ($row) {
                return $row->photo_1 ? '<a href="' . asset('storage/' . $row->photo_1) . '" target="_blank"><img src="' . asset('storage/' . $row->photo_1) . '" width="50" height="50"></a>' : '-';
            })
            ->addColumn('photo_2', function ($row) {
                return $row->photo_2 ? '<a href="' . asset('storage/' . $row->photo_2) . '" target="_blank"><img src="' . asset('storage/' . $row->photo_2) . '" width="50" height="50"></a>' : '-';
            })
            ->addColumn('photo_3', function ($row) {
                return $row->photo_3 ? '<a href="' . asset('storage/' . $row->photo_3) . '" target="_blank"><img src="' . asset('storage/' . $row->photo_3) . '" width="50" height="50"></a>' : '-';
            })
            ->addColumn('photo_4', function ($row) {
                return $row->photo_4 ? '<a href="' . asset('storage/' . $row->photo_4) . '" target="_blank"><img src="' . asset('storage/' . $row->photo_4) . '" width="50" height="50"></a>' : '-';
            })
            ->addColumn('address', function ($row) {
                return $row->address;
            })
            ->addColumn('location', function ($row) {
                return $row->latitude . ' - ' . $row->longitude;
            })
            ->addColumn('surveyor', function ($row) {
                return $row->surveyor_name ? $row->surveyor_name->name : '-';
            })
            ->rawColumns(['photo_1', 'photo_2', 'photo_3', 'photo_4'])
            ->addIndexColumn()
            ->make(true);
    }
    public function export(Request $request)
    {
        $startDate = $request->start_date ?? now()->subMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        return Excel::download(new ReportExport('Laporan Pekerjaan Rutin', $startDate, $endDate), 'report_laporan_pekerjaan_rutin_' . now()->format('Y-m-d') . '.xlsx');
    }
}
