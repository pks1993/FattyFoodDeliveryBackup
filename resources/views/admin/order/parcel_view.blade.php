@extends('admin.layouts.master')

@section('card_header')
<div class="card-header">
    <h3 class="card-title pt-2 text-uppercase">Parcel Details</h3>
</div>
@endsection
@section('content')

<div class="p-3">
    <div class="row mb-3">
        <div class="col-md-6">
            <label>Order Id:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->order_id }}" readonly>
        </div>
        
        <div class="col-md-6">
            <label>Delivery Status:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->order_status->order_status_name }}" readonly>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label>Sender Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->from_sender_name }}" readonly>
        </div>
        
        <div class="col-md-6">
            <label>Sender Phone:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->from_sender_phone }}" readonly>
        </div>
    </div>
    
    
    <div class="row mb-3">
        <div class="col-md-12">
            <label>Pickup Adress:</label>
            <textarea name="description" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $parcel_order->from_pickup_address }}</textarea>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Recipent Name:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->to_recipent_name }}" readonly>
        </div>
        
        <div class="col-md-6">
            <label>Recipent Phone:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->to_recipent_phone }}" readonly>
        </div>
    </div>
    
    
    <div class="row mb-3">
        <div class="col-md-12">
            <label>Recipent Adress:</label>
            <textarea name="description" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $parcel_order->to_drop_address }}</textarea>
        </div>
    </div>
    
    <div class="form-group">
        <label>Parcel images:</label><br>
        @if($parcel_order->parcel_images->count() !== 0)
        @foreach ($parcel_order->parcel_images as $parcel_image)
        <img src="../../../../../uploads/parcel/parcel_image/{{ $parcel_image->parcel_image }}" class="img-thumbnail" width="200" height="200">
        @endforeach
        @else
        <img src="../../../../../image/available.png" class="img-thumbnail" width="200" height="200">
        @endif 
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Parcel Type:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->parcel_type->parcel_type_name }}" readonly>
        </div>
        <div class="col-md-6">
            <label>Parcel Weight:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->total_estimated_weight }}" readonly>
        </div>
    </div>

    <div class="form-group mb-3">
        @if ($parcel_order->parcel_extra !== null)
        <label>Need extra cover for loss/damage?:</label><br>
        <img src="../../../../../uploads/parcel/parcel_extra_cover/{{ $parcel_order->parcel_extra->parcel_extra_cover_image }}" class="img-thumbnail" width="200" height="200"><br>
        {{ $parcel_order->parcel_extra->parcel_extra_cover_price }} Ks
        @else

        @endif
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label>Bill Detail:</label><br>
            <table> 
                <tr style="border-bottom: 1px solid #0000002d;">
                    <td>Delivery Fee</td>
                    <td></td>
                    <td></td>
                    <td>- ks.{{ $parcel_order->delivery_fee }}</td>
                </tr>
                <tr>
                    <td>Bill Total Price</td>
                    <td></td>
                    <td></td>
                    <td>- ks.{{ $parcel_order->bill_total_price }}</td>
                </tr>
            </table>
        </div>
    </div>

     <div class="row mb-3">
        <div class="col-md-12">
            <label>Order Description:</label>
            <textarea name="description" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $parcel_order->order_description }}</textarea>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Payment Method:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $parcel_order->payment_method->payment_method_name }}" readonly>
        </div>
    </div>

    <a href="{{ url()->previous() }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
    <div class="float-right">
        {{-- <form action="/fatty/main/admin/customers/delete/{{ $customer->customer_id }}" method="post" class="d-inline">
            {{ csrf_field() }}
            {{ method_field('delete') }}
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
        </form> --}}
    </div>
    
</div>
@endsection 