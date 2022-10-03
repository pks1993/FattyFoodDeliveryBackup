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
                                {{-- <table border="0" cellspacing="5" cellpadding="5">
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
                                </table> --}}
                                {{ $food_orders->appends(request()->input())->links() }}
                                <table id="foods_orders" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="text-center">No.</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">OrderId</th>
                                            <th class="text-center">BookingId</th>
                                            <th class="text-center">CustomerName</th>
                                            <th class="text-center">OrderDate</th>
                                            <th class="text-center">OrderType</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($food_orders as $value)
                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if($value->order_status_id=='1' || $value->order_status_id=="19")
                                                    <a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">{{ "Pending (NotAcceptRestaurant)"}}</a>
                                                @elseif($value->order_status_id=='11')
                                                    <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">{{ "Pending (NotAcceptRider)"}}</a>
                                                @elseif($value->order_status_id=='3')
                                                    <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:red;">{{ "AcceptByRestaurant(NotAcceptRider)"}}</a>
                                                @elseif($value->order_status_id=='5')
                                                    <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">{{ "ReadyToPick(NotAcceptRider)"}}</a>
                                                @else
                                                    <a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">{{ "Check Error"}}</a>
                                                @endif
                                            </td>
                                            <td>{{ $value->customer_order_id }}</td>
                                            <td>{{ $value->customer_booking_id }}</td>
                                            <td>
                                                @if ($value->customer_id)
                                                    {{ $value->customer->customer_name }}
                                                @else
                                                    {{ "Empty" }}
                                                @endif
                                            </td>
                                            <td>{{ date('d/m/Y',strtotime($value->created_at)) }}</td>
                                            <td>
                                                @if($value->order_type=="food")
                                                    <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:#bde000;color:black;">{{ $value->order_type }}</a>
                                                @else
                                                    <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:#00dfc2;color:black;">{{ $value->order_type }}</a>
                                                @endif
                                            </td>
                                            <td>
                                                @if($value->order_type=="food")
                                                    <a href="/fatty/main/admin/food_orders/view/{{ $value->order_id }}" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>
                                                @else
                                                    <a href="/fatty/main/admin/parcel_orders/view/{{ $value->order_id }}" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>
                                                @endif
                                                    <a href="/fatty/main/admin/orders/assign/{{ $value->order_id}}/{{ $rider_id }}" onclick="return confirm('Are you sure want to Assign this order?')" title="Admin Assign" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus-circle"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
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
//    $(document).ready(function() {

//     var rider_id="{{ $rider_id }}";
//        // Create date inputs
//        $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,dateFormat: 'dd-M-yy' });

//         $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat: 'dd-M-yy' });

//         // DataTables initialisation
//         var table = $("#foods_orders").DataTable({
//             "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
//             "paging": true, // Allow data to be paged
//             "lengthChange": true,
//             "searching": true, // Search box and search function will be actived
//             "info": true,
//             "autoWidth": true,
//             "processing": true,  // Show processing
//             ajax: "/fatty/main/admin/orders/datatable/assign_order_list_ajax/"+rider_id,
//             columns: [
//             {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
//             {data: 'order_status', name:'order_status'},
//             {data: 'customer_order_id', name:'customer_order_id'},
//             {data: 'customer_booking_id', name:'customer_booking_id'},
//             {data: 'customer_name', name:'customer_name'},
//             {data: 'ordered_date', name:'ordered_date'},
//             {data: 'order_type', name:'order_type'},
//             {data: 'action', name: 'action',className:'btn-group', orderable: false, searchable: false},
//             ],
//             dom: 'lBfrtip',
//             buttons: [
//             'excel', 'pdf', 'print'
//             ],
//         });
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
