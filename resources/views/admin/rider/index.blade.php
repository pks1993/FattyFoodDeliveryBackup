@extends('admin.layouts.master')

@section('css')
<style>
    form>.fa {
        display: none;
    }
    .dt-buttons>button{
        border-radius: revert;
        margin-top: 15px;
        margin-right: 5px;
    }
    .dataTables_length >label {
        margin-right: 15px !important;
        margin-top: 15px;
    }
    .number {
        text-align: center;
    }
    .order_amount {
        text-align: center;
    }
    .order_count {
        text-align: center;
    }
    .action {
        text-align: center;
    }
    .register_date {
        text-align: center;
    }
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-7" style="height: 20px;">
                <div class="flash-message" id="successMessage">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Riders</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
            <div class="col-md-12">
                {{-- <form method='post' action="{{ route('fatty.admin.backup.riders') }}">
                    @csrf
                    <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportexcel" value='Excel Export'>
                    <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportcsv" value='CSV Export'>
                </form> --}}
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{route('fatty.admin.riders.create')}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus-circle"></i> Add rider</a>
                            <a href="{{url('fatty/main/admin/all_riders_location')}}" class="btn btn-info btn-sm">
                                <i class="fa fa-location-arrow"></i> All Rider Location</a>
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Rider Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <table border="0" cellspacing="5" cellpadding="5">
                                    <tbody>
                                        <tr>
                                            <td>Minimum date:</td>
                                            <td><input type="text" id="min" name="min" autocomplete="off"></td>
                                        </tr>
                                        <tr>
                                            <td>Maximum date:</td>
                                            <td><input type="text" id="max" name="max" autocomplete="off"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table id="riders" class="table table-bordered table-striped table-hover display nowrap" border="0" cellspacing="5" cellpadding="5">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Action</th>
                                            <th>Image</th>
                                            <th class="text-left">RiderName</th>
                                            <th class="text-left">RiderPhone</th>
                                            <th class="text-left">RiderLevel</th>
                                            <th class="text-left">MaxOrder</th>
                                            <th class="text-left">MaxDistance(Km)</th>
                                            <th class="text-left">StateName</th>
                                            <th class="text-left">RegisterDate</th>
                                            <th>Latitude</th>
                                            <th>Longitude</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
    @push('scripts')
    <script>

setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2500);

        // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = $('#min').datepicker("getDate");
        var max = $('#max').datepicker("getDate");
        var date = new Date( data[6] );

        if (
        ( min === null && max === null ) ||
        ( min === null && date <= max ) ||
        ( min <= date   && max === null ) ||
        ( min <= date   && date <= max )
        ) {
            return true;
        }
        return false;
    }
    );


    $(document).ready(function() {
        // Create date inputs
        $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,dateFormat: 'dd-M-yy' });

        $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat: 'dd-M-yy' });

            // DataTables initialisation
            var table = $("#riders").DataTable({
                "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
                "paging": true, // Allow data to be paged
                "lengthChange": true,
                "searching": true, // Search box and search function will be actived
                "info": true,
                "autoWidth": true,
                "processing": true,  // Show processing
                ajax: "/fatty/main/admin/riders/datatable/riderajax",
                columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' ,className: "number" , orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false,className: "action"},
                {data: 'rider_image', name:'rider_image',className: "rider_image"},
                {data: 'rider_user_name', name:'rider_user_name'},
                {data: 'rider_user_phone', name:'rider_user_phone'},
                {data: 'rider_level_name', name:'rider_level_name',className:"text-center"},
                {data: 'max_order', name:'max_order',className:"text-center"},
                {data: 'max_distance', name:'max_distance',className:"text-center"},
                {data: 'state', name:'state'},
                {data: 'register_date', name:'register_date',className: "register_date"},
                {data: 'rider_latitude', name:'rider_latitude',className:"text-left"},
                {data: 'rider_longitude', name:'rider_longitude',className:"text-left"},
                {data: 'delete', name:'delete',className:"text-center"},

                ],
                dom: 'lBfrtip',
                buttons: [
                'excel', 'pdf', 'print'
                ],
            });

            // Refilter the table
            $('#min, #max').on('change', function () {
                table.draw();
            });
        });
    </script>
    @endpush
