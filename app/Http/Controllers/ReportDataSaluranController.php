<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReportDataSaluranController extends Controller
{
    public function index() {
        return view('pages.report.data_saluran.index');
    }

    public function data() {
        return DataTables::of(Laporan::with('surveyor_name')->where('section', 'Data Saluran')->get())
            ->addColumn('date', function ($row) {
                return $row->date;
            })
            ->addColumn('title', function ($row) {
                return $row->title;
            })
            ->addColumn('description', function ($row) {
                return $row->description;
            })
            ->addColumn('photo_1', function ($row) {
                return $row->photo_1         ? '<a href="' . asset('storage/' . $row->photo_1) . '" target="_blank">'. asset('storage/' . $row->photo_1) .'</a>'         : '-';
            })
            ->addColumn('photo', function ($row) {
                $html = '<ul>';
                foreach ($row->photo_saluran as $photo) {
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
            ->rawColumns(['photo_1', 'photo'])
            ->addIndexColumn() 

            ->make(true);
    }

}
