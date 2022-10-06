@extends('admin.layouts.master')

@section('css')

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
                    <li class="breadcrumb-item active">Multi Order Limit</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
        </div>
    </div>
</section>
@if($count==0)
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#new_multi_order"><i class="fa fa-plus-circle"></i> New Multi Limit</button>
@endif
    <form method="post" action="{{route('fatty.admin.multi_order.store')}}" id="form">
    <div class="modal fade" id="new_multi_order" tabindex="-1" role="dialog" aria-labelledby="new_multi_order" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="new_multi_order">New Multi Limit</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="multi_order_limit" class="col-form-label">Limit Order:</label>
                    <input type="number" class="form-control" name="multi_order_limit" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="food_multi_order_time" class="col-form-label">Food Limit Time:</label>
                    <input type="number" class="form-control" name="food_multi_order_time" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="parcel_multi_order_time" class="col-form-label">Parcel Limit Time:</label>
                    <input type="number" class="form-control" name="parcel_multi_order_time" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="cancel_count_limit" class="col-form-label">Cancel Limit Count:</label>
                    <input type="number" class="form-control" name="cancel_count_limit" placeholder="0">
                </div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Create</button>
          </div>
        </div>
      </div>
    </div>
@csrf
</form>

<section class="content mt-1">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="multi_order" class="table table-bordered table-striped table-hover display nowrap table-sm" border="0" cellspacing="5" cellpadding="5">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-left">Order Limit</th>
                                <th class="text-left">Food Limit Time</th>
                                <th class="text-left">Parcel Limit Time</th>
                                <th class="text-left">Cancel Limit</th>
                                <th class="text-center">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($count != 0)
                            <tr class="text-center">
                                <td>{{ $multi_order->multi_order_limit_id }}</td>
                                <td>{{ $multi_order->multi_order_limit }}</td>
                                <td>{{ $multi_order->food_multi_order_time }}</td>
                                <td>{{ $multi_order->parcel_multi_order_time }}</td>
                                <td>{{ $multi_order->cancel_count_limit }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#q{{ $multi_order->multi_order_limit_id }}"><i class="fa fa-edit"></i></button>
                                    <form method="post" action="{{route('fatty.admin.multi_order.update',$multi_order->multi_order_limit_id)}}" id="form">
                                    @csrf
                                    <div class="modal fade" id="q{{ $multi_order->multi_order_limit_id }}" tabindex="-1" role="dialog" aria-labelledby="new_state" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h5 class="modal-title" id="new_state">New Block</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="multi_order_limit" class="col-form-label">Limit Order:</label>
                                                    <input type="number" class="form-control" name="multi_order_limit" value="{{ $multi_order->multi_order_limit }}" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="food_multi_order_time" class="col-form-label">Food Limit Time:</label>
                                                    <input type="number" class="form-control" name="food_multi_order_time" value="{{ $multi_order->food_multi_order_time }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="parcel_multi_order_time" class="col-form-label">Parcel Limit Time:</label>
                                                    <input type="number" class="form-control" name="parcel_multi_order_time" value="{{ $multi_order->parcel_multi_order_time }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="cancel_count_limit" class="col-form-label">Cancel Limit Count:</label>
                                                    <input type="number" class="form-control" name="cancel_count_limit" value="{{ $multi_order->cancel_count_limit }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    </form>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
    $(function () {
        $("#multi_order").DataTable({
            "lengthMenu": [[15,25,50, 100,500, -1], [15,25,50,100, 500, "All"]],
                "paging": false, // Allow data to be paged
                "lengthChange": false,
                "searching": false, // Search box and search function will be actived
                "info": false,
                "autoWidth": false,
                "processing": false,  // Show processing
        });
    });
</script>
<script type="text/javascript">
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
<script>
    $(function () {
        $('#start_block_id').select2({
            theme: 'bootstrap4'
        });
        $('#end_block_id').select2({
            theme: 'bootstrap4'
        });
        $('#start_block_id_edit').select2({
            theme: 'bootstrap4'
        });
        $('#end_block_id_edit').select2({
            theme: 'bootstrap4'
        });
    });
</script>
@endpush