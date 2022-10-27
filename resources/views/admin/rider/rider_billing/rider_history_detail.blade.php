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
            min-height: 100%;
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

<div class="container mt-5" style="padding-left: 20px;padding-right:20px;" id="tabcontent">
    <div class="row tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
        <div class="col">
            <div class="card-body" style="width: 100%;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#00dfc2;background-color:#FFFFFF">
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
                                        <div class="col-6 text-right" style="font-size: 15px">{{ number_format($rider_payment->total_parcel_income) }} MMK</div>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4">
                                    <div class="row">
                                        <div class="col-6 text-left" style="font-size: 15px;">Total Food Income</div>
                                        <div class="col-6 text-right" style="font-size: 15px">{{ number_format($rider_payment->total_food_income) }} MMK</div>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4">
                                    <div class="row">
                                        <div class="col-6 text-left" style="font-size: 15px;">Total Income</div>
                                        <div class="col-6 text-right" style="font-size: 15px">{{ number_format($rider_payment->total_food_income + $rider_payment->total_parcel_income) }} MMK</div>
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
                                <td>{{ $rider_payment->payment_voucher }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-left" style="width:140px;">Parcel Benefit</th>
                                <td>{{ number_format($rider_payment->total_parcel_benefit_amount) }} MMK ( {{ $rider_payment->parcel_benefit }} % )</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-left" style="width:140px;">Food Benefit</th>
                                <td>{{ number_format($rider_payment->total_food_benefit_amount)}} MMK ( +{{ $rider_payment->food_benefit }} )</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-left" style="width:140px;">Parcel Order</th>
                                <td>{{ number_format($rider_payment->total_parcel_count)}}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-left" style="width:140px;">Food Order</th>
                                <td>{{ number_format($rider_payment->total_food_count)}}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-left" style="width:140px;">Peak Order</th>
                                <td>{{ $rider_payment->peak_food_order + $rider_payment->peak_parcel_order }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-left" style="width:140px;">Peak Time</th>
                                <td>{{ number_format($rider_payment->total_peak_amount)}} MMK</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-left" style="width:140px;">Reward</th>
                                <td>{{ number_format($rider_payment->total_amount)}} MMK</td>
                            </tr>
                            <tr>
                                <th scope="row" colspan="1" class="text-left" style="font-size: 18px;">Total Order</th>
                                <th>{{ number_format($rider_payment->total_count) }}</th>
                            </tr>
                            <tr>
                                <th scope="row" colspan="1" class="text-left" style="font-size: 18px;">Total Amount</th>
                                <th>{{ number_format($rider_payment->total_amount) }} MMK</th>
                            </tr>
                        </tbody>
                    </table>   
                </div>
                <div class="row">
                    <div class="col text-center" style="margin-top:10px;margin-bottom:10px;">
                        <a href="{{ url()->previous() }}"  class="btn btn-sm" style="width: 80%;background-color:red;color:#FFFFFF;font-weight:510;">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.layouts._partial.script')
</body>
</html>
