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
              <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Riders</li>
              <li class="breadcrumb-item active">Add</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
	<section class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title">Add a new Rider</h3>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/riders')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.riders.store') }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="rider_user_name" class="col-md-12 col-form-label">{{ __('Rider User Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="rider_user_name" type="text" class="form-control @error('rider_user_name') is-invalid @enderror" name="rider_user_name" value="{{ old('rider_user_name') }}" autocomplete="category_image" autofocus>
                                        @error('rider_user_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="rider_user_phone" class="col-md-12 col-form-label">{{ __('Rider User Phone') }} </label>
                                    <div class="col-md-12">
                                        <input id="rider_user_phone" type="text" class="form-control @error('rider_user_phone') is-invalid @enderror" name="rider_user_phone" value="{{ old('rider_user_phone') }}" autocomplete="category_image" autofocus>
                                        @error('rider_user_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label for="state_id" class="col-md-12 col-form-label">{{ __('ပြည်နယ် / တိုင်း') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="state_id" style="width: 100%;" class="form-control @error('state_id') is-invalid @enderror" name="state_id" value="{{ old('state_id') }}" autocomplete="state_id" autofocus>
                                                <option value="">ပြည်နယ် / တိုင်း</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->state_id }}">{{ $state->state_name_mm }} ( {{ $state->state_name_en }} )</option>
                                            @endforeach
                                            
                                        </select>
                                        @error('state_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label for="rider_user_password" class="col-md-12 col-form-label">{{ __('Password') }} <span  style="color: #990000;font-weight:700;">*</span></label>

                                    <div class="col-md-12">
                                        <input id="rider_user_password" type="rider_user_password" class="form-control @error('rider_user_password') is-invalid @enderror" name="rider_user_password" placeholder="Enter minimum 6 characters"  autocomplete="new-rider-user-password"><span toggle="#rider_user_password" class="fa fa-fw fa-eye field-icon toggle-rider_user_password"></span>
                                        @error('rider_user_password')
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
                                <div class="form-group row">
                                    <label for="is_admin_approved" class="col-md-12 col-form-label">{{ __('Admin Approved') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="is_admin_approved" style="width: 100%;" class="form-control @error('is_admin_approved') is-invalid @enderror" name="is_admin_approved" value="{{ old('is_admin_approved') }}" autocomplete="is_admin_approved" autofocus>
                                            <option value="1">Approved</option>
                                            <option value="0">Reject</option>
                                        </select>
                                        @error('is_admin_approved')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="rider_image" class="col-md-12 col-form-label">{{ __('Rider Image') }} </label>
                                    <div class="col-md-6">
                                        <input type="file" style="height: auto;" id="rider_image" class="form-control @error('rider_image') is-invalid @enderror" name="rider_image" autocomplete="rider_image" onchange="loadFileImage(event)">
                                        @error('rider_image')
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
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Create') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/restaurants')}}" class="btn btn-secondary btn-sm">
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
$(document).ready(function () {
    //select2
    $('#state_id').select2();
    $('#is_admin_approved').select2();
});
//Image Show
var loadFileImage= function(event) {
    var image = document.getElementById('imageOne');
    image.src = URL.createObjectURL(event.target.files[0]);
};
//Password Matching
$('#rider_user_password, #password_confirmation').on('keyup', function () {
  if ($('#rider_user_password').val() == $('#password_confirmation').val()) {
    $('#message').html('Matching').css('color', 'green');
  } else 
    $('#message').html('Not Matching').css('color', 'red');
});
</script>

<script type="text/javascript">
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
</script>
@endsection
