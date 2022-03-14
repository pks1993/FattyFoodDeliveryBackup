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
                        <li class="breadcrumb-item active">Admin</li>
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
                                <a href="{{route('fatty.admin.user.create')}}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> add admin</a>
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Administrator Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                {{-- {{ $admins->appends(request()->input())->links() }} --}}
                                <table id="admin_name" class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th class="text-left">Name</th>
                                        <th class="text-left">Phone</th>
                                        <th class="text-left">Role</th>
                                        <th class="text-left">ZoneName</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($admins as $admin)
                                                <tr class="text-center">
                                                    <td>{{$loop->iteration}}</td>
                                                    <td class="text-left">{{$admin->name}}</td>
                                                    <td class="text-left">
                                                        @if($admin->phone==null)
                                                           <p style="color: red;"> {{ "EmptyNumber" }} </p>
                                                        @else
                                                            {{ $admin->phone }}
                                                        @endif
                                                    </td>
                                                    <td class="text-left">
                                                        @if(!empty($admin->getRoleNames()))
                                                            @foreach($admin->getRoleNames() as $value)
                                                                <label>
                                                                    {{$value}} 
                                                                </label>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td class="text-left">
                                                        @if($admin->zone_id=="0")
                                                            <b>{{ "Administrator" }}</b>
                                                        @else
                                                            {{ $admin->zone->zone_name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($admin->image)
                                                            <img src="/uploads/user/{{$admin->image}}" class="img-circle" style="width: 50px;height: 50px;">
                                                        @else
                                                            <img src="{{asset('../image/person.png')}}" class="img-circle" style="width: 50px;height: 50px;">
                                                        @endif
                                                    </td>
                                                    <td class="btn-group">
                                                        <a href="{{route('fatty.admin.user.edit',['user_id'=>$admin->user_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                                        <form action="{{route('fatty.admin.user.destroy', $admin->user_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
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
        $("#admin_name").DataTable({
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
