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
                        <li class="breadcrumb-item active">Parcel Order Report</li>
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
                                <form method='post' action="{{ route('fatty.admin.backup.daily_parcel_orders') }}">
                                @csrf
                                    <input type="submit" class="btn btn-sm mb-1" style="background-color: #000335;color: #FFFFFF;" name="all_parcel_exportexcel" value='All Excel Export'>
                                    <!-- <input type="submit" class="btn btn-sm mb-1" style="background-color: #000335;color: #FFFFFF;" name="all_food_order_exportexcel" value='All Excel Export'> -->
                                </form>
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Parcel Orders Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <div class="col-12 mb-3">
                            <form action="{{ url('fatty/main/admin/riders/parcel_order/report') }}">
                                <input class="col-5 col-md-2" type="date" name="start_date" value="{{ \Carbon\Carbon::parse($date_start)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                                <input class="col-5 col-md-2" type="date" name="end_date" value="{{ \Carbon\Carbon::parse($date_end)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                                <button class="col-1 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                        <div class="col-12">
                            {{ $orders->appends(request()->input())->links() }}
                        </div>
                        <table id="foods_orders" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>Date</th>
                                    <th>RiderName</th>
                                    <th>OrderId(BookingId)</th>
                                    <th>Income</th>
                                    <th>RiderBill(DeliFee)</th>
                                    <th>Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $key => $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $value->created_at }}</td>
                                    <td>
                                        @if($value->rider_id)
                                            {{ $value->rider->rider_user_name }}
                                        @else
                                            {{ "Empty" }}
                                        @endif
                                    </td>
                                    <td>#{{ $value->customer_order_id }} ( {{ $value->customer_booking_id }} )</td>
                                    <td>{{ $value->bill_total_price }}</td>
                                    <td>{{ $value->rider_delivery_fee }}</td>
                                    <td>{{ $value->bill_total_price-$value->rider_delivery_fee }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- {{ $orders->appends(request()->input())->links() }} --}}
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
//     $.fn.dataTable.ext.search.push(
//    function( settings, data, dataIndex ) {
//        var min = $('#min').datepicker("getDate");
//        var max = $('#max').datepicker("getDate");
//        var date = new Date( data[1] );

//        if (
//        ( min === null && max === null ) ||
//        ( min === null && date <= max ) ||
//        ( min <= date   && max === null ) ||
//        ( min <= date   && date <= max )
//        ) {
//            return true;
//        }
//        return false;
//    }
//    );


   $(document).ready(function() {
       // Create date inputs
    //    $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,dateFormat: 'dd-mm-yy' });

    //    $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat: 'dd-mm-yy' });

       // DataTables initialisation
       var table = $("#foods_orders").DataTable({
        //    "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
           "paging": false, // Allow data to be paged
           "lengthChange": false,
           "searching": false, // Search box and search function will be actived
           "info": false,
           "autoWidth": true,
           "processing": true,  // Show processing
        //    ajax: "/fatty/main/admin/riders/parcel_order/datatable/report/parcelorderajax",
        //    columns: [
        //    {data: 'DT_RowIndex', name: 'DT_RowIndex',className:"text-center" , orderable: false, searchable: false},
        //    {data: 'ordered_date', name:'ordered_date'},
        //    {data: 'rider_name', name:'rider_name'},
        //    {data: 'customer_order_id', name:'customer_order_id'},
        //    {data: 'bill_total_price', name:'bill_total_price'},
        //    {data: 'rider_delivery_fee', name:'rider_delivery_fee'},
        //    {data: 'profit', name:'profit'},
        //    {data: 'action', name: 'action',className:'btn-group', orderable: false, searchable: false},
        //    ],
        //    dom: 'lBfrtip',
        //    buttons: [
        //    'excel', 'pdf', 'print'
        //    ],
       });
    //    // Refilter the table
    //    $('#min, #max').on('change', function () {
    //        table.draw();
    //    });
   });
   setTimeout(function() {
       $('#successMessage').fadeOut('fast');
   }, 2000);
</script>
@endpush
