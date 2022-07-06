<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap Example</title>
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
        .border {
        border: 1px solid #28a745!important;
        }
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
    </style>
</head>
<body style="width:100%;font-size:16px !important;">
{{-- <ul class="nav nav-pills navbar-inverse" style="background-color: #343a40;"> --}}
<ul class="nav nav-pills navbar-inverse">
    <li class="nav-item">
        <a href="{{ url('admin_parcel_orders/create/'.$customer_admin_id) }}" class="nav-link" style="width: 100%;color: #FFF;font-size:15px;font-weight:510;">Home</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" style="background-color:#28a745;width: 100%;color: #FFF;font-size:15px;font-weight:510;" id="home-tab" data-toggle="pill" href="#home" role="tab" aria-controls="home" aria-selected="true">Order</a>
    </li>
    <li class="nav-item">
        <a href="{{ url("admin_parcel_orders/logout") }}" style="width: 100%;color: #FFF;font-size:15px;font-weight:510;">Logout</a>
    </li>

</ul>

{{-- <form action="{{ route('fatty.admin.admin_parcel.store') }}" method="post" autocomplete="off" enctype="multipart/form-data" style="margin: 5px;"> --}}
    {{-- @csrf --}}
    <div class="container-fluid">
        <div class="row p-1">
            <div class="form-group col-12 p-1 border border-success rounded">
                <div class="" style="padding:5px;">
                    <div class="col-12">
                        <form action="{{ route('admin_parcel_orders.filter') }}">
                            <label class="col-4" for="start_date">Start:</label>
                            <label class="col-4" for="end_date">End:</label>
                            <label class="col-2" for=""></label>
                            <input class="col-4" type="date" name="start_date" value="{{ now()->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 45%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <input class="col-4" type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 45%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <input type="hidden" name="customer_id" value="{{ $customer_admin_id }}">
                            <button class="col-2" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                    <div class="form-group col-12 table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    {{-- <th>No.</th> --}}
                                    <th>OrderID</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Rider</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parcel_orders as $value)
                                <tr>
                                    {{-- <td>{{ $value->order_id }}</td> --}}
                                    <td>#{{ $value->customer_order_id }}</td>
                                    <td>
                                        @if($value->from_pickup_address)
                                            {{ $value->from_pickup_address }}
                                        @else
                                            <span style="color: red">{{ "Empty" }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($value->to_drop_address)
                                            {{ $value->to_drop_address }}
                                        @else
                                        <span style="color: red">{{ "Empty" }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($value->rider_id)
                                            <a class="btn btn-success text-white rounded">
                                                {{-- {{ $value->rider->rider_user_name }} --}}
                                                <?php
                                                    $words = explode(" ", $value->rider->rider_user_name);
                                                    $rider_name = "";
                                                    foreach ($words as $w) {
                                                    $rider_name .= $w[0];
                                                    }
                                                ?>
                                                {{ $rider_name }}
                                            </a>
                                        @else
                                            <a class="btn btn-danger text-white rounded">
                                                Empty
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ $value->created_at->diffForHumans(null,true,true) }}</td>
                                    <td>
                                        @if($value->order_status_id==11)
                                            {{-- <a class="btn btn-primary text-white rounded"><i class="fa fa-check-circle"></i></a> <span style="color: blue">{{ "Pending" }}</span> --}}
                                            <a href="{{ url('admin_parcel_orders/edit/'.$value->order_id.'/'.$customer_admin_id) }}" class="btn btn-primary text-white rounded" title="Pending Order" style="font-size: 13px;"><i class="fa fa-check-circle"></i></a>  <a href="{{ url('admin_parcel_orders/edit/'.$value->order_id.'/'.$customer_admin_id) }}"><i class="fa fa-angle-right" style="font-size: 20px;font-weight: 600;color:black;"></i></a>
                                        @else
                                            <a href="{{ url('admin_parcel_orders/edit/'.$value->order_id.'/'.$customer_admin_id) }}" class="btn btn-success text-white rounded" title="Accept Order" style="font-size: 13px;"><i class="fa fa-check-circle"></i></a>  <a href="{{ url('admin_parcel_orders/edit/'.$value->order_id.'/'.$customer_admin_id) }}"><i class="fa fa-angle-right" style="font-size: 20px;font-weight: 600;color:black;"></i></a>
                                        @endif
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
{{-- </form> --}}
<script>
    $(document).ready(function () {
        //select2
        $('#from_parcel_city_id').select2();
        $('#to_parcel_city_id').select2();
        $('#rider_id').select2();
    });
</script>
<script>


    function calDistance(){
        var from_lat_lan= document.getElementById("from_parcel_city_id").value;
        var data=from_lat_lan.split(",");
        var from_parcel_city_id=data[0];
        var lat1=data[1];
        var lon1=data[2];
        var to_lat_lan= document.getElementById("to_parcel_city_id").value;
        var data1=to_lat_lan.split(",");
        var to_parcel_city_id=data1[0];
        var lat2=data1[1];
        var lon2=data1[2];
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


