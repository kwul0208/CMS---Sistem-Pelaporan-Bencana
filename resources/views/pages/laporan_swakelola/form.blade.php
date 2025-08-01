@extends('layout.main')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />


<div class="container-fluid">
    <a href="{{ route('laporan_swakelola.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form 
        method="POST" 
        action="{{ isset($laporan_swakelola) ? route('laporan_swakelola.update', $laporan_swakelola) : route('laporan_swakelola.store') }}" 
        enctype="multipart/form-data"
    >
        @csrf
        @if(isset($laporan_swakelola))
            @method('PUT')
            <input type="hidden" name="id" value="{{ $laporan_swakelola->id }}">
        @endif

        <div class="form-group">
            <label for="period">Periode <span style="color: red">*</span></label>
            <select name="period" class="form-control" required>
                <option value="Triwulan I" {{ old('period', $laporan_swakelola->period ?? '') == 'Triwulan I' ? 'selected' : '' }}>Triwulan I</option>
                <option value="Triwulan II" {{ old('period', $laporan_swakelola->period ?? '') == 'Triwulan II' ? 'selected' : '' }}>Triwulan II</option>
                <option value="Triwulan III" {{ old('period', $laporan_swakelola->period ?? '') == 'Triwulan III' ? 'selected' : '' }}>Triwulan III</option>
                <option value="Triwulan IV" {{ old('period', $laporan_swakelola->period ?? '') == 'Triwulan IV' ? 'selected' : '' }}>Triwulan IV</option>
                <option value="APBDP" {{ old('period', $laporan_swakelola->period ?? '') == 'APBDP' ? 'selected' : '' }}>APBDP</option>
            </select>
        </div>
        {{-- Input Text --}}
        <div class="form-group">
            <label for="title">Judul <span style="color: red">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $laporan_swakelola->title ?? '') }}" required>
        </div>
        
        <div class="form-group">
            <label for="date">Tanggal <span style="color: red">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ old('date', isset($laporan_swakelola) ? \Carbon\Carbon::parse($laporan_swakelola->date)->format('Y-m-d') : '') }}" required>
        </div>

        <div class="form-group">
            <label for="description">Deskripsi <span style="color: red">*</span></label>
            <textarea name="description" class="form-control" required>{{ old('description', $laporan_swakelola->description ?? '') }}</textarea>
        </div>
        
        {{-- Alamat & Koordinat --}}
        
        <input type="hidden" name="latitude" class="form-control" value="{{ old('latitude', $laporan_swakelola->latitude ?? '') }}" required>
        <input type="hidden" name="longitude" class="form-control" value="{{ old('longitude', $laporan_swakelola->longitude ?? '') }}" required>
        <div class="form-group">
            <label>Pilih Lokasi di Peta</label>
            <button type="button" id="btn-lokasi" class="btn btn-sm btn-outline-primary mb-2">
                <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya
            </button>
            <div id="map" style="height: 300px;" class="mb-3 border"></div>
        </div>
        
        {{-- Foto Pengukuran --}}
        <div class="form-group">
            <label>Foto Pengukuran <span style="color: red">*</span></label>
            <div id="photos_pengukuran_container" class="row">
                @if (isset($laporan_swakelola))
                    @foreach($laporan_swakelola->photo_swakelola_pengukuran as $photo)
                        <div class=" text-center mb-2">
                            <div class="text-center mb-2 position-relative photo-item" data-photo-id="{{ $photo->id }}">
                                <img src="{{ asset('storage/' . $photo->photo) }}" class="img-thumbnail" width="150">
                                <input type="hidden" name="existing_photo_ids_pengukuran[]" value="{{ $photo->id }}">
                                <button type="button" class="btn btn-sm btn-danger mt-1 delete-photo-pengukuran-btn"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    @endforeach
                @endif
                @if (!isset($laporan_swakelola))
                <div class="input-group mb-2">
                    <input type="file" name="photos_pengukuran[]" class="form-control" accept="image/*" required>
                    <button type="button" class="btn btn-danger remove-photo-btn">Hapus</button>
                </div>
                @endif
                
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="add_photos_pengukuran_btn">+ Tambah Foto Pengukuran</button>
        </div>

        {{-- Foto Hasil --}}
        <div class="form-group">
            <label>Foto Hasil <span style="color: red">*</span></label>
            <div id="photos_hasil_container" class="row">
                @if (isset($laporan_swakelola))
                    @foreach($laporan_swakelola->photo_swakelola_hasil as $photo)
                        <div class=" text-center mb-2">
                           <div class="text-center mb-2 position-relative photo-item" data-photo-id="{{ $photo->id }}">
                                <img src="{{ asset('storage/' . $photo->photo) }}" class="img-thumbnail" width="150">
                                <input type="hidden" name="existing_photo_ids_hasil[]" value="{{ $photo->id }}">
                                <button type="button" class="btn btn-sm btn-danger mt-1 delete-photo-hasil-btn"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    @endforeach
                @endif
                @if (!isset($laporan_swakelola))
                <div class="input-group mb-2">
                    <input type="file" name="photos_hasil[]" class="form-control" accept="image/*" required>
                    <button type="button" class="btn btn-danger remove-photo-btn">Hapus</button>
                </div>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="add_photos_hasil_btn">+ Tambah Foto Hasil</button>
        </div>


        <div class="form-group">
            <label for="pengawas_id">Pengawas <span style="color: red">*</span></label>
            <select name="pengawas_id" class="form-control" required>
                <option value="">-- Pilih --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('pengawas_id', $laporan_swakelola->pengawas_id ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="korwil_id">Korwil <span style="color: red">*</span></label>
            <select name="korwil_id" class="form-control" required>
                <option value="">-- Pilih --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('korwil_id', $laporan_swakelola->korwil_id ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>



        <button type="submit" class="btn btn-primary">
            {{ isset($laporan_swakelola) ? 'Update' : 'Simpan' }}
        </button>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([{{ old('latitude', $laporan_swakelola->latitude ?? -6.2) }}, {{ old('longitude', $laporan_swakelola->longitude ?? 106.8) }}], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([{{ old('latitude', $laporan_swakelola->latitude ?? -6.2) }}, {{ old('longitude', $laporan_swakelola->longitude ?? 106.8) }}], {
        draggable: true
    }).addTo(map);

    setTimeout(() => {
        updateLatLng(-6.2,106.8)
    },1000);

    function updateLatLng(lat, lng) {
        document.querySelector('input[name="latitude"]').value = lat;
        document.querySelector('input[name="longitude"]').value = lng;
    }

    marker.on('dragend', function (e) {
        var latlng = marker.getLatLng();
        updateLatLng(latlng.lat, latlng.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updateLatLng(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById('btn-lokasi').addEventListener('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                var latlng = L.latLng(lat, lng);
                marker.setLatLng(latlng);
                map.setView(latlng, 15); // Zoom ke lokasi
                updateLatLng(lat, lng);
            }, function (error) {
                alert('Gagal mengambil lokasi: ' + error.message);
            });
        } else {
            alert('Browser tidak mendukung Geolokasi.');
        }
    });

</script>
<script>
    function createPhotoInput(name) {
        return `
            <div class="">
            <div class="input-group mb-2">
                <input type="file" name="${name}[]" class="form-control" accept="image/*">
                <button type="button" class="btn btn-danger remove-photo-btn">Hapus</button>
            </div>
            </div>
        `;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Tambah foto pengukuran
        document.getElementById('add_photos_pengukuran_btn').addEventListener('click', function () {
            document.getElementById('photos_pengukuran_container').insertAdjacentHTML('beforeend', createPhotoInput('photos_pengukuran'));
        });

        // Tambah foto hasil
        document.getElementById('add_photos_hasil_btn').addEventListener('click', function () {
            document.getElementById('photos_hasil_container').insertAdjacentHTML('beforeend', createPhotoInput('photos_hasil'));
        });

        // Hapus input foto
        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-photo-btn')) {
                e.target.parentElement.remove();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('photos_pengukuran_container');

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('delete-photo-pengukuran-btn')) {
                const photoItem = e.target.closest('.photo-item');
                if (photoItem) {
                    photoItem.remove(); // Menghapus elemen foto
                }
            }
        });
        const container2 = document.getElementById('photos_hasil_container');

        container2.addEventListener('click', function (e) {
            if (e.target.classList.contains('delete-photo-hasil-btn')) {
                const photoItem = e.target.closest('.photo-item');
                if (photoItem) {
                    photoItem.remove(); // Menghapus elemen foto
                }
            }
        });
    });

</script>

@endsection

