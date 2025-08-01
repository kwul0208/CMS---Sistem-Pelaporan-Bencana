@extends('layout.main')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />


<div class="container-fluid">
    <a href="{{ route('laporan_pekerjaan_rutin.index') }}" class="btn btn-secondary mb-3">
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
        action="{{ isset($laporan_pekerjaan_rutin) ? route('laporan_pekerjaan_rutin.update', $laporan_pekerjaan_rutin) : route('laporan_pekerjaan_rutin.store') }}" 
        enctype="multipart/form-data"
    >
        @csrf
        @if(isset($laporan_pekerjaan_rutin))
            @method('PUT')
            <input type="hidden" name="id" value="{{ $laporan_pekerjaan_rutin->id }}">
        @endif

        <div class="form-group">
            <label for="period">Period <span style="color: red">*</span></label>
            <select name="period" class="form-control" required>
                <option value="Triwulan I" {{ old('period', $laporan_pekerjaan_rutin->period ?? '') == 'Triwulan I' ? 'selected' : '' }}>Triwulan I</option>
                <option value="Triwulan II" {{ old('period', $laporan_pekerjaan_rutin->period ?? '') == 'Triwulan II' ? 'selected' : '' }}>Triwulan II</option>
                <option value="Triwulan III" {{ old('period', $laporan_pekerjaan_rutin->period ?? '') == 'Triwulan III' ? 'selected' : '' }}>Triwulan III</option>
                <option value="Triwulan IV" {{ old('period', $laporan_pekerjaan_rutin->period ?? '') == 'Triwulan IV' ? 'selected' : '' }}>Triwulan IV</option>
                <option value="APBDP" {{ old('period', $laporan_pekerjaan_rutin->period ?? '') == 'APBDP' ? 'selected' : '' }}>APBDP</option>
            </select>
        </div>
        {{-- Input Text --}}
        <div class="form-group">
            <label for="title">Judul <span style="color: red">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $laporan_pekerjaan_rutin->title ?? '') }}" required>
        </div>
        
        <div class="form-group">
            <label for="date">Tanggal <span style="color: red">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ old('date', isset($laporan_pekerjaan_rutin) ? \Carbon\Carbon::parse($laporan_pekerjaan_rutin->date)->format('Y-m-d') : '') }}" required>
        </div>

        <div class="form-group">
            <label for="description">Deskripsi <span style="color: red">*</span></label>
            <textarea name="description" class="form-control" required>{{ old('description', $laporan_pekerjaan_rutin->description ?? '') }}</textarea>
        </div>
        
        {{-- Alamat & Koordinat --}}
        <div class="form-group">
            <label for="address">Alamat <span style="color: red">*</span></label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $laporan_pekerjaan_rutin->address ?? '') }}" required>
        </div>
        
        <input type="hidden" name="latitude" class="form-control" value="{{ old('latitude', $laporan_pekerjaan_rutin->latitude ?? '') }}" required>
        <input type="hidden" name="longitude" class="form-control" value="{{ old('longitude', $laporan_pekerjaan_rutin->longitude ?? '') }}" required>
        <div class="form-group">
            <label>Pilih Lokasi di Peta</label>
            <button type="button" id="btn-lokasi" class="btn btn-sm btn-outline-primary mb-2">
                <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya
            </button>
            <div id="map" style="height: 300px;" class="mb-3 border"></div>
        </div>
        
        {{-- Foto --}}
        <div class="row">
            @for ($i = 1; $i <= 4; $i++)
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="photo_{{ $i }}">Foto {{ $i }} <span style="color: red">*</span></label>
                        @if(isset($laporan_pekerjaan_rutin) && $laporan_pekerjaan_rutin->{'photo_'.$i})
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $laporan_pekerjaan_rutin->{'photo_'.$i}) }}" alt="Foto {{ $i }}" width="150">
                        </div>
                        @endif
                        <input type="file" name="photo_{{ $i }}" class="form-control-file" accept="image/*" {{ isset($laporan_pekerjaan_rutin) ? '' : 'required' }}>
                    </div>
                </div>
             @endfor
        </div>

        <button type="submit" class="btn btn-primary">
            {{ isset($laporan_pekerjaan_rutin) ? 'Update' : 'Simpan' }}
        </button>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([{{ old('latitude', $laporan_pekerjaan_rutin->latitude ?? -6.2) }}, {{ old('longitude', $laporan_pekerjaan_rutin->longitude ?? 106.8) }}], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([{{ old('latitude', $laporan_pekerjaan_rutin->latitude ?? -6.2) }}, {{ old('longitude', $laporan_pekerjaan_rutin->longitude ?? 106.8) }}], {
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

@endsection

