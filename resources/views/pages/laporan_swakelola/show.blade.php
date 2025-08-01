@extends('layout.main')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container-fluid">
    <a href="{{ route('laporan_swakelola.index') }}" class="btn btn-secondary mb-4">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3 class="card-title">Detail Laporan {{$laporan_swakelola->section}}</h3>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Nama Saluran</dt>
                        <dd class="col-sm-8">{{ $laporan_swakelola->title }}</dd>

                        <dt class="col-sm-4">Periode</dt>
                        <dd class="col-sm-8">{{ $laporan_swakelola->period }}</dd>

                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{{ $laporan_swakelola->description }}</dd>

                        <dt class="col-sm-4">Latitude</dt>
                        <dd class="col-sm-8">{{ $laporan_swakelola->latitude }}</dd>

                        <dt class="col-sm-4">Longitude</dt>
                        <dd class="col-sm-8">{{ $laporan_swakelola->longitude }}</dd>

                        <dt class="col-sm-4">Surveyor</dt>
                        <dd class="col-sm-8">{{ $laporan_swakelola->surveyor_name->name ?? '-' }}</dd>
                        
                        <dt class="col-sm-4">Pengawas</dt>
                        <dd class="col-sm-8">{{ $laporan_swakelola->pengawas->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Korwil</dt>
                        <dd class="col-sm-8">{{ $laporan_swakelola->korwil->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Tanggal</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($laporan_swakelola->date)->format('d-m-Y') }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    {{-- Map --}}
                    <div class="mt-4">
                        <label class="font-weight-bold">Lokasi di Peta</label>
                        <div id="map" style="height: 300px;" class="border rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Foto --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Foto-Foto Pengukuran</h5>
            <div class="row">
                 @foreach($laporan_swakelola->photo_swakelola_pengukuran as $i => $photo)
                    @if ($photo)
                        <div class="col-md-4">
                            <label>Foto {{ $i+1 }}</label>
                            <img src="{{ asset('storage/' . $photo->photo) }}" class="img-fluid rounded border" alt="Foto {{ $i }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Foto-Foto Hasil</h5>
            <div class="row">
                 @foreach($laporan_swakelola->photo_swakelola_hasil as $i => $photo)
                    @if ($photo)
                        <div class="col-md-4">
                            <label>Foto {{ $i+1 }}</label>
                            <img src="{{ asset('storage/' . $photo->photo) }}" class="img-fluid rounded border" alt="Foto {{ $i }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var latitude = {{ $laporan_swakelola->latitude ?? -6.2 }};
    var longitude = {{ $laporan_swakelola->longitude ?? 106.8 }};

    var map = L.map('map').setView([latitude, longitude], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([latitude, longitude]).addTo(map)
        .bindPopup("Lokasi Laporan")
        .openPopup();
</script>
@endsection
