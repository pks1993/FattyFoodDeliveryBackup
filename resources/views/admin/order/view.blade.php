@extends('admin.layouts.master')
@section('css')
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
    {{-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script> --}}

@endsection

@section('card_header')
<div class="card-header">
    <h3 class="card-title pt-2 text-uppercase">Food Order Detail</h3>
</div>
@endsection
@section('content')

<div class="p-3">

    <div class="row mb-3">
        <div class="col-md-12 mb-3">
            @if($food_order->order_status_id=='1')
                    <a class="btn btn-warning btn-md mr-2" style="color: white;width: 100%;">Pending(NotAcceptShop)</a>
                @elseif($food_order->order_status_id=='2')
                    <a class="btn btn-danger btn-md mr-2" style="color: white;width: 100%;">CancelByShop</a>
                @elseif($food_order->order_status_id=='9')
                    <a class="btn btn-danger btn-md mr-2" style="color: white;width: 100%;">CancelByCustomer</a>
                @elseif($food_order->order_status_id=='3')
                    <a class="btn btn-success btn-md mr-2" style="color: white;width: 100%;">AcceptByShop</a>
                @elseif($food_order->order_status_id=='4')
                    <a class="btn btn-success btn-md mr-2" style="color: white;width: 100%;">AcceptByRider</a>
                @elseif($food_order->order_status_id=='5')
                    <a class="btn btn-md mr-2" style="color: white;width: 100%;background-color:orange;">ReadyToPickup </a>
                @elseif($food_order->order_status_id=='7')
                    <a class="btn btn-secondary btn-md mr-2" style="color: white;width: 100%;">AcceptCustomer</a>
                @elseif($food_order->order_status_id=='8')
                    <a class="btn btn-md mr-2" style="color: white;width: 100%;background-color:red;">PendingOrder(CustomerNotFound)</a>
                @elseif($food_order->order_status_id=='6')
                    <a class="btn btn-md mr-2" style="color: white;width: 100%;background-color:orange;">StartDelivery</a>
                @elseif($food_order->order_status_id=='10')
                    <a class="btn btn-info btn-md mr-2" style="color: white;width: 100%;">RiderArrivedShop</a>
                @elseif($food_order->order_status_id=='18')
                    <a class="btn btn-md mr-2" style="color: white;width: 100%;background-color:yellow;">KBZ Pending</a>
                @elseif($food_order->order_status_id=='19')
                    <a class="btn btn-md mr-2" style="color: white;width: 100%;background-color:green;">KBZ Success</a>
                @elseif($food_order->order_status_id=='20')
                    <a class="btn btn-md mr-2" style="color: white;width: 100%;background-color:red;">KBZ Fail</a>
                @else
                    <a class="btn btn-md mr-2" style="color: white;width: 100%;background-color:black">CheckError</a>
                @endif
        </div>
        <div class="col-md-6">
            <label>Restaurant:</label><br>
            @if($food_order->restaurant_id)
                <img src="../../../../../uploads/restaurant/{{ $food_order->restaurant->restaurant_image }}" class="img-thumbnail" width="100" height="100">
            @else
                <img src="../../../../../image/available.png" class="img-thumbnail" width="100" height="100">
            @endif
        </div>
        <div class="col-md-6" style="text-align: right" >
            <label for="date">
                Order Date:
            </label>
            <p>
                {{ date('D d M Y',strtotime($food_order->created_at)) }}
                <br>
                {{ $food_order->order_time }}
            </p>

        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Estimated Start Time :</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->estimated_start_time }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Estimated End Time :</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->estimated_end_time }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Payment Method:</label>
            @if($food_order->payment_method_id==2)
                <a style="background-color: blue;color:white" class="btn btn-sm form-control form-control-sm" readonly>{{ $food_order->payment_method->payment_method_name }}</a>
            @elseif($food_order->payment_method_id==1)
                <a style="background-color: green;color:white" class="btn btn-sm form-control form-control-sm" readonly>{{ $food_order->payment_method->payment_method_name }}</a>
            @else
                <a style="background-color: red;color:white" class="btn btn-sm form-control form-control-sm" readonly>Error</a>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Merch Order Id:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->merch_order_id }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Customer Order Id:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->customer_order_id }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Booking Order Id:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->customer_booking_id }}" readonly>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Restaurant Name:</label>
            <textarea name="restaurant_name" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->restaurant->restaurant_name_mm }}</textarea>
        </div>

        <div class="col-md-4">
            <label>Restaurant Address:</label>
            <textarea name="restaurant_address" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->restaurant->restaurant_address }}</textarea>
        </div>
        <div class="col-md-4">
            <label style="color: red">Restaurant Reject Remark:</label>
            <textarea name="description" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->restaurant_remark }}</textarea>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Customer Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->customer->customer_name }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Customer Address Phone:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->customer_address_phone }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Customer Current Adress:</label>
            @if ($food_order->customer_address_id==0)
                <textarea name="current_address" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->current_address }}</textarea>
            @else
                <textarea name="current_address" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->customer_address->current_address }}</textarea>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Rider Name:</label>
            @if ($food_order->rider_id)
                <input type="text" class="form-control form-control-sm" value="{{ $food_order->rider->rider_user_name }}" readonly>
            @else
                <input type="text" class="form-control form-control-sm" value="" readonly>
            @endif
        </div>
        <div class="col-md-4">
            <label>Rider Delivery Fee:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->rider_delivery_fee }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Order Remark:</label>
            <textarea name="description" style="height: 100px;" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->order_description }}</textarea>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-12">
            <label>Bill Detail:</label><br>
            <table>
                @foreach ($food_order->foods as $food)
                <tr>
                    <td>{{ $food->food_qty }} x {{ $food->food_name_mm }}
                        (@foreach ($food->sub_item as $sub_item)
                            @foreach ($sub_item->option as $option)
                            {{ $option->item_name_mm }},
                            @endforeach
                        @endforeach)
                    </td>
                    <td></td>
                    <td></td>
                    <td>- Ks.{{ $food->food_price }}</td>
                </tr>
                @endforeach
                <tr style="border-bottom: 1px solid #0000002d;">
                    <td>Delivery Fee</td>
                    <td></td>
                    <td></td>
                    <td>- ks.{{ $food_order->delivery_fee }}</td>
                </tr>
                <tr>
                    <td>Bill Total Price</td>
                    <td></td>
                    <td></td>
                    <td>- ks.{{ $food_order->bill_total_price }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <label for="location">Location</label>
            @if($rider_address_latitude==0)
                <iframe width="100%" height="400" frameborder="0" style="border:0" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed/v1/directions?key={{ env('GOOGLE_MAP_KEY') }}&origin={{ $food_order->customer_address_latitude }},{{ $food_order->customer_address_longitude }}&destination={{ $food_order->restaurant_address_latitude }},{{ $food_order->restaurant_address_longitude }}&avoid=tolls|highways" allowfullscreen></iframe>
            @else
                <iframe width="100%" height="400" frameborder="0" style="border:0" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed/v1/directions?key={{ env('GOOGLE_MAP_KEY') }}&origin={{ $food_order->customer_address_latitude }},{{ $food_order->customer_address_longitude }}&destination={{ $food_order->restaurant_address_latitude }},{{ $food_order->restaurant_address_longitude }}&waypoints={{ $food_order->rider_address_latitude }},{{ $food_order->rider_address_longitude }}&avoid=tolls|highways" allowfullscreen></iframe>
            @endif
            <label for="location">Location</label>
            <div id="mapCanvas" style="width: 100%;height:400px;"></div>
        </div>
    </div>

    <div class="row mb-5">
        @if (url()->previous() == url()->current())
            <a href="{{ url('fatty/main/admin/daily_food_orders/list') }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;width: 100px;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
        @else
            <a href="{{ $previous_url }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;width: 100px;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
        @endif
    </div>

    <script>
        function initialize() {
            var customer_address_latitude=parseFloat("{{ $customer_address_latitude }}");
            var customer_address_longitude=parseFloat("{{ $customer_address_longitude }}");
            var restaurant_address_latitude=parseFloat("{{ $restaurant_address_latitude }}");
            var restaurant_address_longitude=parseFloat("{{ $restaurant_address_longitude }}");
            var rider_address_latitude=parseFloat("{{ $rider_address_latitude }}");
            var rider_address_longitude=parseFloat("{{ $rider_address_longitude }}");
            var res_name=("{{ $res_name }}")+" ( Restaurant )";
            var cus_name=("{{ $cus_name }}")+" ( User )";
            var rider_name=("{{ $rider_name }}")+" ( Rider )";
            var lat_lng = new Array();
            var myOptions = {
                center: new google.maps.LatLng(customer_address_latitude,customer_address_longitude),
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.ROADMAP,

                panControl: true,
                mapTypeControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_CENTER
                },
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.RIGHT_CENTER
                },
                scaleControl: false,
                streetViewControl: false,
                streetViewControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_CENTER
                }
            };
            var map = new google.maps.Map(document.getElementById("mapCanvas"), myOptions);



            var customer = new google.maps.LatLng(customer_address_latitude, customer_address_longitude);
            lat_lng.push(customer);

            var restaurant = new google.maps.LatLng(restaurant_address_latitude, restaurant_address_longitude);
            lat_lng.push(restaurant);

            var rider = new google.maps.LatLng(rider_address_latitude, rider_address_longitude);
            lat_lng.push(rider);

            var image="http://maps.google.com/mapfiles/ms/icons/yellow-dot.png"
            var marker = new google.maps.Marker({
                    position: restaurant,
                    map: map,
                    icon:{
                        url: image,
                        labelOrigin: new google.maps.Point(10, -10)
                    },
                    title:res_name,
                    label: {
                        text: res_name,
                        color: '#fff',
                        fontSize: '14px',
                        fontWeight: 'bold',
                        className: "map-label"
                    }
                });
            var image1="http://maps.google.com/mapfiles/ms/icons/red-dot.png"
            var marker = new google.maps.Marker({
                    position: customer,
                    map: map,
                    icon:{
                        url: image1,
                        labelOrigin: new google.maps.Point(10, -10)
                    },
                    title:cus_name,
                    label: {
                        text: cus_name,
                        color: '#fff',
                        fontSize: '14px',
                        fontWeight: 'bold',
                        className: "map-label"
                    }
                });

                if(rider_address_latitude !=0){
                    var image2="http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                    var marker = new google.maps.Marker({
                        position: rider,
                        map: map,
                        icon:{
                            url: image2,
                            labelOrigin: new google.maps.Point(10, -10)
                        },
                        title:rider_name,
                        label: {
                            text: rider_name,
                            color: '#fff',
                            fontSize: '14px',
                            fontWeight: 'bold',
                            className: "map-label"
                        }
                    });
                }

            var bounds = new google.maps.LatLngBounds();
            if (rider_address_latitude != 0) {
                bounds.extend(rider);
            }
            if (customer_address_latitude != 0) {
                bounds.extend(customer);
            }
            if (restaurant_address_latitude != 0) {
                bounds.extend(restaurant);
            }
            map.fitBounds(bounds);

            //***********ROUTING****************//

            // //Intialize the Path Array
            // var path = new google.maps.MVCArray();

            // //Intialize the Direction Service
            // var service = new google.maps.DirectionsService();

            // //Set the Path Stroke Color
            // var poly = new google.maps.Polyline({ map: map, strokeColor: '#4986E7' });

            // if (rider_address_latitude == 0) {
            //     //Loop and Draw Path Route between the Points on MAP
            //     for (var i = 0; i < lat_lng.length; i++) {
            //         if ((i + 1) < lat_lng.length) {
            //             var src = lat_lng[i];
            //             var des = lat_lng[i + 1];
            //             path.push(src);
            //             poly.setPath(path);
            //             service.route({
            //                 origin: src,
            //                 destination: des,
            //                 travelMode: google.maps.DirectionsTravelMode.DRIVING
            //             }, function (result, status) {
            //                 if (status == google.maps.DirectionsStatus.OK) {
            //                     for (var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
            //                         path.push(result.routes[0].overview_path[i]);
            //                     }
            //                 }
            //             });
            //         }
            //     }
            // }else{
            //     //Loop and Draw Path Route between the Points on MAP
            //     for (var i = 0; i < lat_lng.length; i++) {
            //         if ((i + 1) <= lat_lng.length) {
            //             var src = lat_lng[i];
            //             var des = lat_lng[i + 1];
            //             path.push(src);
            //             poly.setPath(path);
            //             service.route({
            //                 origin: src,
            //                 destination: des,
            //                 travelMode: google.maps.DirectionsTravelMode.DRIVING
            //             }, function (result, status) {
            //                 if (status == google.maps.DirectionsStatus.OK) {
            //                     for (var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
            //                         path.push(result.routes[0].overview_path[i]);
            //                     }
            //                 }
            //             });
            //         }
            //     }
            // }

        }
        initialize();
    </script>
</div>
@endsection
