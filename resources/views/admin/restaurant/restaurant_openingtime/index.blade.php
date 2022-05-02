@extends('admin.layouts.master')

@section('css')
<style>
.toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 25rem; }
  .toggle.ios .toggle-handle { border-radius: 25rem; }
</style>
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">

@endsection
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
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
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </div>
            <div class="col-md-12">

            </div>
        </div>
    </div>
</section>
<form method="POST" action="{{ route('fatty.admin.restaurants_openingtime.update',$restaurant->restaurant_id) }}" autocomplete="off" enctype="multipart/form-data">
    @csrf
    <div class="p-3">
        <div class="card col-md-8 offset-md-2" style="margin-bottom: 100px;">
            <div class="card-header text-center">
                <h5><b>Restaurant OpeningTime For  " {{ $restaurant->restaurant_name_mm }} " </b></h5>
            </div>
            <div class="card-body">
                @foreach ($available as $value)
                    @if($value->day=="Monday")
                        <div class="row col-md-12 col-xs-12">
                            <div class="col-md-2 col-xs-2">
                                <label>{{ $value->day }}:</label><br>
                                <input type="hidden" name="monday" value="Monday">
                                @if($value->on_off=="1")
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_monday" checked data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @else
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_monday" data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @endif
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>Start:</label>
                                <input type="time" class="form-control form-control-sm" name="opening_time_monday" value="{{ $value->opening_time }}">
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>End:</label>
                                <input type="time" class="form-control form-control-sm" name="closing_time_monday" value="{{ $value->closing_time }}">
                            </div>
                        </div>
                        <hr>
                    @endif
                    @if($value->day=="Tuesday")
                        <div class="row col-md-12 col-xs-12">
                            <div class="col-md-2 col-xs-2">
                                <label>{{ $value->day }}:</label><br>
                                <input type="hidden" name="tuesday" value="Tuesday">
                                @if($value->on_off=="1")
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_tuesday" checked data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @else
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_tuesday" data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @endif
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>Start:</label>
                                <input type="time" class="form-control form-control-sm" name="opening_time_tuesday" value="{{ $value->opening_time }}">
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>End:</label>
                                <input type="time" class="form-control form-control-sm" name="closing_time_tuesday" value="{{ $value->closing_time }}">
                            </div>
                        </div>
                        <hr>
                    @endif
                    @if($value->day=="Wednesday")
                        <div class="row col-md-12 col-xs-12">
                            <div class="col-md-2 col-xs-2">
                                <label>{{ $value->day }}:</label><br>
                            <input type="hidden" name="wednesday" value="Wednesday">
                            @if($value->on_off=="1")
                                <input type="checkbox" class="form-control form-control-sm" name="on_off_wednesday" checked data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                            @else
                                <input type="checkbox" class="form-control form-control-sm" name="on_off_wednesday" data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                            @endif
                        </div>

                        <div class="col-md-5 col-xs-2">
                            <label>Start:</label>
                            <input type="time" class="form-control form-control-sm" name="opening_time_wednesday" value="{{ $value->opening_time }}">
                        </div>

                        <div class="col-md-5 col-xs-2">
                            <label>End:</label>
                            <input type="time" class="form-control form-control-sm" name="closing_time_wednesday" value="{{ $value->closing_time }}">
                        </div>
                        </div>
                        <hr>
                    @endif
                    @if($value->day=="Thursday")
                        <div class="row col-md-12 col-xs-12">
                            <div class="col-md-2 col-xs-2">
                                <label>{{ $value->day }}:</label><br>
                                <input type="hidden" name="thursday" value="Thursday">
                                @if($value->on_off=="1")
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_thursday" checked data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @else
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_thursday" data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @endif
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>Start:</label>
                                <input type="time" class="form-control form-control-sm" name="opening_time_thursday" value="{{ $value->opening_time }}">
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>End:</label>
                                <input type="time" class="form-control form-control-sm" name="closing_time_thursday" value="{{ $value->closing_time }}">
                            </div>
                        </div>
                        <hr>
                    @endif
                    @if($value->day=="Friday")
                        <div class="row col-md-12 col-xs-12">
                            <div class="col-md-2 col-xs-2">
                                <label>{{ $value->day }}:</label><br>
                                <input type="hidden" name="friday" value="Friday">
                                @if($value->on_off=="1")
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_friday" checked data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @else
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_friday" data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @endif
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>Start:</label>
                                <input type="time" class="form-control form-control-sm" name="opening_time_friday" value="{{ $value->opening_time }}">
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>End:</label>
                                <input type="time" class="form-control form-control-sm" name="closing_time_friday" value="{{ $value->closing_time }}">
                            </div>
                        </div>
                        <hr>
                    @endif
                    @if($value->day=="Saturday")
                        <div class="row col-md-12 col-xs-12">
                            <div class="col-md-2 col-xs-2">
                                <label>{{ $value->day }}:</label><br>
                                <input type="hidden" name="saturday" value="Saturday">
                                @if($value->on_off=="1")
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_saturday" checked data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @else
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_saturday" data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @endif
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>Start:</label>
                                <input type="time" class="form-control form-control-sm" name="opening_time_saturday" value="{{ $value->opening_time }}">
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>End:</label>
                                <input type="time" class="form-control form-control-sm" name="closing_time_saturday" value="{{ $value->closing_time }}">
                            </div>
                        </div>
                        <hr>
                    @endif
                    @if($value->day=="Sunday")
                        <div class="row col-md-12 col-xs-12">
                            <div class="col-md-2 col-xs-2">
                                <label>{{ $value->day }}:</label><br>
                                <input type="hidden" name="sunday" value="Sunday">
                                @if($value->on_off=="1")
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_sunday" checked data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @else
                                    <input type="checkbox" class="form-control form-control-sm" name="on_off_sunday" data-toggle="toggle" data-style="ios" data-on="On" data-off="Off" data-onstyle="success" data-offstyle="danger">
                                @endif
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>Start:</label>
                                <input type="time" class="form-control form-control-sm" name="opening_time_sunday" value="{{ $value->opening_time }}">
                            </div>

                            <div class="col-md-5 col-xs-2">
                                <label>End:</label>
                                <input type="time" class="form-control form-control-sm" name="closing_time_sunday" value="{{ $value->closing_time }}">
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <hr>
            <div class="card-footer text-right">
                <a href="{{ url('fatty/main/admin/restaurants') }}" class="btn btn-sm mr-2" style="background-color:#fff;border-color:red;color:black;"><i class="fas fa-angle-double-left"></i>&nbsp;Back</a>
                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure update?')">
                    <i class="fa fa-edit"></i> {{ __('Update') }}
                </button>
            </div>
        </div>

        </div>
    </div>
</form>

@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<script>
    setTimeout(function() {
    $('#successMessage').fadeOut('fast');
}, 2500);
</script>
@endsection
