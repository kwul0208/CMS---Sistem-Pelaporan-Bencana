@extends('layout.main')

@section('content')

    <div class="container-fluid">
        <div style="float:right;" class="mb-5">
            <a href="{{ route('position.create') }}" class="btn btn-primary">Tambah Position</a>
        </div>
        <table id="user-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Link</th>
                    <th>Aksi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->link }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>
                            <a href="/sosmed/{{ $item->id }}/edit" class="btn btn-info">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<!-- jQuery (load first!) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<!-- Optional: Bootstrap DataTables styling (if used) -->
{{-- <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script> --}}

<!-- Stack for child views -->

<script>
    $(document).ready(function () {
        $('#user-table').DataTable({});
        
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });

        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 2000
            });

        @endif
    });
</script>
@endsection
