<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportTanggapDaruratBencanaController extends Controller
{
    public function index() {
        return view('pages.report.tanggap_darurat_bencana.index');
    }
    public function data(Request $request) {
        $query = Laporan::with('surveyor_name')
            ->where('section', 'Tanggap Darurat Bencana')
            ->orderBy('date', 'desc');

        // Default: 1 bulan terakhir
        $startDate = $request->start_date ?? now()->subMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();
        Log::info([$startDate, $endDate]);

        $query->whereBetween('date', [$startDate, $endDate]);

        return DataTables::of($query->get())
            ->addColumn('date', fn($row) => $row->date)
            ->addColumn('title', fn($row) => $row->title)
            ->addColumn('type', fn($row) => $row->type)
            ->addColumn('description', fn($row) => $row->description)
            ->addColumn('photo_1', fn($row) => $row->photo_1 ? '<a href="' . asset('storage/' . $row->photo_1) . '" target="_blank">' . asset('storage/' . $row->photo_1) . '</a>' : '-')
            ->addColumn('photo_2', fn($row) => $row->photo_2 ? '<a href="' . asset('storage/' . $row->photo_2) . '" target="_blank">' . asset('storage/' . $row->photo_2) . '</a>' : '-')
            ->addColumn('photo_3', fn($row) => $row->photo_3 ? '<a href="' . asset('storage/' . $row->photo_3) . '" target="_blank">' . asset('storage/' . $row->photo_3) . '</a>' : '-')
            ->addColumn('photo_4', fn($row) => $row->photo_4 ? '<a href="' . asset('storage/' . $row->photo_4) . '" target="_blank">' . asset('storage/' . $row->photo_4) . '</a>' : '-')
            ->addColumn('photo_5', fn($row) => $row->photo_5 ? '<a href="' . asset('storage/' . $row->photo_5) . '" target="_blank">' . asset('storage/' . $row->photo_5) . '</a>' : '-')
            ->addColumn('video', fn($row) => $row->video ? '<a href="' . asset('storage/' . $row->video) . '" target="_blank">' . asset('storage/' . $row->video) . '</a>' : '-')
            ->addColumn('address', fn($row) => $row->address)
            ->addColumn('location', fn($row) => $row->latitude . ' - ' . $row->longitude)
            ->addColumn('surveyor', fn($row) => $row->surveyor_name->name ?? '-')
            ->rawColumns(['photo_1', 'photo_2', 'photo_3', 'photo_4', 'photo_5', 'video'])
            ->addIndexColumn()
            ->make(true);
    }


    public function export(Request $request)
    {
        $startDate = $request->start_date ?? now()->subMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        return Excel::download(new ReportExport('Tanggap Darurat Bencana',  $startDate, $endDate), 'report_tanggap_darurat_bencana_' . now()->format('Y-m-d') . '.xlsx');
    }


}
