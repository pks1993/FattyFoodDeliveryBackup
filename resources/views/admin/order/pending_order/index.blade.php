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
        <div class="row">
            <div class="col-sm-7" style="width: 30px;">
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
                    <li class="breadcrumb-item active">Pending Ordes</li>
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
                <!-- /.card-header -->
                <div class="card-body">
                    {{-- <table border="0" cellspacing="5" cellpadding="5">
                        <tbody>
                            <tr>
                                <td>Minimum date:</td>
                                <td><input type="text" id="min" name="min"></td>
                                <td><input type="text" id="min" value="{{ now()->format('d-M-Y') }}" name="min"></td>
                            </tr>
                            <tr>
                                <td>Maximum date:</td>
                                <td><input type="text" id="max" name="max"></td>
                                <td><input type="text" id="max" value="{{ now()->format('d-M-Y') }}" name="max"></td>
                            </tr>
                        </tbody>
                    </table> --}}
                    <div class="table-responsive">
                        <table id="orders" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">OrderStatus</th>
                                    <th class="text-center">OrderId</th>
                                    <th class="text-center">CustomerBookingId</th>
                                    <th class="text-center">OrdereDate</th>
                                    <th class="text-center">OrderTime</th>
                                    <th>CustomerName</th>
                                    <th>RestaurantName</th>
                                    <th>RiderName</th>
                                    <th class="text-center">PaymentMethod</th>
                                    <th class="text-center">TotalPrice</th>
                                    <th class="text-center">OrderType</th>
                                    <th class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($item->order_status_id==8)
                                            <a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">Pending(CustomerNotFound)</a>
                                        @endif
                                    </td>
                                    <td>{{ $item->customer_order_id }}</td>
                                    <td>{{ $item->customer_booking_id }}</td>
                                    <td>{{ $item->created_at->format('d/M/Y') }}</td>
                                    <td>{{ $item->order_time }}</td>
                                    <td>
                                        @if ($item->customer_id)
                                            {{ $item->customer->customer_name }}
                                        @else
                                            <span style="color: red">{{ "Empty" }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->restaurant_id)
                                            {{ $item->restaurant->restaurant_name }}
                                        @else
                                            <span style="color: red">{{ "Empty" }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->rider_id)
                                            {{ $item->rider->rider_user_name }}
                                        @else
                                            <span style="color: red">{{ "Empty" }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->payment_method_id)
                                            {{ $item->payment_method->payment_method_name }}
                                        @else
                                            <span style="color: red">{{ "Empty" }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->bill_total_price }}</td>
                                    <td>
                                        @if($item->order_type=="food")
                                            <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:#bde000;color:black;">{{ $item->order_type }}</a>
                                        @else
                                            <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:#00dfc2;color:black;">{{ $item->order_type }}</a>
                                        @endif
                                    </td>
                                    <td class="btn-group">
                                        @if($item->order_type=="food")
                                            <a href="/fatty/main/admin/food_orders/view/{{  $item->order_id }}" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>
                                        @else
                                            <a href="/fatty/main/admin/parcel_orders/view/{{ $item->order_id }}" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>
                                        @endif
                                        <a href="/fatty/main/admin/foods/orders/pending_assign/{{  $item->order_id }}" title="Rider Assign" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus-circle"></i></a>
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
    //  $.fn.dataTable.ext.search.push(
    // function( settings, data, dataIndex ) {
    //     var min = $('#min').datepicker("getDate");
    //     var max = $('#max').datepicker("getDate");
    //     var date = new Date( data[4] );

    //     if (
    //     ( min === null && max === null ) ||
    //     ( min === null && date <= max ) ||
    //     ( min <= date   && max === null ) ||
    //     ( min <= date   && date <= max )
    //     ) {
    //         return true;
    //     }
    //     return false;
    // }
    // );


    // $(document).ready(function() {
    //     // Create date inputs
    //     $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,dateFormat: 'dd-M-yy' });

    //     $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat: 'dd-M-yy' });

    //     // DataTables initialisation
    //     var table = $("#orders").DataTable({
    //         "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
    //         "paging": true, // Allow data to be paged
    //         "lengthChange": true,
    //         "searching": true, // Search box and search function will be actived
    //         "info": true,
    //         "autoWidth": true,
    //         "processing": true,  // Show processing
    //         ajax: "/fatty/main/admin/orders/datatable/pendingorderajax",
    //         columns: [
    //         {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
    //         {data: 'order_status', name:'order_status'},
    //         {data: 'customer_order_id', name:'customer_order_id'},
    //         {data: 'customer_booking_id', name:'customer_booking_id'},
    //         {data: 'ordered_date', name:'ordered_date'},
    //         {data: 'order_time', name:'order_time'},
    //         {data: 'customer_name', name:'customer_name'},
    //         {data: 'restaurant_id', name:'restaurant_id'},
    //         {data: 'rider_id', name:'rider_id'},
    //         {data: 'payment_method_id', name:'payment_method_id'},
    //         {data: 'bill_total_price', name:'bill_total_price'},
    //         {data: 'order_type', name:'order_type'},
    //         {data: 'action', name: 'action',className:'btn-group', orderable: false, searchable: false},
    //         ],
    //         dom: 'lBfrtip',
    //         buttons: [
    //         'excel', 'pdf', 'print'
    //         ],
    //     });
    //     // Refilter the table
    //     $('#min, #max').on('change', function () {
    //         table.draw();
    //     });
    // });
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
