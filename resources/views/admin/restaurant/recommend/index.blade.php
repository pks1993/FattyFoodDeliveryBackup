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
                        <li class="breadcrumb-item active">Recommend Restaurant</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
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
                                <a href="{{route('fatty.admin.recommend_restaurants.create')}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus-circle"></i> Add Recommend Restaurant</a>
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Recommend Restaurant" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <div class="pagination">
                                    {{ $recommend_restaurants->appends(request()->input())->links() }}
                                </div>
                                <table id="recommend_restaurants" class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($recommend_restaurants as $restaurant)
                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td> 
                                            <td class="text-left">{{ $restaurant->restaurant->restaurant_name_mm }}</td> 
                                            <td class="text-left">{{ $restaurant->restaurant->city->city_name_mm }}</td> 
                                            <td class="text-left">{{ $restaurant->restaurant->state->state_name_mm }}</td> 
                                            <td>
                                                @if($restaurant->restaurant->restaurant_image)
                                                    <img src="../../../uploads/restaurant/{{$restaurant->restaurant->restaurant_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                @else
                                                    <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                @endif
                                            </td>
                                            <td class="btn-group text-center">
                                                <a href="{{route('fatty.admin.recommend_restaurants.edit',['recommend_restaurant_id'=>$restaurant->recommend_restaurant_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                                <form action="{{route('fatty.admin.recommend_restaurants.destroy', $restaurant->recommend_restaurant_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                                </form>
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
        $("#recommend_restaurants").DataTable({
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
