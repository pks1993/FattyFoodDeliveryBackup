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
                        <li class="breadcrumb-item active">Notification Templates</li>
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
                        <a href="{{route('fatty.admin.notification_templates.create')}}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> add notification templates</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <table id="notification_templates" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th class="text-left">Title</th>
                                <th class="text-left">Body</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notification_templates as $notification)
                                <tr class="text-center">
                                    <td>{{$loop->iteration}}</td>
                                    <td class="text-left">{{$notification->notification_title}}</td>
                                    <td class="text-left">{!! $notification->notification_body !!}</td>
                                    <td>
                                        @if($notification->notification_image)
                                            <img src="../../../uploads/notification/{{$notification->notification_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                        @else
                                            <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                        @endif
                                    </td>
                                    <td class="btn-group">
                                        <a href="{{route('fatty.admin.notification_templates.edit',['notification_template_id'=>$notification->notification_template_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>
                
                                        <form action="{{route('fatty.admin.notification_templates.destroy', $notification->notification_template_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
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
        $("#notification_templates").DataTable({
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
