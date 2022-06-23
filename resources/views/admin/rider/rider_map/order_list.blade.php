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
            <div class="row mb-1">
                <div class="col-sm-7" style="height: 30px;">
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
                        <li class="breadcrumb-item active">Orders</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
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
                                <a href="{{ url('fatty/main/admin/all_riders_location') }}" class="btn btn-sm btn-info" ><i class="fas fa-location-arrow"></i>&nbsp;All Rider Location</a>
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Orders Information" }}</b></h4>
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
                                            <td><input type="text" id="min" name="min"></td>
                                        </tr>
                                        <tr>
                                            <td>Maximum date:</td>
                                            <td><input type="text" id="max" name="max"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table id="foods_orders" class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th>Status</th>
                                        <th>OrderId</th>
                                        <th>BookingId</th>
                                        <th>CustomerName</th>
                                        <th>OrderDate</th>
                                        <th>OrderType</th>
                                        <th>Action</th>
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

    var rider_id="{{ $rider_id }}";
       // Create date inputs
       $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,dateFormat: 'dd-M-yy' });

        $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat: 'dd-M-yy' });

        // DataTables initialisation
        var table = $("#foods_orders").DataTable({
            "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
            "paging": true, // Allow data to be paged
            "lengthChange": true,
            "searching": true, // Search box and search function will be actived
            "info": true,
            "autoWidth": true,
            "processing": true,  // Show processing
            ajax: "/fatty/main/admin/orders/datatable/assign_order_list_ajax/"+rider_id,
            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
            {data: 'order_status', name:'order_status'},
            {data: 'customer_order_id', name:'customer_order_id'},
            {data: 'customer_booking_id', name:'customer_booking_id'},
            {data: 'customer_name', name:'customer_name'},
            {data: 'ordered_date', name:'ordered_date'},
            {data: 'order_type', name:'order_type'},
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
