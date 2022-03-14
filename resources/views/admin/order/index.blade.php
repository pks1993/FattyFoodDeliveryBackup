@extends('admin.layouts.master')

@section('css')
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-7">
                    <div class="flash-message" id="successMessage">
                        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                            @if(Session::has('alert-' . $msg))
                                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-5">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Restaurant</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
                </div>
                <div class="col-md-12">
                    {{-- <form method='post' action="{{ route('fatty.admin.backup.restaurants') }}">
                       @csrf
                       <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportexcel" value='Excel Export'>
                       <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportcsv" value='CSV Export'>
                    </form> --}}
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Orders Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <div class="pagination">
                                    {{ $food_orders->appends(request()->input())->links() }}
                                </div>
                                <table id="foods_orders" class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th>Status</th>
                                        <th>OrderId</th>
                                        <th>BookingId</th>
                                        <th>CustomerName</th>
                                        <th>OrderType</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($food_orders as $order)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td> 
                                        <td>
                                            @if($order->order_status_id=="1" || $order->order_status_id=="11")
                                                <a class="btn btn-warning btn-sm mr-1" style="color: white;width: 100%;">{{ $order->order_status->order_status_name }}</a>
                                            @elseif($order->order_status_id=="2" || $order->order_status_id=="9" || $order->order_status_id=="16")
                                                <a class="btn btn-danger btn-sm mr-1" style="color: white;width: 100%;">{{ $order->order_status->order_status_name }}</i></a>
                                            @elseif($order->order_status_id=="3" || $order->order_status_id=="4" || $order->order_status_id=="5" || $order->order_status_id=="6" || $order->order_status_id=="10" || $order->order_status_id=="12" || $order->order_status_id=="13" || $order->order_status_id=="14" || $order->order_status_id=="17")
                                                <a class="btn btn-danger btn-sm mr-1" style="color: white;width: 100%;">{{ $order->order_status->order_status_name }}</a>
                                            @else
                                                <a class="btn btn-success btn-sm mr-1" style="color: white;width: 100%">{{ $order->order_status->order_status_name }}</i></a>
                                            @endif
                                        </td>
                                        <td>{{ $order->customer_order_id }}</td>
                                        <td>{{ $order->customer_booking_id }}</td>
                                        <td>{{ $order->customer->customer_name }}</td>
                                        <td>
                                            @if($order->order_type=="food")
                                                <a class="btn btn-primary btn-sm mr-1" style="color: white;width: 100%">{{ $order->order_type }}</i></a>
                                            @else
                                                <a class="btn btn-secondary btn-sm mr-1" style="color: white;width: 100%">{{ $order->order_type }}</i></a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('fatty.admin.food_orders.assign',['order_id'=>$order->order_id])}}" class="btn btn-primary btn-sm mr-1" title="Assign"><i class="fa fa-edit"></i></a>

                                           {{--  <form action="{{route('fatty.admin.restaurants.destroy', $order->ordre_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                            </form> --}}
                                        </td>
                                        {{-- <td class="btn-group text-center">
                                            @if($restaurant->restaurant_user->is_admin_approved=="0")
                                                <a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>
                                            @else
                                                <a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>
                                            @endif
                                            @if($restaurant->restaurant_emergency_status=="0")
                                                <a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-lock-open" title="Restaurant Open"></i></a>
                                            @else
                                                <a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-lock" title="Restaurant Close"></i></a>
                                            @endif


                                            <a href="{{route('fatty.admin.restaurants.edit',['restaurant_id'=>$restaurant->restaurant_id])}}" class="btn btn-primary btn-sm mr-1" title="Edit"><i class="fa fa-edit"></i></a>

                                            <form action="{{route('fatty.admin.restaurants.destroy', $restaurant->restaurant_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                            </form>
                                        
                                        </td> --}}
                                        {{-- <td>
                                            @if($restaurant->restaurant_image)
                                                <img src="../../../uploads/restaurant/{{$restaurant->restaurant_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                            @else
                                                <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                            @endif
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
<script>
    $(function () {
        $("#foods_orders").DataTable({
            // "lengthMenu": [[10,25,50, 100, 250,500, -1], [10,25,50,100, 250, 500, "All"]],
            "paging": false, // Allow data to be paged
            "lengthChange": false,
            "searching": false, // Search box and search function will be actived
            "info": false,
            "autoWidth": true,
            "processing": false,  // Show processing
        });
    });
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
