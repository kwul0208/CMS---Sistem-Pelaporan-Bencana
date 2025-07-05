<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Laporan;
use Illuminate\Http\Request;
use App\Models\Position;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TanggapDaruratBencanaController extends Controller
{
    public function index(Request $request) {
        $query = Laporan::select('laporan.*', 'users.name as surveyor')
            ->leftJoin('users', 'laporan.surveyor', '=', 'users.id')
            ->where('section', 'Tanggap Darurat Bencana')
            ->orderByDesc('laporan.created_at');

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
            'surveyor' => 'required',
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
            $data = Laporan::create($validated);

            // ===== PUSH NOTIFICATION =====
            $projectId = env('FIREBASE_PROJECT_ID');
            $topic = "laporan";
            $fullname = auth()->user()->name;
            $name_request = "Tanggap Darurat Bencana";
            $route = "tanggap_darurat_bencana";

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
            'data' => Laporan::find($id)
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            'surveyor' => 'required',
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

