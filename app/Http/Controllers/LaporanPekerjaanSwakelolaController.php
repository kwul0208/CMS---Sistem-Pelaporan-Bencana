<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\PhotoSwakelola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class LaporanPekerjaanSwakelolaController extends Controller
{
    public function index(Request $request) {
        $query = Laporan::select('laporan.*', 'users.name as surveyor')
            ->with( 'photo_swakelola_pengukuran', 'photo_swakelola_hasil')
            ->leftJoin('users', 'laporan.surveyor', '=', 'users.id')
            ->where('section', 'Laporan Pekerjaan Swakelola')
            ->orderByDesc('laporan.created_at');

        if (auth()->user()->role != 'admin') {
            $query->where('laporan.surveyor', auth()->user()->id);
        }
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('laporan.title', 'LIKE', '%' . $search . '%')
                  ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                  ->orWhere('laporan.date', 'LIKE', '%' . $search . '%');
            });
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diambil.',
            'data' => $query->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photos_pengukuran.*' => 'required|image', // array photo tambahan (boleh kosong)
            'photos_hasil.*' => 'required|image', // array photo tambahan (boleh kosong)
            'latitude' => 'required',
            'longitude' => 'required',
            'date' => 'required|date',
            'pengawas_id' => 'required',
            'korwil_id' => 'required',
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

            // Upload photos[]
            $related_photos_pengukuran = [];
            if ($request->hasFile('photos_pengukuran')) {
                foreach ($request->file('photos_pengukuran') as $photo) {
                    $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                    $photo->storeAs('public/uploads/laporan', $filename);
                    $related_photos_pengukuran[] = 'uploads/laporan/' . $filename;
                }
            }

            $related_photos_hasil = [];
            if ($request->hasFile('photos_hasil')) {
                foreach ($request->file('photos_hasil') as $photo) {
                    $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                    $photo->storeAs('public/uploads/laporan', $filename);
                    $related_photos_hasil[] = 'uploads/laporan/' . $filename;
                }
            }

            $validated['section'] = 'Laporan Pekerjaan Swakelola';
            unset($validated['photos_pengukuran']);
            unset($validated['photos_hasil']);
            $validated['surveyor'] = auth()->user()->id;
            $data = Laporan::create($validated);

            foreach ($related_photos_pengukuran as $path) {
                PhotoSwakelola::create([
                    'laporan_id' => $data->id,
                    'type' => 'Pengukuran',
                    'photo' => $path
                ]);
            }
            foreach ($related_photos_hasil as $path) {
                PhotoSwakelola::create([
                    'laporan_id' => $data->id,
                    'type' => 'Hasil',
                    'photo' => $path
                ]);
            }

            // ===== PUSH NOTIFICATION =====
            $projectId = env('FIREBASE_PROJECT_ID');
            $topic = "laporan";
            $fullname = auth()->user()->name;
            $name_request = "Laporan Pekerjaan Swakelola";
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
            'data' => Laporan::with('photo_swakelola_pengukuran', 'photo_swakelola_hasil')->where('section', 'Laporan Pekerjaan Swakelola')->where('id', $id)->first()
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photos_pengukuran.*' => 'image',
            'photos_hasil.*' => 'image',
            'existing_photo_ids_pengukuran' => 'nullable|array', // ID photo_saluran yang tetap disimpan
            'existing_photo_ids_hasil' => 'nullable|array', // ID photo_saluran yang tetap disimpan
            'latitude' => 'required',
            'longitude' => 'required',
            'date' => 'required|date',
            'pengawas_id' => 'required',
            'korwil_id' => 'required',

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

            //  Hapus photo saluran yang tidak dipertahankan
            $existingPhotoIds_pengukuran = $request->input('existing_photo_ids_pengukuran', []);
            $photosToDelete = PhotoSwakelola::where('laporan_id', $laporan->id)
                ->where('type', 'Pengukuran')
                ->whereNotIn('id', $existingPhotoIds_pengukuran)
                ->get();
            foreach ($photosToDelete as $photo) {
                if (Storage::exists('public/' . $photo->photo)) {
                    Storage::delete('public/' . $photo->photo);
                }
                $photo->delete();
            }
            $existingPhotoIds_hasil = $request->input('existing_photo_ids_hasil', []);
            $photosToDelete = PhotoSwakelola::where('laporan_id', $laporan->id)
                ->where('type', 'Hasil')
                ->whereNotIn('id', $existingPhotoIds_hasil)
                ->get();
            foreach ($photosToDelete as $photo) {
                if (Storage::exists('public/' . $photo->photo)) {
                    Storage::delete('public/' . $photo->photo);
                }
                $photo->delete();
            }
                
            //  Tambah photo baru
            if ($request->hasFile('photos_pengukuran')) {
                foreach ($request->file('photos_pengukuran') as $photo) {
                    $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                    $photo->storeAs('public/uploads/laporan', $filename);
                    PhotoSwakelola::create([
                        'laporan_id' => $laporan->id,
                        'type' => 'Pengukuran',
                        'photo' => 'uploads/laporan/' . $filename,
                    ]);
                }
            }

            if ($request->hasFile('photos_hasil')) {
                foreach ($request->file('photos_hasil') as $photo) {
                    $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                    $photo->storeAs('public/uploads/laporan', $filename);
                    PhotoSwakelola::create([
                        'laporan_id' => $laporan->id,
                        'type' => 'Hasil',
                        'photo' => 'uploads/laporan/' . $filename,
                    ]);
                }
            }

            //  Update data lainnya
            $validated['section'] = 'Laporan Pekerjaan Swakelola';
            unset($validated['photos_pengukuran']);
            unset($validated['photos_hasil']);
            unset($validated['existing_photo_ids_pengukuran']);
            unset($validated['existing_photo_ids_hasil']);
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

        PhotoSwakelola::where('laporan_id', $id)->delete();        

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dihapus.'
        ]);
    }
}
