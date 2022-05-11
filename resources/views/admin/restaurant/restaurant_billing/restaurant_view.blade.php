<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Restaurant Billing</title>
    @include('admin.layouts._partial.css')
    <style>
        .nav-pills li>#list-tab.active,.nav-pills li>#offered-tab.active,.nav-pills li>#history-tab.active, .nav-pills .show>.nav-link {
            background-color: greenyellow !important;
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
    <div class="row" style="height: 25px;margin-top: 10px;">
        <div class="col">
            <ul class="nav nav-pills">
                <li class="nav-item col btn">
                    <a class="nav-link active" style="width: 100%;background:rgb(178, 176, 176);color: #000000;font-size:15px;border-radius:5px;" id="list-tab" data-toggle="pill" href="#list" role="tab" aria-controls="list" aria-selected="false">Offered List</a>
                </li>
                <li class="nav-item col btn">
                    <a class="nav-link" style="width: 100%;background:rgb(178, 176, 176);color: #000000;font-size:15px;border-radius:5px;" id="history-tab" data-toggle="pill" href="#history" role="tab" aria-controls="history" aria-selected="true">History</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="container mt-5" style="padding-left: 20px;padding-right:20px;" id="tabcontent">
    <div class="row tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
        <div class="col">
            <div class="card-body" style="width: 100%;background:rgb(178, 176, 176);color: #000000;font-size:15px;border-radius:5px;padding:10px;">
                <div class="col text-center" style="font-size: 20px;">
                    Comfirmation!
                </div>
                <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                    <div class="col" style="height: 10px;font-weight:510;">Start Date</div>
                    <div class="col text-right" style="height: 10px;">20 Apr 2022</div>
                    <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                </div>
                <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                    <div class="col" style="height: 10px;font-weight:510;">End Date</div>
                    <div class="col text-right" style="height: 10px;">30 Apr 2022</div>
                    <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                </div>
                <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                    <div class="col" style="height: 10px;font-weight:510;">Amount</div>
                    {{-- <div class="col text-right" style="height: 10px;">{{ number_format($restaurant_payment->total_amount) }} ks</div> --}}
                    <div class="col text-right" style="height: 10px;">100,000 ks</div>
                    <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                </div>
                <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                    <div class="col" style="height: 10px;font-weight:510;">Percentage</div>
                    {{-- <div class="col text-right" style="height: 10px;">{{ $restaurant_payment->percentage }} %</div> --}}
                    <div class="col text-right" style="height: 10px;">10 %</div>
                    <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                </div>
                <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                    <div class="col" style="height: 10px;font-weight:510;">Total</div>
                    {{-- <div class="col text-right" style="height: 10px;">{{ number_format($restaurant_payment->pay_amount) }} ks</div> --}}
                    <div class="col text-right" style="height: 10px;">90,000 ks</div>
                </div>
                <div class="row">
                    <div class="col text-center" style="margin-top:50px;margin-bottom:10px;">
                        {{-- <a href="{{ route('fatty.admin.restaurants_billing.update',$restaurant_payment->restaurant_payment_id) }}" class="btn btn-sm btn-danger" style="width: 80%;">Accept</a> --}}
                        <a href="#" class="btn btn-sm btn-danger" style="width: 80%;">Accept</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
        <div class="col">
            <a class="btn" style="width: 45%;height:100%;background:#000000;color:white;font-size:15px;border-radius:5px;">Calendar</a>
        </div>
        <div class="col mt-4">
            <div class="row" style="width: 100%;height:100%;background:rgb(178, 176, 176);color:#000000;font-size:15px;border-radius:5px;margin-left:1px;">
                <div class="col-5" style="padding: 10px;">
                    V004
                </div>
                <div class="col-6 text-right" style="padding: 10px;">
                    {{-- {{ $restaurant_payment->pay_amount }} Ks --}}
                    90,000 Ks
                </div>
                <div class="col-1" style="padding: 11px;">
                    <i class="fas fa-angle-right" style="font-size: 20px;color: rgb(74, 67, 67)"></i>
                </div>
            </div>
        </div>
        <div class="col mt-3">
            <div class="row" style="width: 100%;height:100%;background:rgb(178, 176, 176);color:#000000;font-size:15px;border-radius:5px;margin-left:1px;">
                <div class="col-5" style="padding: 10px;">
                    V004
                </div>
                <div class="col-6 text-right" style="padding: 10px;">
                    90,000 Ks
                </div>
                <div class="col-1" style="padding: 11px;">
                    <i class="fas fa-angle-right" style="font-size: 20px;color: rgb(74, 67, 67)"></i>
                </div>
            </div>
        </div>
        <div class="col mt-3">
            <div class="row" style="width: 100%;height:100%;background:rgb(178, 176, 176);color:#000000;font-size:15px;border-radius:5px;margin-left:1px;">
                <div class="col-5" style="padding: 10px;">
                    V004
                </div>
                <div class="col-6 text-right" style="padding: 10px;">
                    90,000 Ks
                </div>
                <div class="col-1" style="padding: 11px;">
                    <i class="fas fa-angle-right" style="font-size: 20px;color: rgb(74, 67, 67)"></i>
                </div>
            </div>
        </div>
        <div class="col mt-3">
            <div class="row" style="width: 100%;height:100%;background:rgb(178, 176, 176);color:#000000;font-size:15px;border-radius:5px;margin-left:1px;">
                <div class="col-5" style="padding: 10px;">
                    V004
                </div>
                <div class="col-6 text-right" style="padding: 10px;">
                    90,000 Ks
                </div>
                <div class="col-1" style="padding: 11px;">
                    <i class="fas fa-angle-right" style="font-size: 20px;color: rgb(74, 67, 67)"></i>
                </div>
            </div>
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
