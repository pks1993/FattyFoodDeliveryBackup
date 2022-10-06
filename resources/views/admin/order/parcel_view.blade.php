@extends('admin.layouts.master')

@section('card_header')
<div class="card-header">
    <h3 class="card-title pt-2 text-uppercase">Parcel Details</h3>
</div>
@endsection
@section('css')
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
    {{-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script> --}}

@endsection
@section('content')

<div class="row mb-3">
    <div class="col-md-12 mb-3">
        @if($parcel_order->order_status_id=='11')
            <a class="btn btn-danger btn-md mr-2" style="color: white;width: 100%;">Pending(NotAcceptRider)</a>
        @elseif($parcel_order->order_status_id=='12')
            <a class="btn btn-primary btn-md mr-2" style="color: white;width: 100%;">AcceptByRider</a>
        @elseif($parcel_order->order_status_id=='13')
            <a class="btn btn-info btn-md mr-2" style="color: white;width: 100%;background-color:orange;">ArrivedtoPickOrder</a>
        @elseif($parcel_order->order_status_id=='17')
            <a class="btn btn-info btn-md mr-2" style="color: white;width: 100%;background-color:orange;">RiderPickup</a>
        @elseif($parcel_order->order_status_id=='14')
            <a class="btn btn-info btn-md mr-2" style="color: white;width: 100%;background-color:orange;">StartDeliverybyRider </a>
        @elseif($parcel_order->order_status_id=='15')
            <a class="btn btn-success btn-md mr-2" style="color: white;width: 100%;background-color:orange;">AcceptCustomer</a>
        @elseif($parcel_order->order_status_id=='8')
            <a class="btn btn-md mr-2" style="color: white;width: 100%;background-color:orange;">PendingOrder</a>
        @elseif($parcel_order->order_status_id=='16')
            <a class="btn btn-md mr-2" style="color: white;width: 100%;background-color:orange;">CustomerCancel</a>
        @else
            <a class="btn btn-secondary btn-md mr-2" style="color: white;width: 100%;">CheckError</a>
        @endif
    </div>

    <div class="col-md-10">
        <label>Parcel images:</label><br>
        @if($parcel_order->parcel_images->count() !== 0)
            @foreach ($parcel_order->parcel_images as $parcel_image)
                <img src="../../../../../uploads/parcel/parcel_image/{{ $parcel_image->parcel_image }}" class="img-thumbnail" width="200" height="200">
            @endforeach
        @else
                <img src="../../../../../image/available.png" class="img-thumbnail" width="200" height="200">
        @endif
    </div>
    <div class="col-md-2" style="text-align: right" >
        <label for="date">
            Order Date:
        </label>
        <p>
            {{ date('D d M Y',strtotime($parcel_order->created_at)) }}
            <br>
            {{ $parcel_order->order_time }}
        </p>
    </div>
</div>

<div class="p-3">
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Estimated Start Time</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->estimated_start_time }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Estimated End Time:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->estimated_end_time }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Payment Method:</label>
            @if($parcel_order->payment_method_id==2)
                <a style="background-color: blue;color:white" class="btn btn-sm form-control form-control-sm" readonly>{{ $parcel_order->payment_method->payment_method_name }}</a>
            @elseif($parcel_order->payment_method_id==1)
                <a style="background-color: green;color:white" class="btn btn-sm form-control form-control-sm" readonly>{{ $parcel_order->payment_method->payment_method_name }}</a>
            @else
                <a style="background-color: red;color:white" class="btn btn-sm form-control form-control-sm" readonly>Error</a>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Customer Order Id:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->customer_order_id }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Customer Booking Id:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->customer_booking_id }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Parcel Type:</label>
            @if ($parcel_order->parcel_type_id)
                <input type="text" style="background-color: green;color:white" class="btn btn-sm form-control form-control-sm" value="{{ $parcel_order->parcel_type->parcel_type_name }}" readonly>
            @else
                <input type="text" style="background-color: red;color:white" class="btn btn-sm form-control form-control-sm" value="Error" readonly>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Sender Block Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->from_parcel_city_id }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Sender Phone:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->from_sender_phone }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Pickup Adress:</label>
            <textarea name="from_pickup_address" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $parcel_order->from_pickup_address }}</textarea>
        </div>

    </div>


    <div class="row mb-3">
        <div class="col-md-4">
            <label>Recipent Block Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->to_parcel_city_id }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Recipent Phone:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->to_recipent_phone }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Recipent Adress:</label>
            <textarea name="to_drop_addresee" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $parcel_order->to_drop_address }}</textarea>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Sender Pick Note:</label>
            <textarea name="from_pickup_note" style="height: 150px;" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $parcel_order->from_pickup_note }}</textarea>
        </div>
        <div class="col-md-4">
            <label>Recipent Drop Note:</label>
            <textarea name="to_drop_note" style="height: 150px;" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $parcel_order->to_drop_note }}</textarea>
        </div>
        <div class="col-md-4">
            <label>Order Remark:</label>
            <textarea name="order_description" style="height: 150px;" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $parcel_order->order_description }}</textarea>
        </div>

    </div>

    <div class="form-group mb-3">
        @if ($parcel_order->parcel_extra_cover_id != 0)
            <label>Need extra cover for loss/damage?:</label><br>
            <img src="../../../../../uploads/parcel/parcel_extra_cover/{{ $parcel_order->parcel_extra->parcel_extra_cover_image }}" class="img-thumbnail" width="200" height="200"><br>
            {{ $parcel_order->parcel_extra->parcel_extra_cover_price }} Ks
        @endif
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label>Bill Detail:</label><br>
            <table>
                @if ($parcel_order->parcel_extra_cover_id !=0)
                    <tr>
                        <td>Extra Fee</td>
                        <td></td>
                        <td></td>
                        <td>- ks.{{ $parcel_order->parcel_extra->parcel_extra_cover_price }}</td>
                    </tr>
                @endif
                <tr style="border-bottom: 1px solid #0000002d;">
                    <td>Delivery Fee</td>
                    <td></td>
                    <td></td>
                    <td>- ks.{{ $parcel_order->delivery_fee }}</td>
                </tr>
                <tr>
                    <td>Bill Total Price</td>
                    <td></td>
                    <td></td>
                    <td>- ks.{{ $parcel_order->bill_total_price }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <label for="location">Location</label>
            @if($rider_address_latitude==0 && $to_drop_latitude !=0)
                <iframe width="100%" height="400" frameborder="0" style="border:0" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed/v1/directions?key={{ env('GOOGLE_MAP_KEY') }}&origin={{ $parcel_order->from_pickup_latitude }},{{ $parcel_order->from_pickup_longitude }}&destination={{ $parcel_order->to_drop_latitude }},{{ $parcel_order->to_drop_longitude }}&avoid=tolls|highways" allowfullscreen></iframe>
            @elseif($rider_address_latitude==0 && $to_drop_latitude ==0)
                <iframe width="100%" height="400" frameborder="0" style="border:0" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed/v1/directions?key={{ env('GOOGLE_MAP_KEY') }}&origin={{ $parcel_order->from_pickup_latitude }},{{ $parcel_order->from_pickup_longitude }}&avoid=tolls|highways" allowfullscreen></iframe>
            @elseif($rider_address_latitude !=0 && $to_drop_latitude ==0)
                <iframe width="100%" height="400" frameborder="0" style="border:0" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed/v1/directions?key={{ env('GOOGLE_MAP_KEY') }}&origin={{ $parcel_order->from_pickup_latitude }},{{ $parcel_order->from_pickup_longitude }}&destination={{ $parcel_order->rider_address_latitude }},{{ $parcel_order->rider_address_longitude }}&avoid=tolls|highways" allowfullscreen></iframe>
            @else
                <iframe width="100%" height="400" frameborder="0" style="border:0" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed/v1/directions?key={{ env('GOOGLE_MAP_KEY') }}&origin={{ $parcel_order->from_pickup_latitude }},{{ $parcel_order->from_pickup_longitude }}&destination={{ $parcel_order->to_drop_latitude }},{{ $parcel_order->to_drop_longitude }}&waypoints={{ $parcel_order->rider_address_latitude }},{{ $parcel_order->rider_address_longitude }}&avoid=tolls|highways" allowfullscreen></iframe>
            @endif
            <label for="location">Location</label>
            <div id="mapCanvas" style="width: 100%;height:400px;"></div>
        </div>
    </div>

    <div class="row mb-5">
        @if (url()->previous() == url()->current())
            <a href="{{ url('fatty/main/admin/daily_parcel_orders/list') }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;width: 100px;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
        @else
            <a href="{{ $previous_url }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;width: 100px;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
        @endif
    </div>


    <script>
        function initialize() {
            var from_pickup_latitude=parseFloat("{{ $from_pickup_latitude }}");
            var from_pickup_longitude=parseFloat("{{ $from_pickup_longitude }}");
            var to_drop_latitude=parseFloat("{{ $to_drop_latitude }}");
            var to_drop_longitude=parseFloat("{{ $to_drop_longitude }}");
            var rider_address_latitude=parseFloat("{{ $rider_address_latitude }}");
            var rider_address_longitude=parseFloat("{{ $rider_address_longitude }}");
            var rider_name=("{{ $rider_name }}")+" ( Rider )";
            var lat_lng = new Array();
            var myOptions = {
                center: new google.maps.LatLng(from_pickup_latitude,from_pickup_longitude),
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



            var from_pickup = new google.maps.LatLng(from_pickup_latitude, from_pickup_longitude);
            lat_lng.push(from_pickup);

            var to_drop = new google.maps.LatLng(to_drop_latitude, to_drop_longitude);
            lat_lng.push(to_drop);

            var rider = new google.maps.LatLng(rider_address_latitude, rider_address_longitude);
            lat_lng.push(rider);
            if(to_drop_latitude !=0){
                var image="http://maps.google.com/mapfiles/ms/icons/yellow-dot.png"
                var marker = new google.maps.Marker({
                        position: to_drop,
                        map: map,
                        icon:{
                            url: image,
                            labelOrigin: new google.maps.Point(10, -10)
                        },
                        title: 'Drop Address',
                        label: {
                            text: 'Drop Address',
                            color: '#fff',
                            fontSize: '14px',
                            fontWeight: 'bold',
                            className: "map-label"
                        }
                    });
            }
            if(from_pickup_latitude !=0){
                var image1="http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                var marker = new google.maps.Marker({
                        position: from_pickup,
                        map: map,
                        icon:{
                            url: image1,
                            labelOrigin: new google.maps.Point(10, -10)
                        },
                        title:'Pickup Address',
                        label: {
                            text: 'Pickup Address',
                            color: '#fff',
                            fontSize: '14px',
                            fontWeight: 'bold',
                            className: "map-label"
                        }
                    });
            }

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
            if (from_pickup_latitude != 0) {
                bounds.extend(from_pickup);
            }
            if (to_drop_latitude != 0) {
                bounds.extend(to_drop);
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
