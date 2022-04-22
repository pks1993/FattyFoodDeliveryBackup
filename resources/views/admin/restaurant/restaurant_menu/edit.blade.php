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
              <li class="breadcrumb-item active">Food Menu</li>
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
                                    <h2 class="card-title" style="font-size: 20px;"><b>Edit "{{ $food_menu->food_menu_name_mm }}"</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/restaurants/menu/list/'.$food_menu->restaurant_id)}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.restaurants_menu.update',$food_menu->food_menu_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <input type="hidden" name="restaurant_id" value="{{ $food_menu->restaurant_id }}">
                                    <label for="food_menu_name_mm" class="col-md-12 col-form-label">{{ __('Food Menu Name Myanmar') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="food_menu_name_mm" type="text" class="form-control @error('food_menu_name_mm') is-invalid @enderror" name="food_menu_name_mm" value="{{ $food_menu->food_menu_name_mm }}" autocomplete="food_menu_name_mm" autofocus>
                                        @error('food_menu_name_mm')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_menu_name_en" class="col-md-12 col-form-label">{{ __('Food Menu Name English') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="food_menu_name_en" type="text" class="form-control @error('food_menu_name_en') is-invalid @enderror" name="food_menu_name_en" value="{{ $food_menu->food_menu_name_en }}" autocomplete="food_menu_name_en" autofocus>
                                        @error('food_menu_name_en')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_menu_name_ch" class="col-md-12 col-form-label">{{ __('Food Menu Name China') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="food_menu_name_ch" type="text" class="form-control @error('food_menu_name_ch') is-invalid @enderror" name="food_menu_name_ch" value="{{ $food_menu->food_menu_name_ch }}" autocomplete="food_menu_name_ch" autofocus>
                                        @error('food_menu_name_ch')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="restaurant_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="restaurant_id" class="form-control @error('restaurant_id') is-invalid @enderror" name="restaurant_id" value="{{ old('restaurant_id') }}" autocomplete="restaurant_id" autofocus>
                                            <option value="{{ $food_menu->restaurant_id }}">{{ $food_menu->restaurant->restaurant_name_mm }} ( {{ $food_menu->restaurant->restaurant_name_en }} )</option>
                                        </select>
                                        @error('restaurant_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/restaurants/menu/list/'.$food_menu->restaurant_id)}}" class="btn btn-secondary btn-sm">
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
        <br><br><br>
  </section>

@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function () {
    $('#restaurant_id').select2();
});
</script>
@endsection
