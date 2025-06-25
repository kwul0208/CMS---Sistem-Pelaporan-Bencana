@extends('layout.main')

@section('content')

    <div class="container-fluid">
        <div style="float:right;" class="mb-5">
            <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah User</a>
        </div>
        <table id="user-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Aksi</th>
                </tr>
            </thead>
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
    function hapusUser(id) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('hapus-form-' + id).submit();
            }
        });
    }
    $(document).ready(function () {
        $('#user-table').DataTable({ // perhatikan huruf besar D
            processing: true,
            serverSide: true,
            ajax: '{{ route('users.data') }}',
            columns: [
                { data: 'name' },
                { data: 'email' },
                { data: 'position_name' },
                {
                    data: 'id',
                    render: function (data, type, row) {
                        return `<a href="/users/${data}/edit" class="btn btn-info">Edit</a> |
                                <button onclick="hapusUser(${data})" class="btn btn-danger">Hapus</button>
                                <form id="hapus-form-${data}" method="POST" action="/users/${data}" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>`;
                    }
                }
            ]
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });

        @endif
    });
</script>
@endsection
