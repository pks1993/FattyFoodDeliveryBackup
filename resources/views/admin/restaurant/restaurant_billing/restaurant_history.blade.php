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
                    <a href="{{ url('fatty/main/admin/restaurant_billing/data_list/'.$restaurant_id) }}" class="nav-link" style="background-color:#FFFFFF;width: 100%;border-color:#bde000;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px;">Offered List</a>
                </li>
                <li class="nav-item col btn">
                    <a class="nav-link active" style="background-color:#FFFFFF;width: 100%;border-color:#bde000;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px;" id="history-tab" data-toggle="pill" href="#history" role="tab" aria-controls="history" aria-selected="true">History</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="container mt-4" style="padding-left: 20px;padding-right:20px;" id="tabcontent">
    <div class="row tab-pane fade show active" id="history" role="tabpanel" aria-labelledby="history-tab">
        <div class="col">
            <a class="btn mb-1" style="width: 45%;height:100%;background:#bde000;color:white;font-size:15px;border-radius:5px;">Calendar</a>
        </div>
        @if($check)
            @foreach ($restaurant_payment as $value)
                <div class="col mt-3">
                    <div class="row" style="width: 100%;height:100%;background:#FFFFFF;color:#000000;font-size:15px;border-radius:5px;margin-left:1px;">
                        <div class="col-5" style="padding: 10px;">
                            {{ $value->payment_voucher }}
                        </div>
                        <div class="col-6 text-right" style="padding: 10px;">
                            {{ $value->pay_amount }} Ks
                        </div>
                        <div class="col-1" style="padding: 11px;">
                            <i class="fas fa-angle-right" style="font-size: 20px;color: rgb(74, 67, 67)"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="card-body mt-3" style="width: 100%;height:100%;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#bde000;background-color:#FFFFFF">
                <div class="col text-center" style="padding:70px 0px;">
                    <h4 style="font-weight:500;color:red;"> Empty Billing Voucher! </h4>
                </div>
            </div>
        @endif
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
