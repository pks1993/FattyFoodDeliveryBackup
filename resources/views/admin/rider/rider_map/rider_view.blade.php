<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rider Location Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/util.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/main.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">


    <style type="text/css">
        #map {
          height: 100%;
        }
        html,
        body  {
            height: 95%;
            margin:0;
            margin-bottom: 100px;
            padding: 0px 5px 0px 5px;
        }
    </style>
</head>
<body>
    <div class="p-3">
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ url('fatty/main/admin/all_riders_location') }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
            </div>
            <div class="col-md-6">
                <div class="float-right">
                    <a href="{{ url('fatty/main/admin/assign/order/list/'.$rider->rider_id) }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color: blue;color:black;">AssignOrder&nbsp;<i class="fas fa-angle-double-right"></i></a>
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
        <div class="row mb-3">
            <div class="col-md-2">
                <label>Rider Image :</label><br>
                @if($rider->rider_image)
                    {{-- <img src="../../../../../uploads/rider/{{ $rider->rider_image}}" class="img-thumbnail" width="200" height="200"> --}}
                    <img src="../../../../../image/available.png" class="img-thumbnail" width="200px" height="200px">
                @else
                    <img src="../../../../../image/available.png" class="img-thumbnail" width="200px" height="200px">
                @endif
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-primary">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $filter_count }}</h3></span></div>
                        <p>Total Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-warning">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $processing_count }}</h3></span></div>
                        <p>Processing Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-success">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $delivered_count }}</h3></span></div>
                        <p>Delivered Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-primary">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $total_amount }}</h3></span></div>
                        <p>Total Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-xs-2">
                <div class="small-box bg-info">
                    <div class="col-12">
                        <div><span class="count"><h3>{{ $total_delivery_fee }}</h3></span></div>
                        <p>Total Delivery Fee</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Rider Name:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_user_name }}" readonly>
            </div>

            <div class="col-md-4">
                <label>Rider Phone:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_user_phone }}" readonly>
            </div>

            <div class="col-md-4">
                <label>Rider Password:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_user_password }}" readonly>
            </div>

        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Rider Latitude:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_latitude }}" readonly>
            </div>
            <div class="col-md-4">
                <label>Rider Longitude:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_longitude }}" readonly>
            </div>

            <div class="col-md-4">
                <label>State:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->state->state_name_mm }}" readonly>
            </div>

        </div>
        <div class="row mb-3">
            <div class="col-md-1">
                <label>Active:</label><br>
                @if ($rider->active_inactive_status == 0)
                    <a class="btn btn-danger btn-sm mr-1" style="color: white;" title="HasOrder"><i class="fas fa-thumbs-down"></i></a>
                @else
                    <a class="btn btn-success btn-sm mr-1" style="color: white;" title="HasNotOrder"><i class="fas fa-thumbs-up"></i></a>
                @endif
            </div>
            <div class="col-md-1">
                <label>Free:</label><br>
                @if ($rider->is_order == 0)
                    <a class="btn btn-success btn-sm mr-1" style="color: white;" title="HasNotOrder"><i class="fas fa-thumbs-up"></i></a>
                @else
                    <a class="btn btn-danger btn-sm mr-1" style="color: white;" title="HasOrder"><i class="fas fa-thumbs-down"></i></a>
                @endif
            </div>
            <div class="col-md-1">
                <label>Approved:</label><br>
                @if ($rider->is_admin_approved == 0)
                    <a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>
                @else
                    <a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>
                @endif
            </div>
            <div class="col-md-1">
                <label>NotBan:</label><br>
                @if ($rider->is_ban == 0)
                    <a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Not Approved"></i></a>
                @else
                    <a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Approved"></i></a>
                @endif
            </div>
            <div class="col-md-1">
                <label>Level:</label><br>
                <a class="btn btn-info btn-sm mr-1" style="color: white;" title="HasOrder">{{ $rider->rider_level_id }}</a>
            </div>
            <div class="col-md-1">
                <label>MaxOrder:</label><br>
                <a class="btn btn-info btn-sm mr-1" style="color: white;" title="HasOrder">{{ $rider->max_order }}</a>
            </div>
            <div class="col-md-1">
                <label>ExistOrder:</label><br>
                <a class="btn btn-info btn-sm mr-1" style="color: white;" title="HasOrder">{{ $rider->exist_order }}</a>
            </div>
            <div class="col-md-1">
                <label>MaxDistance:</label><br>
                <a class="btn btn-info btn-sm mr-1" style="color: white;" title="HasOrder">{{ $rider->max_distance }} Km</a>
            </div>
        </div>
        <hr>
        <div class="row mb-3">
            <div class="col-md-6 mb-4">
                <form action="{{ url('fatty/main/admin/riders/detail',$rider_id) }}">
                    <input class="col-5 col-md-5" type="date" name="start_date" value="{{ \Carbon\Carbon::parse($date_start)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                    <input class="col-5 col-md-5" type="date" name="end_date" value="{{ \Carbon\Carbon::parse($date_end)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                    <button class="col-1 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <div class="col-md-6 mb-4">
                <form action="{{ url('fatty/main/admin/riders/detail/search',$rider_id) }}">
                    <input class="col-9 col-md-7" type="type" name="search_name" placeholder="Filter Enter Order Id OR Booking Id" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                    <input type="hidden" name="start_date" value="{{ $date_start }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                    <input type="hidden" name="end_date" value="{{ $date_end }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                    <button class="col-2 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-sliders"></i></button>
                </form>
            </div>
            <div class="mb-1">
                {{ $total_orders->appends(request()->input())->links() }}
            </div>
            <div class="table-responsive">
                <table id="orders" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr class="text-center">
                            <th>No.</th>
                            <th>OrderStatus</th>
                            <th>CustomerType</th>
                            <th>OrderType</th>
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
                            <th>PaymentType</th>
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
                                @elseif($item->order_status_id=='11')
                                    <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Pending(NotAcceptRider)</a>
                                @elseif($item->order_status_id=='12')
                                    <a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">AcceptByRider</a>
                                @elseif($item->order_status_id=='13')
                                    <a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ArrivedtoPickOrder</a>
                                @elseif($item->order_status_id=='17')
                                    <a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">RiderPickup</a>
                                @elseif($item->order_status_id=='14')
                                    <a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">StartDeliverybyRider </a>
                                @elseif($item->order_status_id=='15')
                                    <a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;;">AcceptCustomer</a>
                                @elseif($item->order_status_id=='16')
                                    <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">CustomerCancel</a>
                                @else
                                    <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:black">CheckError {{ $item->order_status_id }}</a>
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
                            <td>
                                @if($item->order_type=="food")
                                    <a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">{{ $item->order_type }}</a>
                                @elseif ($item->order_type=="parcel")
                                    <a class="btn btn-primary btn-sm mr-2" style="color: white;width: 100%;">{{ $item->order_type }}</a>
                                @else
                                    <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>
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
                                    <span class="btn btn-sm btn-primary" style="width: 100%;">{{ "Cash_On_Delivery" }}</span>
                                @elseif($item->payment_method_id==2)
                                    <span class="btn btn-sm btn-success" style="width: 100%;">{{ "Kpay" }}</span>
                                @else
                                    <span class="btn btn-sm btn-secondary" style="width: 100%;">{{ "Empty" }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->order_type=="food")
                                    <a href="/fatty/main/admin/food_orders/view/{{ $item->order_id }}" class="btn btn-info btn-sm mr-2" title="order detail"><i class="fas fa-eye"></i></a>
                                @else
                                    <a href="/fatty/main/admin/parcel_orders/view/{{ $item->order_id }}" class="btn btn-info btn-sm mr-2"><i class="fas fa-eye"></i></a>
                                @endif
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
            </div>
        </div>
    </div>
    <script>
        setTimeout(function() {
            $('#successMessage').fadeOut('fast');
        }, 2000);
    </script>
</body>
</html>


