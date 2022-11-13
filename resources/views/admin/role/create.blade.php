@extends('admin.layouts.master')

@section('css')
@endsection
@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
              @if(Session('error'))
                  <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                      {{Session('error')}}
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
              @endif
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Role</li>
              <li class="breadcrumb-item active">Add</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title">Add a new Role</h3>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{route('fatty.admin.roles.index')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>



                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.roles.store') }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="zone_id" class="col-md-12 col-form-label">{{ __('AdminName') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="zone_id" style="width: 100%;" class="form-control @error('zone_id') is-invalid @enderror" name="zone_id" value="{{ old('zone_id') }}" autocomplete="zone_id" autofocus>
                                          @if(Auth::user()->is_main_admin=="1")
                                            <option value="{{ "0" }}">Super Admin</option>
                                            @foreach($zones as $value)
                                              <option value="{{ $value->zone_id }}">{{ $value->zone_name }}</option>
                                            @endforeach
                                          @else
                                            @foreach($zones as $value)
                                              <option value="{{ $value->zone_id }}">{{ $value->zone_name }}</option>
                                            @endforeach
                                          @endif
                                        </select>
                                        @error('zone_id')
                                          <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                          </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="name" class="col-md-12 col-form-label">{{ __('Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autocomplete="name" autofocus>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                 <div class="form-group row">
                                  <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                       <h3><strong>Admin Permissions: </strong></h3><strong><input type="checkbox" name="select-all" id="select-all"> select all</strong>
                                          @foreach($permission_admin->chunk(4) as $permissions)
                                            <div class="row">
                                              @foreach($permissions as $value)
                                                <div class="col-md-3">
                                                  <label>
                                                    {{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                                    {{ $value->name }}
                                                  </label>
                                                </div>
                                              @endforeach
                                            </div>
                                          @endforeach
                                          @foreach($permission_tax->chunk(4) as $permissions)
                                            <div class="row">
                                              @foreach($permissions as $value)
                                                <div class="col-md-3">
                                                  <label>
                                                    {{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                                    {{ $value->name }}
                                                  </label>
                                                </div>
                                              @endforeach
                                            </div>
                                          @endforeach
                                          <hr>
                                      <h3><strong>Other Permissions:</strong></h3>
                                        @foreach($permission_other->chunk(4) as $permissions)
                                          <div class="row">
                                              @foreach($permissions as $value)
                                                <div class="col-md-3">
                                                  <label>
                                                    {{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                                    {{ $value->name }}
                                                  </label>
                                                </div>
                                              @endforeach
                                            </div>
                                        @endforeach
                                        <hr>
                                        <h3><strong>Other Permissions:</strong></h3>
                                        @foreach($order->chunk(3) as $permissions)
                                          <div class="row">
                                              @foreach($permissions as $value)
                                                <div class="col-md-4">
                                                  <label>
                                                    {{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                                    {{ $value->name }}
                                                  </label>
                                                </div>
                                              @endforeach
                                            </div>
                                        @endforeach 
                                        @foreach($invoice->chunk(3) as $permissions)
                                          <div class="row">
                                              @foreach($permissions as $value)
                                                <div class="col-md-4">
                                                  <label>
                                                    {{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                                    {{ $value->name }}
                                                  </label>
                                                </div>
                                              @endforeach
                                            </div>
                                        @endforeach 
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Create') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/roles')}}" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-ban"></i> {{ __('Cancel') }}
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br><br><br><br>
  </section>

@endsection
@section('script')
<script type="text/javascript">
$('#zone_id').select2();
// Listen for click on toggle checkbox
$('#select-all').click(function(event) {  
    if(this.checked) {
        // Iterate each checkbox
        $(':checkbox').each(function() {
            this.checked = true;                        
        });
    }else {
        $(':checkbox').each(function() {
            this.checked = false;                       
        });
    }
});
</script>
@endsection
