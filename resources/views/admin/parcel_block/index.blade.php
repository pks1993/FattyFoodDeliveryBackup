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
                    <li class="breadcrumb-item active">Parcel Block</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#new_state"><i class="fa fa-plus-circle"></i> New Parcel State</button>
<form method="post" action="{{route('fatty.admin.parcel_block.store')}}" id="form">
@csrf
<div class="modal fade" id="new_state" tabindex="-1" role="dialog" aria-labelledby="new_state" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="new_state">New Parcel Block</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="block_name_mm" class="col-form-label">Block Name Myanmar:</label>
                <input type="text" class="form-control" name="block_name_mm" placeholder="Enter Block Name">
            </div>
            <div class="form-group">
                <label for="block_name_en" class="col-form-label">Block Name English:</label>
                <input type="text" class="form-control" name="block_name_en" placeholder="Enter Block Name">
            </div>
            <div class="form-group">
                <label for="block_name_ch" class="col-form-label">Block Name China:</label>
                <input type="text" class="form-control" name="block_name_ch" placeholder="Enter Block Name">
            </div>
            <div class="form-group">
                <label for="latitude" class="col-form-label">Latitude:</label>
                <input type="text" class="form-control" name="latitude" placeholder="21.123456">
            </div>
            <div class="form-group">
                <label for="longitude" class="col-form-label">Longitude:</label>
                <input type="text" class="form-control" name="longitude" placeholder="97.123456">
            </div>
            <div class="form-group">
                <label for="state_id" class="col-form-label">{{ __('တိုင်း / ပြည်နယ်') }}</label>
                <select class="form-control" name="state_id" id="state_id" required>
                    <option value="">တိုင်း/ပြည်နယ်</option>
                    @foreach($parcel_states as $st)
                        <option value="{{$st->state_id}}">{{$st->state_name_mm}} ( {{ $st->state_name_en }} )</option>
                    @endforeach

                </select>
            </div>
            <div class="form-group">
                <label for="city_id" class="col-md-12 col-form-label">{{ __('မြို့နယ်') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                <div class="col-md-12">
                    <select id="city_id" class="form-control @error('city_id') is-invalid @enderror" name="city_id" value="{{ old('city_id') }}" autocomplete="city_id" autofocus>
                    </select>
                    @error('city_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
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
                    {{ $parcel_block->appends(request()->input())->links() }}
                    <table id="parcel_block" class="table table-bordered table-striped table-hover display nowrap" border="0" cellspacing="5" cellpadding="5">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-left">BlockNameMM</th>
                                <th class="text-left">BlockNameEn</th>
                                <th class="text-left">BlockNameCh</th>
                                <th class="text-left">CityName</th>
                                <th class="text-left">StateName</th>
                                <th class="text-left">Latitude</th>
                                <th class="text-left">Longitude</th>
                                <th class="text-center">Edit</th>
                                <th class="text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parcel_block as $parcel)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $parcel->block_name_mm }}</td>
                                <td class="text-left">{{ $parcel->block_name_en }}</td>
                                <td class="text-left">{{ $parcel->block_name_ch }}</td>
                                <td class="text-left">
                                    @if($parcel->city_id)
                                        {{ $parcel->cities->city_name_mm }} ( {{ $parcel->cities->city_name_mm }} )
                                    @else
                                        {{ "Unknown" }}
                                    @endif
                                </td>
                                <td class="text-left">
                                    @if($parcel->state_id)
                                        {{ $parcel->states->state_name_mm }} ( {{ $parcel->states->state_name_en }} )
                                    @else
                                        {{ "Unknown" }}
                                    @endif
                                </td>
                                <td class="text-left">
                                    @if($parcel->latitude)
                                        {{ $parcel->latitude }}
                                    @else
                                        {{ "0.00" }}
                                    @endif
                                </td>
                                <td class="text-left">
                                    @if($parcel->longitude)
                                        {{ $parcel->longitude }}
                                    @else
                                        {{ "0.00" }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('fatty/main/admin/parcel_block/edit',$parcel->parcel_block_id) }}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>
                                    {{-- <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#q{{ $parcel->parcel_block_id }}"><i class="fa fa-edit"></i></button> --}}
                                    {{-- <form method="post" action="{{route('fatty.admin.parcel_block.update',$parcel->parcel_block_id)}}" id="form">
                                    @csrf
                                    <div class="modal fade" id="q{{ $parcel->parcel_block_id }}" tabindex="-1" role="dialog" aria-labelledby="new_state" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h5 class="modal-title" id="new_state">New Parcel State</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="block_name" class="col-form-label">Block Name:</label>
                                                    <input type="text" class="form-control" value="{{ $parcel->block_name }}" name="block_name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="latitude" class="col-form-label">Latitude:</label>
                                                    <input type="text" class="form-control" name="latitude" value="{{ $parcel->latitude }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="longitude" class="col-form-label">Longitude:</label>
                                                    <input type="text" class="form-control" name="longitude" value="{{ $parcel->longitude }}">
                                                </div>
                                                <div class="form-group row">
                                                    <label for="state_id" class="col-md-12 col-form-label">{{ __('တိုင်း / ပြည်နယ်') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <select id="state_id_edit" style="width: 100%;" class="form-control @error('state_id') is-invalid @enderror" name="state_id" value="{{ old('state_id') }}" autocomplete="state_id" required="true" autofocus>
                                                            <option value="{{ $parcel->state_id }}">{{ $parcel->states->state_name_mm }} ( {{ $parcel->states->state_name_en }} )</option>
                                                            @foreach($parcel_states as $state)
                                                                <option value="{{ $state->state_id }}">{{ $state->state_name_mm }} ( {{ $state->state_name_en }} )</option>
                                                            @endforeach
                                                        </select>
                                                        @error('state_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="city_id" class="col-md-12 col-form-label">{{ __('မြို့နယ်') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <select id="city_id_edit" class="form-control @error('city_id') is-invalid @enderror" name="city_id" value="{{ old('city_id') }}" autocomplete="city_id" required="true" autofocus>
                                                            <option value="{{ $parcel->city_id }}">{{ $parcel->cities->city_name_mm }} ( {{ $parcel->cities->city_name_en }} )</option>
                                                            @foreach($parcel_cities as $value)
                                                                @if($value->state_id==$parcel->state_id)
                                                                    <option value="{{ $value->city_id }}">{{ $value->city_name_mm }} ( {{ $value->city_name_en }} )</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @error('city_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    </form> --}}
                                </td>
                                <td>
                                    <form action="{{route('fatty.admin.parcel_block.destroy', $parcel->parcel_block_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
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
        $("#parcel_block").DataTable({
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
        $('#state_id').select2({
            theme: 'bootstrap4'
        });
        $('#city_id').select2({
            theme: 'bootstrap4'
        });
    });
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
        $('#state_id_edit').on('change', function(){
            $('#city_id_edit').empty();
            var id = $(this).val();
            if(id){
                $.ajax({
                    type: 'get',
                    url: '/fatty/main/admin/city/list/'+id,
                    success: function(data){ 
                        $('#city_id_edit').append(`<option value="">မြို့နယ်</option>`);
                        $.each(data, function(index,value) {
                            $('#city_id_edit').append('<option value='+value.city_id+'>'+value.city_name_mm + ' ( '+value.city_name_en+' ) '+'</option>');
                        });
                    }
                });  
            }    
        }); 
    });
</script>
@endpush
