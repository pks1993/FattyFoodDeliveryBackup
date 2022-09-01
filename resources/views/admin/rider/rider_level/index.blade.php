@extends('admin.layouts.master')

@section('css')
<style>
    form>.fa {
        display: none;
    }
    .dt-buttons>button{
        border-radius: revert;
        margin-top: 15px;
        margin-right: 5px;
    }
    .dataTables_length >label {
        margin-right: 15px !important;
        margin-top: 15px;
    }
    .number {
        text-align: center;
    }
    .order_amount {
        text-align: center;
    }
    .order_count {
        text-align: center;
    }
    .action {
        text-align: center;
    }
    .register_date {
        text-align: center;
    }
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
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
                    <li class="breadcrumb-item active">Riders</li>
                    <li class="breadcrumb-item active">Level</li>
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
                            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#riderlevel"><i class="fa fa-plus-circle"></i> add level</a>

                            <!-- Modal -->
                            <div class="modal fade" id="riderlevel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Restaurant Level</h5>
                                            <a class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </a>
                                        </div>
                                        <form method="POST" action="{{ route('fatty.admin.riders_level.store') }}" autocomplete="off" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                @csrf
                                                <div class="form-group row">
                                                    <label for="level_name" class="col-md-12 col-form-label">{{ __('Level Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="level_name" type="text" class="form-control @error('level_name') is-invalid @enderror" name="level_name" value="{{ old('level_name') }}" placeholder="{{ "Enter Level Name" }}" autocomplete="level_name" autofocus required='true'>
                                                        @error('level_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="max_order" class="col-md-12 col-form-label">{{ __('Max Order') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="max_order" type="text" class="form-control @error('max_order') is-invalid @enderror" name="max_order" value="{{ old('max_order') }}" placeholder="{{ "Enter Max Order" }}" autocomplete="max_order" autofocus required='true'>
                                                        @error('max_order')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="max_distance" class="col-md-12 col-form-label">{{ __('Max Distance') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="max_distance" type="text" class="form-control @error('max_distance') is-invalid @enderror" name="max_distance" value="{{ old('max_distance') }}" placeholder="{{ "Enter Max Distance" }}" autocomplete="max_distance" autofocus required='true'>
                                                        @error('max_distance')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Create</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <h4><b>{{ "Rider Level Information" }}</b></h4>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="rider_level" class="table table-bordered table-striped table-hover display nowrap" border="0" cellspacing="5" cellpadding="5">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th>LevelName</th>
                                <th class="text-center">MaxOrder</th>
                                <th class="text-center">MaxDistance(Km)</th>
                                <th class="text-center">Edit</th>
                                <th class="text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rider_level as $level)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $level->level_name }}</td>
                                <td>{{ $level->max_order }}</td>
                                <td>{{ $level->max_distance }}</td>
                                <td>
                                    <a href="#"class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#type{{ $level->rider_level_id }}"><i class="fa fa-edit"></i></a>
                                </td>
                                <td>
                                    <form action="{{route('fatty.admin.riders_level.destroy', $level->rider_level_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Modal -->
                            <div class="modal fade" id="type{{ $level->rider_level_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                            <a class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </a>
                                        </div>
                                        <form method="POST" action="{{ route('fatty.admin.riders_level.update',$level->rider_level_id) }}" autocomplete="off" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                @csrf
                                                <div class="form-group row">
                                                    <label for="level_name" class="col-md-12 col-form-label">{{ __('Level Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="level_name" type="text" class="form-control @error('level_name') is-invalid @enderror" name="level_name" value="{{ $level->level_name }}" placeholder="{{ "Enter Level Name" }}" autocomplete="level_name" autofocus required='true'>
                                                        @error('level_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="max_order" class="col-md-12 col-form-label">{{ __('Max Order') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="max_order" type="text" class="form-control @error('max_order') is-invalid @enderror" name="max_order" value="{{ $level->max_order }}" placeholder="{{ "Enter Max Order" }}" autocomplete="max_order" autofocus required='true'>
                                                        @error('max_order')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="max_distance" class="col-md-12 col-form-label">{{ __('Max Distance') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                                    <div class="col-md-12">
                                                        <input id="max_distance" type="text" class="form-control @error('max_distance') is-invalid @enderror" name="max_distance" value="{{ $level->max_distance }}" placeholder="{{ "Enter Max Distance" }}" autocomplete="max_distance" autofocus required='true'>
                                                        @error('max_distance')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @endsection
    @push('scripts')
    <script>

    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2500);

    $(document).ready(function() {
            // DataTables initialisation
            var table = $("#rider_level").DataTable({
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
    </script>
    @endpush
