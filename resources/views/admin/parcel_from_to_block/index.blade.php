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
                <label for="state_id" class="col-form-label">{{ __('တိုင်း / ပြည်နယ်') }}</label>
                <select class="form-control" name="state_id" id="state_id" required>
                    <option value="">တိုင်း/ပြည်နယ်</option>
                    @foreach($states as $st)
                        <option value="{{$st->state_id}}">{{$st->state_name_mm}} ( {{ $st->state_name_en }} )</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="city_id" class="col-form-label">{{ __('မြို့နယ်') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                <select id="city_id" class="form-control @error('city_id') is-invalid @enderror" name="city_id" value="{{ old('city_id') }}" autocomplete="city_id" autofocus>
                </select>
                @error('city_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="parcel_from_block_id" class="col-form-label">From Block Name:</label>
                <select type="text" class="form-control" id="parcel_from_block_id" name="parcel_from_block_id">
                    {{-- <option value="">Choose From Block Name</option>
                    @foreach ($blocks as $value)
                        <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="form-group">
                <label for="parcel_to_block_id" class="col-form-label">To Block Name:</label>
                <select type="text" class="form-control" id="parcel_to_block_id" name="parcel_to_block_id">
                    {{-- <option value="">Choose To Block Name</option>
                    @foreach ($blocks as $value)
                        <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="form-group">
                <label for="delivery_fee" class="col-form-label">Customer Deli Fee</label>
                <input type="number" id="customer_delivery_fee" value="0" class="form-control" name="delivery_fee">
            </div>
            <div class="form-group">
                <label for="percentage" class="col-form-label">Percentage</label>
                <input type="number" id="percentage" value="0" class="form-control" name="percentage" onchange="calDeli()">
            </div>
            <div class="form-group">
                <label for="rider_delivery_fee" class="col-form-label">Rider Deli Fee</label>
                <input type="number" id="rider_delivery_fee" value="0" class="form-control" name="rider_delivery_fee">
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
                <div class="card-body table-responsive">
                    {{ $parcel_from_to_block->appends(request()->input())->links() }}
                    <table id="parcel_from_to_block" class="table table-bordered table-striped table-hover display nowrap" border="0" cellspacing="5" cellpadding="5">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-left">From</th>
                                <th class="text-left">To</th>
                                <th class="text-center">CusDeliFee</th>
                                <th class="text-center">RiderDeliFee</th>
                                <th class="text-center">Percentage</th>
                                <th class="text-center">Remark</th>
                                <th class="text-center">CityName</th>
                                <th class="text-center">StateName</th>
                                <th class="text-center">Edit</th>
                                <th class="text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parcel_from_to_block as $parcel)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">
                                    @if ($parcel->from_block)
                                        {{ $parcel->from_block->block_name_mm }} ( {{ $parcel->from_block->block_name_en }} )
                                    @else
                                        <span style="color: #990000">"Empty"</span>
                                    @endif
                                </td>
                                <td class="text-left">
                                    @if ($parcel->to_block)
                                        {{ $parcel->to_block->block_name_mm }} ( {{ $parcel->to_block->block_name_en }} )
                                    @else
                                        <span style="color: #990000">"Empty"</span>
                                    @endif
                                </td>
                                <td>{{ number_format($parcel->delivery_fee) }}</td>
                                <td>{{ number_format($parcel->rider_delivery_fee) }}</td>
                                <td>{{ $parcel->percentage }} {{ "%" }}</td>
                                <td>
                                    @if($parcel->remark)
                                        {{ $parcel->remark }}
                                    @else
                                        <span style="color: red">{{ "Empty" }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($parcel->from_block->cities)
                                        {{ $parcel->from_block->cities->city_name_mm }} ( {{ $parcel->from_block->cities->city_name_en }} )
                                    @else
                                        <span style="color: red">{{ "Empty" }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($parcel->from_block->states)
                                        {{ $parcel->from_block->states->state_name_mm }} ( {{ $parcel->from_block->states->state_name_en }} )
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
   $(document).ready(function() {
       var table = $("#parcel_from_to_block").DataTable({
           "lengthMenu": [[15,50, 100, 250, -1], [15,50,100,250, "All"]],
           "paging": false, // Allow data to be paged
           "lengthChange": false,
           "searching": false, // Search box and search function will be actived
           "info": false,
           "autoWidth": false,
           "processing": false,  // Show processing
        //    ajax: "/fatty/main/admin/parcel_from_to_block/dataTables/ajaxparcelfromtoblock",
        //    columns: [
        //    {data: 'DT_RowIndex', name: 'DT_RowIndex',className:"text-center" , orderable: false, searchable: false},
        //    {data: 'from_block_name', name:'from_block_name'},
        //    {data: 'to_block_name', name:'to_block_name'},
        //    {data: 'cus_delivery_fee', name:'cus_delivery_fee',className:"text-center"},
        //    {data: 'rider_delivery_fee', name:'rider_delivery_fee',className:"text-center"},
        //    {data: 'percentage', name:'percentage',className:"text-center"},
        //    {data: 'remark', name:'remark'},
        //    {data: 'action', name: 'action', orderable: false, searchable: false,className:'btn-group'},
        //    ],
        //    dom: 'lBfrtip',
        //    buttons: [
        //    'excel', 'pdf', 'print'
        //    ],
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
<script>
    function calDeli(){
        var cus_deli=document.getElementById("customer_delivery_fee").value;
        var percent=document.getElementById("percentage").value;
        var price=cus_deli*percent/100;
        document.getElementById("rider_delivery_fee").value=price;

    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#state_id').on('change', function(){
            $('#city_id').empty();
            var id = $(this).val();
            if(id){
                $.ajax({
                    type: 'get',
                    url: '/fatty/main/admin/city/list/'+id,
                    success: function(data){ 
                        $('#city_id').append(`<option value="">မြို့နယ်</option>`);
                        $.each(data, function(index,value) {
                            $('#city_id').append('<option value='+value.city_id+'>'+value.city_name_mm + ' ( '+value.city_name_en+' ) '+'</option>');
                        });
                    }
                });  
            }    
        }); 
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#city_id').on('change', function(){
            $('#parcel_from_block_id').empty();
            $('#parcel_to_block_id').empty();
            var city_id = $(this).val();
            if(city_id){
                $.ajax({
                    type: 'get',
                    url: '/fatty/main/admin/parcel_block/list/'+city_id,
                    success: function(data){ 
                        $('#parcel_from_block_id').append(`<option value="">Choose From Block Name</option>`);
                        $.each(data, function(index,value) {
                            $('#parcel_from_block_id').append('<option value='+value.parcel_block_id+'>'+value.block_name_mm + ' ( '+value.block_name_en+' ) '+'</option>');
                        });
                        $('#parcel_to_block_id').append(`<option value="">Choose To Block Name</option>`);
                        $.each(data, function(index,value) {
                            $('#parcel_to_block_id').append('<option value='+value.parcel_block_id+'>'+value.block_name_mm + ' ( '+value.block_name_en+' ) '+'</option>');
                        });
                    }
                });  
            }    
        }); 
    });
</script>
<script>
    $(function () {
        $('#state_id').select2({
            theme: 'bootstrap4'
        });
        $('#city_id').select2({
            theme: 'bootstrap4'
        });
    });
</script>
@endpush
