@extends('admin.layouts.master')

@section('css')
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <div class="flash-message" id="successMessage">
                        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                            @if(Session::has('alert-' . $msg))
                                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Food</li>
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
                        <a href="{{route('fatty.admin.foods.create')}}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus-circle"></i> Add Food</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <div class="pagination">
                            {{ $foods->appends(request()->input())->links() }}
                        </div>
                        <table id="foods" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>Action</th>
                                <th class="text-left">FoodName</th>
                                <th class="text-left">MenuName</th>
                                <th class="text-left">RestaurantName</th>
                                <th>Price</th>
                                <th>Image</th>
                                <th>Open/Close</th>
                                <th>Recommend</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($foods as $food)
                                <tr class="text-center">
                                    <td>{{$loop->iteration}}</td>
                                    <td class="btn-group">
                                        <a href="{{url('fatty/main/admin/foods/sub_items',['food_id'=>$food->food_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-plus-circle"></i></a>

                                        <a href="{{route('fatty.admin.foods.edit',['food_id'=>$food->food_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                        <form action="{{route('fatty.admin.foods.destroy', $food->food_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                    <td class="text-left">{{$food->food_name_mm}}</td>
                                    <td class="text-left">{{$food->menu->food_menu_name_mm}}</td>
                                    <td class="text-left">{{$food->restaurant->restaurant_name_mm}}</td>
                                    <td>{{$food->food_price}}</td>
                                    <td>
                                        @if($food->food_image)
                                            <img src="/uploads/food/{{$food->food_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                        @else
                                            <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                        @endif
                                    </td>
                                    <td>
                                        @if($food->emergency_status=="0")
                                            <p class="btn btn-success btn-sm"><i class="fas fa-lock-open"></i></p>
                                        @else
                                            <p class="btn btn-danger btn-sm"><i class="fas fa-lock"></i></p>
                                        @endif
                                    </td>
                                    <td>
                                        @if($food->food_recommend_status=="0")
                                            <p class="btn btn-success btn-sm"><i class="fas fa-thumbs-up"></i></p>
                                        @else
                                            <p class="btn btn-danger btn-sm"><i class="fas fa-thumbs-down"></i></p>
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

    </section>
@endsection

@push('scripts')
<script>
$(function () {
    $("#foods").DataTable({
        // "lengthMenu": [[10,25,50, 100, 250,500, -1], [10,25,50,100, 250, 500, "All"]],
        "paging": false, // Allow data to be paged
        "lengthChange": false,
        "searching": false, // Search box and search function will be actived
        "info": false,
        "autoWidth": true,
        "processing": false,  // Show processing
    });
});
</script>
<script type="text/javascript">
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
