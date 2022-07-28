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
    <div class="container-fluid">
        <div class="row p-1">
            <div class="form-group col-12 p-1">
                {{-- <div class="card"> --}}
                    {{-- <div class="col-12"> --}}
                        {{-- <h3 align="center" style="font-weight: 600;font-size:20px;margin-top:10px">Customer Admin Login</h3><br /> --}}

                        {{-- @if(isset(Auth::user()->email))
                            <script>window.location="/main/successlogin";</script>
                        @endif --}}

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

    <div class="flash-message" id="successMessage">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
            @endif
        @endforeach
    </div>

<script>
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
</body>
</html>



