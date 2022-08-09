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
                        <li class="breadcrumb-item active">Rider Group</li>
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
                        <a href="{{route('fatty.admin.rider_group.create')}}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Add Rider Group</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <table id="rider_group" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th class="text-left">GroupName</th>
                                <th class="text-left">BranchName</th>
                                <th class="text-left">ZoneName</th>
                                <th class="text-left">CreateAdmin</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rider_group as $group)
                                <tr class="text-center">
                                    <td>{{$loop->iteration}}</td>
                                    <td class="text-left">{{$group->rider_group_name}}</td>
                                    <td class="text-left">{{$group->branch->branch_name}}</td>
                                    <td class="text-left">{{ $group->zone->zone_name }}</td>
                                    <td class="text-left">{{$group->user->name}}</td>
                                    <td class="btn-group">
                                        <a href="{{route('fatty.admin.rider_group.edit',['rider_group_id'=>$group->rider_group_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                        <form action="{{route('fatty.admin.rider_group.destroy', $group->rider_group_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
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

    </section>
@endsection

@push('scripts')
<script>
    $(function () {
        $("#rider_group").DataTable();
    });
</script>
<script type="text/javascript">
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
