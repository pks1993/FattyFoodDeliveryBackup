<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login | Fatty</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @include('admin.layouts._partial.meta')
  @include('admin.layouts._partial.css')
<!--===============================================================================================-->
  <link rel="icon" type="image/png" href="{{asset('favicon.ico')}}"/>
<!--===============================================================================================-->
  {{-- <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css"> --}}
<!--===============================================================================================-->
  <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
  <link rel="stylesheet" type="text/css" href="fonts/icon-font.min.css">
  <link rel="stylesheet" type="text/css" href="css/util.css">
  <link rel="stylesheet" type="text/css" href="css/login-form.css">
  {{-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> --}}
<!--===============================================================================================-->
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
</head>
<body>
  <div class="wrapper">
    <div class="wrapper-login" style="background-image: url({{url('image/orange.jpg')}});">
      {{-- <div class="wrapper-login" style="background-color: #FF6604;"> --}}
      <div class="wrap-login p-t-20 p-b-30">
        <div class="row mb-2">
          <div class="col-sm-12">
           <div class="flash-message" id="successMessage">
              @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                @endif
              @endforeach
            </div>          
          </div>
        </div>
         <span class="login100-form-title p-b-41" style="color: white;">
             <h5>Account Login</h5>
        </span>
        <form class="login-form validate-form p-b-33 p-t-5" style="background-color: #FFFFFF;border-style: solid;border-color: orangered;border-width: 5px;padding: 7px;" method="POST" action="{{route('fatty.post.login')}}" autocomplete="off">
          @csrf
          <div class="login-img" style="text-align: center;">

              <img src="{{asset('logo/user_logo.png')}}" style="align-items: center;width: 70px;height: 100%;" alt="" class="img-circle mt-2">
          </div>
          <div class="wrap-input100 validate-input" data-validate = "Enter User Name">
            <input class="input100" type="text" name="name" placeholder="User Name" value="{{old('name')}}">
            <span class="focus-input100" data-placeholder="&#xe82a;"></span>
          </div>

          <div class="wrap-input100 validate-input" data-validate="Enter password">
            <input class="input100" type="password" name="password" placeholder="Password" id="pass" value="{{old('password')}}"><span toggle="#pass" class="fa fa-fw fa-eye field-icon toggle-password"></span>

            <span class="focus-input100" data-placeholder="&#xe80f;"></span>
          </div>
          <div class="container-login-form-btn m-t-32 mb-4">
            <button type="submit" class="btn btn-primary login-form-btn">
              Login
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@include('admin.layouts._partial.script')
<script type="text/javascript">
  setTimeout(function() {
  $('#successMessage').fadeOut('fast');
}, 1500);
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
</body>
</html>
