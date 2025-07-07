<?php

namespace App\Http\Controllers;

use App\Models\Sosmed;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SosmedController extends Controller
{
    public function index() {
        $data = Sosmed::all();
        return view('pages.sosmed.index')->with('data', $data);
    }


    public function edit(Sosmed $sosmed) {
        $model = $sosmed;
        $sosmeds = Sosmed::all();
        return view('pages.sosmed.form', compact('model', 'sosmeds'));
    }

    public function update(Request $request, Sosmed $sosmed) {
        $data = $request->validate([
            'name' => 'required',
            'link' => 'required'
        ]);
        
        $sosmed->update($data);

        return redirect()->route('sosmed.index')->with('success', 'Data berhasil diupdate.');
    }

    public function getSosmed(){
        $sosmed = Sosmed::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diambil.',
            'data' => $sosmed
        ]);
    }
}
