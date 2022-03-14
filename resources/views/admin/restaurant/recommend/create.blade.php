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
              <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Recommend Restaurant</li>
              <li class="breadcrumb-item active">Add</li>
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
                                    <h3 class="card-title" style="font-weight: 550;">New recommend Restaurant</h3>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/recommend_restaurants')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.recommend_restaurants.show') }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="state_id" class="col-md-12 col-form-label">{{ __('ပြည်နယ် / တိုင်း') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="state_id" style="width: 100%;" class="form-control @error('state_id') is-invalid @enderror" name="state_id" value="{{ old('state_id') }}" autocomplete="state_id" autofocus>
                                            <option value="">ပြည်နယ် / တိုင်း</option>
                                            @foreach($states as $value)
                                                <option value="{{ $value->state_id }}">{{ $value->state_name_mm }} ( {{ $value->state_name_en }} )</option>
                                            @endforeach
                                        </select>
                                        <span  style="color: #990000;">* MDY is not need select city *</span>
                                        @error('state_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="city_id" class="col-md-12 col-form-label">{{ __('မြို့နယ်') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="city_id" style="width: 100%;" class="form-control @error('city_id') is-invalid @enderror" name="city_id" value="{{ old('city_id') }}" autocomplete="city_id" autofocus>
                                        </select>
                                        @error('city_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- <div class="form-group row">
                                    <label for="restaurant_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="restaurant_id" class="form-control @error('restaurant_id') is-invalid @enderror" name="restaurant_id" value="{{ old('restaurant_id') }}" multiple="true" autocomplete="category_image" autofocus>
                                            <option value="">Choose Restaurant</option>
                                            @foreach($restaurants as $restaurant)
                                                <option value="{{ $restaurant->restaurant_id }}">{{ $restaurant->restaurant_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('restaurant_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div> --}}
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Next') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/recommend_restaurants')}}" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-ban"></i> {{ __('Back') }}
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
        $('#state_id').on('change', function(){
            $('#city_id').empty();
            $('#restaurant_id').empty();
            var id = $(this).val();
            if(id){
                $.ajax({
                    type: 'get',
                    url: '/fatty/main/admin/restaurants/city/list/'+id,
                    success: function(data){ 
                        $('#city_id').append(`<option value="">မြို့နယ်</option>`);
                        $.each(data, function(index,value) {
                            $('#city_id').append('<option value='+value.city_id+'>'+value.city_name_mm + ' ( '+value.city_name_en+' ) '+'</option>');
                        });
                    }
                });  
            }

            // if(id){
            //     $.ajax({
            //         type: 'get',
            //         url: '/fatty/main/admin/restaurants/list/'+id,
            //         success: function(data){ 
            //             $('#restaurant_id').append(`<option value="">Choose Restaurant</option>`);
            //             $.each(data, function(index,value) {
            //                 $('#restaurant_id').append('<option value='+value.restaurant_id+'>'+value.restaurant_name+'</option>');
            //             });
            //         }
            //     });  
            // }
        });
    }); 
    $('#city_id').select2();
    $('#state_id').select2();
    $('#restaurant_id').select2();
</script>

@endsection
