@extends('admin.layouts.master')

@section('card_header')
<div class="card-header">
    <h3 class="card-title pt-2 text-uppercase">Customer View</h3>
</div>
@endsection
@section('content')

<div class="p-3">

    <div class="form-group">
        <label>Profile Image :</label><br>
        @if($customer->image)
            <img src="../../../../../uploads/customer/{{ $customer->image}}" class="img-thumbnail" width="200" height="200">
        @else
            <img src="../../../../../image/available.png" class="img-thumbnail" width="200" height="200">
        @endif
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Customer Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $customer->customer_name }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Customer Phone:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $customer->customer_phone }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Customer Latitude:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $customer->latitude }}" readonly>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Customer Longitude:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $customer->longitude }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Order Count:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $customer->order_count }}" readonly>
        </div>

        <div class="col-md-4">
            <label>Order Amount:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $customer->order_amount }}" readonly>
        </div>

    </div>
    @if(url()->previous()==url()->current())
        <a href="{{ url('fatty/main/admin/customers') }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
    @else
        <a href="{{ url()->previous() }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
    @endif
    <div class="float-right">
    <form action="/fatty/main/admin/customers/delete/{{ $customer->customer_id }}" method="post" class="d-inline">
        {{ csrf_field() }}
        {{ method_field('delete') }}
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
    </form>
    </div>

</div>
@endsection
