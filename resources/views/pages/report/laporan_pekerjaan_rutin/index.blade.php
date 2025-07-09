@extends('layout.main')

@section('content')

    <div class="container-fluid">
        <div style="float:right;" class="mb-5">
        </div>
        <table id="laporan_pekerjaan_rutin-table" class="table table-bordered table-striped" style="width:100%;overflow-x:auto;">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="date" id="start_date" class="form-control" value="{{ \Carbon\Carbon::now()->subMonth()->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <input type="date" id="end_date" class="form-control" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                </div>
                <div class="col-md-3">
                    {{-- <button id="filter" class="btn btn-primary">Filter</button> --}}
                    <a href="#" id="export-btn" class="btn btn-primary">Export Excel dengan Filter</a>
                </div>
            </div>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Saluran</th>
                    <th>Deskripsi</th>
                    <th>Foto 1</th>
                    <th>Foto 2</th>
                    <th>Foto 3</th>
                    <th>Foto 4</th>
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
            $('#laporan_pekerjaan_rutin-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('report.laporan-pekerjaan-rutin.data') }}',
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
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
                    { data: 'photo_2' },
                    { data: 'photo_3' },
                    { data: 'photo_4' },
                    { data: 'location' },
                    { data: 'surveyor' }
                ]
            });


            $('#filter').on('click', function () {
                $('#laporan_pekerjaan_rutin-table').DataTable().ajax.reload();
            });

            $('#export-btn').on('click', function(e) {
                e.preventDefault();
                let start = $('#start_date').val();
                let end = $('#end_date').val();
                let url = '{{ route("report.laporan-pekerjaan-rutin.export") }}' + '?start_date=' + start + '&end_date=' + end;
                window.location.href = url;
            });
        });

    </script>
@endsection

