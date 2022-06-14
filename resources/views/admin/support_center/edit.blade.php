@extends('admin.layouts.master')

@section('css')
@endsection
@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">

          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Support Center</li>
              <li class="breadcrumb-item active">Edit</li>
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
                            <h2 class="card-title" style="font-size: 23px;">Edit Id <b> " #{{ $support_center->support_center_id }} "</b></h2>
                        </div>
                        <div class="col-md-6" style="text-align: right">
                            <a href="{{url('fatty/main/admin/support_center')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{route('fatty.admin.support_center.update',$support_center->support_center_id)}}" method="POST" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="support_center_type" class="col-md-12 col-form-label">{{ __('Application') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                            <div class="col-md-12">
                                <select id="support_center_type" class="form-control @error('support_center_type') is-invalid @enderror" name="support_center_type" value="{{ old('support_center_type') }}" autocomplete="support_center_type" autofocus>
                                    @if($support_center->support_center_type=="customer")
                                        <option value="customer">Customer</option>
                                        <option value="rider">Rider</option>
                                        <option value="restaurant">Restauarnt</option>
                                    @elseif($support_center->support_center_type=="rider")
                                        <option value="rider">Rider</option>
                                        <option value="customer">Customer</option>
                                        <option value="restaurant">Restauarnt</option>
                                    @else
                                        <option value="restaurant">Restauarnt</option>
                                        <option value="customer">Customer</option>
                                        <option value="rider">Rider</option>
                                    @endif
                                </select>
                                
                                @error('support_center_type')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="type" class="col-md-12 col-form-label">{{ __('Type') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                            <div class="col-md-12">
                                <input id="type" type="text" class="form-control @error('type') is-invalid @enderror" name="type" value="{{ $support_center->type }}" autocomplete="type" autofocus>
                                @error('type')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone_mm" class="col-md-12 col-form-label">{{ __('Phone Myanmar') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                            <div class="col-md-12">
                                <input id="phone_mm" type="text" class="form-control @error('phone_mm') is-invalid @enderror" name="phone_mm" value="{{ $support_center->phone_mm }}" autocomplete="phone_mm" autofocus>
                                @error('phone_mm')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone_en" class="col-md-12 col-form-label">{{ __('Phone English') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                            <div class="col-md-12">
                                <input id="phone_en" type="text" class="form-control @error('phone_en') is-invalid @enderror" name="phone_en" value="{{ $support_center->phone_en }}" autocomplete="phone_en" autofocus>
                                @error('phone_en')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone_ch" class="col-md-12 col-form-label">{{ __('Phone China') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                            <div class="col-md-12">
                                <input id="phone_ch" type="text" class="form-control @error('phone_ch') is-invalid @enderror" name="phone_ch" value="{{ $support_center->phone_ch }}" autocomplete="phone_ch" autofocus>
                                @error('phone_ch')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-save"></i> {{ __('Create') }}
                                </button>
                                <a href="{{url('fatty/main/admin/support_center')}}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-ban"></i> {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
		<br><br><br><br><br>
  </section>

@endsection
@section('script')
<script>
    $(document).ready(function () {
    //select2
    $('#support_center_type').select2();
});
</script>
@endsection
