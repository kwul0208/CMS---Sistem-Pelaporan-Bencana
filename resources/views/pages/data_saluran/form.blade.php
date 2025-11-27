@extends('layout.main')

@section('content')


<div class="container-fluid">
    <a href="{{ route('data_saluran.index') }}" class="btn btn-secondary mb-3">
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
        action="{{ isset($data_saluran) ? route('data_saluran.update', $data_saluran) : route('data_saluran.store') }}" 
        enctype="multipart/form-data"
    >
        @csrf
        @if(isset($data_saluran))
            @method('PUT')
            <input type="hidden" name="id" value="{{ $data_saluran->id }}">
        @endif
        {{-- Input Text --}}
        <div class="form-group">
            <label for="title">Nama Saluran <span style="color: red">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $data_saluran->title ?? '') }}" required>
        </div>
        
        <div class="form-group">
            <label for="date">Tanggal <span style="color: red">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ old('date', isset($data_saluran) ? \Carbon\Carbon::parse($data_saluran->date)->format('Y-m-d') : '') }}" required>
        </div>

        <div class="form-group">
            <label for="description">Deskripsi <span style="color: red">*</span></label>
            <textarea name="description" class="form-control" required>{{ old('description', $data_saluran->description ?? '') }}</textarea>
        </div>
        
        {{-- Alamat & Koordinat --}}
        
        <input type="hidden" name="latitude" class="form-control" value="{{ old('latitude', $data_saluran->latitude ?? '') }}" required>
        <input type="hidden" name="longitude" class="form-control" value="{{ old('longitude', $data_saluran->longitude ?? '') }}" required>
        <div class="form-group">
            <label>Pilih Lokasi di Peta</label>
            <button type="button" id="btn-lokasi" class="btn btn-sm btn-outline-primary mb-2">
                <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya
            </button>
            <div id="map" style="height: 300px;" class="mb-3 border"></div>
        </div>
        
        {{-- Foto --}}
        <div class="row">
            @for ($i = 1; $i <= 1; $i++)
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="photo_{{ $i }}">Foto Form Survey <span style="color: red">*</span></label>
                        @if(isset($data_saluran) && $data_saluran->{'photo_'.$i})
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $data_saluran->{'photo_'.$i}) }}" alt="Foto {{ $i }}" width="150">
                            </div>
                        @endif
                        <input type="file" name="photo_{{ $i }}" class="form-control-file" accept="image/*" {{ isset($data_saluran) ? '' : 'required' }}>
                    </div>
                </div>
            @endfor
        </div>

        <div class="form-group">
            <div id="photos_hasil_container" class="row">
                @if (isset($data_saluran))
                    @foreach($data_saluran->photo_saluran as $photo)
                        <div class=" text-center mb-2">
                           <div class="text-center mb-2 position-relative photo-item" data-photo-id="{{ $photo->id }}">
                                <img src="{{ asset('storage/' . $photo->photo) }}" class="img-thumbnail" width="150">
                                <input type="hidden" name="existing_photo_ids_hasil[]" value="{{ $photo->id }}">
                                <button type="button" class="btn btn-sm btn-danger mt-1 delete-photo-hasil-btn"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="add_photos_hasil_btn">+ Tambah Gambar</button>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ isset($data_saluran) ? 'Update' : 'Simpan' }}
        </button>
    </form>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6AUPIR0eIiGldIIo0b06uqLxlZDyQh-I"></script>

<script>
    let map;
    let marker;

    function initMap() {
        const initialLat = parseFloat("{{ old('latitude', $data_saluran->latitude ?? -6.2) }}");
        const initialLng = parseFloat("{{ old('longitude', $data_saluran->longitude ?? 106.8) }}");
        const initialPos = { lat: initialLat, lng: initialLng };

        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 13,
            center: initialPos,
        });

        marker = new google.maps.Marker({
            position: initialPos,
            map: map,
            draggable: true,
        });

        updateLatLng(initialPos.lat, initialPos.lng);

        // Update koordinat saat marker digeser
        marker.addListener('dragend', function () {
            const pos = marker.getPosition();
            updateLatLng(pos.lat(), pos.lng());
        });

        // Update marker saat peta diklik
        map.addListener('click', function (event) {
            marker.setPosition(event.latLng);
            updateLatLng(event.latLng.lat(), event.latLng.lng());
        });
    }

    function updateLatLng(lat, lng) {
        document.querySelector('input[name="latitude"]').value = lat;
        document.querySelector('input[name="longitude"]').value = lng;
    }

    document.getElementById('btn-lokasi').addEventListener('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                marker.setPosition(pos);
                map.setCenter(pos);
                map.setZoom(15);
                updateLatLng(pos.lat, pos.lng);
            }, function (error) {
                alert('Gagal mengambil lokasi: ' + error.message);
            });
        } else {
            alert('Browser tidak mendukung Geolokasi.');
        }
    });

    // Inisialisasi map saat DOM siap
    document.addEventListener('DOMContentLoaded', function () {
        initMap();
    });
</script>

<script>
    function createPhotoInput(name) {
        return `
            <div class="">
            <div class="input-group mb-2">
                <input type="file" name="photos[]" class="form-control" accept="image/*">
                <button type="button" class="btn btn-danger remove-photo-btn">Hapus</button>
            </div>
            </div>
        `;
    }

    document.addEventListener('DOMContentLoaded', function () {

        // Tambah foto hasil
        document.getElementById('add_photos_hasil_btn').addEventListener('click', function () {
            console.log("XXX");
            
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


