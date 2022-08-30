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
                            <div class="col-12">
                                <form action="{{ url('fatty/main/admin/monthly_food_orders') }}">
                                    <input class="col-5 col-md-2" type="month" name="start_date" value="{{ \Carbon\Carbon::parse($date_start)->startOfMonth()->format('Y-m') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <button class="col-1 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                                </form>
                            </div>
                            <div class="mt-3">
                                {{ $orders->appends(request()->input())->links() }}
                            </div>
                            <table id="orders" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>OrderStatus</th>
                                        <th>CustomerType</th>
                                        <th>OrderId(BookingId)</th>
                                        <th>OrdereDate</th>
                                        <th>OrderTime</th>
                                        <th>Duration</th>
                                        <th>CustomerName</th>
                                        <th>RestaurantName</th>
                                        <th>RiderName</th>
                                        <th>DeliFeeRider</th>
                                        <th>DeliFeeCustomer</th>
                                        <th>TotalPrice</th>
                                        <th>PaymentMethod</th>
                                        <th>Detail</th>
                                        <th>Pending</th>
                                        <th>Complete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($item->order_status_id=='1')
                                                <a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">Pending(NotAcceptShop)</a>
                                            @elseif($item->order_status_id=='2')
                                                <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">CancelByShop</a>
                                            @elseif($item->order_status_id=='9')
                                                <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">CancelByCustomer</a>
                                            @elseif($item->order_status_id=='3')
                                                <a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">AcceptByShop</a>
                                            @elseif($item->order_status_id=='4')
                                                <a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">AcceptByRider</a>
                                            @elseif($item->order_status_id=='5')
                                                <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ReadyToPickup </a>
                                            @elseif($item->order_status_id=='7')
                                                <a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">AcceptCustomer</a>
                                            @elseif($item->order_status_id=='8')
                                                <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:red;">PendingOrder(CustomerNotFound)</a>
                                            @elseif($item->order_status_id=='6')
                                                <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">StartDelivery</a>
                                            @elseif($item->order_status_id=='10')
                                                <a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;">RiderArrivedShop</a>
                                            @elseif($item->order_status_id=='18')
                                                <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:yellow;">KBZ Pending</a>
                                            @elseif($item->order_status_id=='19')
                                                <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:green;">KBZ Success</a>
                                            @elseif($item->order_status_id=='20')
                                                <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:red;">KBZ Fail</a>
                                            @else
                                                <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:black">CheckError</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->customer_id==null)
                                                <a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>
                                            @else
                                                @if($item->customer->customer_type_id==null)
                                                    <a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>
                                                @elseif($item->customer->customer_type_id==2)
                                                    <a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>
                                                @elseif($item->customer->customer_type_id==1)
                                                    <a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>
                                                @else
                                                    <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>
                                                @endif
                                            @endif
                                        </td>
                                        <td>#{{ $item->customer_order_id }} ( {{ $item->customer_booking_id}} )</td>
                                        <td>{{ date('d/M/Y',strtotime($item->created_at)) }}</td>
                                        <td>{{ $item->order_time }}</td>
                                        <td>
                                            @if($item->order_status_id==7 || $item->order_status_id==8 || $item->order_status_id==2 || $item->order_status_id==9)
                                                {{ $item->updated_at->diffForHumans($item->created_at,true,true) }}
                                            @else
                                                {{ $item->created_at->diffForHumans(null,true,true) }}
                                            @endif
                                        </td>
                                        <td class="text-left">
                                            @if($item->customer_id)
                                                {{ $item->customer->customer_name }}
                                            @else
                                                {{ "Empty" }}
                                            @endif
                                        </td>
                                        <td class="text-left">
                                            @if($item->restaurant_id)
                                                {{ $item->restaurant->restaurant_name_mm }}
                                            @else
                                                {{ "Empty" }}
                                            @endif
                                        </td>
                                        <td class="text-left">
                                            @if($item->rider_id)
                                                {{ $item->rider->rider_user_name }}
                                            @else
                                                {{ "Empty" }}
                                            @endif
                                        </td>
                                        <td>{{ $item->rider_delivery_fee }}</td>
                                        <td>{{ $item->delivery_fee }}</td>
                                        <td>{{ $item->bill_total_price }}</td>
                                        <td class="text-left">
                                            @if($item->payment_method_id==1)
                                                <span class="btn btn-sm btn-primary">{{ "Cash_On_Delivery" }}</span>
                                            @elseif($item->payment_method_id==2)
                                                <span class="btn btn-sm btn-success">{{ "Kpay" }}</span>
                                            @else
                                                <span class="btn btn-sm btn-secondary">{{ "Empty" }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="/fatty/main/admin/food_orders/view/{{ $item->order_id }}" class="btn btn-info btn-sm mr-2" title="order detail"><i class="fas fa-eye"></i></a>
                                        </td>
                                        <td>
                                            <a href="/fatty/main/admin/pending/orders/define/{{ $item->order_id }}" onclick="return confirm('Are You Sure Want to Pending Order?')" class="btn btn-primary btn-sm mr-2" title="Order Pending"><i class="fas fa-plus-circle"></i></a>
                                        </td>
                                        <td>
                                            <a href="/fatty/main/admin/complete_order/update/{{ $item->order_id }}" onclick="return confirm('Are You Sure Want to Complete Order?')" class="btn btn-success btn-sm mr-2" title="Order Complete"><i class="fas fa-plus-circle"></i></a>
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
    $(document).ready(function() {
        var table = $("#orders").DataTable({
            "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
            "paging": false, // Allow data to be paged
            "lengthChange": false,
            "searching": false, // Search box and search function will be actived
            "info": false,
            "autoWidth": false,
            "processing": false,  // Show processing
            dom: 'lBfrtip',
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
