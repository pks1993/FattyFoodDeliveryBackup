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
                        <li class="breadcrumb-item active">Pending Orders Assign</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
                </div>
                <div class="col-md-12">
                    {{-- <form method='post' action="{{ route('fatty.admin.backup.restaurants') }}">
                       @csrf
                       <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportexcel" value='Excel Export'>
                       <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportcsv" value='CSV Export'>
                    </form> --}}
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
                                <a href="{{url('fatty/main/admin/pending/orders/lists')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Orders Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <div class="pagination">
                            {{-- {{ $rider_all->appends(request()->input())->links() }} --}}
                        </div>
                        <table id="rider_all" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>Name</th>
                                <th>State</th>
                                <th>Image</th>
                                <th>Free/NotFree</th>
                                <th>Assign</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rider_all as $order)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->rider_user_name }}</td>
                                <td>
                                    @if ($order->state)
                                        {{ $order->state->state_name_mm }}
                                    @else
                                        <span style="color: red">{{ "Empty" }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->rider_image)
                                        <img src="../../../../../../uploads/rider/{{$order->rider_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                    @else
                                        <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                    @endif
                                </td>
                                <td>
                                    @if($order->is_order=="1")
                                        <a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-biking" title="Not Free"></i></a>
                                    @else
                                        <a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-biking" title="Free"></i></a>
                                    @endif
                                </td>
                                <td>
                                    {{-- @if($order->is_order=="0") --}}
                                        <button class="btn btn-primary btn-sm mr-1" title="Assign" data-toggle="modal" data-target="#rider_id{{ $order->rider_id }}"><i class="fa fa-plus-circle"></i></button>

                                    <!-- Button trigger modal -->
                                    {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rider_id{{ $order->rider_id }}">
                                      Launch demo modal
                                    </button> --}}

                                    <!-- Modal -->
                                    <div class="modal fade" id="rider_id{{ $order->rider_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <form method="POST" action="{{ route('fatty.admin.food_orders.pending_assign.notification',$order->rider_id) }}" autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                      <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Rider Leader Order Assign</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body" style="text-align: left;">
                                            <p><b>Are you sure assign "{{ $order->rider_user_name }}"</b></p>
                                            <input type="hidden" name="order_id" value="{{ $order_id }}">
                                          </div>
                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fa fa-save"></i> {{ __('Update') }}
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                  </form>
                                    </div>
                                    {{-- @else --}}
                                        {{-- <button class="btn btn-danger btn-sm mr-1" title="Not Assign"><i class="fa fa-minus-circle"></i></button> --}}
                                        {{-- <button class="btn btn-primary btn-sm mr-1" title="Assign" data-toggle="modal" data-target="#rider_id{{ $order->rider_id }}"><i class="fa fa-plus-circle"></i></button> --}}
                                    {{-- @endif --}}

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
    </section>
@endsection
@push('scripts')
<script>
    $(function () {
        $("#rider_all").DataTable({
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
