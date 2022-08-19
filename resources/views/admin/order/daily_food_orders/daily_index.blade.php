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
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-secondary">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $filter_count }}</h3></span></div>
                        <p>Total Food Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-primary">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $pending_orders }}</h3></span></div>
                        <p>Total Pending Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-info">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $processing_orders }}</h3></span></div>
                        <p>Processing Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-success">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $delivered_orders }}</h3></span></div>
                        <p>Delivered Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-danger">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $customer_cancel_orders }}</h3></span></div>
                        <p>Customer Cancel</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-danger">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $restaurant_cancel_orders }}</h3></span></div>
                        <p>Restaurant Cancel</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="flash-message" id="successMessage">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                    @endif
                    @endforeach
                </div>
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
                        <form action="{{ url('fatty/main/admin/daily_food_orders/date_filter') }}">
                            <input class="col-5 col-md-2" type="date" name="start_date" value="{{ \Carbon\Carbon::parse($date_start)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <input class="col-5 col-md-2" type="date" name="end_date" value="{{ \Carbon\Carbon::parse($date_end)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <button class="col-1 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="div mb-2">
                        <form action="{{ url('fatty/main/admin/daily_food_orders/search') }}">
                            <input class="col-9 col-md-4" type="type" name="search_name" placeholder="Filter Enter Order Id OR Booking Id" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <input type="hidden" name="start_date" value="{{ $date_start }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <input type="hidden" name="end_date" value="{{ $date_end }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <button class="col-2 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="orders" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>OrderStatus</th>
                                    <th>CustomerType</th>
                                    <th>OrderId</th>
                                    <th>BookingId</th>
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
                                @foreach ($total_orders as $item)
                                <tr class="text-center">
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
                                    <td>{{ $item->customer_order_id }}</td>
                                    <td class="text-left">{{ $item->customer_booking_id }}</td>
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
                            <tbody>
                            </tbody>
                        </table>
                        {{ $total_orders->appends(request()->input())->links() }}
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
    }, 2000);
</script>
@endpush
