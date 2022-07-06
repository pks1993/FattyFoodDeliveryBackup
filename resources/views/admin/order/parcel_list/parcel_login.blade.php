{{-- <!DOCTYPE html>
<html>
 <head>
  <title>Simple Login System in Laravel</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style type="text/css">
   .box{
    width:90%;
    margin:0 auto;
    border:1px solid #ccc;
   }
  </style>
 </head>
 <body>
  <br />
  <div class="container box">
   <h3 align="center">Customer Admin Login</h3><br />

   @if(isset(Auth::user()->email))
    <script>window.location="/main/successlogin";</script>
   @endif

   @if ($message = Session::get('error'))
   <div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
   </div>
   @endif

   @if (count($errors) > 0)
    <div class="alert alert-danger">
     <ul>
     @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
     @endforeach
     </ul>
    </div>
   @endif

   <form method="post" action="{{ url('admin_parcel_orders/login/check') }}">
    {{ csrf_field() }}
    <div class="form-group">
     <label>Enter Phone</label>
     <input type="text" name="phone" class="form-control" placeholder="0987654321"/>
    </div>
    <div class="form-group">
     <label>Enter Password</label>
     <input type="password" name="password" class="form-control" placeholder="at lease 6 letter"/>
    </div>
    <div class="form-group">
     <input type="submit" name="login" class="btn btn-primary" value="Login" />
    </div>
   </form>
  </div>
 </body>
</html> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Fatty Food Delivery</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/admin.css')}}">


    <style>
        .p-1 {
        28a745padding: 0.25rem!important;
        }
        .rounded {
        border-radius: 0.25rem!important;
        }
        .border-success {
        border-color: #28a745!important;
        }
        .border {
        border: 1px solid #28a745!important;
        }
        .border2 {
        border-color: #007bff!important;
        }
        .border-primary {
        border: 1px solid #007bff!important;
        }
        col-12 {
        -ms-flex: 0 0 100%;
        flex: 0 0 100%;
        max-width: 100%;
        }
        /* .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff;
            line-height: 35px;
            text-align: center;
            background-color: #28a745;
            height: 35px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #ffffff transparent transparent transparent;
            border-style: solid;
            border-width: 5px 4px 0 4px;
            height: 0;
            left: 50%;
            margin-left: -4px;
            margin-top: -2px;
            position: absolute;
            top: 70%;
            width: 0;
        } */
        .nav>li>a:focus, .nav>li>a:hover {
            text-decoration: none;
            background-color: #343a40;
            color: #fff;
        }
        .nav-pills li>#list-tab.active,.nav-pills li>#offered-tab.active,.nav-pills li>#home-tab.active, .nav-pills .show>.nav-link {
            background-color: #28a745 !important;
            color: #FFFFFF !important;
        }
        .tab-pane{
            display : none;
        }
        .tab-pane.active{
            display : block;
        }
    </style>
</head>
<body style="width:100%;font-size:16px !important;">
    <div style="background-color: black;color:white;padding-top:5px;padding-bottom:5px;text-align:center;font-size:20px;">
        <strong>Fatty</strong> Food Delivery
    </div>


{{-- <form action="{{ route('fatty.admin.admin_parcel.store') }}" method="post" autocomplete="off" enctype="multipart/form-data" style="margin: 5px;"> --}}
    {{-- @csrf --}}
    <div class="container-fluid">
        <div class="row p-1">
            <div class="form-group col-12 p-1">
                {{-- <div class="card"> --}}
                    {{-- <div class="col-12"> --}}
                        {{-- <h3 align="center" style="font-weight: 600;font-size:20px;margin-top:10px">Customer Admin Login</h3><br /> --}}

                        @if(isset(Auth::user()->email))
                            <script>window.location="/main/successlogin";</script>
                        @endif

                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        @endif

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                            <ul>
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                            </ul>
                            </div>
                        @endif

                        <form method="post" action="{{ url('admin_parcel_orders/login/check') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                            <input type="text" name="phone" style="height: 30px;font-size: 15px" class="form-control" placeholder="Fatty ID"/>
                            </div>
                            <div class="form-group">
                            <input type="password" name="password" style="height: 30px;font-size:15px;" class="form-control" placeholder="Fatty Password"/>
                            </div>
                            <div class="form-group">
                            <input type="submit" name="login" class="btn btn-block btn-primary" style="font-size: 15px;" value="Sign In" />
                            </div>
                        </form>
                    {{-- </div> --}}
                {{-- </div> --}}
            </div>

        </div>
    </div>
{{-- </form> --}}

</body>
</html>



