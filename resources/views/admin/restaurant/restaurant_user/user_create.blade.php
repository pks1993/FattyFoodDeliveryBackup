@extends('admin.layouts.master')

@section('css')
<style>
.field-icon {
    float: right;
    margin-left: -25px;
    margin-top: -27px;
    position: relative;
    z-index: 2;
    padding-right: 20px;
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
                    <li class="breadcrumb-item active">Restaurant</li>
                    <li class="breadcrumb-item active">User</li>
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </div>
            <div class="col-md-12">
    
            </div>
        </div>
    </div>
</section>
	<section class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title"><b>Add New Restaurant User</b></h3>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/restaurants')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.restaurants_user.store') }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="restaurant_user_phone" class="col-md-12 col-form-label">{{ __('Restaurant Login User Phone') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="number" id="restaurant_user_phone" class="form-control @error('restaurant_user_phone') is-invalid @enderror" name="restaurant_user_phone" autocomplete="restaurant_user_phone">{{ old('restaurant_user_phone') }}
                                        @error('restaurant_user_phone')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-12 col-form-label">{{ __('Password') }} <span  style="color: #990000;font-weight:700;">*</span></label>

                                    <div class="col-md-12">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter minimum 6 characters"  autocomplete="new-password"><span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
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
                                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="minimum 6 characters"  autocomplete="new-password"><span toggle="#password_confirmation" class="fa fa-fw fa-eye field-icon toggle-password"></span> <span id='message'></span>

                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure create?')">
                                            <i class="fa fa-save"></i> {{ __('Create') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/restaurants')}}" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure cancel?')">
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
//Password Matching
$('#password, #password_confirmation').on('keyup', function () {
  if ($('#password').val() == $('#password_confirmation').val()) {
    $('#message').html('Matching').css('color', 'green');
  } else 
    $('#message').html('Not Matching').css('color', 'red');
});

//Password toggle
$(".toggle-password").click(function() {

    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

setTimeout(function() {
    $('#successMessage').fadeOut('fast');
}, 2500);
</script>
@endsection
