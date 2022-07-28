@extends('admin.layouts.master')

@section('card_header')
<div class="card-header">
    <h3 class="card-title pt-2 text-uppercase">Food Order Detail</h3>
</div>
@endsection
@section('content')

<div class="p-3">

    <div class="form-group">
        <label>Restaurant:</label><br>
        @if($food_order->restaurant_id)
        <img src="../../../../../uploads/restaurant/{{ $food_order->restaurant->restaurant_image }}" class="img-thumbnail" width="200" height="200">
        @else
        <img src="../../../../../image/available.png" class="img-thumbnail" width="200" height="200">
        @endif
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Restaurant Name:</label>
            <textarea name="description" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->restaurant->restaurant_name_mm }}</textarea>
        </div>

        <div class="col-md-6">
            <label>Restaurant Address:</label>
            <textarea name="description" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->restaurant->restaurant_address }}</textarea>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Order Id:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->customer_order_id }}" readonly>
        </div>

        <div class="col-md-6">
            <label>Delivery Status:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->order_status->order_status_name }}" readonly>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label>Current Adress:</label>
            @if ($food_order->customer_address_id==0)
            <textarea name="description" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->current_address }}</textarea>
            @else
            <textarea name="description" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->customer_address->current_address }}</textarea>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Customer Address Phone:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->customer_address_phone }}" readonly>
        </div>
        <div class="col-md-6">
            <label>Payment Method:</label>
            <input type="text" class="form-control form-control-sm" value="{{ $food_order->payment_method->payment_method_name }}" readonly>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label>Bill Detail:</label><br>
            <table>
                @foreach ($food_order->foods as $food)
                <tr>
                    <td>{{ $food->food_qty }} x {{ $food->food_name_mm }}
                        (@foreach ($food->sub_item as $sub_item)
                        @foreach ($sub_item->option as $option)
                        {{ $option->item_name_mm }}
                        @endforeach
                        @endforeach)
                    </td>
                    <td></td>
                    <td></td>
                    <td>- Ks.{{ $food->food_price }}</td>
                </tr>
                {{-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <br><br> --}}
                @endforeach
                <tr style="border-bottom: 1px solid #0000002d;">
                    <td>Delivery Fee</td>
                    <td></td>
                    <td></td>
                    <td>- ks.{{ $food_order->delivery_fee }}</td>
                </tr>
                <tr>
                    <td>Bill Total Price</td>
                    <td></td>
                    <td></td>
                    <td>- ks.{{ $food_order->bill_total_price }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label>Order Description:</label>
            <textarea name="description" class="form-control form-control-sm" cols="50" rows="3" readonly>{{ $food_order->order_description }}</textarea>
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
