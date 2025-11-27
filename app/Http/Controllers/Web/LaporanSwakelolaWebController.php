<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\PhotoSwakelola;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;


class LaporanSwakelolaWebController extends Controller
{
    public function index() {
        $title = 'Laporan Pekerjaan Swakelola';
        return view('pages.laporan_swakelola.index')->with('title', $title);
    }

    public function data() {
        $query = Laporan::with('surveyor_name')
            ->where('section', 'Laporan Pekerjaan Swakelola')
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

    public function show(Laporan $laporan_swakelola)
    {
        return view('pages.laporan_swakelola.show', compact('laporan_swakelola'));
    }



    public function create() {
        $users = User::all();
        return view('pages.laporan_swakelola.form')->with('users', $users);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photos_pengukuran' => 'nullable|array',
            'photos_pengukuran.*' => 'image', // array photo wajib
            'photos_hasil' => 'nullable|array',
            'photos_hasil.*' => 'image', // array photo wajib
            'latitude' => 'required',
            'longitude' => 'required',
            'date' => 'required|date',
            'pengawas_id' => 'required',
            'korwil_id' => 'required',
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
            $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');
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
            $route = "laporan_swakelola";

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

            return redirect()->route('laporan_swakelola.index')->with('success', 'Laporan berhasil ditambah.');

        } catch (\Throwable $th) {
            return redirect()->route('laporan_swakelola.index')->with('error', 'Laporan gagal ditambah.');
        }
    }

    public function edit(Laporan $laporan_swakelola) {
        $users = User::all();
        // return $laporan_swakelola->load('photos_pengukuran', 'photos_hasil');
        return view('pages.laporan_swakelola.form', compact('laporan_swakelola', 'users'));
    }


    public function update(Request $request, Laporan $laporan) {
        $validated = $request->validate([
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
            'period' => 'required',
        ]);

        // Cek dan update jika ada file baru

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
            $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');
            $laporan->update($validated);

        return redirect()->route('laporan_swakelola.index')->with('success', 'Laporan berhasil diupdate.');
    }

    public function destroy(Laporan $laporan_swakelola) {
        $laporan_swakelola->delete();
        return redirect()->route('laporan_swakelola.index')->with('success', 'Laporan berhasil dihapus.');
    }

}
