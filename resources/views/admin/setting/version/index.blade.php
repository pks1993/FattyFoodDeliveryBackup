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
</style>
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
                    <li class="breadcrumb-item active">Setting</li>
                    <li class="breadcrumb-item active">Version Lists</li>
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
                        {{-- <div class="col-6">
                            <a href="{{route('fatty.admin.rider_group.create')}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add Rider Group</a>
                        </div> --}}
                        <div class="col-6">
                            <h4><b>Version Information</b></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="version_data" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">No.</th>
                                    <th>OsType</th>
                                    <th style="text-align: center">Link</th>
                                    <th style="text-align:center">CurrentVersion</th>
                                    <th style="text-align:center">ForceUpdate</th>
                                    <th style="text-align:center">Available</th>
                                    <th style="text-align:center">Action</th>
                                </tr>
                            </thead>
                                @foreach ($version_data as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-left">{{ $item->os_type }}</td>
                                    <td><a href="{{ $item->link }}">{{ $item->link }}</a></td>
                                    <td>{{ $item->current_version }}</td>
                                    <td style="color: white">
                                        @if($item->is_force_update==1)
                                            <a class="brn btn-sm btn-success">Open</a>
                                        @else
                                            <a class="brn btn-sm btn-danger">Close</a>
                                        @endif
                                    </td>
                                    <td style="color: white">
                                        @if($item->is_available==1)
                                            <a class="brn btn-sm btn-success">Open</a>
                                        @else
                                            <a class="brn btn-sm btn-danger">Close</a>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#q{{ $item->version_update_id }}"><i class="fa fa-edit"></i></button>
                                        <form method="post" action="{{route('fatty.admin.version.update',$item->version_update_id)}}" id="form">
                                            @csrf
                                            <div class="modal fade" id="q{{ $item->version_update_id }}" tabindex="-1" role="dialog" aria-labelledby="new_state" aria-hidden="true" style="text-align: left;">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title" id="new_state">"{{ $item->os_type }}" Edit</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="os_type" class="col-form-label">Os Type:</label>
                                                            <input type="text" class="form-control" value="{{ $item->os_type }}" name="os_type" disabled>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="link" class="col-form-label">Link:</label>
                                                            <input type="text" class="form-control" value="{{ $item->link }}" name="link">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="current_version" class="col-form-label">Current Version:</label>
                                                            <input type="text" class="form-control" value="{{ $item->current_version }}" name="current_version">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="is_force_update" class="col-form-label">ForceUpdate:</label>
                                                            <select class="form-control" name="is_force_update" id="is_force_update">
                                                                @if($item->is_force_update==0)
                                                                    <option value="0">Close</option>
                                                                    <option value="1">Open</option>
                                                                @else
                                                                    <option value="1">Open</option>
                                                                    <option value="0">Close</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="is_available" class="col-form-label">Available:</label>
                                                            <select class="form-control" name="is_available" id="is_available">
                                                                @if($item->is_available==0)
                                                                    <option value="0">Close</option>
                                                                    <option value="1">Open</option>
                                                                @else
                                                                    <option value="1">Open</option>
                                                                    <option value="0">Close</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            <tbody>
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
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
