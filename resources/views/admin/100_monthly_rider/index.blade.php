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
                    <li class="breadcrumb-item active">Rider</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
            <div class="col-md-12">
                {{-- <form method='post' action="{{ route('fatty.admin.backup.riders') }}">
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
                            {{-- <a href="{{route('fatty.admin.riders.create')}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add rider</a> --}}
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Rider Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <div class="pagination">
                                    {{ $riders->appends(request()->input())->links() }}
                                </div>
                                <table id="riders" class="table table-hover">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            {{-- <th>Action</th> --}}
                                            <th>Image</th>
                                            <th class="text-left">RiderName</th>
                                            <th class="text-left">RiderPhone</th>
                                            {{-- <th class="text-left">Latitude</th> --}}
                                            {{-- <th class="text-left">Longitude</th> --}}
                                            <th class="text-left">TotalOrder</th>
                                            <th class="text-left">FoodOrder</th>
                                            <th class="text-left">ParcelOrder</th>
                                            <th class="text-left">OrderAmount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($riders as $rider)
                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td> 
                                            {{-- <td class="btn-group text-center">
                                                <a style="pointer-events: none" href="{{route('fatty.admin.riders.edit',['rider_id'=>$rider->rider_id])}}" class="btn btn-primary btn-sm mr-1" title="Edit" disabled="true"><i class="fa fa-edit"></i></a>
                                                <a href="#" class="btn btn-primary btn-sm mr-1" title="Edit" disabled="true"><i class="fa fa-edit"></i></a>
                                                <form style="pointer-events: none" action="{{route('fatty.admin.riders.destroy', $rider->rider_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                                </form>
                                                
                                                <form style="pointer-events: none" action="{{route('fatty.admin.riders.destroy', $rider->rider_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                                </form>
                                                
                                            </td> --}}
                                            <td>
                                                @if($rider->rider_image)
                                                <img src="../../../uploads/rider/{{$rider->rider_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                @else
                                                <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                @endif
                                            </td>
                                            <td class="text-left">{{ $rider->rider_user_name }}</td>
                                            <td class="text-left">{{ $rider->rider_user_phone }}</td>
                                            {{-- <td class="text-left">{{ $rider->rider_latitude }}</td> --}}
                                            {{-- <td class="text-left">{{ $rider->rider_longitude }}</td>  --}}
                                            <td class="text-center">{{ $rider->count }}</td>
                                            <td class="text-center">{{ $rider->rider_order_monthly->where('order_type','food')->count() }}</td>
                                            <td class="text-center">{{ $rider->rider_order_monthly->where('order_type','parcel')->count() }}</td>
                                            <td class="text-center">{{ $rider->rider_order_monthly->sum('bill_total_price') }}</td>
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
            $("#riders").DataTable({
            "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
            "paging": true, // Allow data to be paged
            "lengthChange": true,
            "searching": true, // Search box and search function will be actived
            "info": true,
            "autoWidth": true,
            "processing": true,  // Show processing
            // ajax: "/fatty/main/admin/customers/datatable/ssd",
            // columns: [
            // {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
            // {data: 'customer_name', name:'customer_name'},
            // {data: 'customer_phone', name:'customer_phone'},
            // {data: 'register_date', name:'register_date'},
            // {data: 'order_count', name:'order_count'},
            // {data: 'order_amount', name:'order_amount'},
            // {data: 'action', name: 'action', orderable: false, searchable: false},
            // ],
            dom: 'PlBfrtip',
            buttons: [
            'excel', 'pdf', 'print'
            ],
        });
        });
        setTimeout(function() {
            $('#successMessage').fadeOut('fast');
        }, 2000);
    </script>
    @endpush
    