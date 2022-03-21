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
                    <li class="breadcrumb-item active">Monthly Food Ordes</li>
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
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane table-responsive active" id="Admin">
                            <table id="orders" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>OrderStatusName</th>
                                        <th>CustomerOrderId</th>
                                        <th>CustomerBookingId</th>
                                        <th>CustomerName</th>
                                        <th>RestaurantName</th>
                                        <th>RiderName</th>
                                        <th>TotalPrice</th>
                                        <th>PaymentMethod</th>
                                        <th>OrderTime</th>
                                        <th>OrderDate</th>
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
        $(function () {
            $("#orders").DataTable({
                "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
                "paging": true, // Allow data to be paged
                "lengthChange": true,
                "searching": true, // Search box and search function will be actived
                "info": true,
                "autoWidth": true,
                "processing": true,  // Show processing
                ajax: "/fatty/main/admin/orders/datatable/monthlyfoodorderajax",
                columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
                {data: 'order_status_name', name:'order_status_name'},
                {data: 'customer_order_id', name:'customer_order_id'},
                {data: 'customer_booking_id', name:'customer_booking_id'},
                {data: 'customer_name', name:'customer_name'},
                {data: 'restaurant_name', name:'restaurant_name'},
                {data: 'rider_name', name:'rider_name'},
                {data: 'bill_total_price', name:'bill_total_price'},
                {data: 'payment_method_name', name:'payment_method_name'},
                {data: 'order_time', name:'order_time'},
                {data: 'ordered_date', name:'ordered_date'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                dom: 'PlBfrtip',
                buttons: [
                'excel', 'pdf', 'print'
                ],
            });
        });
        setTimeout(function() {
            $('#successMessage').fadeOut('fast');
        }, 2000);
    </script>
    @endpush
    