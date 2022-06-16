@extends('admin.layouts.master')

@section('css')
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-7" style="height: 20px;">
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
            <div class="col-md-12">

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
                                <h4><b>{{ "Up Ads Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <table id="up_ads" class="table table-bordered table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th>Action</th>
                                        <th>#</th>
                                        <th>Sort</th>
                                        <th>#Id</th>
                                        <th>Restaurant</th>
                                        <th>Image Myanmar</th>
                                        <th>Image English</th>
                                        <th>Image China</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tablecontents">
                                        @foreach($up_ads as $ads)
                                            <tr class="row1 text-center" data-id="{{ $ads->up_ads_id }}">
                                                <td class="btn-group" style="text-align: left;">
                                                    <a href="{{route('fatty.admin.up_ads.edit',['up_ads_id'=>$ads->up_ads_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                                    <form action="{{route('fatty.admin.up_ads.destroy', $ads->up_ads_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                                <td class="pl-3" width="20px"><i class="fa fa-sort"></i></td>
                                                <td>{{ $ads->sort_id }}</td>
                                                <td>{{ $ads->up_ads_id }}</td>
                                                <td class="text-left">{{ $ads->restaurant->restaurant_name_mm }} ({{ $ads->restaurant->restaurant_name_en }})</td>
                                                <td>
                                                    @if($ads->image_mm)
                                                        <img src="/uploads/up_ads/{{$ads->image_mm}}" class="img-rounded" style="width: 300px;height: 100px;">
                                                    @else
                                                        <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 300px;height: 100px;">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($ads->image_en)
                                                        <img src="/uploads/up_ads/{{$ads->image_en}}" class="img-rounded" style="width: 300px;height: 100px;">
                                                    @else
                                                        <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 300px;height: 100px;">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($ads->image_ch)
                                                        <img src="/uploads/up_ads/{{$ads->image_ch}}" class="img-rounded" style="width: 300px;height: 100px;">
                                                    @else
                                                        <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 300px;height: 100px;">
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
        $("#up_ads").DataTable({
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
    });
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>

<script type="text/javascript">
    $(function () {
      $( "#tablecontents" ).sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function() {
            sendOrderToServer();
        }
      });

      function sendOrderToServer() {
          var order = [];
          var token = $('meta[name="csrf-token"]').attr('content');
          $('tr.row1').each(function(index,element) {
            order.push({
              id: $(this).attr('data-id'),
              position: index+1
            });
          });
          $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ url('fatty/main/admin/ads/up_ads/sort_update') }}",
                data: {
                    order: order,
                    _token: token
                },
            success: function(response) {
                if (response.status == "success") {
                    location.reload();
                } else {
                    location.reload();
                }
            }
          });
        }
      });
  </script>
@endpush
