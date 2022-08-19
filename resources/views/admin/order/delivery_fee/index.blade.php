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
                    <li class="breadcrumb-item">Food Fees</li>
                    <li class="breadcrumb-item">Lists</li>
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
                        {{-- <div class="col-6">
                            <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#new_deli_fee"><i class="fa fa-plus-circle"></i> Add Deli Fees</a></button>
                            <form method="post" action="{{route('fatty.admin.food_order_delivery_fee.create')}}" id="form">
                                @csrf
                                <div class="modal fade" id="new_deli_fee" tabindex="-1" role="dialog" aria-labelledby="new_deli_fee" aria-hidden="true" style="text-align: left;">
                                    <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="new_deli_fee">"Add New Deli Fee"</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="distance" class="col-form-label">Distance :</label>
                                                <input type="text" value="0.00" class="form-control" name="distance" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="rider_delivery_fee" class="col-form-label">Rider Deli Fee :</label>
                                                <input type="text" value="0" class="form-control" name="rider_delivery_fee">
                                            </div>
                                            <div class="form-group">
                                                <label for="customer_delivery_fee" class="col-form-label">Customer Deli Fee :</label>
                                                <input type="text" value="0" class="form-control" name="customer_delivery_fee">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-sm">Create</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </form>
                        </div> --}}
                        <div class="col-12" style="text-align: left">
                            <h4><b>Food Delivery Fee Information</b></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="food_fees" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">No.</th>
                                    <th style="text-align: center">Distance(Km)</th>
                                    <th style="text-align: center">RiderDeliFee(Ks)</th>
                                    <th style="text-align:center">CustomerDeliFee(Ks)</th>
                                    <th style="text-align:center">Action</th>
                                </tr>
                            </thead>
                                @foreach ($food_fees as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->distance }}</td>
                                    <td>{{ $item->rider_delivery_fee }}</td>
                                    <td>{{ $item->customer_delivery_fee }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#q{{ $item->food_order_deli_fee_id }}"><i class="fa fa-edit"></i></button>
                                        <form method="post" action="{{route('fatty.admin.food_order_delivery_fee.update',$item->food_order_deli_fee_id)}}" id="form">
                                            @csrf
                                            <div class="modal fade" id="q{{ $item->food_order_deli_fee_id }}" tabindex="-1" role="dialog" aria-labelledby="new_deli_fee" aria-hidden="true" style="text-align: left;">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title" id="new_deli_fee">Distances "{{ $item->distance }}" Edit</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="distance" class="col-form-label">Distance :</label>
                                                            <input type="text" value="{{ $item->distance }}" class="form-control" name="distance" disabled>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="rider_delivery_fee" class="col-form-label">Rider Deli Fee :</label>
                                                            <input type="text" value="{{ $item->rider_delivery_fee }}" class="form-control" name="rider_delivery_fee">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="customer_delivery_fee" class="col-form-label">Customer Deli Fee :</label>
                                                            <input type="text" value="{{ $item->customer_delivery_fee }}" class="form-control" name="customer_delivery_fee">
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
