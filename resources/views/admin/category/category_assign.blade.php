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
                        <li class="breadcrumb-item active">Category Assign</li>
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
                        <p style="font-size: 23px;"><b>{{ "Restaurant Type Information" }}</b></p>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <table id="categories" class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-center">CategoryName</th>
                                        <th class="text-center">Image</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category_assign as $category)
                                            <tr class="text-center">
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ $category->category->restaurant_category_name_mm }}</td>
                                                <td class="text-center">
                                                    @if($category->category->restaurant_category_image)
                                                        <img src="/uploads/category/{{$category->category->restaurant_category_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                    @else
                                                        <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($category->restaurant_category_id==8)
                                                        <a href="{{ route('fatty.admin.assign_categories.edit',$category->category_assign_id) }}"class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>
                                                    @else
                                                        <a href="{{ route('fatty.admin.assign_categories.edit',$category->category_assign_id) }}"class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>
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
<script type="text/javascript">
    $(function () {
        $("#categories").DataTable({
            // "lengthMenu": [[10,25,50, 100, 250,500, -1], [10,25,50,100, 250, 500, "All"]],
            "paging": true, // Allow data to be paged
            "lengthChange": false,
            "searching": false, // Search box and search function will be actived
            "info": true,
            "autoWidth": true,
            "processing": true,  // Show processing
        });
        $("#restaurant_category_id").select2();
    });
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
