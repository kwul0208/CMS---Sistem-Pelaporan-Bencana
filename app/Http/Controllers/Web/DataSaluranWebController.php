<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\PhotoSaluran;
use App\Models\PhotoSwakelola;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;


class DataSaluranWebController extends Controller
{
    public function index() {
        $title = 'Data Saluran';
        return view('pages.data_saluran.index')->with('title', $title);
    }

    public function data() {
        $query = Laporan::with('surveyor_name')
            ->where('section', 'Data Saluran')
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

    public function show(Laporan $data_saluran)
    {
        return view('pages.data_saluran.show', compact('data_saluran'));
    }



    public function create() {
        $users = User::all();
        return view('pages.data_saluran.form')->with('users', $users);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photo_1' => 'required|image', // foto utama
            'photos.*' => 'nullable|image', // array photo tambahan (boleh kosong)
            'latitude' => 'required',
            'longitude' => 'required',
            'date' => 'required|date',
        ]);

        
        $validated = $validator->validated();

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
            $validated['surveyor'] = auth()->user()->id;
            unset($validated['photos']);
            $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');
            $data = Laporan::create($validated);

            // Simpan ke tabel photo_saluran
            foreach ($related_photos as $path) {
                PhotoSaluran::create([
                    'laporan_id' => $data->id,
                    'photo' => $path
                ]);
            }

            // // ===== PUSH NOTIFICATION =====
            // $projectId = env('FIREBASE_PROJECT_ID');
            // $topic = "laporan";
            // $fullname = auth()->user()->name;
            // $name_request = "Data Saluran";
            // $route = "data_saluran";

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

            return redirect()->route('data_saluran.index')->with('success', 'Laporan berhasil ditambah.');

        } catch (\Throwable $th) {
            return $th;
            return redirect()->route('data_saluran.index')->with('error', 'Laporan gagal ditambah.');
        }
    }

    public function edit(Laporan $data_saluran) {
        $users = User::all();
        // return $data_saluran->load('photos_pengukuran', 'photos_hasil');
        return view('pages.data_saluran.form', compact('data_saluran', 'users'));
    }


    public function update(Request $request, Laporan $laporan) {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'photo_1' => 'nullable|image', // boleh kosong
            'photos.*' => 'image',
            'existing_photo_ids' => 'nullable|array', // ID photo_saluran yang tetap disimpan
            'latitude' => 'required',
            'longitude' => 'required',
            'date' => 'required|date',
        ]);

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
            $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');
            $laporan->update($validated);


        return redirect()->route('data_saluran.index')->with('success', 'Laporan berhasil diupdate.');
    }

    public function destroy(Laporan $data_saluran) {
        $data_saluran->delete();
        return redirect()->route('data_saluran.index')->with('success', 'Laporan berhasil dihapus.');
    }

}
