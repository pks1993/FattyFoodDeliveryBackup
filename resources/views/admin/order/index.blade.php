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
                        <li class="breadcrumb-item active">Restaurant</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
                </div>
                <div class="col-md-12">
                    {{-- <form method='post' action="{{ route('fatty.admin.backup.restaurants') }}">
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
                                    {{-- @foreach($food_orders as $order)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($order->order_status_id=="1" || $order->order_status_id=="11")
                                                <a class="btn btn-warning btn-sm mr-1" style="color: white;width: 100%;">{{ $order->order_status->order_status_name }}</a>
                                            @elseif($order->order_status_id=="2" || $order->order_status_id=="9" || $order->order_status_id=="16")
                                                <a class="btn btn-danger btn-sm mr-1" style="color: white;width: 100%;">{{ $order->order_status->order_status_name }}</i></a>
                                            @elseif($order->order_status_id=="3" || $order->order_status_id=="4" || $order->order_status_id=="5" || $order->order_status_id=="6" || $order->order_status_id=="10" || $order->order_status_id=="12" || $order->order_status_id=="13" || $order->order_status_id=="14" || $order->order_status_id=="17")
                                                <a class="btn btn-danger btn-sm mr-1" style="color: white;width: 100%;">{{ $order->order_status->order_status_name }}</a>
                                            @else
                                                <a class="btn btn-success btn-sm mr-1" style="color: white;width: 100%">{{ $order->order_status->order_status_name }}</i></a>
                                            @endif
                                        </td>
                                        <td>{{ $order->customer_order_id }}</td>
                                        <td>{{ $order->customer_booking_id }}</td>
                                        <td>{{ $order->customer->customer_name }}</td>
                                        <td>
                                            @if($order->order_type=="food")
                                                <a class="btn btn-primary btn-sm mr-1" style="color: white;width: 100%">{{ $order->order_type }}</i></a>
                                            @else
                                                <a class="btn btn-secondary btn-sm mr-1" style="color: white;width: 100%">{{ $order->order_type }}</i></a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('fatty.admin.food_orders.assign',['order_id'=>$order->order_id])}}" class="btn btn-primary btn-sm mr-1" title="Assign"><i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach --}}
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
       var table = $("#foods_orders").DataTable({
           "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
           "paging": true, // Allow data to be paged
           "lengthChange": true,
           "searching": true, // Search box and search function will be actived
           "info": true,
           "autoWidth": true,
           "processing": true,  // Show processing
           ajax: "/fatty/main/admin/orders/datatable/assginorderajax",
           columns: [
           {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
           {data: 'order_status', name:'order_status'},
        //    {data: 'order_id', name:'order_id'},
           {data: 'customer_order_id', name:'customer_order_id'},
           {data: 'customer_booking_id', name:'customer_booking_id'},
        //    {data: 'ordered_date', name:'ordered_date'},
        //    {data: 'order_time', name:'order_time'},
        //    {data: 'customer_id', name:'customer_id'},
        //    {data: 'restauant_id', name:'restauant_id'},
        //    {data: 'rider_id', name:'rider_id'},
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
