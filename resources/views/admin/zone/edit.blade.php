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
              <li class="breadcrumb-item active">Zone</li>
              <li class="breadcrumb-item active">Add</li>
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
                                    <h2 class="card-title" style="font-size: 25px;"><b>Add a new zone</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/zones')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.zones.update',$zones->zone_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="zone_name" class="col-md-12 col-form-label">{{ __('Zone Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="zone_name" type="text" class="form-control @error('zone_name') is-invalid @enderror" name="zone_name" value="{{ $zones->zone_name }}" autocomplete="zone_name" required="true" autofocus>
                                        @error('zone_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="state_id" class="col-md-12 col-form-label">{{ __('တိုင်း / ပြည်နယ်') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="state_id" style="width: 100%;" class="form-control @error('state_id') is-invalid @enderror" name="state_id" value="{{ old('state_id') }}" autocomplete="state_id" required="true" autofocus>
                                            <option value="{{ $zones->state_id }}">{{ $zones->state->state_name_mm }} ( {{ $zones->state->state_name_en }} )</option>
                                            @foreach($states as $state)
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
                                        <select id="city_id" class="form-control @error('city_id') is-invalid @enderror" name="city_id" value="{{ old('city_id') }}" autocomplete="city_id" required="true" autofocus>
                                            <option value="{{ $zones->city_id }}">{{ $zones->city->city_name_mm }} ( {{ $zones->city->city_name_en }} )</option>
                                            @foreach($cities as $value)
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
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/zones')}}" class="btn btn-secondary btn-sm">
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
