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
                    <li class="breadcrumb-item active">Order Block Route</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#new_start_order_block"><i class="fa fa-plus-circle"></i> New Block Route</button>
<form method="post" action="{{route('fatty.admin.order_block_route.store')}}" id="form">
@csrf
<div class="modal fade" id="new_start_order_block" tabindex="-1" role="dialog" aria-labelledby="new_start_order_block" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="new_start_order_block">New Block Route</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="order_start_block_id" class="col-form-label">Group Name:</label>
                <select class="form-control" name="order_start_block_id" id="order_start_block_id" required>
                    <option value="">Choose Group Name</option>
                    @foreach($group_block as $block)
                        <option value="{{$block->order_start_block_id}}">{{ $block->start_block->block_name }}   <span style="font-weight: 800;font-size:20px;">&nbsp;- &nbsp;</span>   {{ $block->end_block->block_name }}</option>
                    @endforeach

                </select>
            </div>
            <div class="form-group">
                <label for="start_block_id" class="col-form-label">Start Block Name:</label>
                <select class="form-control" name="start_block_id" id="start_block_id" required>
                    <option value="">Choose Block Name</option>
                    @foreach($parcel_block as $block)
                        <option value="{{$block->parcel_block_id}}">{{$block->block_name}}</option>
                    @endforeach

                </select>
            </div>
            <div class="form-group">
                <label for="end_block_id" class="col-form-label">End Block Name:</label>
                <select class="form-control" name="end_block_id" id="end_block_id" required>
                    <option value="">Choose Block Name</option>
                    @foreach($parcel_block as $block)
                        <option value="{{$block->parcel_block_id}}">{{$block->block_name}}</option>
                    @endforeach

                </select>
            </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create</button>
      </div>
    </div>
  </div>
</div>
</form>

<section class="content mt-1">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    {{ $order_block->appends(request()->input())->links() }}
                    <table id="order_block" class="table table-bordered table-striped table-hover display nowrap table-sm" border="0" cellspacing="5" cellpadding="5">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-left">GroupName</th>
                                <th class="text-left">StartBlock</th>
                                <th class="text-left">EndBlock</th></th>
                                <th class="text-center">Edit</th>
                                <th class="text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order_block as $item)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left"> {{ $item->group_block->start_block->block_name }}   <span style="font-weight: 800;font-size:20px;">&nbsp;- &nbsp;</span>   {{ $item->group_block->end_block->block_name }} </td>
                                <td class="text-left"> {{ $item->start_block->block_name }} </td>
                                <td class="text-left"> {{ $item->end_block->block_name }} </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#q{{ $item->order_route_block_id }}"><i class="fa fa-edit"></i></button>
                                    <form method="post" action="{{route('fatty.admin.order_block_route.update',$item->order_route_block_id)}}" id="form">
                                    @csrf
                                    <div class="modal fade" id="q{{ $item->order_route_block_id }}" tabindex="-1" role="dialog" aria-labelledby="new_state" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h5 class="modal-title" id="new_state">New Block</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            </div>
                                            <div class="modal-body">
                                                <form>
                                                    <div class="form-group">
                                                        <label for="order_start_block_id" class="col-form-label">Group Name:</label>
                                                        <select class="form-control" name="order_start_block_id" id="order_start_block_id_edit" required>
                                                            <option value="{{$item->order_start_block_id}}">{{ $item->group_block->start_block->block_name }}   <span style="font-weight: 800;font-size:20px;">&nbsp;- &nbsp;</span>   {{ $item->group_block->end_block->block_name }}</option>
                                                            @foreach($group_block as $block)
                                                                <option value="{{$block->order_start_block_id}}">{{ $block->start_block->block_name }}   <span style="font-weight: 800;font-size:20px;">&nbsp;- &nbsp;</span>   {{ $block->end_block->block_name }}</option>
                                                            @endforeach
                                        
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="start_block_id" class="col-form-label">Start Block Name:</label>
                                                        <select class="form-control" name="start_block_id" id="start_block_id_edit">
                                                            <option value="{{$item->start_block_id}}">{{$item->start_block->block_name}}</option>
                                                            @foreach($parcel_block as $value)
                                                                <option value="{{$value->parcel_block_id}}">{{$value->block_name}}</option>
                                                            @endforeach
                                        
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="end_block_id" class="col-form-label">End Block Name:</label>
                                                        <select class="form-control" name="end_block_id" id="end_block_id_edit">
                                                            <option value="{{$item->end_block_id}}">{{$item->end_block->block_name}}</option>
                                                            @foreach($parcel_block as $value)
                                                                <option value="{{$value->parcel_block_id}}">{{$value->block_name}}</option>
                                                            @endforeach
                                        
                                                        </select>
                                                    </div>
                                                </form>
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
                                <td>
                                    <form action="{{route('fatty.admin.order_block_route.destroy', $item->order_route_block_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
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
        $("#order_block").DataTable({
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
        $('#order_start_block_id').select2({
            theme: 'bootstrap4'
        });
        $('#order_start_block_id_edit').select2({
            theme: 'bootstrap4'
        });

    });
</script>
@endpush