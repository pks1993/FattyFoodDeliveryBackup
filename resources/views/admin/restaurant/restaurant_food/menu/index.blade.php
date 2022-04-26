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
                        <li class="breadcrumb-item active">Food Menu</li>
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
                                <!-- Button trigger modal -->
                                <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#foodmenu"><i class="fa fa-plus"></i> add food menu</a>

                                <!-- Modal -->
                                <div class="modal fade" id="foodmenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                  <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Restaurant Type</h5>
                                            <a class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </a>
                                        </div>
                                        <form method="POST" action="{{ route('fatty.admin.food_menu.store') }}" autocomplete="off" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                @csrf
                                                <div class="form-group row">
                                                    <label for="food_menu_name" class="col-md-12 col-form-label">{{ __('Food Menu Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="food_menu_name" type="text" class="form-control @error('food_menu_name') is-invalid @enderror" name="food_menu_name" value="{{ old('food_menu_name') }}" placeholder="{{ "Enter Restaurant Type Name" }}" autocomplete="food_menu_name" autofocus required='true'>
                                                        @error('food_menu_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="restaurant_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <select id="restaurant_id" class="form-control @error('restaurant_id') is-invalid @enderror" name="restaurant_id" value="{{ old('restaurant_id') }}" autocomplete="restaurant_id" autofocus required='true'>
                                                            <option value="">{{ "Choose Restaurant" }}</option>
                                                            @foreach($restaurants as $value)
                                                                <option value="{{ $value->restaurant_id }}">{{ $value->restaurant_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('restaurant_id')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Create</button>
                                            </div>
                                        </form>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Restaurant Type Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <div class="pagination">
                                    {{ $food_menu->appends(request()->input())->links() }}
                                </div>
                                <table id="food_menu" class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th>MenuName</th>
                                        <th>RestaurantName</th>
                                        <th class="text-center">Image</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($food_menu as $menu)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $menu->food_menu_name }}</td>
                                                    <td>{{ $menu->restaurant->restaurant_name_mm }}</td>
                                                    <td class="text-center">
                                                        @if($menu->restaurant->restaurant_image)
                                                            <img src="/uploads/restaurant/{{$menu->restaurant->restaurant_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                        @else
                                                            <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                        @endif
                                                    </td>
                                                    <td class="btn-group">
                                                        <a href="{{route('fatty.admin.food_menu.edit',['food_menu_id'=>$menu->food_menu_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                                        <form action="{{route('fatty.admin.food_menu.destroy', $menu->food_menu_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
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
<script type="text/javascript">
    $(function () {
        $("#food_menu").DataTable({
            // "lengthMenu": [[10,25,50, 100, 250,500, -1], [10,25,50,100, 250, 500, "All"]],
            "paging": false, // Allow data to be paged
            "lengthChange": false,
            "searching": false, // Search box and search function will be actived
            "info": false,
            "autoWidth": true,
            "processing": false,  // Show processing
        });
        $("#restaurant_id").select2();
    });
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
