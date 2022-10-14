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
                    <div class="table-responsive">
                        <div class="text-center">
                            <h5> <b> Title: &nbsp;  {{ \Carbon\Carbon::now()->format('F Y') }} &nbsp; detail</b></h5>
                        </div>
                        @foreach ($rider_benefit as $value)
                            @if($value->is_target==1)
                                <table class="table table-bordered">
                                    <thead>
                                        @if ($type != "list")
                                            <tr>
                                                <th colspan="4">
                                                    <div class="row">
                                                        <div class="col-6 text-left" style="font-size: 18px;">InvoiceId</div>
                                                        <div class="col-6 text-right" style="font-size: 18px">{{ $payment_voucher}}</div>
                                                    </div>
                                                </th>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th colspan="4">
                                                <div class="row">
                                                    <div class="col-6 text-left" style="font-size: 18px;">Total Order</div>
                                                    <div class="col-6 text-right" style="font-size: 18px">{{ $value->total_order }}</div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="col">Order</th>
                                            <th scope="col">Order Count</th>
                                            <th scope="col">Benefit</th>
                                            <th scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td scope="row">Food Order</td>
                                            <td>{{ $value->food_order }}</td>
                                            <td>{{ $value->food_benefit }}</td>
                                            <td>{{ number_format($value->total_food_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Parcel Order</td>
                                            <td>{{ $value->parcel_order }}</td>
                                            <td>{{ $value->parcel_benefit }}%</td>
                                            <td>{{ number_format($value->total_parcel_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Peak Time Order</td>
                                            <td>
                                                <div>{{ $value->peak_food_order }}F,</div>
                                                <div>{{ $value->peak_parcel_order }}P</div>
                                            </td>
                                            <td>
                                                <div>{{ $value->peak_time_amount }},</div>
                                                <div>{{ $value->peak_time_percentage }}%</div>
                                            </td>
                                            <td>{{ number_format($value->total_peak_amount)}}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" colspan="3" class="text-center" style="font-size: 18px;">Total Amount</th>
                                            <td>{{ number_format($total_amount1) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-6 text-left">
                            @if(url()->previous()==url()->current())
                                <a href="{{ url('fatty/main/admin/riders_billing/list') }}"  class="btn btn-sm btn-danger" style="color:#FFFFFF;font-weight:510;">Back</a>
                            @else
                                <a href="{{ url()->previous() }}"  class="btn btn-sm btn-danger" style="color:#FFFFFF;font-weight:510;">Back</a>
                            @endif
                        </div>
                        <div class="col-6 text-right">
                            @if ($type=="list")
                                <a href="{{ url('fatty/main/admin/riders_billing/store','[{"rider_id":'.$rider_id.',"total_amount":'.$value->reward.',"start_date":"'.$start_date.'","end_date":"'.$end_date.'","duration":'.$duration.'}]') }}" class="btn btn-sm btn-success" style="color:#FFFFFF;font-weight:510;">Confirm</a>
                            @endif
                        </div>
                    </div>
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
