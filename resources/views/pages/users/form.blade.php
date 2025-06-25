@extends('layout.main')

@section('content')
    <div class="container-fluid">
        <a href="{{ route('users.index') }}" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
        <form method="POST" action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}">
            @csrf
            @if(isset($user)) @method('PUT') @endif
            <div class="col-12">
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name ?? '' }}" placeholder="Nama" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email ?? '' }}" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role">
                        <option value="user" {{ isset($user) && $user->role == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ isset($user) && $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Password {{ isset($user) ? '(opsional)' : '' }}</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form> 
    </div>

@endsection

