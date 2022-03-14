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
                        <li class="breadcrumb-item active">Roles</li>
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
                        <a href="{{route('fatty.admin.roles.create')}}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> add role
                        </a>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="roles" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">No.</th>          
                                <th>RoleName</th>
                                <th>AdminName</th>
                                <th>ZoneName</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                    @foreach($main_roles as $val)
                                        <tr class="text-center">
                                            <td>{{$loop->iteration}}</td>
                                            <td class="text-left">{{$val->name}}</td>
                                            <td class="text-left">
                                                @foreach($admins as $admin)
                                                    @if($admin->user_id==$val->user_id)
                                                        {{ $admin->name }} <b>( AdminID#{{ $admin->user_id }} )</b>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td class="text-left">
                                                @if($val->zone_id=="0")
                                                    {{ "Super Admin" }}
                                                @else
                                                    @foreach($zones as $zone)
                                                        @if($zone->zone_id==$val->zone_id)
                                                            {{ $zone->zone_name }}
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="btn-group">
                                                <a href="{{route('fatty.admin.roles.edit', $val->id)}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                                <form action="{{route('fatty.admin.roles.destroy', $val->id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
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
        $("#roles").DataTable();
    });
</script>
<script type="text/javascript">
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
