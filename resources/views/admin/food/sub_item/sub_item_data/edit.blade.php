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
              <li class="breadcrumb-item active">SubItem</li>
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
                                    <h2 class="card-title" style="font-size: 18px;"><b>Edit SubItem for "{{ $food_subdataitem->food->food_name }}"</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/foods/sub_items',$food_subdataitem->food_id)}}" class="btn btn-danger btn-sm"><i class="fa fa-backward"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.foods.sub_items.data.update',$food_subdataitem->food_sub_item_data_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="restaurant_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="restaurant_id" style="width: 100%;" class="form-control @error('restaurant_id') is-invalid @enderror" disabled name="restaurant_id" value="{{ old('restaurant_id') }}" autocomplete="restaurant_id">
                                            <option value="{{ $food_subdataitem->restaurant_id }}">{{ $food_subdataitem->restaurant->restaurant_name_mm }} / {{ $food_subdataitem->restaurant->restaurant_name_en }} / {{ $food_subdataitem->restaurant->restaurant_name_ch }}</option>
                                        </select>
                                        @error('restaurant_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_id" class="col-md-12 col-form-label">{{ __('Food Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="food_id" style="width: 100%;" class="form-control @error('food_id') is-invalid @enderror" disabled name="food_id" value="{{ old('food_id') }}" autocomplete="food_id">
                                            <option value="{{ $food_subdataitem->food_id }}">{{ $food_subdataitem->food->food_name_mm }} / {{ $food_subdataitem->food->food_name_en }} / {{ $food_subdataitem->food->food_name_ch }}</option>
                                        </select>
                                        @error('food_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_sub_item_id" class="col-md-12 col-form-label">{{ __('Section Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="food_sub_item_id" style="width: 100%;" class="form-control @error('food_sub_item_id') is-invalid @enderror" disabled name="food_sub_item_id" value="{{ old('food_sub_item_id') }}" autocomplete="food_sub_item_id" autofocus>
                                            <option value="{{ $food_subdataitem->food_sub_item_id }}">{{ $food_subdataitem->food_sub_item->section_name_mm }} / {{ $food_subdataitem->food_sub_item->section_name_en }} / {{ $food_subdataitem->food_sub_item->section_name_ch }}</option>
                                            {{-- @if($food_subdataitem->required_type=="0")
                                                <option value="1">Required</option>
                                            @else
                                                <option value="0">Select</option>
                                            @endif --}}
                                        </select>
                                        @error('food_sub_item_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="required_type" class="col-md-12 col-form-label">{{ __('Required Type') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        {{-- <select id="required_type" style="width: 100%;" class="form-control @error('required_type') is-invalid @enderror" name="required_type" value="{{ old('required_type') }}" autocomplete="required_type" autofocus> --}}
                                            {{-- <option value="{{ $food_subdataitem->required_type }}"> --}}
                                                {{-- @if($food_subdataitem->food_sub_item->required_type=="0")
                                                    {{ "Select" }}
                                                @else
                                                    {{ "Required" }}
                                                @endif --}}
                                                @if($food_subdataitem->food_sub_item->required_type=="0")
                                                    <span class="fa fa-square" style="color: blue;"></span> CheckBox
                                                @else
                                                    <span class="fa fa-circle" style="color: red;"></span> Radio
                                                @endif
                                            {{-- </option> --}}

                                        {{-- </select> --}}
                                        @error('required_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="item_name_mm" class="col-md-12 col-form-label">{{ __('Item Name Myanmar') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="item_name_mm" style="width: 100%;" class="form-control @error('item_name_mm') is-invalid @enderror" name="item_name_mm" value="{{ $food_subdataitem->item_name_mm }}" autocomplete="item_name_mm" autofocus>
                                        @error('item_name_mm')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="item_name_en" class="col-md-12 col-form-label">{{ __('Item Name English') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="item_name_en" style="width: 100%;" class="form-control @error('item_name_en') is-invalid @enderror" name="item_name_en" value="{{ $food_subdataitem->item_name_en }}" autocomplete="item_name_en" autofocus>
                                        @error('item_name_en')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="item_name_ch" class="col-md-12 col-form-label">{{ __('Item Name China') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="item_name_ch" style="width: 100%;" class="form-control @error('item_name_ch') is-invalid @enderror" name="item_name_ch" value="{{ $food_subdataitem->item_name_ch }}" autocomplete="item_name_ch" autofocus>
                                        @error('item_name_ch')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="food_sub_item_price" class="col-md-12 col-form-label">{{ __('Price') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="food_sub_item_price" type="number" class="form-control @error('food_sub_item_price') is-invalid @enderror" name="food_sub_item_price" value="{{ $food_subdataitem->food_sub_item_price }}" autocomplete="food_sub_item_price" autofocus>
                                        @error('food_sub_item_price')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="instock" class="col-md-12 col-form-label">{{ __('Instock') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="instock" style="width: 100%" class="form-control @error('instock') is-invalid @enderror" name="instock" value="{{ old('instock') }}" autocomplete="instock" autofocus>
                                            <option value="{{ $food_subdataitem->required_type }}">
                                                @if($food_subdataitem->instock=="0")
                                                    {{ "No" }}
                                                @else
                                                    {{ "Yes" }}
                                                @endif
                                            </option>
                                            @if($food_subdataitem->instock=="0")
                                                <option value="1">Yes</option>
                                            @else
                                                <option value="0">No</option>
                                            @endif
                                        </select>
                                        </select>
                                        @error('instock')
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
                                        <a href="{{url('fatty/main/admin/foods/sub_items',$food_subdataitem->food_id)}}" class="btn btn-secondary btn-sm">
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
    $('#food_id').select2();
    $('#instock').select2();
    $('#restaurant_id').select2();
    $('#required_type').select2();
    $('#food_sub_item_id').select2();
});
</script>
@endsection
