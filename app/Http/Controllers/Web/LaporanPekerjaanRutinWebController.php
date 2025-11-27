<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;


class LaporanPekerjaanRutinWebController extends Controller
{
    public function index() {
        $title = 'Laporan Pekerjaan Rutin';
        return view('pages.laporan_pekerjaan_rutin.index')->with('title', $title);
    }

    public function data() {
        $query = Laporan::with('surveyor_name')
            ->where('section', 'Laporan Pekerjaan Rutin')
            ->orderByDesc('created_at');
        if (auth()->user()->role != 'admin') {
            $query->where('surveyor', auth()->user()->id);
        }

        return DataTables::of($query->get())
            ->addColumn('surveyor', function (Laporan $laporan) {
                return $laporan->surveyor_name ? $laporan->surveyor_name->name : '-';
            })
            ->make(true);
    }

    public function show(Laporan $laporan_pekerjaan_rutin)
    {
        return view('pages.laporan_pekerjaan_rutin.show', compact('laporan_pekerjaan_rutin'));
    }



    public function create() {
        return view('pages.laporan_pekerjaan_rutin.form');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photo_1' => 'required|image',
            'photo_2' => 'required|image',
            'photo_3' => 'required|image',
            'photo_4' => 'required|image',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'date' => 'required|date',
            'period' => 'required',
        ]);


        $validated = $validator->validated();

        try {
            foreach (['photo_1', 'photo_2', 'photo_3', 'photo_4'] as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('public/uploads/laporan', $filename);
                    $validated[$field] = 'uploads/laporan/' . $filename;
                }
            }
            $validated['section'] = 'Laporan Pekerjaan Rutin';
            $validated['surveyor'] = auth()->user()->id;
            $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');
            $data = Laporan::create($validated);

            // ===== PUSH NOTIFICATION =====
            $projectId = env('FIREBASE_PROJECT_ID');
            $topic = "laporan";
            $fullname = auth()->user()->name;
            $name_request = "Laporan Pekerjaan Rutin";
            $route = "laporan_pekerjaan_rutin";

            $message = [
                "message" => [
                    "topic" => $topic,
                    "notification" => [
                        "title" => $name_request,
                        "body" => "{$fullname} telah menginput data laporan {$name_request}"
                    ],
                    "data" => [
                        "route" => $route
                    ]
                ]
            ];
            
            try {
                $accessToken = getAccessToken();
                $response = sendMessage($accessToken, $projectId, $message);
            } catch (\Exception $e) {
            }
            // ===== END PUSH NOTIFICATION =====

            return redirect()->route('laporan_pekerjaan_rutin.index')->with('success', 'Laporan berhasil ditambah.');

        } catch (\Throwable $th) {
            return redirect()->route('laporan_pekerjaan_rutin.index')->with('error', 'Laporan gagal ditambah.');
        }
    }

    public function edit(Laporan $laporan_pekerjaan_rutin) {
        return view('pages.laporan_pekerjaan_rutin.form', compact('laporan_pekerjaan_rutin'));
    }


    public function update(Request $request, Laporan $laporan) {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'photo_1' => 'nullable|image',
            'photo_2' => 'nullable|image',
            'photo_3' => 'nullable|image',
            'photo_4' => 'nullable|image',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'date' => 'required|date',
            'period' => 'required',
        ]);

        // Cek dan update jika ada file baru

        $laporan = Laporan::where('section', 'Laporan Pekerjaan Rutin')->where('id', $request->id)->first();

        foreach (['photo_1', 'photo_2', 'photo_3', 'photo_4'] as $field) {
            if ($request->hasFile($field)) {
                // Optional: hapus file lama jika diperlukan
                if ($laporan->$field) {
                    Storage::delete('public/' . $laporan->$field);
                }

                $file = $request->file($field);
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/uploads/laporan', $filename);
                $validated[$field] = 'uploads/laporan/' . $filename;
            }
        }
        $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');
        $laporan->update($validated);

        return redirect()->route('laporan_pekerjaan_rutin.index')->with('success', 'Laporan berhasil diupdate.');
    }

    public function destroy(Laporan $laporan_pekerjaan_rutin) {
        $laporan_pekerjaan_rutin->delete();
        return redirect()->route('laporan_pekerjaan_rutin.index')->with('success', 'Laporan berhasil dihapus.');
    }

}
