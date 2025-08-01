@extends('layout.main')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container-fluid">
    <a href="{{ route('tanggap_darurat_bencana.index') }}" class="btn btn-secondary mb-4">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3 class="card-title">Detail Laporan {{$tanggap_darurat_bencana->section}}</h3>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Judul</dt>
                        <dd class="col-sm-8">{{ $tanggap_darurat_bencana->title }}</dd>

                        <dt class="col-sm-4">Tipe</dt>
                        <dd class="col-sm-8">{{ $tanggap_darurat_bencana->type }}</dd>

                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{{ $tanggap_darurat_bencana->description }}</dd>

                        <dt class="col-sm-4">Alamat</dt>
                        <dd class="col-sm-8">{{ $tanggap_darurat_bencana->address }}</dd>

                        <dt class="col-sm-4">Latitude</dt>
                        <dd class="col-sm-8">{{ $tanggap_darurat_bencana->latitude }}</dd>

                        <dt class="col-sm-4">Longitude</dt>
                        <dd class="col-sm-8">{{ $tanggap_darurat_bencana->longitude }}</dd>

                        <dt class="col-sm-4">Surveyor</dt>
                        <dd class="col-sm-8">{{ $tanggap_darurat_bencana->surveyor_name->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Tanggal</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($tanggap_darurat_bencana->date)->format('d-m-Y') }}</dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <label class="font-weight-bold">Video</label>
                    @if($tanggap_darurat_bencana->video)
                        <video width="100%" height="300" controls class="border rounded">
                            <source src="{{ asset('storage/' . $tanggap_darurat_bencana->video) }}" type="video/mp4">
                            Browser tidak mendukung video.
                        </video>
                    @else
                        <p class="text-muted"><em>Tidak ada video</em></p>
                    @endif
                </div>
                <div class="col-md-12">
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
                @for ($i = 1; $i <= 5; $i++)
                    @php $foto = $tanggap_darurat_bencana->{'photo_'.$i}; @endphp
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
    var latitude = {{ $tanggap_darurat_bencana->latitude ?? -6.2 }};
    var longitude = {{ $tanggap_darurat_bencana->longitude ?? 106.8 }};

    var map = L.map('map').setView([latitude, longitude], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([latitude, longitude]).addTo(map)
        .bindPopup("Lokasi Laporan")
        .openPopup();
</script>
@endsection
