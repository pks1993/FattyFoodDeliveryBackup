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
                        <li class="breadcrumb-item active">Tutorial</li>
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
                        <a href="{{route('fatty.admin.tutorials.create')}}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Add tutorials</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <table id="tutorials" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th class="text-left">Tutorial Name</th>
                                <th>Tutorial Video</th>
                                <th>Tutorial Photo</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tutorials as $tutorial)
                                <tr class="text-center">
                                    <td>{{$loop->iteration}}</td>
                                    <td class="text-left">{{$tutorial->name}}</td>
                                    <td>
                                        @if($tutorial->video)
                                            <video style="width: 250px;height: 150px;" controls>
                                                <source src="../../../uploads/tutorial/{{$tutorial->video}}" type="video/mp4">
                                                <source src="../../../uploads/tutorial/{{$tutorial->video}}" type="video/ogg">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tutorial->photo)
                                            <img src="../../../uploads/tutorial_coverphoto/{{$tutorial->photo}}" class="rounded" style="width: 130px;height: 100px;">
                                        @else
                                            <img src="{{asset('../../../image/available.png')}}" class="rounded" style="width: 150px;height: 130px;">
                                        @endif
                                    </td>
                                    <td class="btn-group text-center">
                                        <a href="{{route('fatty.admin.tutorials.edit',['tutorial_id'=>$tutorial->tutorial_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>
                                    
                                        <form action="{{route('fatty.admin.tutorials.destroy', $tutorial->tutorial_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
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
        $("#tutorials").DataTable();
    });
</script>
<script type="text/javascript">
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
