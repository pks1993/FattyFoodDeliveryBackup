@extends('admin.layouts.master')

@section('css')
<style>
    form>.fa {
        display: none;
    }
    .dt-buttons>button{
        border-radius: revert;
        margin-top: 15px;
        margin-right: 5px;
    }
    .dataTables_length >label {
        margin-right: 15px !important;
        margin-top: 15px;
    }
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            {{-- <div class="col-sm-7">
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
                    <li class="breadcrumb-item active">Daily Parcel Ordes</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div> --}}
            <div class="col-sm-3 col-xs-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $all_count }}</h3></span></div>
                        <p>Total Parcel Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 col-xs-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $processing_orders }}</h3></span></div>
                        <p>Total Processing Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 col-xs-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $delivered_orders }}</h3></span></div>
                        <p>Total Delivered Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 col-xs-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $cancel_orders }}</h3></span></div>
                        <p>Total Cancel Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-12">
                        <form action="{{ url('fatty/main/admin/daily_parcel_orders/date_filter') }}">
                            <input class="col-5 col-md-2" type="date" name="start_date" value="{{ now()->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <input class="col-5 col-md-2" type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <button class="col-1 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="div mb-2">
                        <form action="{{ url('fatty/main/admin/daily_parcel_orders/search') }}">
                            <input class="col-9 col-md-4" type="type" name="search_name" placeholder="Filter Enter Order Id OR Booking Id" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <input type="hidden" name="start_date" value="{{ $date_start }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <input type="hidden" name="end_date" value="{{ $date_end }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                            <button class="col-2 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="orders" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">No.</th>
                                    <th style="text-align: center">OrderStatus</th>
                                    <th style="text-align: center">Type</th>
                                    <th>OrderId</th>
                                    <th>BookingId</th>
                                    <th>OrderDate</th>
                                    <th>OrderTime</th>
                                    <th>CustomerName</th>
                                    <th>RiderName</th>
                                    <th>PaymentMethod</th>
                                    <th>RiderDeliFee</th>
                                    <th>TotalPrice</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                                @foreach ($total_orders as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($item->order_status_id=='11')
                                            <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Pending(NotAcceptRider)</a>
                                        @elseif($item->order_status_id=='12')
                                            <a class="btn btn-primary btn-sm mr-2" style="color: white;width: 100%;">AcceptByRider</a>
                                        @elseif($item->order_status_id=='13')
                                            <a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ArrivedtoPickOrder</a>
                                        @elseif($item->order_status_id=='17')
                                            <a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">RiderPickup</a>
                                        @elseif($item->order_status_id=='14')
                                            <a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">StartDeliverybyRider </a>
                                        @elseif($item->order_status_id=='15')
                                            <a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">AcceptCustomer</a>
                                        @elseif($item->order_status_id=='8')
                                            <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">PendingOrder</a>
                                        @elseif($item->order_status_id=='16')
                                            <a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">CustomerCancel</a>
                                        @else
                                            <a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->customer_id==null)
                                            <a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>
                                        @else
                                            @if($item->customer->customer_type_id==null)
                                                <a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>
                                            @elseif($item->customer->customer_type_id==2)
                                                <a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>
                                            @elseif($item->customer->customer_type_id==1)
                                                <a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>
                                            @else
                                                <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $item->customer_order_id }}</td>
                                    <td class="text-left">{{ $item->customer_booking_id }}</td>
                                    <td>{{ date('d/M/Y',strtotime($item->created_at)) }}</td>
                                    <td>{{ $item->order_time }}</td>
                                    <td class="text-left">
                                        @if($item->customer_id)
                                            {{ $item->customer->customer_name }}
                                        @else
                                            {{ "Empty" }}
                                        @endif
                                    </td>
                                    <td class="text-left">
                                        @if($item->rider_id)
                                            {{ $item->rider->rider_user_name }}
                                        @else
                                            {{ "Empty" }}
                                        @endif
                                    </td>
                                    <td class="text-left">
                                        @if($item->payment_method_id)
                                            {{ "Cash_On_Delivery" }}
                                        @else
                                            {{ "Empty" }}
                                        @endif
                                    </td>
                                    <td>{{ $item->rider_delivery_fee }}</td>
                                    <td>{{ $item->bill_total_price }}</td>
                                    <td class="btn-group">
                                        <a href="/fatty/main/admin/parcel_orders/view/{{ $item->order_id }}" class="btn btn-success btn-sm mr-2"><i class="fas fa-eye"></i></a>
                                        @if($item->customer_id==null)
                                            <a href="/fatty/main/admin/parcel_orders/edit" class="btn btn-danger btn-sm mr-2 disabled" title="Do Not Edit"><i class="fas fa-edit"></i></a>
                                        @else
                                            @if ($item->customer->customer_type_id==3 && $item->order_type=="parcel")
                                                <a href="/fatty/main/admin/parcel_orders/edit/{{ $item->order_id }}" class="btn btn-primary btn-sm mr-2" title="Parcel Edit"><i class="fas fa-edit"></i></a>
                                            @else
                                                <a href="/fatty/main/admin/parcel_orders/edit" class="btn btn-danger btn-sm mr-2 disabled" title="Do Not Edit"><i class="fas fa-edit"></i></a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            <tbody>
                            </tbody>
                        </table>
                        {{ $total_orders->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
    // $(document).ready(function() {
    //     var table = $("#orders").DataTable({
    //         "paging": false,
    //         "lengthChange": true,
    //         "searching": true,
    //         "info": false,
    //         "autoWidth": true,
    //         "processing": true,
    //         dom: 'lBfrtip',
    //         buttons: [
    //         'excel', 'pdf', 'print'
    //         ],
    //     });
    // });
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
