@extends('layout.main')

@section('content')

    <div class="container-fluid">
        <div style="float:right;" class="mb-5">
            <a href="{{ route('report.data-saluran.export') }}" class="btn btn-primary">Export Excel dengan File</a>
        </div>
        <table id="data_saluran-table" class="table table-bordered table-striped" style="width:100%;overflow-x:auto;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Saluran</th>
                    <th>Deskripsi</th>
                    <th>Foto Form Survey</th>
                    <th>Foto</th>
                    <th>Koordinat</th>
                    <th>Surveyor</th>
                </tr>
            </thead>
        </table>
    </div>
    <!-- DataTables core CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables Buttons extension CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables core JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Buttons + dependencies -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>



    <script>
        $(document).ready(function () {
            $('#data_saluran-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('report.data-saluran.data') }}',
                scrollX: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Tanggap Darurat',
                        text: 'Export Excel'
                    }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'date' },
                    { data: 'title' },
                    { data: 'description' },
                    { data: 'photo_1' },
                    { data: 'photo' },
                    { data: 'location' },
                    { data: 'surveyor' }
                ]
            });
        });

    </script>
@endsection


