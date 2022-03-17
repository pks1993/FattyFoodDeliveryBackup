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
                                    <h3 class="card-title" style="font-weight: 550;">Edit "{{ $restaurants->restaurant_name }}"</h3>
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
                                    <label for="restaurant_name" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="restaurant_name" type="text" class="form-control @error('restaurant_name') is-invalid @enderror" name="restaurant_name" value="{{ $restaurants->restaurant_name }}" autocomplete="category_image" autofocus>
                                        @error('restaurant_name')
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
                                            <option value="{{ $restaurants->restaurant_category_id }}">{{ $restaurants->category->restaurant_category_name }}</option>
                                            @foreach($categories as $value)
                                                <option value="{{ $value->restaurant_category_id }}">{{ $value->restaurant_category_name }}</option>
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
                                    <label for="zone_id" class="col-md-12 col-form-label">{{ __('Zone Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="zone_id" style="width: 100%;" class="form-control @error('zone_id') is-invalid @enderror" name="zone_id" value="{{ old('zone_id') }}" autocomplete="zone_id">
                                            @if(Auth::user()->is_main_admin=="1")
                                                <option value="{{ $restaurants->zone_id }}">{{ $restaurants->zone->zone_name }}</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->zone_id }}">{{ $zone->zone_name }}</option>
                                                @endforeach
                                            @else
                                                <option value="{{ $restaurants->zone_id }}">{{ $restaurants->zone->zone_name }}
                                            @endif
                                     </select>
                                        @error('zone_id')
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
                                            <option value="{{ $restaurants->state_id }}">{{ $restaurants->state->state_name_mm }}
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
                                            <option value="{{ $restaurants->city_id }}">{{ $restaurants->city->city_name_mm }} ( {{ $restaurants->city->city_name_en }} )</option>
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
                                <div class="form-group row">
                                    <label for="address" class="col-md-12 col-form-label">{{ __('Address') }} </label>
                                    <div class="col-md-12">
                                        <textarea style="height: 100px;" id="address" class="form-control @error('address') is-invalid @enderror" name="address" autocomplete="address">{{ $restaurants->address }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-12 col-form-label">{{ __('Password') }} <span  style="color: #990000;font-weight:700;">*</span> <span style="font-size: 13px; color: red;">{{ "( At least 6 charater )" }}</span></label>
                                    <div class="col-md-12">
                                        <input id="password" type="password" value="{{ $restaurants->password }}" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="new-password">
                                        <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
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
                                        <input id="password_confirmation" type="password" value="{{ $restaurants->password }}" class="form-control" name="password_confirmation"  autocomplete="new-password">
                                        <span toggle="#password_confirmation" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                         <span id='message'></span>

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
                                                <image src="{{asset('../../../image/available.png')}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                            @else
                                                <image src="{{asset('../../../uploads/restaurant/'.$restaurants->restaurant_image )}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Update') }}
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
    $('#zone_id').on('change', function(){
        $('#state_id').empty();
        $('#city_id').empty();
        var id = $(this).val();
        if(id){
            $.ajax({
                type: 'get',
                url: '/fatty/main/admin/branch/city/list/'+id,
                success: function(data){ 
                    $('#city_id').append(`<option value="">မြို့နယ်</option>`);
                    $.each(data, function(index,value) {
                        $('#city_id').append('<option value='+value.city_id+'>'+value.city_name_mm + ' ( '+value.city_name_en+' ) '+'</option>');
                    });
                }
            });  
        }
        if(id){
            $.ajax({
                type: 'get',
                url: '/fatty/main/admin/branch/state/list/'+id,
                success: function(data){ 
                    $.each(data, function(index,value) {
                        $('#state_id').append('<option value='+value.state_id+'>'+value.state_name_mm + ' ( '+value.state_name_en+' ) '+'</option>');
                    });
                }
            });  
        }
    }); 
    //select2
    $('#city_id').select2();
    $('#state_id').select2();
    $('#zone_id').select2();
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
