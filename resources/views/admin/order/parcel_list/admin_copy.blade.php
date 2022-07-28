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


{{-- <form action="{{ route('fatty.admin.admin_parcel.store') }}" method="post" autocomplete="off" enctype="multipart/form-data" style="margin: 5px;"> --}}
    {{-- @csrf --}}
    <div class="container-fluid">
        <div class="row p-1">
            <div class="form-group col-12 p-1 border border-success rounded" style="margin-top: 5px;">
                <div class="row" style="padding:10px;">
                    <div class="col-12 mb-3">
                        <a href="{{ url('admin_parcel_orders/list/'.$parcel_order->customer_id) }}" class="float-right text-dark"><i class="fa fa-times"></i></a>
                    </div>
                    <div class="col-12 p-1">
                        {{-- <a onclick="copyOrder({{ $parcel_order->order_id }})" class="btn btn-lg btn-block btn-secondary text-white">Copy</a> --}}
                        <a onclick="copyOrder({{ $parcel_order->order_id }})" class="btn btn-lg btn-block btn-secondary text-white">Copy</a>
                    </div>
                    <div class="col-12">
                        <div class="row" id="copy{{ $parcel_order->order_id }}">
                            <input type="hidden" value="{{ $parcel_order->customer_id }}" name="customer_id" id="customer_id">
                            <div class="col-12 text-center">
                                <strong>Order:{{ $parcel_order->customer_order_id }}</strong>
                            </div>
                            <div class="col-12 text-center">
                                <strong>-----</strong>
                            </div>
                            <div class="col-12 p-0 text-left">
                                @if($parcel_order->from_parcel_city_id)
                                    From: {{ $parcel_order->from_block->block_name }}
                                @else
                                    From:
                                @endif
                            </div>
                            <div class="col-12 p-0 text-left">
                                <a href="tel:{{ $parcel_order->from_sender_phone }}">{{ $parcel_order->from_sender_phone }}</a>
                            </div>
                            <div class="col-12 p-0 text-left">
                                {!! nl2br(e($parcel_order->from_pickup_note)) !!}
                            </div>
                            <div class="col-12 text-center">
                                <strong>-----</strong>
                            </div>
                            <div class="col-12 p-0 text-left">
                                @if($parcel_order->to_parcel_city_id)
                                    To: {{ $parcel_order->to_block->block_name }}
                                @else
                                    To:
                                @endif
                            </div>
                            <div class="col-12 p-0 text-left">
                                <a href="tel:{{ $parcel_order->to_recipent_phone }}">{{ $parcel_order->to_recipent_phone }}</a>
                            </div>
                            <div class="col-12 p-0 text-left">
                                {{-- {{ $parcel_order->to_drop_note }} --}}
                                {!! nl2br(e($parcel_order->to_drop_note)) !!}
                            </div>
                            <div class="col-12 text-center">
                                <strong>-----</strong>
                            </div>
                            <div class="col-12 p-0 text-left">
                                Price: {{ $parcel_order->bill_total_price}} Ks
                            </div>
                            <div class="col-12 p-0 text-left text-danger font-weight-bold">
                                {{-- Remark: {{ $parcel_order->parcel_order_note }} --}}
                                Remark: {!! nl2br(e($parcel_order->parcel_order_note)) !!}
                            </div>
                            <div class="col-12 p-0 text-left">
                                Booking: {{ $parcel_order->customer_booking_id }}
                            </div>
                            <div class="col-12 p-0 text-left">
                                @if($parcel_order->rider_id)
                                    Rider: {{ $parcel_order->rider->rider_user_name }} - <a href="tel:{{ $parcel_order->rider->rider_user_phone }}">{{ $parcel_order->rider->rider_user_phone }}</a>
                                @else
                                    Rider:
                                @endif
                            </div>
                            <div class="col-12 p-0 text-left">
                                Created By: {{ $parcel_order->customer->customer_name }} - <a href="tel:{{ $parcel_order->customer->customer_phone }}">{{ $parcel_order->customer->customer_phone }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
{{-- </form> --}}
<script>
    function copyOrder(x) {
        var id=document.getElementById('customer_id').value;
	    var range = document.createRange();
	    range.selectNode(document.getElementById('copy' + x));
	    window.getSelection().removeAllRanges(); // clear current selection
	    window.getSelection().addRange(range); // to select text
	    document.execCommand("copy");
	    window.getSelection().removeAllRanges();// to deselect

	    alert('Location has been copied!');

	    // location.assign('http://174.138.22.156/admin_parcel_orders/list/'+id);
	    location.assign('http://128.199.241.129/admin_parcel_orders/list/'+id);
	}

</script>
</body>
</html>


