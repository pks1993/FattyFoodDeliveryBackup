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
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-7">
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
                    <li class="breadcrumb-item active">Daily Parcel Ordes</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
            {{-- <div class="col-md-12">
                <form method='post' action="{{ route('fatty.admin.backup.customers') }}">
                    @csrf
                    <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportexcel" value='Excel Export'>
                    <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportcsv" value='CSV Export'>
                </form>
            </div> --}}
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-header">
                <!-- <a href="{{url('fatty/main/admin/parcel_orders/create')}}" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus-circle"></i>Add new parcel
                                </a> -->
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane table-responsive active" id="Admin">
                            <table border="0" cellspacing="5" cellpadding="5">
                                <tbody>
                                    <tr>
                                        <td>Minimum date:</td>
                                        <td><input type="text" id="min" value="{{ now()->format('d-M-Y') }}" name="min"></td>
                                    </tr>
                                    <tr>
                                        <td>Maximum date:</td>
                                        <td><input type="text" id="max" value="{{ now()->format('d-M-Y') }}" name="max"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table id="orders" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>OrderStatus</th>
                                        <th>CustomerType</th>
                                        <th>OrderId</th>
                                        <th>BookingId</th>
                                        <th>OrdereDate</th>
                                        <th>OrderTime</th>
                                        <th>CustomerName</th>
                                        <th>RiderName</th>
                                        <th>PaymentMethod</th>
                                        <th>TotalPrice</th>
                                        <th>Detail</th>
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
     // Custom filtering function which will search data in column four between two values
     $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = $('#min').datepicker("getDate");
        var max = $('#max').datepicker("getDate");
        var date = new Date( data[5] );

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
        var table = $("#orders").DataTable({
            "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
            "paging": true, // Allow data to be paged
            "lengthChange": true,
            "searching": true, // Search box and search function will be actived
            "info": true,
            "autoWidth": true,
            "processing": true,  // Show processing
            ajax: "/fatty/main/admin/orders/datatable/dailyparcelorderajax",
            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
            // {data: 'order_status_name', name:'order_status_name'},
            {data: 'status', name:'status'},
            {data: 'customer_type', name:'customer_type',className:'text-center'},
            {data: 'customer_order_id', name:'customer_order_id',className:'text-center'},
            {data: 'customer_booking_id', name:'customer_booking_id',className:'text-center'},
            {data: 'ordered_date', name:'ordered_date',className:'text-center'},
            {data: 'order_time', name:'order_time',className:'text-center'},
            {data: 'customer_name', name:'customer_name'},
            {data: 'rider_name', name:'rider_name'},
            {data: 'payment_method_name', name:'payment_method_name'},
            {data: 'bill_total_price', name:'bill_total_price'},
            {data: 'action', name: 'action',className:'btn-group', orderable: false, searchable: false},
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
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
