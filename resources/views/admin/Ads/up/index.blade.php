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
                        <li class="breadcrumb-item active">Ads</li>
                        <li class="breadcrumb-item active">Up Ads</li>
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
                                <a href="{{ route('fatty.admin.up_ads.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus-circle"></i> add up ads</a>
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
                                    {{ $up_ads->appends(request()->input())->links() }}
                                </div>
                                <table id="up_ads" class="table table-bordered  table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($up_ads as $ads)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if($ads->image)
                                                        <img src="/uploads/up_ads/{{$ads->image}}" class="img-rounded" style="width: 100%;height: 220px;">
                                                    @else
                                                        <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 100%;height: 220px;">
                                                    @endif
                                                </td>
                                                <td class="btn-group" style="text-align: left;">
                                                    <a href="{{route('fatty.admin.up_ads.edit',['up_ads_id'=>$ads->up_ads_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                                    <form action="{{route('fatty.admin.up_ads.destroy', $ads->up_ads_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
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
        $("#up_ads").DataTable({
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
