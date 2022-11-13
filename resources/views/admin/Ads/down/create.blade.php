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
                                    <h2 class="card-title" style="font-size: 25px;"><b>Add A New DownAds</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/ads/down_ads')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.down_ads.store') }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="state_id" class="col-form-label">{{ __('တိုင်း / ပြည်နယ်') }}</label>
                                    <select class="form-control" name="state_id" id="state_id" required>
                                        <option value="">တိုင်း/ပြည်နယ်</option>
                                        @foreach($states as $st)
                                            <option value="{{$st->state_id}}">{{$st->state_name_mm}} ( {{ $st->state_name_en }} )</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label for="city_id" class="col-form-label">{{ __('မြို့နယ်') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <select id="city_id" class="form-control @error('city_id') is-invalid @enderror" name="city_id" value="{{ old('city_id') }}" autocomplete="city_id" autofocus required>
                                    </select>
                                    @error('city_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
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
        var image = document.getElementById('imageOne');
        image.src = URL.createObjectURL(event.target.files[0]);
        var image1 = document.getElementById('image2');
        image1.src = URL.createObjectURL(event.target.files[1]);
        var image2 = document.getElementById('image3');
        image2.src = URL.createObjectURL(event.target.files[2]);
    };
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#state_id').on('change', function(){
            $('#city_id').empty();
            var id = $(this).val();
            if(id){
                $.ajax({
                    type: 'get',
                    url: '/fatty/main/admin/city/list/'+id,
                    success: function(data){ 
                        $('#city_id').append(`<option value="">မြို့နယ်</option>`);
                        $.each(data, function(index,value) {
                            $('#city_id').append('<option value='+value.city_id+'>'+value.city_name_mm + ' ( '+value.city_name_en+' ) '+'</option>');
                        });
                    }
                });  
            }    
        }); 
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#city_id').on('change', function(){
            $('#parcel_from_block_id').empty();
            $('#parcel_to_block_id').empty();
            var city_id = $(this).val();
            if(city_id){
                $.ajax({
                    type: 'get',
                    url: '/fatty/main/admin/parcel_block/list/'+city_id,
                    success: function(data){ 
                        $('#parcel_from_block_id').append(`<option value="">Choose From Block Name</option>`);
                        $.each(data, function(index,value) {
                            $('#parcel_from_block_id').append('<option value='+value.parcel_block_id+'>'+value.block_name_mm + ' ( '+value.block_name_en+' ) '+'</option>');
                        });
                        $('#parcel_to_block_id').append(`<option value="">Choose To Block Name</option>`);
                        $.each(data, function(index,value) {
                            $('#parcel_to_block_id').append('<option value='+value.parcel_block_id+'>'+value.block_name_mm + ' ( '+value.block_name_en+' ) '+'</option>');
                        });
                    }
                });  
            }    
        }); 
    });
</script>
@endsection
