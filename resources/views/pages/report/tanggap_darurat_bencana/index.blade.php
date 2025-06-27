@extends('layout.main')

@section('content')

    <div class="container-fluid">
        <table id="tanggap_darurat_bencana-table" class="table table-bordered table-striped" style="width:100%;overflow-x:auto;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Judul</th>
                    <th>Jenis Bencana</th>
                    <th>Deskripsi</th>
                    <th>Foto 1</th>
                    <th>Foto 2</th>
                    <th>Foto 3</th>
                    <th>Foto 4</th>
                    <th>Foto 5</th>
                    <th>Vidio</th>
                    <th>Lokasi</th>
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
            $('#tanggap_darurat_bencana-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('report.tanggap-darurat-bencana.data') }}',
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
                    { data: 'type' },
                    { data: 'description' },
                    { data: 'photo_1' },
                    { data: 'photo_2' },
                    { data: 'photo_3' },
                    { data: 'photo_4' },
                    { data: 'photo_5' },
                    { data: 'video' },
                    { data: 'address' },
                    { data: 'location' },
                    { data: 'surveyor' }
                ]
            });
        });

    </script>
@endsection

