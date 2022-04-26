@extends('admin.layouts.master')

@section('css')
@endsection
@section('content')
<section class="content-header mb-4">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6" style="height: 20px;">
                <div class="flash-message" id="successMessage">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="col-sm-6" style="height: 20px">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Food Sub Item</li>
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
                                    <h2 class="card-title" style="font-size: 18px;"><b>Add SubItem for "{{ $food_subitem->food->food_name_mm }}"</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/foods/sub_items',$food_subitem->food_id)}}" class="btn btn-danger btn-sm"><i class="fa fa-backward"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.foods.sub_items.data.update',$food_subitem->food_sub_item_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="restaurant_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="restaurant_id" style="width: 100%;" class="form-control @error('restaurant_id') is-invalid @enderror" name="restaurant_id" value="{{ old('restaurant_id') }}" autocomplete="restaurant_id">
                                            <option value="{{ $food_subitem->restaurant_id }}">{{ $food_subitem->restaurant->restaurant_name_mm }}</option>
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
                                        <select id="food_id" style="width: 100%;" class="form-control @error('food_id') is-invalid @enderror" name="food_id" value="{{ old('food_id') }}" autocomplete="food_id">
                                            <option value="{{ $food_subitem->food_id }}">{{ $food_subitem->food->food_name_mm }}</option>
                                        </select>
                                        @error('food_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="section_name" class="col-md-12 col-form-label">{{ __('Section Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        {{-- <input id="section_name" type="text" class="form-control @error('section_name') is-invalid @enderror" name="section_name" value="{{ $food_subitem->section_name_mm }}" autocomplete="section_name" autofocus> --}}
                                        <select id="section_name" style="width: 100%;" class="form-control @error('section_name') is-invalid @enderror" name="section_name" value="{{ old('section_name') }}" autocomplete="section_name">
                                            <option value="{{ $food_subitem->section_name_mm }}">{{ $food_subitem->section_name_mm }}</option>
                                        </select>
                                        @error('section_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="required_type" class="col-md-12 col-form-label">{{ __('Required Type') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="required_type" style="width: 100%;" class="form-control @error('required_type') is-invalid @enderror" name="required_type" value="{{ old('required_type') }}" autocomplete="required_type" autofocus>
                                            <option value="{{ $food_subitem->required_type }}">
                                                @if($food_subitem->required_type=="0")
                                                    {{ "Select" }}
                                                @else
                                                    {{ "Required" }}
                                                @endif
                                            </option>
                                            @if($food_subitem->required_type=="0")
                                                <option value="1">Required</option>
                                            @else
                                                <option value="0">Select</option>
                                            @endif
                                        </select>
                                        @error('required_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <button type="button" name="add" class="form-control" id="add">Add Other Data</button>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12" id="dynamic_field"></div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Create') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/foods/sub_items',$food_subitem->food_id)}}" class="btn btn-secondary btn-sm">
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
    $('#section_name').select2();
});
</script>
<script>
    $(document).ready(function() {
    var i=1;
    $('#add').click(function() {
        i++;
        $('#dynamic_field').append('<div class="form-group row" id="row'+i+'"><div class="col-md-4"><input placeholder="Enter Myanmar Name" class="form-control" type="text" name="item_name_mm[]" value=""></div><div class="col-md-4"><input placeholder="Enter English Name" class="form-control" type="text" name="item_name_en[]" value=""></div><div class="col-md-4"><input placeholder="Enter China Name" class="form-control" type="text" name="item_name_ch[]" value=""></div><div class="col-md-4 mt-2"><input class="form-control" placeholder="Enter Item Price" type="text" name="food_sub_item_price[]" value="0"></div><div class="col-md-3 mt-2"><select id="instock" class="form-control" name="instock[]" autocomplete="instock" autofocus> <option value="1">Yes</option><option value="0">No</option></select></div><button type="button" class="btn_remove btn btn-danger btn-sm mt-2" name="remove" id="'+ i +'"><i class="fa fa-times" aria-hidden="true"></i></button></div></div>')

    });
    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr("id");
        $('#row' + button_id + '').remove();
    });
});
</script>
<script type="text/javascript">
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endsection
