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
              <li class="breadcrumb-item active">DownAds</li>
              <li class="breadcrumb-item active">Edit</li>
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
                                    <h4 class="card-title" style="font-size: 25px;">Edit DownAds for "<b>{{ $down_ads->restaurant->restaurant_name_mm }}</b>"</h4>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/ads/down_ads')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.down_ads.update',$down_ads->down_ads_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="restaurant_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} </label>
                                    <div class="col-md-12">
                                        <select style="height: auto;" id="restaurant_id" class="form-control @error('restaurant_id') is-invalid @enderror" name="restaurant_id" autocomplete="restaurant_id">
                                            <option value="{{ $down_ads->restaurant_id }}">{{ $down_ads->restaurant->restaurant_name_mm }} ({{ $down_ads->restaurant->restaurant_name_en }})</option>
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
                                    <label for="image_mm" class="col-md-12 col-form-label">{{ __('Ads Image Myanmar') }} </label>
                                    <div class="col-md-6">
                                        <input type="file" style="height: auto;" id="image_mm" class="form-control @error('image_mm') is-invalid @enderror" name="image_mm" autocomplete="image_mm" onchange="loadFileImageMm(event)">
                                        @error('image_mm')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            @if($down_ads->image_mm)
                                                <image src="../../../../../../uploads/down_ads/{{$down_ads->image_mm}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                            @else
                                                <image src="{{asset('../image/available.png')}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="image_en" class="col-md-12 col-form-label">{{ __('Ads Image English') }} </label>
                                    <div class="col-md-6">
                                        <input type="file" style="height: auto;" id="image_en" class="form-control @error('image_en') is-invalid @enderror" name="image_en" autocomplete="image_en" onchange="loadFileImageEn(event)">
                                        @error('image_en')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            @if($down_ads->image_en)
                                                <image src="../../../../../../uploads/down_ads/{{$down_ads->image_en}}" id="imageTwo" style="width: 100%;height: 150px;"></image>
                                            @else
                                                <image src="{{asset('../image/available.png')}}" id="imageTwo" style="width: 100%;height: 150px;"></image>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="image_ch" class="col-md-12 col-form-label">{{ __('Ads Image China') }} </label>
                                    <div class="col-md-6">
                                        <input type="file" style="height: auto;" id="image_ch" class="form-control @error('image_ch') is-invalid @enderror" name="image_ch" autocomplete="image_ch" onchange="loadFileImageCh(event)">
                                        @error('image_ch')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            @if($down_ads->image_ch)
                                                <image src="../../../../../../uploads/down_ads/{{$down_ads->image_ch}}" id="imageThree" style="width: 100%;height: 150px;"></image>
                                            @else
                                                <image src="{{asset('../image/available.png')}}" id="imageThree" style="width: 100%;height: 150px;"></image>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/ads/down_ads')}}" class="btn btn-secondary btn-sm">
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
    var imagemm = document.getElementById('imageOne');
    imagemm.src = URL.createObjectURL(event.target.files[0]);
};
var loadFileImageEn= function(event) {
    var imageen = document.getElementById('imageTwo');
    imageen.src = URL.createObjectURL(event.target.files[0]);
};
var loadFileImageCh= function(event) {
    var imagech = document.getElementById('imageThree');
    imagech.src = URL.createObjectURL(event.target.files[0]);
};
</script>
@endsection
