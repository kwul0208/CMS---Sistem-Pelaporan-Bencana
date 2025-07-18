<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use App\Models\Laporan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LaporanPekerjaanRutinController extends Controller
{
    public function index(Request $request) {
        $query = Laporan::select('laporan.*', 'users.name as surveyor')
            ->leftJoin('users', 'laporan.surveyor', '=', 'users.id')
            ->where('section', 'Laporan Pekerjaan Rutin')->orderByDesc('laporan.created_at');
        
        if (auth()->user()->role != 'admin') {
            $query->where('laporan.surveyor', auth()->user()->id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('laporan.title', 'LIKE', '%' . $search . '%')
                  ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                  ->orWhere('laporan.date', 'LIKE', '%' . $search . '%');
            });
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data laporan berhasil diambil.',
            'data' => $query->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photo_1' => 'required|image',
            'photo_2' => 'required|image',
            'photo_3' => 'required|image',
            'photo_4' => 'required|image',
            'latitude' => 'required',
            'longitude' => 'required',
            'surveyor' => 'required',
            'date' => 'required|date',
            'period' => 'required',
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
            foreach (['photo_1', 'photo_2', 'photo_3', 'photo_4'] as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('public/uploads/laporan', $filename);
                    $validated[$field] = 'uploads/laporan/' . $filename;
                }
            }
            $validated['section'] = 'Laporan Pekerjaan Rutin';
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
                Log::info('Message sent successfully: ' . print_r($response, true));
            } catch (\Exception $e) {
                Log::info('Error: ' . $e->getMessage());
            }
            // ===== END PUSH NOTIFICATION =====

            return response()->json([
                'status' => 'success',
                'message' => 'Data laporan berhasil disimpan.',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show($id) {
        return response()->json([
            'status' => 'success',
            'message' => 'Data laporan berhasil diambil.',
            'data' => Laporan::where('section', 'Laporan Pekerjaan Rutin')->where('id', $id)->first()
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'title' => 'required',
            'description' => 'required',
            'photo_1' => 'nullable|image',
            'photo_2' => 'nullable|image',
            'photo_3' => 'nullable|image',
            'photo_4' => 'nullable|image',
            'latitude' => 'required',
            'longitude' => 'required',
            'surveyor' => 'required',
            'date' => 'required|date',
            'period' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

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

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil diupdate.'
        ]);
    }


    public function delete($id) {
        $laporan = Laporan::find($id);

        if (!$laporan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Laporan tidak ditemukan.'
            ], 404);
        }

        $laporan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dihapus.'
        ]);
    }
}
