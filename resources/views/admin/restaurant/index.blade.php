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
                                {{-- <a href="{{route('fatty.admin.restaurants.create')}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add Restaurant</a> --}}
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Restaurant Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <div class="pagination">
                                    {{ $restaurants->appends(request()->input())->links() }}
                                </div>
                                <table id="restaurants" class="table table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th>Action</th>
                                        <th>Image</th>
                                        <th class="text-left">NameMyanmar</th>
                                        <th class="text-left">NameEnglish</th>
                                        <th class="text-left">NameChina</th>
                                        <th class="text-left">CategoryName</th>
                                        <th class="text-left">Address</th>
                                        <th class="text-left">CityName</th>
                                        <th class="text-left">StateName</th>
                                        <th class="text-left">UserName</th>
                                        <th class="text-left">Password</th>
                                        <th class="text-left">Open/Close</th>
                                        <th class="text-left">Approved</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($restaurants as $restaurant)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td> 
                                        <td class="btn-group text-center">
                                            {{-- <a style="pointer-events: none" href="{{route('fatty.admin.restaurants.edit',['restaurant_id'=>$restaurant->restaurant_id])}}" class="btn btn-primary btn-sm mr-1" title="Edit" disabled="true"><i class="fa fa-edit"></i></a> --}}
                                            <a href="#" class="btn btn-primary btn-sm mr-1" title="Edit" disabled="true"><i class="fa fa-edit"></i></a>
                                            <form style="pointer-events: none" action="{{route('fatty.admin.restaurants.destroy', $restaurant->restaurant_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                            </form>

                                            {{-- <form style="pointer-events: none" action="{{route('fatty.admin.restaurants.destroy', $restaurant->restaurant_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                            </form> --}}
                                        
                                        </td>
                                        <td>
                                            @if($restaurant->restaurant_image)
                                                <img src="../../../uploads/restaurant/{{$restaurant->restaurant_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                            @else
                                                <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                            @endif
                                        </td>
                                        <td class="text-left">{{ $restaurant->restaurant_name_mm }}</td>
                                        <td class="text-left">{{ $restaurant->restaurant_name_en }}</td>
                                        <td class="text-left">{{ $restaurant->restaurant_name_ch }}</td>
                                        <td class="text-left">{{ $restaurant->category->restaurant_category_name_mm }}</td> 
                                        <td class="text-left">{{ $restaurant->restaurant_address }}</td>
                                        <td class="text-left">{{ $restaurant->city->city_name_mm }}</td>
                                        <td class="text-left">{{ $restaurant->state->state_name_mm }}</td>
                                        <td class="text-left">{{ $restaurant->restaurant_user->restaurant_user_phone }}</td>
                                        <td class="text-left">{{ $restaurant->restaurant_user->restaurant_user_password }}</td>
                                        <td>
                                            @if($restaurant->restaurant_emergency_status=="0")
                                                <a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-lock-open" title="Restaurant Open"></i></a>
                                            @else
                                                <a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-lock" title="Restaurant Close"></i></a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($restaurant->restaurant_user->is_admin_approved=="0")
                                                <a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>
                                            @else
                                                <a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>
                                            @endif
                                        </td>
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
        $("#restaurants").DataTable({
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
