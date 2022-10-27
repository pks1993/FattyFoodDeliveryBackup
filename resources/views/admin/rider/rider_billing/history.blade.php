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
        <div class="row mb-3">
            <div class="col-md-12">
                <ul class="nav nav-pills">
                    <li class="nav-item col-md-4 btn">
                        {{-- <a class="nav-link active" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" id="list-tab" data-toggle="pill" href="#list" role="tab" aria-controls="list" aria-selected="false">List</a> --}}
                        <a class="nav-link" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" href="{{ url('fatty/main/admin/v1/riders_billing/list') }}">List</a>
                    </li>
                    <li class="nav-item col-md-4 btn">
                        {{-- <a class="nav-link active" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" id="offered-tab" data-toggle="pill" href="#offered" role="tab" aria-controls="offered" aria-selected="true">Offered</a> --}}
                        <a class="nav-link" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" href="{{ url('fatty/main/admin/riders_billing/offered') }}">Offered</a>
                    </li>
                    <li class="nav-item col-md-4 btn">
                        <a class="nav-link active" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" id="history-tab" data-toggle="pill" href="#history" role="tab" aria-controls="history" aria-selected="true">History</a>
                        {{-- <a class="nav-link" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" href="{{ url('fatty/main/admin/riders_billing/history') }}">History</a> --}}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<div class="col-md-8 offset-2 flash-message" id="successMessage">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))
    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
    @endif
    @endforeach
</div>
<section class="tab-content">
    <div class="row tab-pane fade show active" id="history" role="tabpanel" aria-labelledby="history-tab">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {{-- <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" id="min" name="min" placeholder="Start Date">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="max" name="max" placeholder="End Date">
                        </div>
                    </div> --}}
                    <div class="tab-content">
                        <div class="table-responsive">
                            <div class="mt-3">
                                {{ $cus_order_done->appends(request()->input())->links() }}
                            </div>
                            <table id="" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>#Id.</th>
                                        <th class="text-left">RiderName</th>
                                        <th>Voucher</th>
                                        <th>StartOffered</th>
                                        <th>LastOffered</th>
                                        <th>Duration</th>
                                        <th>TotalAmount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cus_order_done as $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $value->rider->rider_user_name }} (#{{ $value->rider_id }})</td>
                                        <td>{{ $value->payment_voucher }}</td>
                                        <td>{{ date('d/M/Y', strtotime($value->start_offered_date)) }}</td>
                                        <td>{{ date('d/M/Y', strtotime($value->last_offered_date)) }}</td>
                                        <td>{{ $value->duration }} days </td>
                                        <td>{{ number_format($value->total_amount) }} MMK</td>
                                        <td class="text-center">
                                            {{-- <a href="{{ url('fatty/main/admin/v1/riders_billing/detail','[{"rider_id":'.$value->rider_id.',"total_amount":'.$value->total_amount.',"start_date":"'.$value->start_offered_date.'","end_date":"'.$value->last_offered_date.'","duration":'.$value->duration.',"type":"offered","payment_voucher":"'.$value->payment_voucher.'"}]') }}" class="btn btn-sm btn-info mr-1" title="Detail"><i class="fas fa-eye"></i></a> --}}
                                            <a href="{{ url('fatty/main/admin/riders_billing/detail','[{"type":"history","rider_payment_id":"'.$value->rider_payment_id.'"}]') }}" class="btn btn-sm btn-info mr-1" title="Detail"><i class="fas fa-eye"></i></a>
                                            <a class="btn btn-sm btn-success" style="color:white" title="Done"><i class="fa fa-check-square-o" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
