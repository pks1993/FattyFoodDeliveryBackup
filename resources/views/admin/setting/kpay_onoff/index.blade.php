@extends('admin.layouts.master')

@section('css')
<style>
    form>.fa {
        display: none;
    }
    .dt-buttons>button{
        border-radius: revert;
        margin-top: 15px;
        margin-right: 5px;
    }
    .dataTables_length >label {
        margin-right: 15px !important;
        margin-top: 15px;
    }
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
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
                    <li class="breadcrumb-item active">Setting</li>
                    <li class="breadcrumb-item active">Kpay Lists</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <h4><b>Android Payment Method</b></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="payment_data" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">No.</th>
                                    <th style="text-align: center">Method Name</th>
                                    <th style="text-align:center">OnOff Android</th>
                                    <th style="text-align:center">Edit</th>
                                </tr>
                            </thead>
                                @foreach ($payment_data as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->payment_method_name }}</td>
                                    <td>
                                        @if($item->on_off_status==1)
                                            <a href="{{ url('fatty/main/admin/kpay_onoff_android/update/'.$item->payment_method_id) }}" title="Open Payment" onclick="return confirm('Are you sure want to close payment?')" class="btn btn-sm btn-success">{{ "On" }}</a>
                                        @else
                                            <a href="{{ url('fatty/main/admin/kpay_onoff_android/update/'.$item->payment_method_id) }}" title="Close Payment" onclick="return confirm('Are you sure want to open payment?')" class="btn btn-sm btn-danger">{{ "Off" }}</a>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#q{{ $item->payment_method_id }}"><i class="fa fa-edit"></i></button>
                                        <form method="post" action="{{route('fatty.admin.kpay.update',$item->payment_method_id)}}" id="form">
                                            @csrf
                                            <div class="modal fade" id="q{{ $item->payment_method_id }}" tabindex="-1" role="dialog" aria-labelledby="new_state" aria-hidden="true" style="text-align: left;">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title" id="new_state">"{{ $item->payment_method_name }}" Edit</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="payment_method_name" class="col-form-label">Payment Method Name:</label>
                                                            <input type="text" class="form-control" value="{{ $item->payment_method_name }}" name="payment_method_name">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="on_off_status" class="col-form-label">OnOff Android:</label>
                                                            <select class="form-control" name="on_off_status" id="on_off_status">
                                                                @if($item->on_off_status==0)
                                                                    <option value="0">Off</option>
                                                                    <option value="1">On</option>
                                                                @else
                                                                    <option value="1">On</option>
                                                                    <option value="0">Off</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <h4><b>iOS Payment Method</b></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="close_payment_data" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">No.</th>
                                    <th style="text-align: center">Method Name</th>
                                    <th style="text-align: center">iOS Version</th>
                                    <th style="text-align: center">Edit</th>
                                </tr>
                            </thead>
                                @foreach ($close_payment_data as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ "KBZPay"}}</td>
                                    <td>{{ $item->version }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#e{{ $item->payment_method_close_id }}"><i class="fa fa-edit"></i></button>
                                        <form method="post" action="{{route('fatty.admin.kpay.update',$item->payment_method_close_id)}}" id="form">
                                            @csrf
                                            <div class="modal fade" id="e{{ $item->payment_method_close_id }}" tabindex="-1" role="dialog" aria-labelledby="new_state" aria-hidden="true" style="text-align: left;">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title" id="new_state">"{{ $item->payment_method_name }}" Edit</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="payment_method_name1" class="col-form-label">Payment Method Name:</label>
                                                            <input type="text" class="form-control" value="{{ "KBZPay" }}" disabled>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="version" class="col-form-label">iOS Version:</label>
                                                            <input type="text" class="form-control" value="{{ $item->version }}" name="version">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            <tbody>
                            </tbody>
                        </table>
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
    }, 2000);
</script>
@endpush
