@extends('admin.layouts.master')

@section('css')
@endsection
@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">RiderGroup</li>
              <li class="breadcrumb-item active">Add</li>
            </ol>
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
                                    <h2 class="card-title">Edit<b> " {{ $rider_group->rider_group_name }} ( ID# {{ $rider_group->rider_group_id }} ) "</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/rider_group')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.rider_group.update',$rider_group->rider_group_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ Auth::user()->user_id }}">
                                <div class="form-group row">
                                    <label for="rider_group_name" class="col-md-12 col-form-label">{{ __('Rider Group Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="rider_group_name" type="text" class="form-control @error('rider_group_name') is-invalid @enderror" name="rider_group_name" value="{{ $rider_group->rider_group_name }}" autocomplete="rider_group_name" required="true" autofocus>
                                        @error('rider_group_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="zone_id" class="col-md-12 col-form-label">{{ __('Zone Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="zone_id" style="width: 100%;" class="form-control @error('zone_id') is-invalid @enderror" name="zone_id" value="{{ old('zone_id') }}" autocomplete="zone_id" required="true" autofocus>
                                            @if(Auth::user()->is_main_admin=="1")
                                                <option value="{{ $rider_group->zone_id }}">{{ $rider_group->zone->zone_name }}</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->zone_id }}">{{ $zone->zone_name }}</option>
                                                @endforeach
                                            @else
                                                <option value="{{ $rider_group->zone_id }}">{{ $rider_group->zone->zone_name }}</option>
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
                                    <label for="user_id" class="col-md-12 col-form-label">{{ __('Leader Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="user_id" style="width: 100%;" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ old('user_id') }}" autocomplete="user_id">
                                            <option value="{{ $rider_group->user_id }}">{{ $rider_group->user->name }}</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="office_latitude" class="col-md-12 col-form-label">{{ __('Office Latitude') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="office_latitude" style="width: 100%;" class="form-control @error('office_latitude') is-invalid @enderror" value="{{ $rider_group->office_latitude }}" name="office_latitude" value="{{ old('office_latitude') }}" autocomplete="office_latitude">
                                        @error('office_latitude')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="office_longitude" class="col-md-12 col-form-label">{{ __('Office Longitude') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="office_longitude" style="width: 100%;" class="form-control @error('office_longitude') is-invalid @enderror" value="{{ $rider_group->office_longitude }}" name="office_longitude" value="{{ old('office_longitude') }}" autocomplete="office_longitude">
                                        @error('office_longitude')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/rider_group')}}" class="btn btn-secondary btn-sm">
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
    $('#zone_id').on('change', function(){
        $('#user_id').empty();
        var id = $(this).val();
        if(id){
            $.ajax({
                type: 'get',
                url: '/fatty/main/admin/all_user/list/'+id,
                success: function(data){ 
                    $('#user_id').append(`<option value="">Choose User Name</option>`);
                    $.each(data, function(index,value) {
                        $('#user_id').append('<option value='+value.user_id+'>'+value.name+' ('+value.zone_name+')</option>');
                    });
                }
            });  
        }
});
    $('#zone_id').select2();
    $('#user_id').select2();
});
</script>
@endsection
