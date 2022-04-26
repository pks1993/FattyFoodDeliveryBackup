@extends('admin.layouts.master')

@section('css')
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
              <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Food</li>
              <li class="breadcrumb-item active">Edit</li>
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
                                    <h4 class="card-title" style="font-size: 25px;">Edit <b> "{{ $foods->food_name_mm }}" </b></h4>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/restaurants/food/list',$foods->restaurant_id)}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.restaurants_food.update',$foods->food_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="food_name_mm" class="col-md-12 col-form-label">{{ __('Food Name Myanmar') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="hidden" name="restaurant_id" value="{{ $foods->restaurant_id }}">
                                        <input id="food_name_mm" type="text" class="form-control @error('food_name_mm') is-invalid @enderror" name="food_name_mm" value="{{ $foods->food_name_mm }}" autocomplete="food_name_mm" autofocus>
                                        @error('food_name_mm')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_name_en" class="col-md-12 col-form-label">{{ __('Food Name English') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="food_name_en" type="text" class="form-control @error('food_name_en') is-invalid @enderror" name="food_name_en" value="{{ $foods->food_name_en }}" autocomplete="food_name_en" autofocus>
                                        @error('food_name_en')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_name_ch" class="col-md-12 col-form-label">{{ __('Food Name China') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="food_name_ch" type="text" class="form-control @error('food_name_ch') is-invalid @enderror" name="food_name_ch" value="{{ $foods->food_name_ch }}" autocomplete="food_name_ch" autofocus>
                                        @error('food_name_ch')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_menu_id" class="col-md-12 col-form-label">{{ __('Food Menu') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="food_menu_id" style="width: 100%;" class="form-control @error('food_menu_id') is-invalid @enderror" name="food_menu_id" value="{{ old('food_menu_id') }}" autocomplete="food_menu_id" autofocus>
                                            <option value="{{ $foods->food_menu_id }}">{{ $foods->menu->food_menu_name_mm }} ({{ $foods->menu->food_menu_name_en }})</option>u</option>
                                            @foreach ($food_menu as $menu)
                                                <option value="{{ $menu->food_menu_id }}">{{ $menu->food_menu_name_mm }} ( {{ $menu->food_menu_name_en }} )</option>
                                            @endforeach
                                        </select>
                                        @error('food_menu_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_price" class="col-md-12 col-form-label">{{ __('Price') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="food_price" type="number" class="form-control @error('food_price') is-invalid @enderror" name="food_price" value="{{ $foods->food_price }}" autocomplete="food_price" autofocus>
                                        @error('food_price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_description" class="col-md-12 col-form-label">{{ __('Food Description') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <textarea id="food_description" class="form-control @error('food_description') is-invalid @enderror" name="food_description" value="{{ old('food_description') }}" autocomplete="food_description" autofocus>{{ $foods->food_description }}</textarea>
                                        @error('food_description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="image" class="col-md-12 col-form-label">{{ __('Restaurant Image') }} </label>
                                    <div class="col-md-6">
                                        <input type="file" style="height: auto;" id="image" class="form-control @error('image') is-invalid @enderror" name="image" autocomplete="image" onchange="loadFileImage(event)">
                                        @error('image')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            @if($foods->food_image==null)
                                                <image src="{{asset('../image/available.png')}}" id="image_one" style="width: 100%;height: 150px;"></image>
                                            @else
                                                <image src="../../../../../../../uploads/food/{{$foods->food_image}}" id="image_one" style="width: 100%;height: 150px;"></image>
                                            @endif
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/restaurants/food/list',$foods->restaurant_id)}}" class="btn btn-secondary btn-sm">
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
    $('#food_menu_id').select2();
    // $('#food_category_id').select2();
    $('#restaurant_id').select2();
});

//Image Show
var loadFileImage= function(event) {
    var image = document.getElementById('image_one');
    image.src = URL.createObjectURL(event.target.files[0]);
};
</script>
@endsection
