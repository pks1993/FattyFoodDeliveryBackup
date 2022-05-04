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
                    <li class="breadcrumb-item active">Category</li>
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
                            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#category"><i class="fa fa-plus"></i> add category</a>

                            <!-- Modal -->
                            <div class="modal fade" id="category" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Restaurant Type</h5>
                                            <a class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </a>
                                        </div>
                                        <form method="POST" action="{{ route('fatty.admin.restaurant_categories.store') }}" autocomplete="off" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                @csrf
                                                <div class="form-group row">
                                                    <label for="restaurant_category_name_mm" class="col-md-12 col-form-label">{{ __('Category Name Myanmar') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="restaurant_category_name_mm" type="text" class="form-control @error('restaurant_category_name_mm') is-invalid @enderror" name="restaurant_category_name_mm" value="{{ old('restaurant_category_name_mm') }}" placeholder="{{ "Enter Category Name By Myanmar" }}" autocomplete="restaurant_category_name_mm" autofocus required='true'>
                                                        @error('restaurant_category_name_mm')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="restaurant_category_name_en" class="col-md-12 col-form-label">{{ __('Category Name English') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="restaurant_category_name_en" type="text" class="form-control @error('restaurant_category_name_en') is-invalid @enderror" name="restaurant_category_name_en" value="{{ old('restaurant_category_name_en') }}" placeholder="{{ "Enter Category Name By English" }}" autocomplete="restaurant_category_name_en" autofocus required='true'>
                                                        @error('restaurant_category_name_en')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="restaurant_category_name_ch" class="col-md-12 col-form-label">{{ __('Category Name China') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="restaurant_category_name_ch" type="text" class="form-control @error('restaurant_category_name_ch') is-invalid @enderror" name="restaurant_category_name_ch" value="{{ old('restaurant_category_name_ch') }}" placeholder="{{ "Enter Category Name By China" }}" autocomplete="restaurant_category_name_ch" autofocus required='true'>
                                                        @error('restaurant_category_name_ch')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="restaurant_category_image" class="col-md-12 col-form-label">{{ __('Category Image') }} </label>
                                                    <div class="col-md-6">
                                                        <input type="file" style="height: auto;" id="restaurant_category_image" class="form-control @error('restaurant_category_image') is-invalid @enderror" name="restaurant_category_image" autocomplete="restaurant_category_image" onchange="loadFileImage(event)">
                                                        @error('restaurant_category_image')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mt-2">
                                                        <div class="form-group">
                                                            <image src="{{asset('../../../image/available.png')}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                                        </div>
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
                            {{-- <div class="pagination">
                                {{ $categories->appends(request()->input())->links() }}
                            </div> --}}
                            <table id="categories" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th class="text-center">CategoryType</th>
                                        <th class="text-left">CategoryNameMM</th>
                                        <th class="text-left">CategoryNameEN</th>
                                        <th class="text-left">CategoryNameCh</th>
                                        <th>Assign</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($category->category_assign)
                                                @if($category->category_assign->category_type_id==1)
                                                    <p style="width: 100px;" class="btn btn-sm btn-success" style="color: white" onclick="return confirm(\'Are you sure want to close assign status this category?\')" title="Category Assign">{{ $category->category_assign->category_type->category_type_name }}</p>
                                                @elseif($category->category_assign->category_type_id==2)
                                                    <p style="width: 100px;" class="btn btn-sm btn-danger" style="color: white" onclick="return confirm(\'Are you sure want to close assign status this category?\')" title="Category Assign">{{ $category->category_assign->category_type->category_type_name }}</p>
                                                @elseif($category->category_assign->category_type_id==3)
                                                    <p style="width: 100px;" class="btn btn-sm btn-primary" style="color: white" onclick="return confirm(\'Are you sure want to close assign status this category?\')" title="Category Assign">{{ $category->category_assign->category_type->category_type_name }}</p>
                                                @else
                                                    <p style="width: 100px;" class="btn btn-sm btn-secondary" style="color: white" onclick="return confirm(\'Are you sure want to create assign status this category?\')" title="Category Assign">Empty Assign</p>
                                                @endif
                                            @else
                                                <p style="width: 100px;" class="btn btn-sm btn-secondary" style="color: white" onclick="return confirm(\'Are you sure want to create assign status this category?\')" title="Category Assign">Empty Assign</p>
                                            @endif
                                        </td>
                                        <td class="text-left">{{ $category->restaurant_category_name_mm }}</td>
                                        <td class="text-left">{{ $category->restaurant_category_name_en }}</td>
                                        <td class="text-left">{{ $category->restaurant_category_name_ch }}</td>
                                        <td>
                                            @if($category->category_assign)
                                                <a href="{{ url('fatty/main/admin/restaurant/categories/assign/edit',$category->category_assign->category_assign_id) }}" class="btn btn-sm btn-primary" style="color: white;width: 45px;" onclick="return confirm(\'Are you sure want to create assign status this category?\')" title="Category Assign Edit">Edit</a>
                                                {{-- <a href="#" class="btn btn-sm btn-primary" style="color: white;width: 45px;" onclick="return confirm(\'Are you sure want to create assign status this category?\')" title="Category Assign Edit">Edit</a> --}}
                                            @else
                                                <a href="{{ url('fatty/main/admin/restaurant/categories/assign/create',$category->restaurant_category_id) }}" class="btn btn-sm btn-success" style="color: white;width: 45px;" onclick="return confirm(\'Are you sure want to create assign status this category?\')" title="Category Assign Create">Add</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($category->restaurant_category_image)
                                            <img src="/uploads/category/{{$category->restaurant_category_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                            @else
                                            <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                            @endif
                                        </td>
                                        <td class="btn-group" style="text-align: left;">
                                            <a href="#"class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#type{{ $category->restaurant_category_id }}"><i class="fa fa-edit"></i></a>

                                            <!-- Modal -->
                                            <div class="modal fade" id="type{{ $category->restaurant_category_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                                            <a class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </a>
                                                        </div>
                                                        <form method="POST" action="{{ route('fatty.admin.restaurant_categories.update',$category->restaurant_category_id) }}" autocomplete="off" enctype="multipart/form-data">
                                                            <div class="modal-body">
                                                                @csrf
                                                                <div class="form-group row">
                                                                    <label for="restaurant_category_name_mm" class="col-md-12 col-form-label">{{ __('Category Name Myanmar') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                                    <div class="col-md-12">
                                                                        <input id="restaurant_category_name_mm" type="text" class="form-control @error('restaurant_category_name_mm') is-invalid @enderror" name="restaurant_category_name_mm" value="{{ $category->restaurant_category_name_mm }}" placeholder="{{ "Enter Category Name By Myanmar" }}" autocomplete="restaurant_category_name_mm" autofocus required='true'>
                                                                        @error('restaurant_category_name_mm')
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label for="restaurant_category_name_en" class="col-md-12 col-form-label">{{ __('Category Name English') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                                    <div class="col-md-12">
                                                                        <input id="restaurant_category_name_en" type="text" class="form-control @error('restaurant_category_name_en') is-invalid @enderror" name="restaurant_category_name_en" value="{{ $category->restaurant_category_name_en }}" placeholder="{{ "Enter Category Name By English" }}" autocomplete="restaurant_category_name_en" autofocus required='true'>
                                                                        @error('restaurant_category_name_en')
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label for="restaurant_category_name_ch" class="col-md-12 col-form-label">{{ __('Category Name China') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                                    <div class="col-md-12">
                                                                        <input id="restaurant_category_name_ch" type="text" class="form-control @error('restaurant_category_name_ch') is-invalid @enderror" name="restaurant_category_name_ch" value="{{ $category->restaurant_category_name_ch }}" placeholder="{{ "Enter Category Name By China" }}" autocomplete="restaurant_category_name_ch" autofocus required='true'>
                                                                        @error('restaurant_category_name_ch')
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label for="image" class="col-md-12 col-form-label">{{ __('Category Image') }} </label>
                                                                    <div class="col-md-6">
                                                                        <input type="file" style="height: auto;" id="image_edit" class="form-control @error('image') is-invalid @enderror" name="image_edit" autocomplete="image" onchange="loadFileImageTwo(event)">
                                                                        @error('image')
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="col-md-6 mt-2">
                                                                        <div class="form-group">
                                                                            @if($category->restaurant_category_image==null)
                                                                            <image src="{{asset('../image/available.png')}}" id="imageTwo" style="width: 100%;height: 150px;"></image>
                                                                            @else
                                                                            <image src="../../../../../uploads/category/{{$category->restaurant_category_image}}" id="imageTwo" style="width: 100%;height: 150px;"></image>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <form action="{{route('fatty.admin.restaurant_categories.destroy', $category->restaurant_category_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
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
        $("#categories").DataTable({
            "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
                "paging": true, // Allow data to be paged
                "lengthChange": true,
                "searching": true, // Search box and search function will be actived
                "info": true,
                "autoWidth": true,
                "processing": true,  // Show processing
                dom: 'lBfrtip',
                buttons: [
                 'excel', 'pdf', 'print'
                ],
        });
        $("#restaurant_id").select2();
    });
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
    //Image Show
    var loadFileImage= function(event) {
        var image = document.getElementById('imageOne');
        image.src = URL.createObjectURL(event.target.files[0]);
    };
    var loadFileImageTwo= function(event) {
        var image2 = document.getElementById('imageTwo');
        image2.src = URL.createObjectURL(event.target.files[0]);
    };
</script>
@endpush
