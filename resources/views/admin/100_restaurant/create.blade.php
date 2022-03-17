@extends('admin.layouts.master')

@section('css')
<style>
.field-icon {
    float: right;
    margin-left: -25px;
    margin-top: -27px;
    position: relative;
    z-index: 2;
    padding-right: 20px;
}
</style>
@endsection
@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
              @if(Session('error'))
                  <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                      {{Session('error')}}
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
              @endif
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Restaurant</li>
              <li class="breadcrumb-item active">Add</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title">Add a new Restaurant</h3>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/restaurants')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.restaurants.store') }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="restaurant_name_mm" class="col-md-12 col-form-label">{{ __('Restaurant Name Myanmar') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="restaurant_name_mm" type="text" class="form-control @error('restaurant_name_mm') is-invalid @enderror" name="restaurant_name_mm" value="{{ old('restaurant_name_mm') }}" autocomplete="category_image" autofocus>
                                        @error('restaurant_name_mm')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="restaurant_name_en" class="col-md-12 col-form-label">{{ __('Restaurant Name English') }} </label>
                                    <div class="col-md-12">
                                        <input id="restaurant_name_en" type="text" class="form-control @error('restaurant_name_en') is-invalid @enderror" name="restaurant_name_en" value="{{ old('restaurant_name_en') }}" autocomplete="category_image" autofocus>
                                        @error('restaurant_name_en')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="restaurant_name_ch" class="col-md-12 col-form-label">{{ __('Restaurant Name China') }} </label>
                                    <div class="col-md-12">
                                        <input id="restaurant_name_ch" type="text" class="form-control @error('restaurant_name_ch') is-invalid @enderror" name="restaurant_name_ch" value="{{ old('restaurant_name_ch') }}" autocomplete="category_image" autofocus>
                                        @error('restaurant_name_ch')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="restaurant_category_id" class="col-md-12 col-form-label">{{ __('Category Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="restaurant_category_id" style="width: 100%;" class="form-control @error('restaurant_category_id') is-invalid @enderror" name="restaurant_category_id" value="{{ old('restaurant_category_id') }}" autocomplete="restaurant_category_id" autofocus>
                                            <option value="">{{ "Choose Category Name" }}</option>
                                            @foreach($categories as $value)
                                                <option value="{{ $value->restaurant_category_id }}">{{ $value->restaurant_category_name_mm }} ( {{ $value->restaurant_category_name_ch }} )</option>
                                            @endforeach
                                        </select>
                                        @error('restaurant_category_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label for="state_id" class="col-md-12 col-form-label">{{ __('ပြည်နယ် / တိုင်း') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="state_id" style="width: 100%;" class="form-control @error('state_id') is-invalid @enderror" name="state_id" value="{{ old('state_id') }}" autocomplete="state_id" autofocus>
                                                <option value="">ပြည်နယ် / တိုင်း</option>
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
                                        <select id="city_id" style="width: 100%;" class="form-control @error('city_id') is-invalid @enderror" name="city_id" value="{{ old('city_id') }}" autocomplete="city_id" autofocus>
                                        </select>
                                        @error('city_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="restaurant_latitude" class="col-md-12 col-form-label">{{ __('Restaurant Latitude') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="restaurant_latitude" class="form-control @error('restaurant_latitude') is-invalid @enderror" name="restaurant_latitude" autocomplete="restaurant_latitude">{{ old('restaurant_latitude') }}
                                        @error('restaurant_latitude')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="restaurant_longitude" class="col-md-12 col-form-label">{{ __('Restaurant Longitude') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="restaurant_longitude" class="form-control @error('restaurant_longitude') is-invalid @enderror" name="restaurant_longitude" autocomplete="restaurant_longitude">{{ old('restaurant_longitude') }}
                                        @error('restaurant_longitude')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="address" class="col-md-12 col-form-label">{{ __('Address') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <textarea style="height: 100px;" id="address" class="form-control @error('address') is-invalid @enderror" name="address" autocomplete="address">{{ old('address') }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="restaurant_user_phone" class="col-md-12 col-form-label">{{ __('Restaurant User Phone') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="restaurant_user_phone" class="form-control @error('restaurant_user_phone') is-invalid @enderror" name="restaurant_user_phone" autocomplete="restaurant_user_phone">{{ old('restaurant_user_phone') }}
                                        @error('restaurant_user_phone')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-12 col-form-label">{{ __('Password') }} <span  style="color: #990000;font-weight:700;">*</span></label>

                                    <div class="col-md-12">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter minimum 6 characters"  autocomplete="new-password"><span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password_confirmation" class="col-md-12 col-form-label">{{ __('Confirm Password') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="minimum 6 characters"  autocomplete="new-password"><span toggle="#password_confirmation" class="fa fa-fw fa-eye field-icon toggle-password"></span> <span id='message'></span>

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="restaurant_image" class="col-md-12 col-form-label">{{ __('Restaurant Image') }} </label>
                                    <div class="col-md-6">
                                        <input type="file" style="height: auto;" id="restaurant_image" class="form-control @error('restaurant_image') is-invalid @enderror" name="restaurant_image" autocomplete="restaurant_image" onchange="loadFileImage(event)">
                                        @error('restaurant_image')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            <image src="{{asset('../../../image/available.png')}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Create') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/restaurants')}}" class="btn btn-secondary btn-sm">
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
                url: '/fatty/main/admin/restaurants/city/list/'+id,
                success: function(data){ 
                    $('#city_id').append(`<option value="">မြို့နယ်</option>`);
                    $.each(data, function(index,value) {
                        $('#city_id').append('<option value='+value.city_id+'>'+value.city_name_mm + ' ( '+value.city_name_en+' ) '+'</option>');
                    });
                }
            });  
        }
    }); 
    //select2
    $('#city_id').select2();
    $('#state_id').select2();
    $('#restaurant_category_id').select2();
    $('#restaurant_type_id').select2();
});
//Image Show
var loadFileImage= function(event) {
    var image = document.getElementById('imageOne');
    image.src = URL.createObjectURL(event.target.files[0]);
};
//Password Matching
$('#password, #password_confirmation').on('keyup', function () {
  if ($('#password').val() == $('#password_confirmation').val()) {
    $('#message').html('Matching').css('color', 'green');
  } else 
    $('#message').html('Not Matching').css('color', 'red');
});
</script>

<script type="text/javascript">
//Password toggle
$(".toggle-password").click(function() {

    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});
</script>
@endsection
