@extends('layout.main')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container-fluid">
    <a href="{{ route('laporan_pekerjaan_rutin.index') }}" class="btn btn-secondary mb-4">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3 class="card-title">Detail Laporan {{$laporan_pekerjaan_rutin->section}}</h3>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Judul</dt>
                        <dd class="col-sm-8">{{ $laporan_pekerjaan_rutin->title }}</dd>

                        <dt class="col-sm-4">Periode</dt>
                        <dd class="col-sm-8">{{ $laporan_pekerjaan_rutin->period }}</dd>

                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{{ $laporan_pekerjaan_rutin->description }}</dd>

                        <dt class="col-sm-4">Alamat</dt>
                        <dd class="col-sm-8">{{ $laporan_pekerjaan_rutin->address }}</dd>

                        <dt class="col-sm-4">Latitude</dt>
                        <dd class="col-sm-8">{{ $laporan_pekerjaan_rutin->latitude }}</dd>

                        <dt class="col-sm-4">Longitude</dt>
                        <dd class="col-sm-8">{{ $laporan_pekerjaan_rutin->longitude }}</dd>

                        <dt class="col-sm-4">Surveyor</dt>
                        <dd class="col-sm-8">{{ $laporan_pekerjaan_rutin->surveyor_name->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Tanggal</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($laporan_pekerjaan_rutin->date)->format('d-m-Y') }}</dd>
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
            <h5 class="card-title">Foto-Foto</h5>
            <div class="row">
                @for ($i = 1; $i <= 4; $i++)
                    @php $foto = $laporan_pekerjaan_rutin->{'photo_'.$i}; @endphp
                    @if ($foto)
                        <div class="col-md-4">
                            <label>Foto {{ $i }}</label>
                            <img src="{{ asset('storage/' . $foto) }}" class="img-fluid rounded border" alt="Foto {{ $i }}">
                        </div>
                    @endif
                @endfor
            </div>
        </div>
    </div>
</div>

{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var latitude = {{ $laporan_pekerjaan_rutin->latitude ?? -6.2 }};
    var longitude = {{ $laporan_pekerjaan_rutin->longitude ?? 106.8 }};

    var map = L.map('map').setView([latitude, longitude], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([latitude, longitude]).addTo(map)
        .bindPopup("Lokasi Laporan")
        .openPopup();
</script>
@endsection
