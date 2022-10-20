@extends('admin.layouts.master')

@section('css')
@endsection
@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Parcel Block</li>
              <li class="breadcrumb-item active">Edit</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h2 class="card-title" style="font-size: 25px;"><b>Edit Block</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/parcel_block')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{route('fatty.admin.parcel_block.update',$parcel_block_id)}}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="block_name_mm" class="col-form-label">Block Name Myanmar:</label>
                                    <input type="text" class="form-control" value="{{ $parcel_block->block_name_mm }}" name="block_name">
                                </div>
                                <div class="form-group">
                                    <label for="block_name_en" class="col-form-label">Block Name English:</label>
                                    <input type="text" class="form-control" value="{{ $parcel_block->block_name_en }}" name="block_name">
                                </div>
                                <div class="form-group">
                                    <label for="block_name_ch" class="col-form-label">Block Name China:</label>
                                    <input type="text" class="form-control" value="{{ $parcel_block->block_name_ch }}" name="block_name">
                                </div>
                                <div class="form-group">
                                    <label for="latitude" class="col-form-label">Latitude:</label>
                                    <input type="text" class="form-control" name="latitude" value="{{ $parcel_block->latitude }}">
                                </div>
                                <div class="form-group">
                                    <label for="longitude" class="col-form-label">Longitude:</label>
                                    <input type="text" class="form-control" name="longitude" value="{{ $parcel_block->longitude }}">
                                </div>
                                <div class="form-group">
                                    <label for="state_id" class="col-form-label">{{ __('တိုင်း / ပြည်နယ်') }}</label>
                                    <select class="form-control" name="state_id" id="state_id" required>
                                        <option value="{{ $parcel_block->state_id }}">{{ $parcel_block->states->state_name_mm }} ( {{ $parcel_block->states->state_name_en }} )</option>
                                        @foreach($parcel_states as $state)
                                            <option value="{{ $state->state_id }}">{{ $state->state_name_mm }} ( {{ $state->state_name_en }} )</option>
                                        @endforeach
                    
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="city_id" class="col-md-12 col-form-label">{{ __('မြို့နယ်') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="city_id" class="form-control @error('city_id') is-invalid @enderror" name="city_id" value="{{ old('city_id') }}" autocomplete="city_id" autofocus>
                                            <option value="{{ $parcel_block->city_id }}">{{ $parcel_block->cities->city_name_mm }} ( {{ $parcel_block->cities->city_name_en }} )</option>
                                            @foreach($parcel_cities as $value)
                                                <option value="{{ $value->city_id }}">{{ $value->city_name_mm }} ( {{ $value->city_name_en }} )</option>
                                            @endforeach
                                        </select>
                                        @error('city_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/parcel_block')}}" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-ban"></i> {{ __('Cancel') }}
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br><br><br><br>
  </section>

@endsection
@section('script')
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
    $('#country_name').select2();
    $('#state_id').select2();
    $('#city_id').select2();
});
</script>
@endsection
