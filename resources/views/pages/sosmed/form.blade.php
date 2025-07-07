@extends('layout.main')

@section('content')
    <div class="container-fluid">
        <a href="{{ route('sosmed.index') }}" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
        <form method="POST" action="{{ isset($model) ? route('sosmed.update', $model) : route('sosmed.store') }}">
            @csrf
            @if(isset($model)) @method('PUT') @endif
            <div class="col-12">
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $model->name ?? '' }}" placeholder="Nama" required>
                </div>
                <div class="form-group">
                    <label for="name">Link</label>
                    <input type="text" class="form-control" id="link" name="link" value="{{ $model->link ?? '' }}" placeholder="Link" required>
                </div>
                <div class="form-group">
                    <label for="parent_id">Status</label>
                    <select class="form-control" id="parent_id" name="parent_id">
                        <option value="active" {{ isset($model) && $model->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="not active" {{ isset($model) && $model->status == 'not active' ? 'selected' : '' }}>Not Active</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form> 
    </div>

@endsection

