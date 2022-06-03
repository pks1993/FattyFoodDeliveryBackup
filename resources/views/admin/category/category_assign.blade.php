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
                                        <th class="text-center">#</th>
                                        <th class="text-center">SortNo.</th>
                                        <th class="text-center">#ID</th>
                                        <th class="text-center">#sortId</th>
                                        <th>CategoryName</th>
                                        <th class="text-center">Type.</th>
                                        <th class="text-center">Image</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tablecontents">
                                        @foreach($category_assign as $category)
                                            <tr class="row1 text-center" data-id="{{ $category->category_assign_id }}">
                                                <td class="pl-3" width="20px"><i class="fa fa-sort"></i></td>
                                                {{-- <td class="text-center" width="20px;">{{ $category->sort_id }}</td> --}}
                                                <td class="text-center" width="20px;">{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ $category->category_assign_id }}</td>
                                                <td class="text-center">{{ $category->sort_id }}</td>
                                                <td class="text-left">{{ $category->category->restaurant_category_name_mm }} ({{ $category->category->restaurant_category_name_en }})</td>
                                                <td class="text-center">
                                                    @if($category->category_type_id==1)
                                                        <p style="width: 100px;" class="btn btn-sm btn-success">{{ $category->category_type->category_type_name }}</p>
                                                    @elseif($category->category_type_id==2)
                                                        <p style="width: 100px;" class="btn btn-sm btn-danger">{{ $category->category_type->category_type_name }}</p>
                                                    @elseif($category->category_type_id==3)
                                                        <p style="width: 100px;" class="btn btn-sm btn-primary">{{ $category->category_type->category_type_name }}</p>
                                                    @else
                                                        <p style="width: 100px;" class="btn btn-sm btn-secondary">Empty</p>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($category->category->restaurant_category_image)
                                                        <img src="/uploads/category/{{$category->category->restaurant_category_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                    @else
                                                        <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{route('fatty.admin.assign_categorises.destroy', $category->category_assign_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                    {{-- @if($category->restaurant_category_id==8)
                                                        <a href="{{ route('fatty.admin.assign_categories.edit',$category->category_assign_id) }}"class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>
                                                    @else
                                                        <a href="{{ route('fatty.admin.assign_categories.edit',$category->category_assign_id) }}"class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>
                                                    @endif --}}
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
            "lengthMenu": [[25, 50, 100, 250, -1], [25, 50,100, 250, "All"]],
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
            url: "{{ url('fatty/main/admin/restaurant/categories/assign/sort/update') }}",
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
