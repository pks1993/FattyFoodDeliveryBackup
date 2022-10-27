<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rider Billing</title>
    @include('admin.layouts._partial.css')
    <style>
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
                    <a class="nav-link active" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px;" id="list-tab" data-toggle="pill" href="#list" role="tab" aria-controls="list" aria-selected="false">Offered List</a>
                </li>
                <li class="nav-item col btn">
                    <a href="{{ url('fatty/main/admin/rider_billing/data_history/'.$rider_id) }}" class="nav-link" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px;">History</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="container mt-5 mb-5" style="padding-left: 20px;padding-right:20px;" id="tabcontent">
    <div class="row tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
        <div class="col">
            @if($check)
                @foreach ($rider_payments as $value)
                    @if($value)
                        <div class="card-body mt-4" style="width: 100%;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#00dfc2;background-color:#FFFFFF">
                            <div class="col text-center" style="font-size: 20px;font-weight:510">
                                Comfirmation!
                            </div>
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="4">
                                                <div class="row">
                                                    <div class="col-6 text-left" style="font-size: 15px;">Total Parcel Income</div>
                                                    <div class="col-6 text-right" style="font-size: 15px">{{ number_format($value->total_parcel_income) }} MMK</div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">
                                                <div class="row">
                                                    <div class="col-6 text-left" style="font-size: 15px;">Total Food Income</div>
                                                    <div class="col-6 text-right" style="font-size: 15px">{{ number_format($value->total_food_income) }} MMK</div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">
                                                <div class="row">
                                                    <div class="col-6 text-left" style="font-size: 15px;">Total Income</div>
                                                    <div class="col-6 text-right" style="font-size: 15px">{{ number_format($value->total_food_income + $value->total_parcel_income) }} MMK</div>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:140px;">InvoiceId</th>
                                            <td>{{ $value->payment_voucher }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:140px;">Parcel Benefit</th>
                                            <td>{{ number_format($value->total_parcel_benefit_amount) }} MMK ( {{ $value->parcel_benefit }} % )</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:140px;">Food Benefit</th>
                                            <td>{{ number_format($value->total_food_benefit_amount)}} MMK ( +{{ $value->food_benefit }} )</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:140px;">Parcel Order</th>
                                            <td>{{ number_format($value->total_parcel_count)}}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:140px;">Food Order</th>
                                            <td>{{ number_format($value->total_food_count)}}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:140px;">Peak Order</th>
                                            <td>{{ $value->peak_food_order + $value->peak_parcel_order }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:140px;">Peak Time</th>
                                            <td>{{ number_format($value->total_peak_amount)}} MMK</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:140px;">Reward</th>
                                            <td>{{ number_format($value->total_amount)}} MMK</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" colspan="1" class="text-left" style="font-size: 18px;">Total Order</th>
                                            <th>{{ number_format($value->total_count) }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="row" colspan="1" class="text-left" style="font-size: 18px;">Total Amount</th>
                                            <th>{{ number_format($value->total_amount) }} MMK</th>
                                        </tr>
                                    </tbody>
                                </table>   
                            </div>
                            <div class="row">
                                <div class="col text-center" style="margin-top:10px;margin-bottom:10px;">
                                    <a href="{{ url('fatty/main/admin/rider_billing/update',$value->rider_payment_id) }}"  class="btn btn-sm" style="width: 80%;background-color:#00dfc2;color:#FFFFFF;font-weight:510;">Accept</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="card-body" style="width: 100%;height:200px;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#00dfc2;background-color:#FFFFFF">
                    <div class="col text-center" style="padding:70px 0px;">
                        <h4 style="font-weight:500;color:red;"> Empty Billing Voucher! </h4>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- <div class="container mt-5 mb-5" style="padding-left: 20px;padding-right:20px;" id="tabcontent">
    <div class="row tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
        <div class="col">
            @if($check)
                @foreach ($rider_payments as $rider_payment)
                    @if($rider_payment)
                        <div class="card-body mt-4" style="width: 100%;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#00dfc2;background-color:#FFFFFF">
                            <div class="col text-center" style="font-size: 20px;font-weight:510">
                                Comfirmation!
                            </div>
                            <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                                <div class="col" style="height: 10px;font-weight:510;">Voucher No.</div>
                                <div class="col text-right" style="height: 10px;">{{ $rider_payment->payment_voucher }}</div>
                                <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                            </div>
                            <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                                <div class="col" style="height: 10px;font-weight:510;">Start Date</div>
                                <div class="col text-right" style="height: 10px;">{{ date('d M Y', strtotime($rider_payment->start_offered_date)) }}</div>
                                <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                            </div>
                            <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                                <div class="col" style="height: 10px;font-weight:510;">End Date</div>
                                <div class="col text-right" style="height: 10px;">{{ date('d M Y', strtotime($rider_payment->last_offered_date)) }}</div>
                                <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                            </div>
                            <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                                <div class="col" style="height: 10px;font-weight:510;">Duration</div>
                                <div class="col text-right" style="height: 10px;">{{ $rider_payment->duration }} Days</div>
                                <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                            </div>
                            <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                                <div class="col" style="height: 10px;font-weight:510;">Amount</div>
                                <div class="col text-right" style="height: 10px;">{{ number_format($rider_payment->total_amount) }} ks</div>
                                <div class="col-12" style="height: 10px;"><hr style="border-top: 1px dashed black;"></div>
                            </div>
                            <div class="row" style="margin-right:20px;margin-left:20px;margin-top:20px;">
                                <div class="col" style="height: 10px;font-weight:510;">Total</div>
                                <div class="col text-right" style="height: 10px;">{{ number_format($rider_payment->total_amount) }} ks</div>
                            </div>
                            <div class="row">
                                <div class="col text-center" style="margin-top:50px;margin-bottom:10px;">
                                    <a href="{{ url('fatty/main/admin/rider_billing/update',$rider_payment->rider_payment_id) }}"  class="btn btn-sm" style="width: 80%;background-color:#00dfc2;color:#FFFFFF;font-weight:510;">Accept</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="card-body" style="width: 100%;height:200px;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#00dfc2;background-color:#FFFFFF">
                    <div class="col text-center" style="padding:70px 0px;">
                        <h4 style="font-weight:500;color:red;"> Empty Billing Voucher! </h4>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div> --}}
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
