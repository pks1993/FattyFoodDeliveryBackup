@extends('admin.layouts.master')

@section('css')
@endsection
@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-9">
              @if(Session('error'))
                  <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                      {{Session('error')}}
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
              @endif
          </div>
          <div class="col-sm-3">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Users</li>
              <li class="breadcrumb-item active">Register</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title">Admin Register</h3>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/user')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.user.store') }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="name" class="col-md-12 col-form-label">{{ __('User Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"  autocomplete="name" autofocus>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="is_main_admin" class="col-md-12 col-form-label">{{ __('Super Admin') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    @if(Auth::user()->is_main_admin=="1")
                                        <div class="col-md-6 text-md-left">
                                            <input id="is_main_admin_one" type="radio" name="is_main_admin" value="1" onclick="myFunction1()"> {{ "Super Admin" }}
                                        </div>
                                        <div class="col-md-6 text-md-left">
                                            <input id="is_main_admin_two" type="radio" name="is_main_admin" value="0" onclick="myFunction2()" checked="true"> {{ "Zone Admin" }}
                                        </div>
                                    @else
                                        <div class="col-md-6 text-md-left">
                                            <input id="is_main_admin_one" disabled="true" type="radio" name="is_main_admin" value="1" onclick="myFunction1()"> {{ "Super Admin" }} <b style="color: red;">{{ "( NoChoice )" }}</b>
                                        </div>
                                        <div class="col-md-6 text-md-left">
                                            <input id="is_main_admin_two" type="radio" name="is_main_admin" value="0" onclick="myFunction2()" checked="true"> {{ "Zone Admin" }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group row" id="zonename">
                                    <label for="zone_id" class="col-md-12 col-form-label">{{ __('Zone Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>

                                    <div class="col-md-12">
                                        <select id="zone_id" class="form-control @error('zone_id') is-invalid @enderror"  name="zone_id" value="{{ old('zone_id') }}"  autocomplete="zone_id">
                                            @foreach($zones as $value)
                                                <option value="{{$value->zone_id}}">{{$value->zone_name}}</option>
                                            @endforeach
                                        </select>
                                        @error('zone_id')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="roles" class="col-md-12 col-form-label">{{ __('Role') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="roles" class="form-control @error('roles') is-invalid @enderror"  name="roles" value="{{ old('roles') }}"  autocomplete="roles" required="required">
                                            @foreach($roles as $role)
                                                <option value="{{$role->id}}">{{$role->name}} ( {{ $role->zone_name }} )</option>
                                            @endforeach
                                        </select>
                                        @error('roles')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="phone" class="col-md-12 col-form-label">{{ __('Phone Number') }} </label>

                                    <div class="col-md-12">
                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}"  autocomplete="phone">
                                        @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email" class="col-md-12 col-form-label">{{ __('E-Mail Address') }} </label>

                                    <div class="col-md-12">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  autocomplete="email">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-12 col-form-label">{{ __('Password') }} <span  style="color: #990000;font-weight:700;">*</span></label>

                                    <div class="col-md-12">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="new-password">

                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password_confirmation" class="col-md-12 col-form-label">{{ __('Confirm Password') }} <span  style="color: #990000;font-weight:700;">*</span></label>

                                    <div class="col-md-12">
                                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="image" class="col-md-12 col-form-label">{{ __('Profile Image') }}</label>
                                    <div class="col-md-6">
                                        <input type="file" style="height: auto;" id="image" class="form-control @error('image') is-invalid @enderror" value="{{ old('image') }}" name="image" autocomplete="image" onchange="loadFileImage(event)">
                                        @error('image')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            <image src="{{asset('../../../image/available.png')}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-12 offset-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Register') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/user')}}" class="btn btn-secondary">
                                            {{ __('Cancel') }}
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
    $('#roles').select2();
    $('#zone_id').select2();
    $('#is_main_admin').select2();

    var loadFileImage= function(event) {
        var image = document.getElementById('imageOne');
        image.src = URL.createObjectURL(event.target.files[0]);
    };
</script>
<script>
function myFunction1() {
  var checkBox = document.getElementById("is_main_admin_one");
  var zonename = document.getElementById("zonename");
  if (checkBox.checked == true){
    zonename.style.display = "none";
  }
}
function myFunction2() {
  var checkBox = document.getElementById("is_main_admin_two");
  var zonename = document.getElementById("zonename");
  var aa= document.getElementById("zone_id");
  if (checkBox.checked == true){
    zonename.style.display = "block";
  }
}
</script>
@endsection
