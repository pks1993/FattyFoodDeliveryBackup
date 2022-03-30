@extends('admin.layouts.master')

@section('card_header')
<div class="card-header">
    <h3 class="card-title pt-2 text-uppercase">Rider View</h3>
</div>
@endsection
@section('content')

<div class="p-3">
    
    <div class="form-group">
        <label>Rider Image :</label><br>
        @if($rider->image)
            <img src="../../../../../uploads/rider/{{ $rider->rider_image}}" class="img-thumbnail" width="200" height="200">
        @else
            <img src="../../../../../image/available.png" class="img-thumbnail" width="200" height="200">
        @endif 
    </div>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Rider Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_user_name }}" readonly>
        </div>
        
        <div class="col-md-4">
            <label>Rider Phone:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_user_phone }}" readonly>
        </div>
        
        <div class="col-md-4">
            <label>Rider Latitude:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_latitude }}" readonly>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Rider Longitude:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_longitude }}" readonly>
        </div>
        
        <div class="col-md-4">
            <label>State:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $rider->state->state_name_mm }}" readonly>
        </div>
        
        <div class="col-md-4">
            <label>Is Admin Approved:</label><br>
            @if ($rider->is_admin_approved == 0)
            <a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>
            @else
            <a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>
            @endif
        </div>
        
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
    <div class="float-right">
    <form action="/fatty/main/admin/customers/delete/{{ $rider->customer_id }}" method="post" class="d-inline">
        {{ csrf_field() }}
        {{ method_field('delete') }}
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
    </form>
    </div>
    
</div>
@endsection 