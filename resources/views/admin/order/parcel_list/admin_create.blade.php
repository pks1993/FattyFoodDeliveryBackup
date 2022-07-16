<!DOCTYPE html>
<html lang="en">
<head>
    <title>Fatty Food Delivery</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/admin.css')}}">


    <style>
        .p-1 {
        28a745padding: 0.25rem!important;
        }
        .rounded {
        border-radius: 0.25rem!important;
        }
        .border-success {
        border-color: #28a745!important;
        }
        /* .border {
        border: 1px solid #28a745!important;
        } */
        .border2 {
        border-color: #007bff!important;
        }
        .border-primary {
        border: 1px solid #007bff!important;
        }
        col-12 {
        -ms-flex: 0 0 100%;
        flex: 0 0 100%;
        max-width: 100%;
        }
        /* .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff;
            line-height: 35px;
            text-align: center;
            background-color: #28a745;
            height: 35px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #ffffff transparent transparent transparent;
            border-style: solid;
            border-width: 5px 4px 0 4px;
            height: 0;
            left: 50%;
            margin-left: -4px;
            margin-top: -2px;
            position: absolute;
            top: 70%;
            width: 0;
        } */
        .nav>li>a:focus, .nav>li>a:hover {
            text-decoration: none;
            background-color: #343a40;
            color: #fff;
        }
        .nav-pills li>#list-tab.active,.nav-pills li>#offered-tab.active,.nav-pills li>#home-tab.active, .nav-pills .show>.nav-link {
            background-color: #28a745 !important;
            color: #FFFFFF !important;
        }
        .tab-pane{
            display : none;
        }
        .tab-pane.active{
            display : block;
        }
        .btn-rider {
            color: #fff;
            background-color: #6c757d;
            box-shadow: none;
        }

    </style>
</head>
<body style="width:100%">
{{-- <ul class="nav nav-pills navbar-inverse" style="background-color: #343a40;"> --}}
<div class="container-fluid" id="from_region" style="display: none">
    <div class="row p-1">
        <div class="form-group col-12">
            <a class="btn btn-block text-white p-1" style="background-color: #28a745;color:white;font-size:20px;text-align:left" onclick="from_region()"><i class="fa fa-angle-left float-left mt-1 mr-2"></i> From</a>
            {{-- <div id="from_region" style="display: block"> --}}
                @foreach ($from_cities as $item)
                    <a class="btn btn-block btn-secondary text-white p-2 mt-2" style="font-size: 17px;text-align:left" onclick="getFromRegion1(`{{$item->parcel_block_id}}`,`{{$item->block_name}}`,`{{ $item->latitude }}`,`{{ $item->longitude }}`),calculatePrice();"> {{ $item->block_name }} </a>
                @endforeach
            {{-- </div> --}}
        </div>
    </div>
</div>
<div class="container-fluid" id="to_region" style="display: none">
    <div class="row p-1">
        <div class="form-group col-12">
            <a class="btn btn-block text-white p-1" style="background-color: #007bff;color:white;font-size:20px;text-align:left" onclick="to_region()"><i class="fa fa-angle-left float-left mt-1 mr-2"></i> To</a>
            {{-- <div id="to_region" style="display: block"> --}}
                @foreach ($to_cities as $item)
                    <a class="btn btn-block btn-secondary text-white p-2 mt-2" style="font-size: 17px;text-align:left" onclick="getToRegion1(`{{$item->parcel_block_id}}`,`{{$item->block_name}}`,`{{ $item->latitude }}`,`{{ $item->longitude }}`),calculatePrice();"> {{ $item->block_name }} </a>
                @endforeach
            {{-- </div> --}}
        </div>
    </div>
</div>
<div class="container-fluid" id="driver" style="display: none">
    <div class="row p-1">
        <div class="form-group col-12">
            <a class="btn btn-block text-white p-1" style="background-color: #007bff;color:white;font-size:17px;text-align:left" onclick="showDriver()"><i class="fa fa-angle-left float-left mt-1 mr-2"></i> Driver</a>
            {{-- <div id="driver" style="display: block"> --}}
                @foreach ($riders as $item)
                    <a class="btn btn-block btn-rider text-white p-2 mt-2" style="background-color:rgb(240, 240, 240);color:black !important;font-size: 17px;text-align:left" onclick="getRider(`{{$item->rider_id}}`,`{{$item->rider_user_name}}`)">
                        <div class="row">
                            <div class="col-6 text-left">
                                {{ $item->rider_user_name }} <sup><span class="text-danger font-weight-bold">
                                    <?php
                                    $count=DB::select("select * from customer_orders where rider_id='$item->rider_id' and Date(created_at)=Date(now())");
                                    echo count($count);
                                    ?>
                                </span></sup>
                            </div>
                            <div class="col-6 text-right">
                                <i class="fa fa-dot-circle text-success"></i>Test Block
                            </div>
                        </div>
                    </a>
                @endforeach
            {{-- </div> --}}
        </div>
    </div>
</div>
<div id="order_form" style="display: block">
    <ul class="nav nav-pills navbar-inverse" style="background-color: #343a40;">
        <li class="nav-item">
            <a class="nav-link" style="background-color:#28a745;width: 100%;color: #FFF;font-size:15px;font-weight:510;" id="home-tab" data-toggle="pill" href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
        </li>
        <li class="nav-item">
            <a href="{{ url('admin_parcel_orders/list/'.$customer_admin_id) }}" class="nav-link" style="width: 100%;color: #FFF;font-size:15px;font-weight:510;">Order</a>
        </li>
        <li class="nav-item">
            <a href="{{ url("admin_parcel_orders/report/".$customer_admin_id) }}" style="width: 100%;color: #FFF;font-size:15px;font-weight:510;">RiderReport</a>
        </li>
        <li class="nav-item">
            <a href="{{ url("admin_parcel_orders/logout_check") }}" style="width: 100%;color: #FFF;font-size:15px;font-weight:510;">Logout</a>
        </li>
    </ul>
    <div class="col-12 mt-3" style="font-size: 15px;">
        <div class="flash-message" id="successMessage">
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                @endif
            @endforeach
        </div>
    </div>

    <form action="{{ route('admin_parcel.store') }}" method="post" autocomplete="off" enctype="multipart/form-data" style="margin: 5px;">
    {{-- <form action="" method="post" autocomplete="off" enctype="multipart/form-data" style="margin: 5px;"> --}}
        @csrf
        <div class="container-fluid">
            <div class="row p-1">
                <div class="form-group col-12 p-1 border border-success rounded">
                    {{-- <div class="card" style="padding:0px;"> --}}
                        <div class="form-group col-12">
                            <i class="fa fa-user" style="font-size:17px;"> <span style="font-size: 14px;">{{ $customer->customer_name }}</span></i>
                            <p style="font-weight: 600;font-size:17px;">ORDER NO # <span>{{ $customer_order_count }}</span></p>
                            <input type="hidden" name="customer_id" value="{{ $customer_admin_id }}">
                            <input type="hidden" name="rider_restaurant_distance" id="rider_restaurant_distance">
                            {{-- <select id="from_parcel_city_id" style="width: 100%;" class="form-control @error('from_parcel_city_id') is-invalid @enderror" name="from_parcel_city_id" value="{{ old('from_parcel_city_id') }}" autocomplete="from_parcel_city_id" autofocus onchange="calDistance()">
                                <option value="">From</option>
                                @foreach($from_cities as $value)
                                    <option value="{{ $value->parcel_city_id }},{{ $value->latitude }},{{ $value->longitude }}">{{ $value->city_name_mm }}/{{ $value->city_name_en }}</option>
                                @endforeach
                            </select>
                            @error('from_parcel_city_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror --}}
                            <input class="btn btn-block text-white p-2" id="fromRegion" value="From"  style="background-color: #28a745;color:white;font-size:17px;" onclick="from_region()">
                            <input type="hidden" class="btn btn-block" name="from_lat" id="from_lat" value="0">
                            <input type="hidden" class="btn btn-block" name="from_lon" id="from_lon" value="0">
                            <input type="hidden" class="btn btn-block" name="from_parcel_city_id" id="from_parcel_city_id" value="0">
                        </div>
                        <div class="form-group col-12">
                            <input id="from_sender_phone" type="text" style="font-size: 15px;height:40px;" class="form-control @error('from_sender_phone') is-invalid @enderror" name="from_sender_phone" autocomplete="category_image" autofocus placeholder="From Phone">
                            @error('from_sender_phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <textarea id="from_pickup_note" style="font-size: 15px;" class="form-control @error('from_pickup_note') is-invalid @enderror" style="height:70px;" name="from_pickup_note" value="{{ old('from_pickup_note') }}" autocomplete="category_image" autofocus placeholder="From Address"></textarea>
                            @error('from_pickup_note')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    {{-- </div> --}}
                </div>
                <div class="form-group col-12 p-1 border2 border-primary rounded2">
                    {{-- <div class="card" style="padding:5px;"> --}}
                        <div class="form-group col-12 mt-3">
                            {{-- <select id="to_parcel_city_id" style="width: 100%;" class="form-control @error('to_parcel_city_id') is-invalid @enderror" name="to_parcel_city_id" value="{{ old('to_parcel_city_id') }}" autocomplete="to_parcel_city_id" autofocus onchange="calDistance1()">
                                <option value="">To</option>
                                @foreach($to_cities as $value)
                                    <option value="{{ $value->parcel_city_id }},{{ $value->latitude }},{{ $value->longitude }}">{{ $value->city_name_mm }}/{{ $value->city_name_en }}</option>
                                @endforeach
                            </select>
                            @error('to_parcel_city_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror --}}
                            {{-- <a class="btn btn-block text-white p-2" id="toRegion" style="background-color: #007bff;color:white;font-size:17px;" onclick="to_region()">To</a> --}}
                            <input class="btn btn-block text-white p-2" name="to_parcel_city_id" id="toRegion" value="To"  style="background-color: #007bff;color:white;font-size:17px;" onclick="to_region()">
                            <input type="hidden" class="btn btn-block" name="to_lat" id="to_lat" value="0">
                            <input type="hidden" class="btn btn-block" name="to_lon" id="to_lon" value="0">
                            <input type="hidden" class="btn btn-block" name="to_parcel_city_id" id="to_parcel_city_id" value="0">
                        </div>
                        <div class="form-group col-12">
                            <input id="to_recipent_phone" style="font-size: 15px;height:40px;" type="text" class="form-control @error('to_recipent_phone') is-invalid @enderror" name="to_recipent_phone" autocomplete="category_image" autofocus placeholder="To Phone">
                            @error('to_recipent_phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <textarea id="to_drop_note" style="font-size: 15px;" class="form-control @error('to_drop_note') is-invalid @enderror" style="height:70px;" name="to_drop_note" value="{{ old('to_drop_note') }}" autocomplete="category_image" autofocus placeholder="To Address"></textarea>
                            @error('to_drop_note')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    {{-- </div> --}}
                </div>
                <div class="form-group col-12">
                    <input id="price" type="text" style="font-size: 15px;height:40px;" class="form-control @error('price') is-invalid @enderror" placeholder="Price" name="price" autocomplete="category_image" autofocus placeholder="From Phone">
                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-12">
                    {{-- <select class="rider_id" id="rider_id" style="width: 100%;" class="form-control @error('rider_id') is-invalid @enderror" name="rider_id" value="{{ old('rider_id') }}" autocomplete="rider_id" autofocus>
                        <option value="0"> All Rider</option>
                        @foreach($riders as $value)
                            <option value="{{ $value->rider_id }}"> {{ $value->rider_user_name }} ( @if($value->is_order)HasOrder @else Free @endif )</option>
                        @endforeach
                    </select>
                    @error('rider_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror --}}
                    {{-- <a class="btn btn-block border p-2" id="driverData" style="font-size:20px;text-align:center" onclick="showDriver()"> Driver</a> --}}
                    <input class="btn btn-block border p-2" id="driverData" value="Driver"  style="font-size:20px;text-align:center" onclick="showDriver()">
                    <input type="hidden" class="btn btn-block" name="rider_id" id="rider_id" value="0">
                </div>
                <div class="form-group col-12">
                    <textarea id="parcel_order_note" style="font-size: 15px;height:100px;" class="form-control @error('parcel_order_note') is-invalid @enderror" style="height:100px;" name="parcel_order_note" autocomplete="category_image" autofocus placeholder="Remark"></textarea>
                    @error('parcel_order_note')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-12">
                    <button type="submit" class="btn btn-sm btn-block" style="height: 35px;font-size: 15px;background-color:#0062cc;color:white">
                    {{ __('Upload') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>

    function getRider(id,name){
        var x = document.getElementById("driver");
        if (x.style.display === "none") {
            x.style.display = "block";
            document.getElementById("order_form").style.display = "none";
        } else {
            x.style.display = "none";
            document.getElementById("order_form").style.display = "block";
            document.getElementById("driverData").value = name;
            document.getElementById("rider_id").value = id;
        }
    }

    function getFromRegion1(id,name,lat,lon){
        var x = document.getElementById("from_region");
        if (x.style.display === "none") {
            x.style.display = "block";
            document.getElementById("order_form").style.display = "none";
        } else {
            x.style.display = "none";
            document.getElementById("order_form").style.display = "block";
            document.getElementById("fromRegion").value = name;
            document.getElementById("from_lat").value = lat;
            document.getElementById("from_lon").value = lon;
            document.getElementById("from_parcel_city_id").value = id;
        }
        var lat1=document.getElementById("from_lat").value;
        var lon1=document.getElementById("from_lon").value;
        var lat2=document.getElementById("to_lat").value;;
        var lon2=document.getElementById("to_lon").value;;
        var distances=(calcCrow(lat1,lon1,lat2,lon2).toFixed(1));
            if(distances <= 2) {
                var customer_delivery_fee=1200;
            }else if(distances > 2 && distances < 3.5){
                var customer_delivery_fee=1500;
            }else if(distances == 3.5){
                var customer_delivery_fee=1800;
            }else if(distances > 3.5 && distances < 4.5){
                var customer_delivery_fee=2100;
            }else if(distances == 4.5){
                   var customer_delivery_fee=2400;
            }else if(distances > 4.5 && distances < 6){
                   var customer_delivery_fee=2700;
            }else if(distances == 6){
                   var customer_delivery_fee=3000;
            }else if(distances > 6 && distances < 7.5){
                   var customer_delivery_fee=3300;
            }else if(distances==7.5){
                   var customer_delivery_fee=3600;
            }else if(distances > 7.5 && distances < 9){
                   var customer_delivery_fee=3900;
            }else if(distances==9){
                   var customer_delivery_fee=4200;
            }else if(distances > 9 && distances < 10.5){
                   var customer_delivery_fee=4500;
            }else if(distances==10.5){
                   var customer_delivery_fee=4800;
            }else if(distances > 10.5 && distances < 12){
                   var customer_delivery_fee=5100;
            }else if(distances==12){
                   var customer_delivery_fee=5400;
            }else if(distances > 12 && distances < 13.5){
                   var customer_delivery_fee=5700;
            }else if(distances==13.5){
                   var customer_delivery_fee=6000;
            }else if(distances > 13.5 && distances < 15){
                   var customer_delivery_fee=6300;
            }else if(distances==15){
                   var customer_delivery_fee=6600;
            }else if(distances > 15 && distances < 16.5){
                   var customer_delivery_fee=6700;
            }else if(distances==16.5){
                   var customer_delivery_fee=7000;
            }else if(distances > 16.5 && distances < 18){
                   var customer_delivery_fee=7300;
            }else if(distances==18){
                   var customer_delivery_fee=7500;
            }else if(distances > 18 && distances < 19.5){
                var customer_delivery_fee=7800;
            }else if(distances >= 19.5){
                var customer_delivery_fee=8100;
            }else{
                var customer_delivery_fee=8100;
            }
        // document.getElementById('price').value=customer_delivery_fee;
        document.getElementById('rider_restaurant_distance').value=distances;
        function calcCrow(lat1, lon1, lat2, lon2)
        {
        var R = 6371; // km
        var dLat = toRad(lat2-lat1);
        var dLon = toRad(lon2-lon1);
        var lat1 = toRad(lat1);
        var lat2 = toRad(lat2);

        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        var d = R * c;
        return d;
        }

        // Converts numeric degrees to radians
        function toRad(Value)
        {
            return Value * Math.PI / 180;
        }
    }

    function calculatePrice(){
		var fromRegion = document.getElementById('from_parcel_city_id').value;
		var toRegion = document.getElementById('to_parcel_city_id').value;

		var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", "/admin_parcel_orders/calculate/price/"+fromRegion+"/"+toRegion, true);
        xmlhttp.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {

                document.getElementById("price").value = this.responseText;

            }
        }
        xmlhttp.send();
	}

    function getToRegion1(id,name,lat,lon){
        var x = document.getElementById("to_region");
        if (x.style.display === "none") {
            x.style.display = "block";
            document.getElementById("order_form").style.display = "none";
        } else {
            x.style.display = "none";
            document.getElementById("order_form").style.display = "block";
            document.getElementById("toRegion").value = name;
            document.getElementById("to_lat").value = lat;
            document.getElementById("to_lon").value = lon;
            document.getElementById("to_parcel_city_id").value = id;
        }


        var lat1=document.getElementById("from_lat").value;
        var lon1=document.getElementById("from_lon").value;
        var lat2=document.getElementById("to_lat").value;;
        var lon2=document.getElementById("to_lon").value;;
        var distances=(calcCrow(lat1,lon1,lat2,lon2).toFixed(1));
            if(distances <= 2) {
                var customer_delivery_fee=1200;
            }else if(distances > 2 && distances < 3.5){
                var customer_delivery_fee=1500;
            }else if(distances == 3.5){
                var customer_delivery_fee=1800;
            }else if(distances > 3.5 && distances < 4.5){
                var customer_delivery_fee=2100;
            }else if(distances == 4.5){
                   var customer_delivery_fee=2400;
            }else if(distances > 4.5 && distances < 6){
                   var customer_delivery_fee=2700;
            }else if(distances == 6){
                   var customer_delivery_fee=3000;
            }else if(distances > 6 && distances < 7.5){
                   var customer_delivery_fee=3300;
            }else if(distances==7.5){
                   var customer_delivery_fee=3600;
            }else if(distances > 7.5 && distances < 9){
                   var customer_delivery_fee=3900;
            }else if(distances==9){
                   var customer_delivery_fee=4200;
            }else if(distances > 9 && distances < 10.5){
                   var customer_delivery_fee=4500;
            }else if(distances==10.5){
                   var customer_delivery_fee=4800;
            }else if(distances > 10.5 && distances < 12){
                   var customer_delivery_fee=5100;
            }else if(distances==12){
                   var customer_delivery_fee=5400;
            }else if(distances > 12 && distances < 13.5){
                   var customer_delivery_fee=5700;
            }else if(distances==13.5){
                   var customer_delivery_fee=6000;
            }else if(distances > 13.5 && distances < 15){
                   var customer_delivery_fee=6300;
            }else if(distances==15){
                   var customer_delivery_fee=6600;
            }else if(distances > 15 && distances < 16.5){
                   var customer_delivery_fee=6700;
            }else if(distances==16.5){
                   var customer_delivery_fee=7000;
            }else if(distances > 16.5 && distances < 18){
                   var customer_delivery_fee=7300;
            }else if(distances==18){
                   var customer_delivery_fee=7500;
            }else if(distances > 18 && distances < 19.5){
                var customer_delivery_fee=7800;
            }else if(distances >= 19.5){
                var customer_delivery_fee=8100;
            }else{
                var customer_delivery_fee=8100;
            }
        // document.getElementById('price').value=customer_delivery_fee;
        // document.getElementById('from_parcel_city_id').value=data[0];
        // document.getElementById('to_parcel_city_id').value=data[1];
        document.getElementById('rider_restaurant_distance').value=distances;
        function calcCrow(lat1, lon1, lat2, lon2)
        {
        var R = 6371; // km
        var dLat = toRad(lat2-lat1);
        var dLon = toRad(lon2-lon1);
        var lat1 = toRad(lat1);
        var lat2 = toRad(lat2);

        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        var d = R * c;
        return d;
        }

        // Converts numeric degrees to radians
        function toRad(Value)
        {
            return Value * Math.PI / 180;
        }
    }


    function cal_rider(){
        var x = document.getElementById("driver");
        if (x.style.display === "none") {
            x.style.display = "block";
            document.getElementById("order_form").style.display = "none";
        } else {
            x.style.display = "none";
            document.getElementById("order_form").style.display = "block";
        }
    }
    function getFromRegion(){
        var x = document.getElementById("driver");
        if (x.style.display === "none") {
            x.style.display = "block";
            document.getElementById("order_form").style.display = "none";
        } else {
            x.style.display = "none";
            document.getElementById("order_form").style.display = "block";
        }
    }

    function showDriver(){
      var x = document.getElementById("driver");
      if (x.style.display === "none") {
        x.style.display = "block";
        document.getElementById("order_form").style.display = "none";
      } else {
        x.style.display = "none";
        document.getElementById("order_form").style.display = "block";
      }
    }
    function from_region(){
      var x = document.getElementById("from_region");
      if (x.style.display === "none") {
        x.style.display = "block";
        document.getElementById("order_form").style.display = "none";
      } else {
        x.style.display = "none";
        document.getElementById("order_form").style.display = "block";
      }
    }
    function to_region(){
      var x = document.getElementById("to_region");
      if (x.style.display === "none") {
        x.style.display = "block";
        document.getElementById("order_form").style.display = "none";
      } else {
        x.style.display = "none";
        document.getElementById("order_form").style.display = "block";
      }
    }

    // function getFromRegion(this){
    //     var aa=this.value;
    //     console.log(aa);

    // }
    </script>
{{-- <script>
    $(document).ready(function () {
        //select2
        $('#from_parcel_city_id').select2();
        $('#to_parcel_city_id').select2();
        $('#rider_id').select2();
    });
</script> --}}
<script>
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
<script>


    function calDistance(){
        var lat1=document.getElementById("from_lat").value;
        var lon1=document.getElementById("from_lon").value;
        var lat2=document.getElementById("to_lat").value;;
        var lon2=document.getElementById("to_lon").value;;
        var distances=(calcCrow(lat1,lon1,lat2,lon2).toFixed(1));
            if(distances <= 2) {
                var customer_delivery_fee=1200;
            }else if(distances > 2 && distances < 3.5){
                var customer_delivery_fee=1500;
            }else if(distances == 3.5){
                var customer_delivery_fee=1800;
            }else if(distances > 3.5 && distances < 4.5){
                var customer_delivery_fee=2100;
            }else if(distances == 4.5){
                   var customer_delivery_fee=2400;
            }else if(distances > 4.5 && distances < 6){
                   var customer_delivery_fee=2700;
            }else if(distances == 6){
                   var customer_delivery_fee=3000;
            }else if(distances > 6 && distances < 7.5){
                   var customer_delivery_fee=3300;
            }else if(distances==7.5){
                   var customer_delivery_fee=3600;
            }else if(distances > 7.5 && distances < 9){
                   var customer_delivery_fee=3900;
            }else if(distances==9){
                   var customer_delivery_fee=4200;
            }else if(distances > 9 && distances < 10.5){
                   var customer_delivery_fee=4500;
            }else if(distances==10.5){
                   var customer_delivery_fee=4800;
            }else if(distances > 10.5 && distances < 12){
                   var customer_delivery_fee=5100;
            }else if(distances==12){
                   var customer_delivery_fee=5400;
            }else if(distances > 12 && distances < 13.5){
                   var customer_delivery_fee=5700;
            }else if(distances==13.5){
                   var customer_delivery_fee=6000;
            }else if(distances > 13.5 && distances < 15){
                   var customer_delivery_fee=6300;
            }else if(distances==15){
                   var customer_delivery_fee=6600;
            }else if(distances > 15 && distances < 16.5){
                   var customer_delivery_fee=6700;
            }else if(distances==16.5){
                   var customer_delivery_fee=7000;
            }else if(distances > 16.5 && distances < 18){
                   var customer_delivery_fee=7300;
            }else if(distances==18){
                   var customer_delivery_fee=7500;
            }else if(distances > 18 && distances < 19.5){
                var customer_delivery_fee=7800;
            }else if(distances >= 19.5){
                var customer_delivery_fee=8100;
            }else{
                var customer_delivery_fee=8100;
            }
        document.getElementById('price').value=customer_delivery_fee;
        document.getElementById('rider_restaurant_distance').value=distances;
    function calcCrow(lat1, lon1, lat2, lon2)
    {
      var R = 6371; // km
      var dLat = toRad(lat2-lat1);
      var dLon = toRad(lon2-lon1);
      var lat1 = toRad(lat1);
      var lat2 = toRad(lat2);

      var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      var d = R * c;
      return d;
    }

    // Converts numeric degrees to radians
    function toRad(Value)
    {
        return Value * Math.PI / 180;
    }
}
    function calDistance1(){
        var lat1=document.getElementById("from_lat").value;
        var lon1=document.getElementById("from_lon").value;
        var lat2=document.getElementById("to_lat").value;;
        var lon2=document.getElementById("to_lon").value;;
        var distances=(calcCrow(lat1,lon1,lat2,lon2).toFixed(1));
            if(distances <= 2) {
                var customer_delivery_fee=1200;
            }else if(distances > 2 && distances < 3.5){
                var customer_delivery_fee=1500;
            }else if(distances == 3.5){
                var customer_delivery_fee=1800;
            }else if(distances > 3.5 && distances < 4.5){
                var customer_delivery_fee=2100;
            }else if(distances == 4.5){
                   var customer_delivery_fee=2400;
            }else if(distances > 4.5 && distances < 6){
                   var customer_delivery_fee=2700;
            }else if(distances == 6){
                   var customer_delivery_fee=3000;
            }else if(distances > 6 && distances < 7.5){
                   var customer_delivery_fee=3300;
            }else if(distances==7.5){
                   var customer_delivery_fee=3600;
            }else if(distances > 7.5 && distances < 9){
                   var customer_delivery_fee=3900;
            }else if(distances==9){
                   var customer_delivery_fee=4200;
            }else if(distances > 9 && distances < 10.5){
                   var customer_delivery_fee=4500;
            }else if(distances==10.5){
                   var customer_delivery_fee=4800;
            }else if(distances > 10.5 && distances < 12){
                   var customer_delivery_fee=5100;
            }else if(distances==12){
                   var customer_delivery_fee=5400;
            }else if(distances > 12 && distances < 13.5){
                   var customer_delivery_fee=5700;
            }else if(distances==13.5){
                   var customer_delivery_fee=6000;
            }else if(distances > 13.5 && distances < 15){
                   var customer_delivery_fee=6300;
            }else if(distances==15){
                   var customer_delivery_fee=6600;
            }else if(distances > 15 && distances < 16.5){
                   var customer_delivery_fee=6700;
            }else if(distances==16.5){
                   var customer_delivery_fee=7000;
            }else if(distances > 16.5 && distances < 18){
                   var customer_delivery_fee=7300;
            }else if(distances==18){
                   var customer_delivery_fee=7500;
            }else if(distances > 18 && distances < 19.5){
                var customer_delivery_fee=7800;
            }else if(distances >= 19.5){
                var customer_delivery_fee=8100;
            }else{
                var customer_delivery_fee=8100;
            }
        document.getElementById('price').value=customer_delivery_fee;
        // document.getElementById('from_parcel_city_id').value=data[0];
        // document.getElementById('to_parcel_city_id').value=data[1];
        document.getElementById('rider_restaurant_distance').value=distances;
    function calcCrow(lat1, lon1, lat2, lon2)
    {
      var R = 6371; // km
      var dLat = toRad(lat2-lat1);
      var dLon = toRad(lon2-lon1);
      var lat1 = toRad(lat1);
      var lat2 = toRad(lat2);

      var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      var d = R * c;
      return d;
    }

    // Converts numeric degrees to radians
    function toRad(Value)
    {
        return Value * Math.PI / 180;
    }
}

</script>
</body>
</html>


