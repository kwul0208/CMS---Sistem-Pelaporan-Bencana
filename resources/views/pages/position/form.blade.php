@extends('layout.main')

@section('content')
    <div class="container-fluid">
        <a href="{{ route('position.index') }}" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
        <form method="POST" action="{{ isset($model) ? route('position.update', $model) : route('position.store') }}">
            @csrf
            @if(isset($model)) @method('PUT') @endif
            <div class="col-12">
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $model->name ?? '' }}" placeholder="Nama" required>
                </div>
                <div class="form-group">
                    <label for="parent_id">Parent</label>
                    <select class="form-control" id="parent_id" name="parent_id">
                        <option value="">-- Pilih Parent --</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ isset($model) && $model->parent_id == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form> 
    </div>

@endsection

