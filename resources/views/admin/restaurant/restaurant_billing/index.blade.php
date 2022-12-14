@extends('admin.layouts.master')

@section('css')
<style>
    tr>th ,td {
        text-align: center !important;
    }
    .nav-pills li>#list-tab.active,.nav-pills li>#offered-tab.active,.nav-pills li>#history-tab.active, .nav-pills .show>.nav-link {
        color: #fff !important;
        background-color: greenyellow !important;
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
                        <a class="nav-link" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" id="offered-tab" data-toggle="pill" href="#offered" role="tab" aria-controls="offered" aria-selected="true">Offered</a>
                    </li>
                    <li class="nav-item col-md-4 btn">
                        <a class="nav-link" style="width: 100%;border-radius: 0%;background:grey;color: white;font-size:23px;" id="history-tab" data-toggle="pill" href="#history" role="tab" aria-controls="history" aria-selected="true">History</a>
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
                    <form action="{{ route('fatty.admin.restaurants_billing.list') }}" method="get">
                        @csrf
                        <div class="row">
                            <div class="col-md-2 mt-1">
                                <input type="text" style="width: 100%" id="min" name="min" placeholder="Start Date">
                            </div>
                            <div class="col-md-2 mt-1">
                                <input type="text" id="max" style="width: 100%" name="max" placeholder="End Date">
                            </div>
                            <div class="col-md-1 mt-1">
                                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                            <div class="col-md-2 mt-1">
                                <a href="{{ url("fatty/main/admin/restaurants_billing/list?min=$startday&max=$tenday") }}" class="btn btn-sm btn-info">{{ "This Month 1-10 Days" }}</a>
                            </div>
                            <div class="col-md-2 mt-1">
                                <a href="{{ url("fatty/main/admin/restaurants_billing/list?min=$elevenday&max=$twentyday") }}" class="btn btn-sm btn-info">{{ "This Month 11-20 Days" }}</a>
                            </div>
                            <div class="col-md-3 mt-1">
                                <a href="{{ url("fatty/main/admin/restaurants_billing/list?min=$twentyoneday&max=$lastday") }}" class="btn btn-sm btn-info">{{ "This Month 21-30/31 Days" }}</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="restaurants" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>#Id.</th>
                                        <th class="text-left">Restaurant</th>
                                        <th class="text-left">Start_Date</th>
                                        <th class="text-left">End_Date</th>
                                        <th>LastOffered</th>
                                        <th>Duration</th>
                                        <th class="text-left">Amonut</th>
                                        <th>Percentage</th>
                                        <th class="text-left">Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cus_order_list as $value)
                                    <tr>
                                        <td></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $value->restaurant->restaurant_name_mm }} ({{ $value->restaurant->restaurant_name_en }})</td>
                                        <td>{{ date('d/M/Y', strtotime($from_date)) }}</td>
                                        <td>{{ date('d/M/Y', strtotime($to_date)) }}</td>
                                        <td>{{ $value->last_offered_date }}</td>
                                        <td>{{ $value->duration }} days </td>
                                        <td class="text-left">{{ $value->total_amount }}</td>
                                        <td>{{ $value->percentage }} %</td>
                                        <td class="text-left">{{ $value->pay_amount }}</td>
                                        <td class="text-center">
                                            <a href="{{ url('fatty/main/admin/restaurants_billing/store','[{"restaurant_id":'.$value->restaurant_id.',"total_amount":'.$value->total_amount.',"percentage":'.$value->percentage.',"pay_amount":'.$value->pay_amount.',"start_date":"'.$from_date.'","end_date":"'.$to_date.'","duration":'.$value->duration.'}]') }}" class="btn btn-sm btn-danger" style="width: 80px;">Confirm</a>
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
    <div class="row tab-pane fade" id="offered" role="tabpanel" aria-labelledby="offered-tab">
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
                                        <th class="text-left">Restaurant</th>
                                        <th>LastOffered</th>
                                        <th>Duration</th>
                                        <th class="text-left">Amonut</th>
                                        <th>Percentage</th>
                                        <th class="text-left">Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cus_order_offered as $value)
                                    <tr>
                                        <td></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $value->restaurant->restaurant_name_mm }} ({{ $value->restaurant->restaurant_name_en }})</td>
                                        <td>{{ date('d M Y', strtotime($value->last_offered_date)) }}</td>
                                        <td>{{ $value->duration }} days </td>
                                        <td class="text-left">{{ $value->total_amount }}</td>
                                        <td>{{ $value->percentage }} %</td>
                                        <td class="text-left">{{ $value->pay_amount }}</td>
                                        <td class="text-center">
                                            {{-- <a href="{{ route('fatty.admin.restaurants_billing.update',$value->restaurant_payment_id) }}" class="btn btn-sm btn-danger" style="width: 80px;">Call</a> --}}
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
                                        <th class="text-left">Restaurant</th>
                                        <th>LastOffered</th>
                                        <th>Duration</th>
                                        <th class="text-left">Amonut</th>
                                        <th>Percentage</th>
                                        <th class="text-left">Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cus_order_done as $value)
                                    <tr>
                                        <td></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $value->restaurant->restaurant_name_mm }} ({{ $value->restaurant->restaurant_name_en }})</td>
                                        <td>{{ date('d M Y', strtotime($value->last_offered_date)) }}</td>
                                        <td>{{ $value->duration }} days </td>
                                        <td class="text-left">{{ $value->total_amount }}</td>
                                        <td>{{ $value->percentage }} %</td>
                                        <td class="text-left">{{ $value->pay_amount }}</td>
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
