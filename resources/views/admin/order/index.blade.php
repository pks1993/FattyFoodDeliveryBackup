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
                    <a href="{{url('fatty/main/admin/all_riders_location')}}" class="btn btn-info btn-sm">
                        <i class="fa fa-location-arrow"></i> All Rider Location
                    </a>
                    <a class="btn btn-danger btn-sm" style="color: white">
                        Total Orders >> <b>{{ $total_order }}</b>
                    </a>
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
                        <li class="breadcrumb-item active">Order Assign</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
                </div>
                <div class="col-sm-12">
                    {{-- <form method='post' action="{{ route('fatty.admin.backup.restaurants') }}">
                       @csrf
                       <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportexcel" value='Excel Export'>
                       <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportcsv" value='CSV Export'>
                    </form> --}}
                    {{-- <a href="{{url('fatty/main/admin/all_riders_location')}}" class="btn btn-info btn-sm">
                        <i class="fa fa-location-arrow"></i> All Rider Location
                    </a> --}}
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="col-12">
                            <form action="{{ url('fatty/main/admin/foods/orders/date_filter') }}">
                                {{-- <input class="col-5 col-md-2" type="date" name="start_date" value="{{ now()->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px"> --}}
                                <input class="col-5 col-md-2" type="date" name="start_date" value="{{ \Carbon\Carbon::parse($date_start)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                                <input class="col-5 col-md-2" type="date" name="end_date" value="{{ \Carbon\Carbon::parse($date_end)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                                <button class="col-1 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="div mb-2">
                            <form action="{{ url('fatty/main/admin/foods/orders/search') }}">
                                <input class="col-9 col-md-4" type="type" name="search_name" placeholder="Filter Enter Order Id OR Booking Id" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                                <input type="hidden" name="start_date" value="{{ $date_start }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                                <input type="hidden" name="end_date" value="{{ $date_end }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                                <button class="col-2 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table id="foods_orders" class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>Status</th>
                                    <th>OrderId</th>
                                    <th>BookingId</th>
                                    <th>CustomerName</th>
                                    <th>OrderDate</th>
                                    <th>Duration</th>
                                    <th>OrderType</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($item->order_status_id=='1' || $item->order_status_id=="19")
                                            <a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">Pending (NotAcceptRestaurant)</a>
                                        @elseif($item->order_status_id=='11')
                                            <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Pending (NotAcceptRider)</a>
                                        @elseif($item->order_status_id=='3')
                                            <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:red;">AcceptByRestaurant(NotAcceptRider)</a>
                                        @elseif($item->order_status_id=='5')
                                            <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ReadyToPick(NotAcceptRider)</a>
                                        @else
                                            <a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Check Error</a>
                                        @endif
                                    </td>
                                    <td>{{ $item->customer_order_id }}</td>
                                    <td class="text-left">{{ $item->customer_booking_id }}</td>
                                    <td class="text-left">
                                        @if($item->customer_id)
                                            {{ $item->customer->customer_name }}
                                        @else
                                            <span class="text-red">{{ "Empty" }}</span>
                                        @endif
                                    </td>
                                    <td>{{ date('d/M/Y',strtotime($item->created_at)) }}</td>
                                    <td>
                                        {{ $item->created_at->diffForHumans(null,true,true) }}
                                    </td>
                                    <td>
                                        @if($item->order_type=="food")
                                            <a class="btn btn-primary btn-sm mr-1" style="color: white;width: 100%;background-color: #800000;">{{ $item->order_type }}</i></a>
                                        @else
                                            <a class="btn btn-secondary btn-sm mr-1" style="color: white;width: 100%;background-color: #8A2BE2;">{{ $item->order_type }}</i></a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('fatty.admin.food_orders.assign',['order_id'=>$item->order_id])}}" class="btn btn-primary btn-sm mr-1" title="Assign"><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $orders->appends(request()->input())->links() }}
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
//        var date = new Date( data[5] );

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


//    $(document).ready(function() {
//        // Create date inputs
//        $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,dateFormat: 'dd-M-yy' });

//        $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat: 'dd-M-yy' });

//        // DataTables initialisation
//        var table = $("#foods_orders").DataTable({
//            "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
//            "paging": true, // Allow data to be paged
//            "lengthChange": true,
//            "searching": true, // Search box and search function will be actived
//            "info": true,
//            "autoWidth": true,
//            "processing": true,  // Show processing
//            ajax: "/fatty/main/admin/orders/datatable/assginorderajax",
//            columns: [
//            {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
//            {data: 'order_status', name:'order_status'},
//         //    {data: 'order_id', name:'order_id'},
//            {data: 'customer_order_id', name:'customer_order_id'},
//            {data: 'customer_booking_id', name:'customer_booking_id'},
//         //    {data: 'ordered_date', name:'ordered_date'},
//         //    {data: 'order_time', name:'order_time'},
//         //    {data: 'customer_id', name:'customer_id'},
//         //    {data: 'restauant_id', name:'restauant_id'},
//         //    {data: 'rider_id', name:'rider_id'},
//         {data: 'customer_name', name:'customer_name'},
//         {data: 'ordered_date', name:'ordered_date'},
//         {data: 'duration', name:'duration',className:"text-center"},
//            {data: 'order_type', name:'order_type'},
//            {data: 'action', name: 'action',className:'btn-group', orderable: false, searchable: false},
//            ],
//            dom: 'lBfrtip',
//            buttons: [
//            'excel', 'pdf', 'print'
//            ],
//        });
//        // Refilter the table
//        $('#min, #max').on('change', function () {
//            table.draw();
//        });
//    });
   setTimeout(function() {
       $('#successMessage').fadeOut('fast');
   }, 2000);
</script>
@endpush
