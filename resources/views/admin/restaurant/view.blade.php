@extends('admin.layouts.master')

@section('card_header')
<div class="card-header">
    <h3 class="card-title pt-2 text-uppercase">Restaurant View</h3>
</div>
@endsection
@section('content')

<div class="p-3">

    <div class="form-group">
        <label>Restaurant Image :</label><br>
        @if($restaurant->restaurant_image)
            <img src="../../../../../uploads/restaurant/{{ $restaurant->restaurant_image}}" class="img-thumbnail" width="200" height="200">
        @else
            <img src="../../../../../image/available.png" class="img-thumbnail" width="200" height="200">
        @endif
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Restaurant Name Myanmar:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->restaurant_name_mm }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Restaurant Name English:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->restaurant_name_en }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Restaurant Name China:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->restaurant_name_ch }}" readonly>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Restaurant Category Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->category->restaurant_category_name_mm }}" readonly>
        </div>

        <div class="col-md-4">
            <label>State Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->state->state_name_mm }}" readonly>
        </div>

        <div class="col-md-4">
            <label>City Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->city->city_name_mm }}" readonly>
        </div>

    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Category Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->category->restaurant_category_name_mm }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Restaurant User Phone:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->restaurant_phone }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Restaurant User Password:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->restaurant_user->restaurant_user_password }}" readonly>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Restaurant Latitude:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->restaurant_latitude }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Restaurant Longitude:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->restaurant_longitude }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Address:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->restaurant_address }}" readonly>
        </div>

    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Average Time:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->average_time }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Rush Hour Time:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->rush_hour_time }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Percentage:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $restaurant->percentage }}" readonly>
        </div>

    </div>
    <a href="{{ url('fatty/main/admin/restaurants') }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
    <div class="float-right">
    <form action="/fatty/main/admin/restaurant/delete/{{ $restaurant->restaurant_id }}" method="post" class="d-inline">
        {{ csrf_field() }}
        {{ method_field('delete') }}
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
    </form>
    </div>

</div>
@endsection
