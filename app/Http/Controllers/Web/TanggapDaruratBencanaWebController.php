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


class TanggapDaruratBencanaWebController extends Controller
{
    public function index() {
        $title = 'Tanggap Darurat Bencana';
        return view('pages.tanggap_darurat_bencana.index')->with('title', $title);
    }

    public function data() {
        $query = Laporan::with('surveyor_name')
            ->where('section', 'Tanggap Darurat Bencana')
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

    public function show(Laporan $tanggap_darurat_bencana)
    {
        return view('pages.tanggap_darurat_bencana.show', compact('tanggap_darurat_bencana'));
    }



    public function create() {
        return view('pages.tanggap_darurat_bencana.form');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
            'description' => 'required',
            'photo_1' => 'required|image',
            'photo_2' => 'required|image',
            'photo_3' => 'required|image',
            'photo_4' => 'required|image',
            'photo_5' => 'required|image',
            'video' => 'required|mimetypes:video/mp4,video/quicktime',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        try {
            foreach (['photo_1', 'photo_2', 'photo_3', 'photo_4', 'photo_5', 'video'] as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('public/uploads/laporan', $filename);
                    $validated[$field] = 'uploads/laporan/' . $filename;
                }
            }
            $validated['section'] = 'Tanggap Darurat Bencana';
            $validated['surveyor'] = auth()->user()->id;
            $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');
            $data = Laporan::create($validated);

            // // ===== PUSH NOTIFICATION =====
            // $projectId = env('FIREBASE_PROJECT_ID');
            // $topic = "laporan";
            // $fullname = auth()->user()->name;
            // $name_request = "Tanggap Darurat Bencana";
            // $route = "tanggap_darurat_bencana";

            // $message = [
            //     "message" => [
            //         "topic" => $topic,
            //         "notification" => [
            //             "title" => $name_request,
            //             "body" => "{$fullname} telah menginput data laporan {$name_request}"
            //         ],
            //         "data" => [
            //             "route" => $route
            //         ]
            //     ]
            // ];
            
            // try {
            //     $accessToken = getAccessToken();
            //     $response = sendMessage($accessToken, $projectId, $message);
            // } catch (\Exception $e) {
            // }
            // // ===== END PUSH NOTIFICATION =====

            return redirect()->route('tanggap_darurat_bencana.index')->with('success', 'Laporan berhasil ditambah.');

        } catch (\Throwable $th) {
            return redirect()->route('tanggap_darurat_bencana.index')->with('error', 'Laporan gagal ditambah.');
        }
    }

    public function edit(Laporan $tanggap_darurat_bencana) {
        return view('pages.tanggap_darurat_bencana.form', compact('tanggap_darurat_bencana'));
    }


    public function update(Request $request, Laporan $laporan) {
        $validated = $request->validate([
            'title' => 'required',
            'type' => 'required',
            'description' => 'required',
            'photo_1' => 'nullable|image',
            'photo_2' => 'nullable|image',
            'photo_3' => 'nullable|image',
            'photo_4' => 'nullable|image',
            'photo_5' => 'nullable|image',
            'video' => 'nullable|mimetypes:video/mp4,video/quicktime',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'date' => 'required|date',
        ]);

        // Cek dan update jika ada file baru

        $laporan = Laporan::where('section', 'Tanggap Darurat Bencana')->where('id', $request->id)->first();

        foreach (['photo_1', 'photo_2', 'photo_3', 'photo_4', 'photo_5', 'video'] as $field) {
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

        return redirect()->route('tanggap_darurat_bencana.index')->with('success', 'Laporan berhasil diupdate.');
    }

    public function destroy(Laporan $tanggap_darurat_bencana) {
        $tanggap_darurat_bencana->delete();
        return redirect()->route('tanggap_darurat_bencana.index')->with('success', 'Laporan berhasil dihapus.');
    }

}
