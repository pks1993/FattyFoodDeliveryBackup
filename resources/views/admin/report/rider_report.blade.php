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
                        <li class="breadcrumb-item active">Rider Order</li>
                        <li class="breadcrumb-item active">Report</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
                </div>
                <div class="col-md-12">
                    <form action="{{ url('fatty/main/admin/riders/rider_order/report_filter') }}">
                        <input class="col-3" type="date" name="date" value="{{ $date }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                        <button class="col-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="content mb-5">
        <div class="row">
            <div class="col-md-12">
                <div class="container-fluid text-center font-weight-bold">
                    <div class="row">
                        <div class="col-6">
                            Driver
                        </div>

                        <div class="col-6" id="sort_by_qty">
                            <a class="btn btn-sm btn-success text-white">Qty</a>
                        </div>
                    </div>
                </div>
                <div id="qty">
                    @foreach ($riders as $rider)
                        <a class="btn btn-block border mt-1" id="show{{ $rider->rider_id }}">
                            <div class="container-fluid">
                                <div class="row" id="{{ $rider->rider_id }}" onclick="showOrderDetail(this)">
                                    <div class="col-6 text-left">
                                        {{ $rider->rider_user_name }}
                                    </div>
                                    <div class="col-6">
                                        <?php
                                            $count=DB::select("select * from customer_orders where order_status_id in (15,7,8) and rider_id='$rider->rider_id' and Date(created_at) ='".$date."'");
                                            echo count($count);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a class="btn btn-block btn-success text-white border mt-1" id="hide{{ $rider->rider_id }}" style="display: none;">
                            <div class="container-fluid">
                                <div class="row" id="{{ $rider->rider_id }}" onclick="hideOrderDetail(this)">
                                    <div class="col-6">
                                        {{ $rider->rider_user_name }}
                                    </div>
                                    <div class="col-6">
                                        <?php
                                            $count=DB::select("select * from customer_orders where order_status_id in (15,7,8) and rider_id='$rider->rider_id' and Date(created_at) ='".$date."'");
                                            echo count($count);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div id="order{{ $rider->rider_id }}" class="container-fluid mt-2" style="border: 1px solid gray; border-radius: 5px; display: none;">
                            <div class="row p-1">
                                <div class="col-12 text-center p-0">
                                    <strong>{{ $rider->rider_user_name }}</strong>
                                    <hr>
                                </div>
                                <div class="col-2 text-center p-0">
                                    <strong>ORDER</strong>
                                </div>
                                <div class="col-3 text-center p-0">
                                    <strong>From</strong>
                                </div>
                                <div class="col-3 text-center p-0">
                                    <strong>To</strong>
                                </div>
                                <div class="col-2 text-center p-0">
                                    <strong>Price</strong>
                                </div>
                                <div class="col-2 text-center p-0">
                                    <strong>Time</strong>
                                </div>
                                {{-- <div class="col-2 p-0">
                                </div> --}}
                                {{-- @foreach ($orders as $order)
                                    @if ($rider->rider_id==$order->rider_id) --}}
                                        <?php
                                            $rider_order=DB::select("select * from customer_orders where order_status_id in (15,7,8) and rider_id='$rider->rider_id' and Date(created_at) ='".$date."'");
                                        ?>
                                        @foreach ($rider_order as $value)
                                            @if($value->order_type=="food")
                                                <div class="col-2 text-center p-0">
                                                    <strong>{{ $value->customer_order_id }}</strong>
                                                </div>
                                                <div class="col-3 bg-secondary text-center text-white rounded p-0">
                                                    @if($value->restaurant_id)
                                                        @foreach ($restaurants as $restaurant)
                                                            @if($value->restaurant_id==$restaurant->restaurant_id)
                                                                {{ $restaurant->restaurant_name_mm }}
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{ "Empty" }}
                                                    @endif
                                                </div>
                                                <div class="col-3 bg-secondary text-center text-white rounded p-0">
                                                    {{ $value->current_address }}
                                                </div>
                                                <div class="col-2 text-center p-0">
                                                    {{ $value->bill_total_price }}
                                                </div>
                                                <div class="col-2 text-center p-0">
                                                    @if($value->order_status_id==7 || $value->order_status_id==8)
                                                        {{ \Carbon\Carbon::parse($value->updated_at)->diffForHumans($value->created_at,true,true) }}
                                                    @else
                                                        {{ \Carbon\Carbon::parse($value->created_at)->diffForHumans(null,true,true) }}
                                                    @endif
                                                </div>
                                                <div class="col-12">
                                                    <hr class="mt-1 mb-1">
                                                </div>
                                            @else
                                                <div class="col-2 text-center p-0">
                                                    <strong>{{ $value->customer_order_id }}</strong>
                                                </div>
                                                <div class="col-3 bg-secondary text-center text-white rounded p-0">
                                                    @if($value->from_parcel_city_id)
                                                        @foreach ($blocks as $block)
                                                            @if($value->from_parcel_city_id==$block->parcel_block_id)
                                                                {{ $block->block_name }}
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{ "Empty" }}
                                                    @endif
                                                </div>
                                                <div class="col-3 bg-secondary text-center text-white rounded p-0">
                                                    @if($value->to_parcel_city_id)
                                                        @foreach ($blocks as $block)
                                                            @if($value->to_parcel_city_id==$block->parcel_block_id)
                                                                {{ $block->block_name }}
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{ "Empty" }}
                                                    @endif
                                                </div>
                                                <div class="col-2 text-center p-0">
                                                    {{ $value->bill_total_price }}
                                                </div>
                                                <div class="col-2 text-center p-0">
                                                    <?php
                                                        $time=0;
                                                        $start=\Carbon\Carbon::parse($value->created_at);
                                                        $end=\Carbon\Carbon::parse($value->updated_at);

                                                        $time +=$end->diffInSeconds($start);
                                                    ?>
                                                    {{ \Carbon\CarbonInterval::seconds($time)->cascade()->forHumans(null,true) }}
                                                </div>
                                                <div class="col-12">
                                                    <hr class="mt-1 mb-1">
                                                </div>
                                            @endif
                                        @endforeach
                                    {{-- @endif
                                @endforeach --}}
                                <div class="col-4 text-center font-weight-bold">
                                    Order Qty
                                    <br>
                                    <?php
                                        $orderqty=DB::select("select * from customer_orders where order_status_id in (15,7,8) and rider_id='$rider->rider_id' and Date(created_at) ='".$date."'");
                                        echo count($orderqty);
                                    ?>
                                </div>
                                <div class="col-4 text-center font-weight-bold">
                                    Income
                                    <br>
                                    <?php
                                        $order_qty=DB::select("select sum(bill_total_price) as price from customer_orders where order_status_id in (15,7,8) and rider_id='$rider->rider_id' and Date(created_at) ='".$date."'");
                                        echo $order_qty[0]->price;
                                    ?>
                                </div>
                                <div class="col-4 text-center font-weight-bold">
                                    Time
                                    <br>
                                    <?php
                                    $order_qty=DB::select("select created_at,updated_at from customer_orders where order_status_id in (15,7,8) and rider_id='$rider->rider_id' and Date(created_at) ='".$date."'");
                                    // $data=[];
                                    $time=0;
                                    foreach ($order_qty as $value) {
                                        $start=\Carbon\Carbon::parse($value->created_at);
                                        $end=\Carbon\Carbon::parse($value->updated_at);

                                        $time +=$end->diffInSeconds($start);
                                    }
                                    $result=\Carbon\CarbonInterval::seconds($time)->cascade()->forHumans(null,true);
                                    echo $result;
                                    ?>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
<script>
    function showOrderDetail(x){
		var driver_id = x.getAttribute('id');

		document.getElementById('show' + driver_id).style.display = 'none';
		document.getElementById('hide' + driver_id).style.display = 'block';
		document.getElementById('order' + driver_id).style.display = 'block';
	}

	function hideOrderDetail(x){
		var driver_id = x.getAttribute('id');

		document.getElementById('show' + driver_id).style.display = 'block';
		document.getElementById('hide' + driver_id).style.display = 'none';
		document.getElementById('order' + driver_id).style.display = 'none';
	}
</script>

<script>
   $(document).ready(function() {
       // DataTables initialisation
       var table = $("#foods_orders").DataTable({
           "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
           "paging": true, // Allow data to be paged
           "lengthChange": true,
           "searching": true, // Search box and search function will be actived
           "info": true,
           "autoWidth": true,
           "processing": true,  // Show processing
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
