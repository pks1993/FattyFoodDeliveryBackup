@extends('admin.layouts.master')

@section('card_header')
<div class="card-header">
    <h3 class="card-title pt-2 text-uppercase">Food View</h3>
</div>
@endsection
@section('content')

<div class="p-3" style="font-size: 16px">

    <div class="form-group">
        <label>Food Image : <b style="color: rgb(226, 18, 18)">" {{ $foods->created_at->format('d M Y') }} "</b></label><br>
        @if($foods->food_image)
            <img src="../../../../../../../uploads/food/{{ $foods->food_image}}" class="img-thumbnail" width="250" height="250">
        @else
            <img src="../../../../../../../image/available.png" class="img-thumbnail" width="250" height="250">
        @endif
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Food Name Myanmar:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $foods->food_name_mm }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Food Name English:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $foods->food_name_en }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Food Name China:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $foods->food_name_ch }}" readonly>
        </div>
    </div>

    <div class="row mb-3">

        <div class="col-md-4">
            <label>Food Menu Name Myanmar:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $foods->menu->food_menu_name_mm }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Food Menu Name English:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $foods->menu->food_menu_name_en }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Food Menu Name China:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $foods->menu->food_menu_name_ch }}" readonly>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Food Price:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $foods->food_price }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Restaurant Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $foods->restaurant->restaurant_name_mm }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Description:</label>
            <textarea type="text" class="form-control form-control-sm" style="height: 75px;" readonly>{{ $foods->food_description }}</textarea>
        </div>

    </div>
    <a href="{{ url('/fatty/main/admin/restaurants/food/list/'.$foods->restaurant_id) }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
    <div class="float-right">
    <form action="/fatty/main/admin/restaurant/delete/{{ $foods->food_id }}" method="post" class="d-inline">
        {{ csrf_field() }}
        {{ method_field('delete') }}
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
    </form>
    </div>

</div>
@endsection
