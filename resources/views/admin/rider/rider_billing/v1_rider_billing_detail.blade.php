@extends('admin.layouts.master')

@section('css')
<style>
    tr>th ,td {
        text-align: center !important;
    }
    .nav-pills li>#list-tab.active,.nav-pills li>#offered-tab.active,.nav-pills li>#history-tab.active, .nav-pills .show>.nav-link {
        color: #fff !important;
        background-color: #00dfc2 !important;
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.4/css/select.dataTables.min.css">
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-7" style="height: 20px;">
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
                    <li class="breadcrumb-item active">Riders Billing</li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="tab-content">
    <div class="row tab-pane fade show active" id="history" role="tabpanel" aria-labelledby="history-tab">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @if ($type != "list")
                            <div class="col-6 text-left">
                                <h5> <b> Title: &nbsp;  {{ \Carbon\Carbon::parse($start_date)->format('F Y') }} &nbsp; detail</b></h5>
                            </div>
                            <div class="col-6 text-right">
                                <h5> <b> InvoiceId: &nbsp;  {{ $payment_voucher }}</b></h5>
                            </div>
                        @else
                            <div class="col-12 text-center">
                                <h5> <b> Title: &nbsp;  {{ \Carbon\Carbon::parse($start_date)->format('F Y') }} &nbsp; detail</b></h5>
                            </div>
                        @endif
                    </div>
                    @foreach ($rider_benefit as $value)
                        @if($value->is_target==1)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="4">
                                                <div class="row">
                                                    <div class="col-6 text-left" style="font-size: 16px;">Total Parcel Income</div>
                                                    <div class="col-6 text-right" style="font-size: 15px">{{ number_format($value->total_parcel_price) }} MMK</div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">
                                                <div class="row">
                                                    <div class="col-6 text-left" style="font-size: 16px;">Total Food Income</div>
                                                    <div class="col-6 text-right" style="font-size: 15px">{{ number_format($value->total_food_price) }} MMK</div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">
                                                <div class="row">
                                                    <div class="col-6 text-left" style="font-size: 18px;">Total Income Amount</div>
                                                    <div class="col-6 text-right" style="font-size: 16px">{{ number_format($value->total_food_price + $value->total_parcel_price) }} MMK</div>
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
                                            <th scope="row" class="text-left" style="width:170px;">Total Count</th>
                                            <td>{{ $value->total_order }}</td>
                                            <td style="width:170px;"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:170px;">Parcel Benefit</th>
                                            <td>{{ number_format($value->total_parcel_amount) }} MMK ( {{ $value->parcel_benefit }} % )</td>
                                            <td style="width:170px;"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:170px;">Food Benefit</th>
                                            <td>{{ number_format($value->total_food_amount)}} MMK ( +{{ $value->food_benefit }} )</td>
                                            <td style="width:170px;"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:170px;">Parcel Order</th>
                                            <td>{{ number_format($value->parcel_order)}}</td>
                                            <td style="width:170px;"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:170px;">Food Order</th>
                                            <td>{{ number_format($value->food_order)}}</td>
                                            <td style="width:170px;"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:170px;">Peak Order</th>
                                            <td>{{ $value->peak_food_order + $value->peak_parcel_order }}</td>
                                            <td style="width:170px;"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:170px;">Peak Time</th>
                                            <td>{{ number_format($value->total_peak_amount)}} MMK</td>
                                            <td style="width:170px;"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left" style="width:170px;">Reward</th>
                                            <td>{{ number_format($value->reward)}} MMK</td>
                                            <td style="width:170px;"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" colspan="2" class="text-center" style="font-size: 18px;">Total Order</th>
                                            <th>{{ number_format($value->total_order) }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="row" colspan="2" class="text-center" style="font-size: 18px;">Total Amount</th>
                                            <th>{{ number_format($value->reward) }} MMK</th>
                                        </tr>
                                    </tbody>
                                </table>   
                            </div>
                            <div class="row">
                                <div class="col-6 text-left">
                                    @if(url()->previous()==url()->current())
                                        <a href="{{ url('fatty/main/admin/v1/riders_billing/list') }}"  class="btn btn-sm btn-danger" style="color:#FFFFFF;font-weight:510;"><< Back</a>
                                    @else
                                        <a href="{{ url()->previous() }}"  class="btn btn-sm btn-danger" style="color:#FFFFFF;font-weight:510;"><< Back</a>
                                    @endif
                                </div>
                                <div class="col-6 text-right">
                                    @if ($type=="list")
                                        <a href="{{ url('fatty/main/admin/riders_billing/store','[{"rider_id":'.$rider_id.',"parcel_benefit":'.$value->parcel_benefit.',"food_benefit":'.$value->food_benefit.',"total_parcel_income":'.$value->total_parcel_price.',"total_food_income":'.$value->total_food_price.',"total_amount":'.$value->reward.',"total_parcel_benefit_amount":'.$value->total_parcel_amount.',"total_food_benefit_amount":'.$value->total_food_amount.',"total_peak_amount":'.$value->total_peak_amount.',"total_count":'.$value->total_order.',"total_food_count":'.$value->food_order.',"total_parcel_count":'.$value->parcel_order.',"peak_food_order":'.$value->peak_food_order.',"peak_parcel_order":'.$value->peak_parcel_order.',"start_date":"'.$start_date.'","end_date":"'.$end_date.'","duration":'.$duration.'}]') }}" class="btn btn-sm btn-success" style="color:#FFFFFF;font-weight:510;" title="Confirm">Confirm <i class="fas fa-check-circle"></i></a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if(count($rider_benefit)==0)
                        <div class="col-6 text-left">
                            @if(url()->previous()==url()->current())
                                <a href="{{ url('fatty/main/admin/v1/riders_billing/list') }}"  class="btn btn-sm btn-danger" style="color:#FFFFFF;font-weight:510;">Back</a>
                            @else
                                <a href="{{ url()->previous() }}"  class="btn btn-sm btn-danger" style="color:#FFFFFF;font-weight:510;">Back</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 3000);
    $(document).ready(function() {
        $("#min").datepicker({ changeMonth: true, changeYear: true,dateFormat: 'dd-M-yy' });
        $("#max").datepicker({ changeMonth: true, changeYear: true, dateFormat: 'dd-M-yy' });
        $('#restaurants').DataTable( {
        // Create date inputs
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            } ],
            select: {
                style:'os',
                selector:'td:first-child'
            },
            order: [[ 1, 'asc' ]]
        } );
    } );
</script>
@endpush
