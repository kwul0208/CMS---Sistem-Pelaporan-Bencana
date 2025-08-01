@extends('layout.main')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />


<div class="container-fluid">
    <a href="{{ route('tanggap_darurat_bencana.index') }}" class="btn btn-secondary mb-3">
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
        action="{{ isset($tanggap_darurat_bencana) ? route('tanggap_darurat_bencana.update', $tanggap_darurat_bencana) : route('tanggap_darurat_bencana.store') }}" 
        enctype="multipart/form-data"
    >
        @csrf
        @if(isset($tanggap_darurat_bencana))
            @method('PUT')
            <input type="hidden" name="id" value="{{ $tanggap_darurat_bencana->id }}">
        @endif

        {{-- Input Text --}}
        <div class="form-group">
            <label for="title">Judul <span style="color: red">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $tanggap_darurat_bencana->title ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="type">Tipe <span style="color: red">*</span></label>
            <select name="type" class="form-control" required>
                <option value="Monitoring Banjir" {{ old('type', $tanggap_darurat_bencana->type ?? '') == 'Monitoring Banjir' ? 'selected' : '' }}>Monitoring Banjir</option>
                <option value="Monitoring Kekeringan" {{ old('type', $tanggap_darurat_bencana->type ?? '') == 'Monitoring Kekeringan' ? 'selected' : '' }}>Monitoring Kekeringan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Deskripsi <span style="color: red">*</span></label>
            <textarea name="description" class="form-control" required>{{ old('description', $tanggap_darurat_bencana->description ?? '') }}</textarea>
        </div>

        {{-- Foto --}}
        <div class="row">
            @for ($i = 1; $i <= 5; $i++)
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="photo_{{ $i }}">Foto {{ $i }} <span style="color: red">*</span></label>
                        @if(isset($tanggap_darurat_bencana) && $tanggap_darurat_bencana->{'photo_'.$i})
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $tanggap_darurat_bencana->{'photo_'.$i}) }}" alt="Foto {{ $i }}" width="150">
                            </div>
                        @endif
                        <input type="file" name="photo_{{ $i }}" class="form-control-file" accept="image/*" {{ isset($tanggap_darurat_bencana) ? '' : 'required' }}>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Video --}}
        <div class="form-group">
            <label for="video">Video <span style="color: red">*</span></label>
            @if(isset($tanggap_darurat_bencana) && $tanggap_darurat_bencana->video)
                <div class="mb-2">
                    <video width="320" controls>
                        <source src="{{ asset('storage/' . $tanggap_darurat_bencana->video) }}" type="video/mp4">
                        Browser tidak mendukung video.
                    </video>
                </div>
            @endif
            <input type="file" name="video" class="form-control-file" accept="video/mp4,video/quicktime" {{ isset($tanggap_darurat_bencana) ? '' : 'required' }}>
        </div>

        {{-- Alamat & Koordinat --}}
        <div class="form-group">
            <label for="address">Alamat <span style="color: red">*</span></label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $tanggap_darurat_bencana->address ?? '') }}" required>
        </div>

        <input type="hidden" name="latitude" class="form-control" value="{{ old('latitude', $tanggap_darurat_bencana->latitude ?? '') }}" required>
        <input type="hidden" name="longitude" class="form-control" value="{{ old('longitude', $tanggap_darurat_bencana->longitude ?? '') }}" required>
        <div class="form-group">
            <label>Pilih Lokasi di Peta</label>
            <button type="button" id="btn-lokasi" class="btn btn-sm btn-outline-primary mb-2">
                <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya
            </button>
            <div id="map" style="height: 300px;" class="mb-3 border"></div>
        </div>

        
        <div class="form-group">
            <label for="date">Tanggal <span style="color: red">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ old('date', isset($tanggap_darurat_bencana) ? \Carbon\Carbon::parse($tanggap_darurat_bencana->date)->format('Y-m-d') : '') }}" required>
        </div>



        <button type="submit" class="btn btn-primary">
            {{ isset($tanggap_darurat_bencana) ? 'Update' : 'Simpan' }}
        </button>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([{{ old('latitude', $tanggap_darurat_bencana->latitude ?? -6.2) }}, {{ old('longitude', $tanggap_darurat_bencana->longitude ?? 106.8) }}], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([{{ old('latitude', $tanggap_darurat_bencana->latitude ?? -6.2) }}, {{ old('longitude', $tanggap_darurat_bencana->longitude ?? 106.8) }}], {
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

