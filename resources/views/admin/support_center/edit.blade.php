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
                            <h2 class="card-title" style="font-size: 23px;">Edit<b>" Id# {{ $support_center->support_center_id }} "</b></h2>
                        </div>
                        <div class="col-md-6" style="text-align: right">
                            <a href="{{url('fatty/main/admin/support_center')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{route('fatty.admin.support_center.update',$support_center)}}" method="POST" autocomplete="off" enctype="multipart/form-data">
                  @csrf
                  <div class="card-body">
                      <div class="form-group row">
                          <label for="phone" class="col-md-12 col-form-label">{{ __('Phone') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                          <div class="col-md-12">
                              <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ $support_center->phone }}" autocomplete="phone" autofocus>
                              @error('phone')
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
                      <div class="form-group row mb-0">
                          <div class="col-md-12">
                              <button type="submit" class="btn btn-primary btn-sm">
                                  <i class="fa fa-save"></i> {{ __('Update') }}
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

@endsection
