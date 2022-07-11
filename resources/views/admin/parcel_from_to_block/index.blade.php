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
                    <li class="breadcrumb-item active">Parcel FromTo Block</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#new_state"><i class="fa fa-plus-circle"></i> New Parcel State</button>
<form method="post" action="{{route('fatty.admin.parcel_from_to_block.store')}}" id="form">
@csrf
<div class="modal fade" id="new_state" tabindex="-1" role="dialog" aria-labelledby="new_state" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="new_state">New FromTo Parcel Block</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label for="parcel_from_block_id" class="col-form-label">From Block Name:</label>
            <select type="text" class="form-control" id="parcel_from_block_id" name="parcel_from_block_id">
                <option value="">Choose From Block Name</option>
                @foreach ($blocks as $value)
                    <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="parcel_to_block_id" class="col-form-label">To Block Name:</label>
            <select type="text" class="form-control" id="parcel_to_block_id" name="parcel_to_block_id">
                <option value="">Choose To Block Name</option>
                @foreach ($blocks as $value)
                    <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="delivery_fee" class="col-form-label">Deli Fee</label>
            <input type="number" value="0" class="form-control" name="delivery_fee">
          </div>
          <div class="form-group">
            <label for="remark" class="col-form-label">Remark</label>
            <textarea type="text" class="form-control" name="remark"></textarea>
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
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane table-responsive active" id="Admin">
                            {{-- <table border="0" cellspacing="5" cellpadding="5">
                                <tbody>
                                    <tr>
                                        <td>Minimum date:</td>
                                        <td><input type="text" id="min" value="{{ now()->format('d-M-Y') }}" name="min"></td>
                                    </tr>
                                    <tr>
                                        <td>Maximum date:</td>
                                        <td><input type="text" id="max" value="{{ now()->format('d-M-Y') }}" name="max"></td>
                                    </tr>
                                </tbody>
                            </table> --}}
                            <table id="parcel_from_to_block" class="table table-bordered table-striped table-hover display nowrap" border="0" cellspacing="5" cellpadding="5">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-left">From</th>
                                        <th class="text-left">To</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Remark</th>
                                        {{-- <th class="text-center">Date</th> --}}
                                        <th class="text-center">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach($parcel_from_to_block as $parcel)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $parcel->from_block->block_name }}</td>
                                        <td class="text-left">{{ $parcel->to_block->block_name }}</td>
                                        <td>{{ $parcel->delivery_fee }}</td>
                                        <td>
                                            @if($parcel->remark)
                                                {{ $parcel->remark }}
                                            @else
                                                <span style="color: red">{{ "Empty" }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#q{{ $parcel->parcel_from_to_block_id }}"><i class="fa fa-edit"></i></button>
                                            <form method="post" action="{{route('fatty.admin.parcel_from_to_block.update',$parcel->parcel_from_to_block_id)}}" id="form">
                                                @csrf
                                                <div class="modal fade" id="q{{ $parcel->parcel_from_to_block_id }}" tabindex="-1" role="dialog" aria-labelledby="update_state" aria-hidden="true" style="text-align: left;">
                                                  <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                      <div class="modal-header">
                                                        <h5 class="modal-title" id="update_state">Update Parcel State</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                          <span aria-hidden="true">&times;</span>
                                                        </button>
                                                      </div>
                                                      <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="parcel_from_block_id" class="col-form-label">From Block Name:</label>
                                                            <select type="text" class="form-control" id="parcel_from_block_id_edit" name="parcel_from_block_id">
                                                                <option value="{{ $parcel->parcel_from_block_id }}">{{ $parcel->from_block->block_name }}</option>
                                                                @foreach ($blocks as $value)
                                                                    @if($value->parcel_block_id!=$parcel->parcel_from_block_id)
                                                                        <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                          </div>
                                                          <div class="form-group">
                                                            <label for="parcel_to_block_id" class="col-form-label">To Block Name:</label>
                                                            <select type="text" class="form-control" id="parcel_to_block_id_edit" name="parcel_to_block_id">
                                                                <option value="{{ $parcel->parcel_to_block_id }}">{{ $parcel->to_block->block_name }}</option>
                                                                @foreach ($blocks as $value)
                                                                    @if($value->parcel_block_id!=$parcel->parcel_to_block_id)
                                                                        <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                          </div>
                                                          <div class="form-group">
                                                            <label for="delivery_fee" class="col-form-label">Deli Fee</label>
                                                            <input type="number" value="{{ $parcel->delivery_fee }}" class="form-control" name="delivery_fee">
                                                          </div>
                                                          <div class="form-group">
                                                            <label for="remark" class="col-form-label">Remark</label>
                                                            <textarea type="text" class="form-control" name="remark">{{ $parcel->remark }}</textarea>
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
                                        <td>
                                            <form action="{{route('fatty.admin.parcel_from_to_block.destroy', $parcel->parcel_from_to_block_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach --}}

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
   $(document).ready(function() {
       var table = $("#parcel_from_to_block").DataTable({
           "lengthMenu": [[15,50, 100, 250, -1], [15,50,100,250, "All"]],
           "paging": true, // Allow data to be paged
           "lengthChange": true,
           "searching": true, // Search box and search function will be actived
           "info": true,
           "autoWidth": true,
           "processing": true,  // Show processing
           ajax: "/fatty/main/admin/parcel_from_to_block/dataTables/ajaxparcelfromtoblock",
           columns: [
           {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
           {data: 'from_block_name', name:'from_block_name'},
           {data: 'to_block_name', name:'to_block_name'},
           {data: 'price', name:'price'},
           {data: 'remark', name:'remark'},
        //    {data: 'created_date', name:'created_date'},
           {data: 'action', name: 'action', orderable: false, searchable: false,className:'btn-group'},
           ],
           dom: 'lBfrtip',
           buttons: [
           'excel', 'pdf', 'print'
           ],
       });
   });
   setTimeout(function() {
       $('#successMessage').fadeOut('fast');
   }, 3000);
</script>
<script>
    $(function () {
        $('#parcel_from_block_id').select2({
            theme: 'bootstrap4'
        });
        $('#parcel_to_block_id').select2({
            theme: 'bootstrap4'
        });
    });
</script>
@endpush
