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
                                    <h2 class="card-title" style="font-size: 25px;"><b>Edit " {{ $foods->food_name }} "</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/foods')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.foods.update',$foods) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="food_name" class="col-md-12 col-form-label">{{ __('Food Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="food_name" type="text" class="form-control @error('food_name') is-invalid @enderror" name="food_name" value="{{ $foods->food_name }}" autocomplete="food_name" autofocus>
                                        @error('food_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="restaurant_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="restaurant_id" style="width: 100%;" class="form-control @error('restaurant_id') is-invalid @enderror" name="restaurant_id" value="{{ old('restaurant_id') }}" autocomplete="restaurant_id">
                                            <option value="{{ $foods->restaurant_id }}">{{ $foods->restaurant->restaurant_name }}</option>
                                            @foreach($restaurants as $value)
                                                <option value="{{ $value->restaurant_id }}">{{ $value->restaurant_name }} ( {{ $value->zone->zone_name }} )</option>
                                            @endforeach
                                        </select>
                                        @error('restaurant_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- <div class="form-group row">
                                    <label for="food_category_id" class="col-md-12 col-form-label">{{ __('Food Category') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="food_category_id" style="width: 100%;" class="form-control @error('food_category_id') is-invalid @enderror" name="food_category_id" value="{{ old('food_category_id') }}" autocomplete="food_category_id" autofocus>
                                            <option value="{{ $foods->food_category_id }}">{{ $foods->category->food_category_name }}</option>
                                            @foreach($food_category as $value)
                                                <option value="{{ $value->food_category_id }}">{{ $value->food_category_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('food_category_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div> --}}
                                <div class="form-group row">
                                    <label for="food_menu_id" class="col-md-12 col-form-label">{{ __('Food Menu') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="food_menu_id" style="width: 100%;" class="form-control @error('food_menu_id') is-invalid @enderror" name="food_menu_id" value="{{ old('food_menu_id') }}" autocomplete="food_menu_id" autofocus>
                                            <option value="{{ $foods->food_menu_id }}">{{ $foods->menu->food_menu_name }}</option>
                                            @foreach($food_menu as $value)
                                                <option value="{{ $value->food_menu_id }}">{{ $value->food_menu_name }}</option>
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
                                    <label for="image" class="col-md-12 col-form-label">{{ __('Category Image') }} </label>
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
                                                <image src="{{asset('../image/available.png')}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                            @else
                                                <image src="../../../../../uploads/food/{{$foods->food_image}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/foods')}}" class="btn btn-secondary btn-sm">
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
    $('#restaurant_id').on('change', function(){
        $('#food_menu_id').empty();
        // $('#food_category_id').empty();
        var id = $(this).val();
        // if(id){
        //     $.ajax({
        //         type: 'get',
        //         url: '/fatty/main/admin/foods/category/list/'+id,
        //         success: function(data){ 
        //             $('#food_category_id').append(`<option value="">Choose Category</option>`);
        //             $.each(data, function(index,value) {
        //                 $('#food_category_id').append('<option value='+value.food_category_id+'>'+value.food_category_name+'</option>');
        //             });
        //         }
        //     });  
        // }
        if(id){
            $.ajax({
                type: 'get',
                url: '/fatty/main/admin/foods/menu/list/'+id,
                success: function(data){ 
                    $('#food_menu_id').append(`<option value="">Choose Menu</option>`);
                    $.each(data, function(index,value) {
                        $('#food_menu_id').append('<option value='+value.food_menu_id+'>'+value.food_menu_name+'</option>');
                    });
                }
            });  
        }
    }); 
    $('#food_menu_id').select2();
    // $('#food_category_id').select2();
    $('#restaurant_id').select2();
});

//Image Show
var loadFileImage= function(event) {
var image = document.getElementById('imageOne');
image.src = URL.createObjectURL(event.target.files[0]);
};
</script>
@endsection
