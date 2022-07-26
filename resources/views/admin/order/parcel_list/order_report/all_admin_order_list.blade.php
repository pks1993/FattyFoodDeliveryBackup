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

        #header{
			height: 50px;
			line-height: 50px;
			font-size: 25px;
			text-align: left;
			padding-left: 5px;
		}

		#header a{
			color: white;
			text-decoration: none;
		}

		.table{
			font-size: 13px;
		}

		div.scrollmenu {
		  overflow: auto;
		  white-space: nowrap;
		  border-radius: 3px;
		  margin: 5px;
		}

		div.scrollmenu a {
		  display: inline-block;
		  color: white;
		  text-align: center;
		  text-decoration: none;
		}
    </style>
</head>
<body style="width:100% !important;font-size: 13px;">
{{-- <ul class="nav nav-pills navbar-inverse" style="background-color: #343a40;"> --}}
<ul class="nav nav-pills navbar-inverse">
    <li class="nav-item">
        <a href="{{ url('admin_parcel_orders/create/'.$customer_admin_id) }}" class="nav-link" style="width: 100%;color: #FFF;font-size:15px;font-weight:510;">Home</a>
    </li>
    <li class="nav-item">
        <a href="{{ url('admin_parcel_orders/list/'.$customer_admin_id) }}" class="nav-link" style="width: 100%;color: #FFF;font-size:15px;font-weight:510;">Order</a>
    </li>
    <li class="nav-item">
        <a href="{{ url('admin_parcel_orders/report/'.$customer_admin_id) }}" class="nav-link" style="width: 100%;color: #FFF;font-size:15px;font-weight:510;">OrderReport</a>
    </li>
    <li class="nav-item">
        <a href="{{ url("admin_rider_order/all_report/".$customer_admin_id) }}" style="background-color:#28a745;width: 100%;color: #FFF;font-size:15px;font-weight:510;" id="home-tab" data-toggle="pill" href="#home" role="tab" aria-controls="home" aria-selected="true">AllOrderReport</a>
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
{{-- <form action="{{ route('fatty.admin.admin_parcel.store') }}" method="post" autocomplete="off" enctype="multipart/form-data" style="margin: 5px;"> --}}
    {{-- @csrf --}}
{{-- </form> --}}
    <div class="container-fluid">
        <div class="row p-1">
            <div class="col-12 p-1 rounded">
                <div class="" style="padding:5px;">
                    <div class="col-12">
                        <form action="{{ route('admin_parcel_all_report.filter',$customer_admin_id) }}">
                            {{-- <select class="col-4" name="month" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:18px;font-weight:510;border-radius:5px;height:40px;">
                                <option value="12">12</option>
                                <option value="11">11</option>
                                <option value="10">10</option>
                                <option value="09">09</option>
                                <option value="08">08</option>
                                <option value="07">07</option>
                                <option value="06">06</option>
                                <option value="05">05</option>
                                <option value="04">04</option>
                                <option value="03">03</option>
                                <option value="02">02</option>
                                <option value="01">01</option>
                            </select>
                            <select class="col-4" name="year" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:18px;font-weight:510;border-radius:5px;height:40px;">
                                <option value="2022">2022</option>
                                <option value="2021">2021</option>
                                <option value="2020">2020</option>
                                <option value="2019">2019</option>
                                <option value="2018">2018</option>
                                <option value="2017">2017</option>
                                <option value="2016">2016</option>
                                <option value="2015">2015</option>
                                <option value="2014">2014</option>
                                <option value="2013">2013</option>
                                <option value="2012">2012</option>
                                <option value="2011">2011</option>
                                <option value="2010">2010</option>
                                <option value="2009">2009</option>
                                <option value="2008">2008</option>
                                <option value="2007">2007</option>
                                <option value="2006">2006</option>
                                <option value="2005">2005</option>
                            </select> --}}
                            <input class="col-7" type="month" name="date" value="{{ now()->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <button class="col-4" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                            {{-- <button class="col-3" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button> --}}
                        </form>
                    </div>
                    <div class="scrollmenu col-12 mt-3">
                        @foreach ($this_month_days as $date)
                        {{-- {{ $today_date }} {{ date('d-m-Y', strtotime($date)) }} --}}
                            @if(date('d-m-Y', strtotime($date))==$today_date)
                                <a href="{{ url('admin_parcel_orders/all_report/date/filter/'.$customer_admin_id.'/'.date('Y-m-d',strtotime($date))) }}" style="font-size: 20px;" class="btn btn-lg btn-success mt-1" id="{{ date('d', strtotime($date)) }}" style="position: relative;">
                                {{ date('d', strtotime($date)) }}
                                    <span class="pl-1 pr-1" style="position: absolute; top: 0px; background-color: red; height: 15px; font-size: 10px; border-radius: 50%;">
                                        <?php
                                            $count=DB::select("select * from customer_orders where customer_id='$customer_admin_id' and Date(created_at)='$date' and order_type='parcel'");
                                            echo count($count);
                                        ?>
                                    </span>
                                </a>
                            @else
                                <a href="{{ url('admin_parcel_orders/all_report/date/filter/'.$customer_admin_id.'/'.date('Y-m-d',strtotime($date))) }}" style="font-size: 20px;" class="btn btn-lg btn-secondary mt-1" id="{{ date('d', strtotime($date)) }}" style="position: relative;">
                                {{ date('d', strtotime($date)) }}
                                    <span class="pl-1 pr-1" style="position: absolute; top: 0px; background-color: red; height: 15px; font-size: 10px; border-radius: 50%;">
                                        <?php
                                            $count=DB::select("select * from customer_orders where customer_id='$customer_admin_id' and Date(created_at)='$date' and order_type='parcel' ");
                                            echo count($count);
                                        ?>
                                    </span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                    <div class="ml-1 mt-4 mr-1" style="margin-bottom: 75px;">
                        <a class="btn btn-block mt-1 font-weight-bold" style="font-size: 14px;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-6">
                                        Order No.
                                    </div>
                                    <div class="col-6">
                                        Price
                                    </div>
                                </div>
                            </div>
                        </a>
                        @foreach ($orders as $order)
                            <a class="btn btn-block border mt-1" id="show{{ $order->order_id }}" style="font-size: 14px;">
                                <div class="container-fluid">
                                    <div class="row" id="{{ $order->order_id }}" onclick="showOrderDetail(this)">
                                        <div class="col-6">
                                            {{ $order->customer_order_id }}
                                        </div>

                                        <div class="col-6">
                                            {{ $order->bill_total_price }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <a class="btn btn-block btn-success text-white border mt-1" id="hide{{ $order->order_id }}" style="display: none;font-size: 14px;">
                                <div class="container-fluid">
                                    <div class="row" id="{{ $order->order_id }}" onclick="hideOrderDetail(this)">
                                        <div class="col-6">
                                            {{ $order->customer_order_id }}
                                        </div>

                                        <div class="col-6">
                                            {{ $order->bill_total_price }}
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <div id="order{{ $order->order_id }}" class="container-fluid mt-2" style="border: 1px solid red; border-radius: 5px; display: none;font-size: 14px;">
                                <div class="row p-1">
                                    <div class="col-12 text-center">
                                        <strong>ORDER NO. {{ $order->customer_order_id }}</strong>
                                        <span class="float-right" onclick="copyDivToClipboard({{ $order->order_id }})"><i class="fas fa-link text-primary"></i></span>
                                        <hr>
                                    </div>
                                    <div class="col-4 text-center">
                                        <a class="btn btn-sm btn-block btn-success text-white">
                                            @if($order->from_parcel_city_id)
                                                {{ $order->from_block->block_name }}
                                            @else
                                                <span style="color: white;">{{ "Null" }}</span>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="col-4 text-center">
                                        <a class="btn btn-sm">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                    <div class="col-4 text-center">
                                        <a class="btn btn-sm btn-block btn-success text-white">
                                            @if($order->to_parcel_city_id)
                                                {{ $order->to_block->block_name }}
                                            @else
                                                <span style="color: white;">{{ "Null" }}</span>
                                            @endif
                                        </a>
                                    </div>

                                    <div class="col-12">
                                        <div class="row" id="copy{{ $order->order_id }}">
                                            <div class="col-12 text-left p-0">
                                                From:   @if($order->from_parcel_city_id)
                                                            {{ $order->from_block->block_name }}
                                                        @else
                                                            <span style="color: white;">{{ "Null" }}</span>
                                                        @endif
                                            </div>
                                            <div class="col-12 text-left p-0">
                                                <a href="tel:{{ $order->from_sender_phone }} ">{{ $order->from_sender_phone }}</a>
                                            </div>

                                                    <div class="col-12 text-left p-0">
                                                        {{ $order->from_pickup_note }}
                                                    </div>

                                                    <div class="col-12">
                                                        -----
                                                    </div>

                                                    <div class="col-12 text-left p-0">
                                                        To: @if($order->to_parcel_city_id)
                                                                {{ $order->to_block->block_name }}
                                                            @else
                                                                <span style="color: white;">{{ "Null" }}</span>
                                                            @endif
                                                    </div>

                                                    <div class="col-12 text-left p-0">
                                                        <a href="tel: {{ $order->to_recipent_phone }}">
                                                            {{ $order->to_recipent_phone }}
                                                        </a>
                                                    </div>

                                                    <div class="col-12 text-left p-0">
                                                        {{ $order->to_drop_note }}
                                                    </div>

                                                    <div class="col-12">
                                                        -----
                                                    </div>

                                                    <div class="col-12 text-left p-0">
                                                        Price: {{ $order->bill_total_price }} ks
                                                    </div>

                                                    <div class="col-12 text-left p-0 text-danger font-weight-bold">
                                                        Remark: {{ $order->parcel_order_note }}
                                                    </div>
                                                    <div class="col-12 text-left p-0">
                                                        {{-- Rider:{{ $order->rider->rider_user_name }} - <a href="tel:{{ $order->rider->rider_user_phone }}">{{ $order->rider->rider_user_phone }}</a> --}}
                                                    </div>
                                                    <div class="col-12 text-left p-0">
                                                        Created By:{{ $order->customer->customer_name }} - <a href="tel: {{ $order->customer->customer_phone }}">{{ $order->customer->customer_phone }}</a>

                                                    </div>
                                                </div>
                                        </div>


                                            <div class="col-12">
                                                <hr class="m-1">
                                            </div>

                                            <div class="col-4 text-left p-0">
                                                Created
                                            </div>

                                            <div class="col-8 text-right p-0">
                                                {{ $order->created_at }}
                                            </div>

                                            <div class="col-4 text-left p-0">
                                                Received
                                            </div>

                                            <div class="col-8 text-right p-0">
                                                {{ $order->updated_at }}
                                            </div>

                                            <div class="col-4 text-left p-0">
                                                Finished
                                            </div>

                                            <div class="col-8 text-right p-0">
                                                {{ $order->updated_at }}
                                            </div>

                                            <div class="col-4 text-left p-0">
                                                Duration
                                            </div>

                                            <div class="col-8 text-right p-0">
                                                @if($order->order_status_id==16 || $order->order_status_id==15)
                                                    {{ $order->updated_at->diffForHumans($order->created_at,true) }}
                                                @else
                                                    {{ $order->created_at->diffForHumans(null,true) }}
                                                @endif
                                            </div>
                                        </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="bg-success text-white" style="position: fixed; bottom: 0px; height: 50px; width: 100%;font-size:16px;">

        <div class="text-left" style="width: 50%; float: left;">
            {{ $day_date }}
        </div>

        <div class="text-right" style="width: 50%; float: left;">
            {{ $day_times }} times / {{ $day_amount }} ks
        </div>
        <!-- month -->
        <div class="text-left" style="width: 50%; float: left;">
            {{ $month_date }}
        </div>

        <div class="text-right" style="width: 50%; float: left;">
            {{ $month_times }} times / {{ $month_amount }} ks
        </div>

    </div>
{{-- <script>
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script> --}}
<script type="text/javascript">
	function showOrderDetail(x){
		var ord_id = x.getAttribute('id');

		document.getElementById('show' + ord_id).style.display = 'none';
		document.getElementById('hide' + ord_id).style.display = 'block';
		document.getElementById('order' + ord_id).style.display = 'block';
	}

	function hideOrderDetail(x){
		var ord_id = x.getAttribute('id');

		document.getElementById('show' + ord_id).style.display = 'block';
		document.getElementById('hide' + ord_id).style.display = 'none';
		document.getElementById('order' + ord_id).style.display = 'none';
	}

	function copyLink(x) {

			document.getElementById('link' + x).style.display = 'block';

	    var range = document.createRange();
	    range.selectNode(document.getElementById('link' + x));
	    window.getSelection().removeAllRanges(); // clear current selection
	    window.getSelection().addRange(range); // to select text
	    document.execCommand("copy");
	    window.getSelection().removeAllRanges();// to deselect

	    alert('Link has been copied!');

	    document.getElementById('link' + x).style.display = 'none';
	}

</script>

<script>
    function copyDivToClipboard(x) {
	    var range = document.createRange();
	    range.selectNode(document.getElementById('copy' + x));
	    window.getSelection().removeAllRanges(); // clear current selection
	    window.getSelection().addRange(range); // to select text
	    document.execCommand("copy");
	    window.getSelection().removeAllRanges();// to deselect

	    alert('Location has been copied!');
	}
</script>
</body>
</html>


