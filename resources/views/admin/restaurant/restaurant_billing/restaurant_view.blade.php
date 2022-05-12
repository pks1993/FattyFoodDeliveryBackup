<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Restaurant Billing</title>
    @include('admin.layouts._partial.css')
    <style>
        body{
            background-color: #e2e1e1;
        }
        .nav-pills li>#list-tab.active,.nav-pills li>#offered-tab.active,.nav-pills li>#history-tab.active, .nav-pills .show>.nav-link {
            background-color: #bde000 !important;
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
<body>
<div class="container">
    <div class="row" style="padding-top: 10px;">
        <div class="col">
            <ul class="nav nav-pills">
                <li class="nav-item col btn">
                    <a class="nav-link active" style="background-color:#FFFFFF;width: 100%;border-color:#bde000;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px;" id="list-tab" data-toggle="pill" href="#list" role="tab" aria-controls="list" aria-selected="false">Offered List</a>
                </li>
                <li class="nav-item col btn">
                    <a href="{{ url('fatty/main/admin/restaurant_billing/data_history/'.$restaurant_id) }}" class="nav-link" style="background-color:#FFFFFF;width: 100%;border-color:#bde000;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px;">History</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="container mt-5" style="padding-left: 20px;padding-right:20px;" id="tabcontent">
    <div class="row tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
        <div class="col">
            @if($restaurant_payment)
                <div class="card-body" style="width: 100%;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#bde000;background-color:#FFFFFF">
                    <div class="col text-center" style="font-size: 20px;font-weight:510">
                        Comfirmation!
                    </div>
                    <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Start Date</div>
                        <div class="col text-right" style="height: 10px;">{{ date('d M Y', strtotime($restaurant_payment->start_offered_date)) }}</div>
                        <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                    </div>
                    <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">End Date</div>
                        <div class="col text-right" style="height: 10px;">{{ date('d M Y', strtotime($restaurant_payment->last_offered_date)) }}</div>
                        <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                    </div>
                    <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Amount</div>
                        <div class="col text-right" style="height: 10px;">{{ number_format($restaurant_payment->total_amount) }} ks</div>
                        {{-- <div class="col text-right" style="height: 10px;">100,000 ks</div> --}}
                        <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                    </div>
                    <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Percentage({{ $restaurant_payment->percentage }}%)</div>
                        <div class="col text-right" style="height: 10px;"> - {{ $restaurant_payment->total_amount*$restaurant_payment->percentage/100 }} ks</div>
                        {{-- <div class="col text-right" style="height: 10px;">10 %</div> --}}
                        <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                    </div>
                    <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Total</div>
                        <div class="col text-right" style="height: 10px;">{{ number_format($restaurant_payment->pay_amount) }} ks</div>
                        {{-- <div class="col text-right" style="height: 10px;">90,000 ks</div> --}}
                    </div>
                    <div class="row">
                        <div class="col text-center" style="margin-top:50px;margin-bottom:10px;">
                            <a href="{{ url('fatty/main/admin/restaurants_billing/update',$restaurant_payment->restaurant_payment_id) }}"  class="btn btn-sm" style="width: 80%;background-color:#bde000;color:#FFFFFF;font-weight:510;">Accept</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card-body" style="width: 100%;height:200px;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#bde000;background-color:#FFFFFF">
                    <div class="col text-center" style="padding:70px 0px;">
                        <h4 style="font-weight:500;color:red;"> Empty Billing Voucher! </h4>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@include('admin.layouts._partial.script')
<script>
    $(".nav-link").on('click',function(){
        $(".nav-link , .tab-pane").addClass('active');
        $(this).removeClass('active');
        $('.tab-pane:eq('+$(this).index()+')').removeClass('active');
    });
</script>
</body>
</html>
