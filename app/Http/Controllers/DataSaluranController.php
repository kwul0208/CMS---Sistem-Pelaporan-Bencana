<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\PhotoSaluran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class DataSaluranController extends Controller
{
    public function index(Request $request) {
        $query = Laporan::select('laporan.*', 'users.name as surveyor')
            ->with('photo_saluran')
            ->leftJoin('users', 'laporan.surveyor', '=', 'users.id')
            ->where('section', 'Data Saluran')
            ->orderByDesc('laporan.created_at');

        if (auth()->user()->role != 'admin') {
            $query->where('laporan.surveyor', auth()->user()->id);
        }
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('laporan.title', 'LIKE', '%' . $search . '%')
                  ->orWhere('laporan.description', 'LIKE', '%' . $search . '%')
                  ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                  ->orWhere('laporan.date', 'LIKE', '%' . $search . '%');
            });
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diambil.',
            'data' => Laporan::where('section', 'Data Saluran')->get(
                
            )
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photo_1' => 'required|image', // foto utama
            'photos.*' => 'required|image', // array photo tambahan (boleh kosong)
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

        DB::beginTransaction();
        try {
            // Upload photo_1
            if ($request->hasFile('photo_1')) {
                $file = $request->file('photo_1');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/uploads/laporan', $filename);
                $validated['photo_1'] = 'uploads/laporan/' . $filename;
            }

            // Upload photos[]
            $related_photos = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                    $photo->storeAs('public/uploads/laporan', $filename);
                    $related_photos[] = 'uploads/laporan/' . $filename;
                }
            }

            $validated['section'] = 'Data Saluran';
            unset($validated['photos']);
            $data = Laporan::create($validated);

            // Simpan ke tabel photo_saluran
            foreach ($related_photos as $path) {
                PhotoSaluran::create([
                    'laporan_id' => $data->id,
                    'photo' => $path
                ]);
            }

            // ===== PUSH NOTIFICATION =====
            $projectId = env('FIREBASE_PROJECT_ID');
            $topic = "laporan";
            $fullname = auth()->user()->name;
            $name_request = "Data Saluran";
            $route = "data_saluran";

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

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data laporan berhasil disimpan.',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
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
            'data' => Laporan::with('photo_saluran')->where('section', 'Data Saluran')->where('id', $id)->first()
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photo_1' => 'nullable|image', // boleh kosong
            'photos.*' => 'image',
            'existing_photo_ids' => 'nullable|array', // ID photo_saluran yang tetap disimpan
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
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $laporan = Laporan::findOrFail($id);

            //  Update photo_1 jika diupload baru
            if ($request->hasFile('photo_1')) {
                // Hapus file lama dari storage jika ada
                if ($laporan->photo_1 && Storage::exists('public/' . $laporan->photo_1)) {
                    Storage::delete('public/' . $laporan->photo_1);
                }

                $file = $request->file('photo_1');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/uploads/laporan', $filename);
                $validated['photo_1'] = 'uploads/laporan/' . $filename;
            }

            //  Hapus photo saluran yang tidak dipertahankan
            $existingPhotoIds = $request->input('existing_photo_ids', []);
            $photosToDelete = PhotoSaluran::where('laporan_id', $laporan->id)
                ->whereNotIn('id', $existingPhotoIds)
                ->get();

            foreach ($photosToDelete as $photo) {
                if (Storage::exists('public/' . $photo->photo)) {
                    Storage::delete('public/' . $photo->photo);
                }
                $photo->delete();
            }

            //  Tambah photo saluran baru
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                    $photo->storeAs('public/uploads/laporan', $filename);
                    PhotoSaluran::create([
                        'laporan_id' => $laporan->id,
                        'photo' => 'uploads/laporan/' . $filename,
                    ]);
                }
            }

            //  Update data lainnya
            $validated['section'] = 'Data Saluran';
            unset($validated['photos']);
            unset($validated['existing_photo_ids']);
            $laporan->update($validated);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data laporan berhasil diperbarui.',
                'data' => $laporan
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
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

        PhotoSaluran::where('laporan_id', $id)->delete();        

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dihapus.'
        ]);
    }
}
