<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rider Billing</title>
    @include('admin.layouts._partial.css')
    <style>
        html {
            scroll-behavior: smooth;
        }
        body{
            background-color: #e2e1e1;
        }
        .nav-pills li>#list-tab.active,.nav-pills li>#offered-tab.active,.nav-pills li>#history-tab.active, .nav-pills .show>.nav-link {
            background-color: #00dfc2 !important;
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
                    <a href="{{ url('fatty/main/admin/rider_billing/data_list/'.$rider_id) }}" class="nav-link" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px;">Offered List</a>
                </li>
                <li class="nav-item col btn">
                    <a class="nav-link active" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px;" id="history-tab" data-toggle="pill" href="#history" role="tab" aria-controls="history" aria-selected="true">History</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="container mt-4" style="padding-left: 20px;padding-right:20px;height:100%;" id="tabcontent">
    <div class="row tab-pane fade show active" id="history" role="tabpanel" aria-labelledby="history-tab">
        <div class="col">
            <form action="{{ route('rider_billing_history.search',$rider_id) }}">
                <input type="date" name="current_date" placeholder="mm/dd/yyyy" class="btn mb-1" style="background-color:#FFFFFF;width: 45%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                <button type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;">Search</button>
            </form>
        </div>
        @if($check)
            @foreach ($rider_payment as $value)
                <a href="{{ url('fatty/main/admin/rider_billing/data_history/detail/'.$value->rider_payment_id) }}" class="col mt-3">
                    <div class="row" style="width: 100%;height:100%;background:#FFFFFF;color:#000000;font-size:15px;border-radius:5px;margin-left:1px;">
                        <div class="col-7" style="padding: 10px;">
                            {{ $value->payment_voucher }} ( {{ date('M Y',strtotime($value->start_offered_date)) }} )
                        </div>
                        <div class="col-4 text-right" style="padding: 10px;">
                            {{ $value->total_amount }} Ks
                        </div>
                        <div class="col-1" style="padding: 11px;">
                            <i class="fas fa-angle-right" style="font-size: 20px;color: rgb(74, 67, 67)"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            <div class="card-body mt-3" style="width: 100%;height:100%;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#00dfc2;background-color:#FFFFFF">
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
