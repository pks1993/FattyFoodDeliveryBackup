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
                    <li class="breadcrumb-item active">Restaurant</li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
            <div class="col-md-12">

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
                                    <h3 class="card-title"><b>Add a new Restaurant</b></h3>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/restaurants')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.restaurants.update',$restaurants->restaurant_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group row">
                                    <label for="restaurant_name_mm" class="col-md-12 col-form-label">{{ __('Restaurant Name Myanmar') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="restaurant_name_mm" type="text" class="form-control @error('restaurant_name_mm') is-invalid @enderror" name="restaurant_name_mm" value="{{ $restaurants->restaurant_name_mm }}" autocomplete="category_image" autofocus>
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
                                        <input id="restaurant_name_en" type="text" class="form-control @error('restaurant_name_en') is-invalid @enderror" name="restaurant_name_en" value="{{ $restaurants->restaurant_name_en }}" autocomplete="category_image" autofocus>
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
                                        <input id="restaurant_name_ch" type="text" class="form-control @error('restaurant_name_ch') is-invalid @enderror" name="restaurant_name_ch" value="{{ $restaurants->restaurant_name_ch }}" autocomplete="category_image" autofocus>
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
                                            <option value="{{ $restaurants->restaurant_category_id }}">{{ $restaurants->category->restaurant_category_name_mm }}</option>
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
                                            <option value="{{ $restaurants->state_id }}">{{ $restaurants->state->state_name_mm }}</option>
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
                                            <option value="{{ $restaurants->city_id }}">{{ $restaurants->city->city_name_mm }}</option>
                                            @foreach($cities as $city)
                                                <option value="{{ $city->city_id }}">{{ $city->city_name_mm }} ( {{ $city->city_name_en }} )</option>
                                            @endforeach
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
                                        <input type="text" id="restaurant_latitude" class="form-control @error('restaurant_latitude') is-invalid @enderror" name="restaurant_latitude" autocomplete="restaurant_latitude" value={{ $restaurants->restaurant_latitude }}>
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
                                        <input type="text" id="restaurant_longitude" class="form-control @error('restaurant_longitude') is-invalid @enderror" name="restaurant_longitude" autocomplete="restaurant_longitude" value={{$restaurants->restaurant_longitude}}>
                                        @error('restaurant_longitude')
                                        <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="restaurant_phone" class="col-md-12 col-form-label">{{ __('Restaurant Phone') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="restaurant_phone" class="form-control @error('restaurant_phone') is-invalid @enderror" value="{{ $restaurants->restaurant_phone }}" name="restaurant_phone" autocomplete="restaurant_phone">{{ old('restaurant_phone') }}
                                        @error('restaurant_phone')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-md-12 col-form-label">{{ __('Address') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <textarea style="height: 100px;" id="address" class="form-control @error('address') is-invalid @enderror" name="address" autocomplete="address">{{ $restaurants->restaurant_address }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="average_time" class="col-md-12 col-form-label">{{ __('Average Time') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="average_time" class="form-control @error('average_time') is-invalid @enderror" name="average_time" autocomplete="average_time" value={{ $restaurants->average_time }}>
                                        @error('average_time')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="rush_hour_time" class="col-md-12 col-form-label">{{ __('Rush Hour Time') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="rush_hour_time" class="form-control @error('rush_hour_time') is-invalid @enderror" name="rush_hour_time" autocomplete="rush_hour_time" value={{$restaurants->rush_hour_time}}>
                                        @error('rush_hour_time')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="percentage" class="col-md-12 col-form-label">{{ __('Percentage') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="number" step="0.01" value="{{ $restaurants->percentage }}" id="percentage" class="form-control @error('percentage') is-invalid @enderror" name="percentage" autocomplete="percentage">{{ old('percentage') }}
                                        @error('percentage')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
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
                                            @if($restaurants->restaurant_image==null)
                                                <image src="{{asset('../image/available.png')}}" id="image_one" style="width: 100%;height: 150px;"></image>
                                            @else
                                                <image src="../../../../../uploads/restaurant/{{$restaurants->restaurant_image}}" id="image_one" style="width: 100%;height: 150px;"></image>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="restaurant_user_phone" class="col-md-12 col-form-label">{{ __('Restaurant Login Phone') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="restaurant_user_phone" class="form-control @error('restaurant_user_phone') is-invalid @enderror" value="{{ $restaurants->restaurant_user->restaurant_user_phone }}" name="restaurant_user_phone" autocomplete="restaurant_user_phone">{{ old('restaurant_user_phone') }}
                                        @error('restaurant_user_phone')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="restaurant_user_password" class="col-md-12 col-form-label">{{ __('Restaurant Login Password') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="restaurant_user_password" class="form-control @error('restaurant_user_password') is-invalid @enderror" value="{{ $restaurants->restaurant_user->restaurant_user_password }}" name="restaurant_user_password" autocomplete="restaurant_user_password">{{ old('restaurant_user_password') }}
                                        @error('restaurant_user_password')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure create?')">
                                            <i class="fa fa-edit"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/restaurants')}}" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure cancel?')">
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
    var image = document.getElementById('image_one');
    image.src = URL.createObjectURL(event.target.files[0]);
};

setTimeout(function() {
    $('#successMessage').fadeOut('fast');
}, 2500);
</script>
@endsection
