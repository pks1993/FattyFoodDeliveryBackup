<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rider Location Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/util.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/main.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">


    <style type="text/css">
        #map {
          height: 100%;
        }
        html,
        body  {
            height: 95%;
            margin: 0;
            padding: 0px 5px 0px 5px;
        }
    </style>
</head>
<body>
    <div class="p-3">

        <div class="form-group">
            <label>Rider Image :</label><br>
            @if($rider->rider_image)
                <img src="../../../../../uploads/rider/{{ $rider->rider_image}}" class="img-thumbnail" width="200" height="200">
            @else
                <img src="../../../../../image/available.png" class="img-thumbnail" width="200" height="200">
            @endif
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Rider Name:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_user_name }}" readonly>
            </div>

            <div class="col-md-4">
                <label>Rider Phone:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_user_phone }}" readonly>
            </div>

            <div class="col-md-4">
                <label>Rider Latitude:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_latitude }}" readonly>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Rider Longitude:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->rider_longitude }}" readonly>
            </div>

            <div class="col-md-4">
                <label>State:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $rider->state->state_name_mm }}" readonly>
            </div>

            <div class="col-md-4">
                <label>Is Admin Approved:</label><br>
                @if ($rider->is_admin_approved == 0)
                <a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>
                @else
                <a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>
                @endif
            </div>

        </div>
        <a href="{{ url('fatty/main/admin/all_riders_location') }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
        <div class="float-right">
            <a href="" class="btn btn-sm mr-2" style="background-color:#fff;border-color: blue;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Assign</a>
        </div>

    </div>
</body>
</html>


