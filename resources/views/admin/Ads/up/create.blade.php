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
              <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Ads</li>
              <li class="breadcrumb-item active">UpAds</li>
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
                                    <h2 class="card-title" style="font-size: 25px;"><b>Add A New UpAds</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/ads/up_ads')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.up_ads.store') }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="restaurant_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} </label>
                                    <div class="col-md-12">
                                        <select style="height: auto;" id="restaurant_id" class="form-control @error('restaurant_id') is-invalid @enderror" name="restaurant_id" autocomplete="restaurant_id">
                                            <option value="">Choose Restaurant</option>
                                            @foreach ($restaurants  as $value)
                                                <option value="{{ $value->restaurant_id }}">{{ $value->restaurant_name_mm }} ({{ $value->restaurant_name_en }})</option>
                                            @endforeach
                                        </select>
                                        @error('restaurant_id')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="image" class="col-md-12 col-form-label">{{ __('Ads Image (Please Choose Myanmar / English / China)') }} </label>
                                    <div class="col-md-12">
                                        <input type="file" style="height: auto;" id="image" class="form-control @error('image') is-invalid @enderror" name="image[]" autocomplete="image" onchange="loadFileImageMm(event)" multiple>
                                        @error('image')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label for="image_myanmar" class="col-md-12 col-form-label">{{ __('Ads Image Myanmar') }} </label>
                                        <div class="form-group">
                                            <image src="{{asset('../../../image/available.png')}}" id="imageOne" style="width: 100%;height: 200px;"></image>
                                        </div>
                                    </div>
                                    <div class="col-md-12 ">
                                        <label for="image_english" class="col-md-12 col-form-label">{{ __('Ads Image English') }} </label>
                                        <div class="form-group">
                                            <image src="{{asset('../../../image/available.png')}}" id="image2" style="width: 100%;height: 200px;"></image>
                                        </div>
                                    </div>
                                    <div class="col-md-12 ">
                                        <label for="image_china" class="col-md-12 col-form-label">{{ __('Ads Image China') }} </label>
                                        <div class="form-group">
                                            <image src="{{asset('../../../image/available.png')}}" id="image3" style="width: 100%;height: 200px;"></image>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Create') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/ads/up_ads')}}" class="btn btn-secondary btn-sm">
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
    $('#restaurant_id').select2();
    //Image Show
    var loadFileImageMm= function(event) {
        var image = document.getElementById('imageOne');
        image.src = URL.createObjectURL(event.target.files[0]);
        var image1 = document.getElementById('image2');
        image1.src = URL.createObjectURL(event.target.files[1]);
        var image2 = document.getElementById('image3');
        image2.src = URL.createObjectURL(event.target.files[2]);
    };
</script>
@endsection
