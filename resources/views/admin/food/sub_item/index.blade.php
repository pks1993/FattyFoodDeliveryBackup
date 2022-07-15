@extends('admin.layouts.master')

@section('css')
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6" style="height: 20px;">
                    <div class="flash-message" id="successMessage">
                        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                        @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-6" style="height: 20px">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Food Sub Item</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content mt-3">
        <div class="row">
            <div class="col-md-12 mb-3" style="height: 20px">
                {{-- <a href="{{url('fatty/main/admin/foods')}}" class="btn btn-danger btn-sm"> --}}
                <a href="{{url('fatty/main/admin/restaurants/food/list',$foods->restaurant_id)}}" class="btn btn-danger btn-sm">
                <i class="fa fa-backward"></i> Back</a>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{route('fatty.admin.foods.sub_items.create',$foods->food_id)}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus-circle"></i> Add Section & Option</a>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <div class="pagination">
                            {{ $food_subitem->appends(request()->input())->links() }}
                        </div>
                        <table id="food_subitem" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <!-- <th>Option</th> -->
                                <th>Action</th>
                                <th class="text-left">RequiredChoice</th>
                                <th class="text-left">SectionNameMyanmar</th>
                                <th class="text-left">SectionNameEnglish</th>
                                <th class="text-left">SectionNameChina</th>
                                <th class="text-left">FoodName</th>
                                <th class="text-left">RestaurantName</th>
                                <th>FoodImage</th>
                                <th>RestaurantImage</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($food_subitem as $subitem)
                                <tr class="text-center">
                                    <td>{{$loop->iteration}}</td>
                                    <td class="btn-group">

                                        <a href="{{route('fatty.admin.foods.sub_items.edit',['food_sub_item_id'=>$subitem->food_sub_item_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                        <form action="{{route('fatty.admin.foods.sub_items.destroy', $subitem->food_sub_item_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                    {{-- <td>
                                        <a href="{{route('fatty.admin.foods.sub_items.data.create',['food_sub_item_id'=>$subitem->food_sub_item_id])}}" class="btn btn-success btn-sm mr-1" title="Add Option"><i class="fa fa-plus-circle"></i></a>
                                    </td> --}}
                                    <td class="text-left">
                                        @if($subitem->required_type=="0")
                                            <span class="fa fa-square" style="color: blue;"></span> CheckBox
                                        @else
                                            <span class="fa fa-circle" style="color: red;"></span> Radio
                                        @endif
                                    </td>
                                    <td class="text-left">{{$subitem->section_name_mm}}</td>
                                    <td class="text-left">{{$subitem->section_name_en}}</td>
                                    <td class="text-left">{{$subitem->section_name_ch}}</td>
                                    <td class="text-left">{{$subitem->food->food_name_mm}}</td>
                                    <td class="text-left">{{$subitem->restaurant->restaurant_name_mm}}</td>
                                    <td>
                                        @if($subitem->food->food_image)
                                            <img src="../../../../../uploads/food/{{$subitem->food->food_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                        @else
                                            <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                        @endif
                                    </td>
                                    <td>
                                        @if($subitem->restaurant->restaurant_image)
                                            <img src="../../../../../uploads/restaurant/{{$subitem->restaurant->restaurant_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                        @else
                                            <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
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


    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="card-title" style="font-weight: 550;">Sub Items Data</p>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <div class="pagination">
                            {{ $food_subitem_data->appends(request()->input())->links() }}
                        </div>
                        <table id="food_subitem_data" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>Action</th>
                                <th class="text-left">ItemNameMyanmar</th>
                                <th class="text-left">ItemNameEnglish</th>
                                <th class="text-left">ItemNameChina</th>
                                <th class="text-left">ItemPrice</th>
                                <th class="text-left">SectionName</th>
                                <th class="text-left">Choice</th>
                                <th>InStock</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($food_subitem_data as $subitem)
                                <tr class="text-center">
                                    <td>{{$loop->iteration}}</td>
                                    <td class="btn-group">
                                        <a href="{{route('fatty.admin.foods.sub_items.data.edit',['food_sub_item_data_id'=>$subitem->food_sub_item_data_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                        <form action="{{route('fatty.admin.foods.sub_items.data.destroy', $subitem->food_sub_item_data_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                    <td class="text-left">{{$subitem->item_name_mm}}</td>
                                    <td class="text-left">{{$subitem->item_name_en}}</td>
                                    <td class="text-left">{{$subitem->item_name_ch}}</td>
                                    <td class="text-left">{{$subitem->food_sub_item_price}}</td>
                                    <td class="text-left">{{$subitem->food_sub_item->section_name_mm}}</td>
                                    <td class="text-left">
                                        @if($subitem->food_sub_item->required_type=="0")
                                            <span class="fa fa-square" style="color: blue;"></span> CheckBox
                                        @else
                                            <span class="fa fa-circle" style="color: red;"></span> Radio
                                        @endif
                                    </td>
                                    <td>
                                        @if($subitem->instock=="1")
                                            <p class="btn btn-success btn-sm"><i class="fas fa-lock-open"></i></p>
                                        @else
                                            <p class="btn btn-danger btn-sm"><i class="fas fa-lock"></i></p>
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
    $("#food_subitem").DataTable({
        // "lengthMenu": [[10,25,50, 100, 250,500, -1], [10,25,50,100, 250, 500, "All"]],
        "paging": false, // Allow data to be paged
        "lengthChange": false,
        "searching": false, // Search box and search function will be actived
        "info": false,
        "autoWidth": true,
        "processing": false,  // Show processing
    });
    $("#food_subitem_data").DataTable({
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
