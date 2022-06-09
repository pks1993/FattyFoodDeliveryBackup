<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <meta http-equiv="refresh" content="5; url=http://159.223.66.158/fatty/main/admin/riders_billing/offered"> --}}
    <title>Today Rider Billing</title>
    @include('admin.layouts._partial.css')
    <style>
        @@media print {
            @page {
                size: 7.24cm 5.3in;
                margin: 0;
            }
        }
    </style>
</head>
<body>

@foreach ($rider_payments as $rider_payment)
    <div class="container" style="padding-left: 0px;padding-right:0px;" id="tabcontent">
        <div class="row tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
            <div class="col-5" style="margin-bottom: 150px;">
                {{-- <div class="card-body" style="width: 100%;height:560px;color: #000000;font-size:15px;border-radius:5px;padding:10px;border-style:solid;border-width:2px;border-color:#00dfc2;background-color:#FFFFFF"> --}}
                <div class="card-body" style="width: 100%;height:560px;color: #000000;font-size:15px;border-radius:5px;padding:10px;background-color:#FFFFFF">
                    <div class="col text-center" style="font-size: 20px;font-weight:510">
                        <img src="{{asset('logo/user_logo.png')}}" style="width: 100px;height:100px;border-radius: 50%;" alt="">
                        {{-- <img src="{{asset('logo/user_logo.png')}}" style="width: 50px;height:50px;border-radius: 50%;" alt=""> --}}
                    </div>
                    <div class="col text-center" style="font-size: 20px;font-weight:510">
                        FATTY Food Delivery
                    </div>
                    <div class="row" style="padding-top:0px;margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Print On.</div>
                        <div class="col text-right" style="height: 10px;">{{ now() }}</div>
                    </div>
                    <div class="row" style="padding-top:8px;margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Voucher No.</div>
                        <div class="col text-right" style="height: 10px;">{{ $rider_payment->payment_voucher }}</div>
                    </div>
                    <div class="row" style="padding-top:8px;margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Name</div>
                        <div class="col text-right" style="height: 10px;">Si Thu Aung</div>
                    </div>
                    <div class="row" style="padding-top:8px;margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Start Date</div>
                        <div class="col text-right" style="height: 10px;">{{ date('d/m/Y', strtotime($rider_payment->start_offered_date)) }}</div>
                    </div>
                    <div class="row" style="padding-top:8px;margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">End Date</div>
                        <div class="col text-right" style="height: 10px;">{{ date('d/m/Y', strtotime($rider_payment->last_offered_date)) }}</div>
                    </div>
                    <div class="row" style="padding-top:8px;margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Order Qty</div>
                        <div class="col text-right" style="height: 10px;">100</div>
                    </div>
                    <div class="row" style="padding-top:8px;margin-right:20px;margin-left:20px;margin-top:20px;">
                        <div class="col" style="height: 10px;font-weight:510;">Deli Fee</div>
                        <div class="col text-right" style="height: 10px;">{{ number_format($rider_payment->total_amount) }} ks</div>
                        <div class="col-12" style="height: 10px;padding-top:25px;"><hr style="border-top: 1px dashed black;"></div>
                    </div>
                    <div class="row" style="padding-top:40px;margin-right:20px;margin-left:20px;margin-top:100px;">
                        <div class="col" style="height: 10px;font-weight:510;">Sign</div>
                        <div class="col text-right" style="height: 10px;">-----------------------------</div>
                    </div>
                    <div class="row" style="margin-bottom:30px;padding-top:50px;margin-right:20px;margin-left:20px;text-align:center;margin-bottom:50px;">
                        <div class="col" style="height: 10px;font-weight:510;">Hot Line- 09 123 456 789</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@include('admin.layouts._partial.script')
<script type="text/JavaScript">

    // window.print();

</script>

</body>
</html>
