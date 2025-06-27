<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReportTanggapDaruratBencanaController extends Controller
{
    public function index() {
        return view('pages.report.tanggap_darurat_bencana.index');
    }

    public function data() {
        return DataTables::of(Laporan::with('surveyor_name')->get())
            ->addColumn('date', function ($row) {
                return $row->date;
            })
            ->addColumn('title', function ($row) {
                return $row->title;
            })
            ->addColumn('type', function ($row) {
                return $row->type;
            })
            ->addColumn('description', function ($row) {
                return $row->description;
            })
            ->addColumn('photo_1', function ($row) {
                return $row->photo_1         ? '<a href="' . asset('storage/' . $row->photo_1) . '" target="_blank">'. asset('storage/' . $row->photo_1) .'</a>'         : '-';
            })
            ->addColumn('photo_2', function ($row) {
                return $row->photo_2         ? '<a href="' . asset('storage/' . $row->photo_2) . '" target="_blank">'. asset('storage/' . $row->photo_2) .'</a>'         : '-';
            })
            ->addColumn('photo_3', function ($row) {
                return $row->photo_3         ? '<a href="' . asset('storage/' . $row->photo_3) . '" target="_blank">'. asset('storage/' . $row->photo_3) .'</a>'         : '-';
            })
            ->addColumn('photo_4', function ($row) {
                return $row->photo_4         ? '<a href="' . asset('storage/' . $row->photo_4) . '" target="_blank">'. asset('storage/' . $row->photo_4) .'</a>'         : '-';
            })
            ->addColumn('photo_5', function ($row) {
                return $row->photo_5         ? '<a href="' . asset('storage/' . $row->photo_5) . '" target="_blank">'. asset('storage/' . $row->photo_5) .'</a>'         : '-';
            })
            ->addColumn('video', function ($row) {
                return $row->video ? '<a href="' . asset('storage/' . $row->video) . '" target="_blank">'. asset('storage/' . $row->video) .'</a>' : '-';
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
            ->rawColumns(['photo_1', 'photo_2', 'photo_3', 'photo_4', 'photo_5', 'video'])
            ->addIndexColumn() 

            ->make(true);
    }

}
