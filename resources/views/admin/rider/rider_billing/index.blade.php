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
                        <a class="nav-link active" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" id="list-tab" data-toggle="pill" href="#list" role="tab" aria-controls="list" aria-selected="false">List</a>
                    </li>
                    <li class="nav-item col-md-4 btn">
                        {{-- <a class="nav-link" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" id="offered-tab" data-toggle="pill" href="#offered" role="tab" aria-controls="offered" aria-selected="true">Offered</a> --}}
                        <a class="nav-link" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" href="{{ url('fatty/main/admin/riders_billing/offered') }}">Offered</a>
                    </li>
                    <li class="nav-item col-md-4 btn">
                        {{-- <a class="nav-link" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" id="history-tab" data-toggle="pill" href="#history" role="tab" aria-controls="history" aria-selected="true">History</a> --}}
                        <a class="nav-link" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" href="{{ url('fatty/main/admin/riders_billing/history') }}">History</a>
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
    <div class="row tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <form action="{{ route('fatty.admin.riders_billing.list') }}" method="get">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 mt-1">
                                <input type="text" id="min" name="min" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('d-M-Y') }}">
                            </div>
                            <div class="col-md-3 mt-1">
                                <input type="text" id="max" name="max" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('d-M-Y') }}">
                            </div>
                            <div class="col-md-3 mt-1">
                                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                                    <i class="fa fa-search"></i> {{ __('filter') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="table-responsive">
                            <div class="mt-3">
                                {{ $order_rider->appends(request()->input())->links() }}
                            </div>
                            <table id="riders" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        {{-- <th></th> --}}
                                        <th>#Id.</th>
                                        <th class="text-left">RiderName</th>
                                        <th class="text-left">Start_Date</th>
                                        <th class="text-left">End_Date</th>
                                        <th>LastOffered</th>
                                        <th>Duration</th>
                                        <th class="text-left">TotalAmount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order_rider as $value)
                                    <tr class="text-center">
                                        {{-- <td></td> --}}
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $value->rider->rider_user_name }} (#{{ $value->rider_id }})</td>
                                        <td>{{ date('d/M/Y', strtotime($from_date)) }}</td>
                                        <td>{{ date('d/M/Y', strtotime($to_date)) }}</td>
                                        <td>{{ $value->last_offered_date }}</td>
                                        <td>{{ $value->duration }} days </td>
                                        <td class="text-left">{{ number_format($value->total_amount) }}</td>
                                        <td>
                                            <a href="{{ url('fatty/main/admin/riders_billing/detail','[{"rider_id":'.$value->rider_id.',"total_amount":'.$value->total_amount.',"start_date":"'.$from_date.'","end_date":"'.$to_date.'","duration":'.$value->duration.',"type":"list","payment_voucher":""}]') }}" class="btn btn-sm btn-info mr-1" style="width: 80px;">Detali</a>
                                            <a href="{{ url('fatty/main/admin/riders_billing/store','[{"rider_id":'.$value->rider_id.',"total_amount":'.$value->total_amount.',"start_date":"'.$from_date.'","end_date":"'.$to_date.'","duration":'.$value->duration.'}]') }}" class="btn btn-sm btn-success" style="width: 80px;">Confirm</a>
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
    {{-- <div class="row tab-pane fade" id="offered" role="tabpanel" aria-labelledby="offered-tab">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" id="min" name="min" placeholder="Start Date">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="max" name="max" placeholder="End Date">
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>#Id.</th>
                                        <th class="text-left">RiderName</th>
                                        <th class="text-left">Start_Date</th>
                                        <th class="text-left">End_Date</th>
                                        <th>LastOffered</th>
                                        <th>Duration</th>
                                        <th class="text-left">TotalAmount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cus_order_offered as $value)
                                    <tr>
                                        <td></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $value->rider->rider_user_name }} (#{{ $value->rider_id }})</td>
                                        <td>{{ date('d/M/Y', strtotime($from_date)) }}</td>
                                        <td>{{ date('d/M/Y', strtotime($to_date)) }}</td>
                                        <td>{{ $value->last_offered_date }}</td>
                                        <td>{{ $value->duration }} days </td>
                                        <td class="text-left">{{ number_format($value->total_amount) }}</td>
                                        <td class="text-center">
                                            <p class="btn btn-sm btn-danger" style="width: 80px;">Call</p>
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
    <div class="row tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" id="min" name="min" placeholder="Start Date">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="max" name="max" placeholder="End Date">
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>#Id.</th>
                                        <th class="text-left">RiderName</th>
                                        <th class="text-left">Start_Date</th>
                                        <th class="text-left">End_Date</th>
                                        <th>LastOffered</th>
                                        <th>Duration</th>
                                        <th class="text-left">TotalAmount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cus_order_done as $value)
                                    <tr>
                                        <td></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $value->rider->rider_user_name }} (#{{ $value->rider_id }})</td>
                                        <td>{{ date('d/M/Y', strtotime($from_date)) }}</td>
                                        <td>{{ date('d/M/Y', strtotime($to_date)) }}</td>
                                        <td>{{ $value->last_offered_date }}</td>
                                        <td>{{ $value->duration }} days </td>
                                        <td class="text-left">{{ number_format($value->total_amount) }}</td>
                                        <td class="text-center">
                                            <p class="btn btn-sm btn-success" style="width: 80px;">Done</p>
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
    </div> --}}
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
