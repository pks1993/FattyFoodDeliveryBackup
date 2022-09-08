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

.preview-images-zone {
    width: 100%;
    border: 1px solid #ddd;
    min-height: 180px;
    /* display: flex; */
    padding: 5px 5px 0px 5px;
    position: relative;
    overflow:auto;
}
.preview-images-zone > .preview-image:first-child {
    height: 185px;
    width: 185px;
    position: relative;
    margin-right: 5px;
}
.preview-images-zone > .preview-image {
    height: 90px;
    width: 90px;
    position: relative;
    margin-right: 5px;
    float: left;
    margin-bottom: 5px;
}
.preview-images-zone > .preview-image > .image-zone {
    width: 100%;
    height: 100%;
}
.preview-images-zone > .preview-image > .image-zone > img {
    width: 100%;
    height: 100%;
}
.preview-images-zone > .preview-image > .tools-edit-image {
    position: absolute;
    z-index: 100;
    color: #fff;
    bottom: 0;
    width: 100%;
    text-align: center;
    margin-bottom: 10px;
    display: none;
}
.preview-images-zone > .preview-image > .image-cancel {
    font-size: 18px;
    position: absolute;
    top: 0;
    right: 0;
    font-weight: bold;
    margin-right: 10px;
    cursor: pointer;
    display: none;
    z-index: 100;
}
.preview-image:hover > .image-zone {
    cursor: move;
    opacity: .5;
}
.preview-image:hover > .tools-edit-image,
.preview-image:hover > .image-cancel {
    display: block;
}
.ui-sortable-helper {
    width: 90px !important;
    height: 90px !important;
}
#image_four {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
  width: 150px;
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
              <li class="breadcrumb-item active">Parcel</li>
              <li class="breadcrumb-item active">Edit</li>
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
                                    <h3 class="card-title">Edit this parcel</h3>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/daily_parcel_orders/list')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.parcel_order.update',$orders->order_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="from_parcel_city_id" class="col-md-12 col-form-label">{{ __('From Pickup Region') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="from_parcel_city_id" style="width: 100%;" class="form-control @error('from_parcel_city_id') is-invalid @enderror" name="from_parcel_city_id" value="{{ old('from_parcel_city_id') }}" autocomplete="from_parcel_city_id" autofocus>
                                            {{-- <option value="{{$orders->from_parcel_city_id}}">{{ $orders->from_parcel_region->city_name_mm }}/{{$orders->from_parcel_region->city_name_en}}</option> --}}
                                            @if($orders->from_parcel_city_id==0 || $orders->from_parcel_city_id==null)
                                                <option value="">Choose Region</option>
                                                @foreach($from_cities as $value)
                                                    <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                                                @endforeach
                                            @else
                                                <option value="{{$orders->from_parcel_city_id}}">{{ $orders->from_block->block_name }}</option>
                                                @foreach($from_city as $value)
                                                    <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('from_parcel_city_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="from_sender_phone" class="col-md-12 col-form-label">{{ __('From Sender Phone Number') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="from_sender_phone" type="text" class="form-control @error('from_sender_phone') is-invalid @enderror" name="from_sender_phone" value="{{ $orders->from_sender_phone }}" autocomplete="category_image" autofocus>
                                        @error('from_sender_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="from_pickup_note" class="col-md-12 col-form-label">{{ __('From Pickup Note') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <textarea id="from_pickup_note" class="form-control @error('from_pickup_note') is-invalid @enderror" style="height:150px;" name="from_pickup_note" value="{{ old('from_pickup_note') }}" autocomplete="category_image" autofocus>{{$orders->from_pickup_note}}</textarea>
                                        @error('from_pickup_note')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="to_parcel_city_id" class="col-md-12 col-form-label">{{ __('To Trop Region') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="to_parcel_city_id" style="width: 100%;" class="form-control @error('to_parcel_city_id') is-invalid @enderror" name="to_parcel_city_id" value="{{ old('to_parcel_city_id') }}" autocomplete="to_parcel_city_id" autofocus>
                                            {{-- <option value="{{$orders->to_parcel_city_id}}">{{ $orders->to_parcel_region->city_name_mm }}/{{$orders->to_parcel_region->city_name_en}}</option> --}}
                                            @if($orders->to_parcel_city_id==0 || $orders->to_parcel_city_id==null)
                                                <option value="">Choose Region</option>
                                                @foreach($to_cities as $value)
                                                    <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                                                @endforeach
                                            @else
                                                <option value="{{$orders->to_parcel_city_id}}">{{ $orders->to_block->block_name }}</option>
                                                @foreach($to_city as $value)
                                                    <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('to_parcel_city_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="to_recipent_phone" class="col-md-12 col-form-label">{{ __('To Drop Phone Number') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="to_recipent_phone" type="text" class="form-control @error('to_recipent_phone') is-invalid @enderror" name="to_recipent_phone" value="{{ $orders->to_recipent_phone }}" autocomplete="category_image" autofocus>
                                        @error('to_recipent_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="to_drop_note" class="col-md-12 col-form-label">{{ __('To Drop Note') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <textarea id="to_drop_note" class="form-control @error('to_drop_note') is-invalid @enderror" style="height:150px;" name="to_drop_note" value="{{ old('to_drop_note') }}" autocomplete="category_image" autofocus>{{$orders->to_drop_note}}</textarea>
                                        @error('to_drop_note')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="parcel_type_id" class="col-md-12 col-form-label">{{ __('Parcel Type') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="parcel_type_id" style="width: 100%;" class="form-control @error('parcel_type_id') is-invalid @enderror" name="parcel_type_id" value="{{ old('parcel_type_id') }}" autocomplete="parcel_type_id" autofocus>
                                            <option value="{{$orders->parcel_type_id}}">{{ $orders->parcel_type->parcel_type_name }}</option>
                                            @foreach($parcel_type as $value)
                                                <option value="{{ $value->parcel_type_id }}">{{ $value->parcel_type_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('parcel_type_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="parcel_order_note" class="col-md-12 col-form-label">{{ __('Add Parcel Note') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <textarea id="parcel_order_note" class="form-control @error('parcel_order_note') is-invalid @enderror" style="height:150px;" name="parcel_order_note" autocomplete="category_image" autofocus>{{ $orders->parcel_order_note }}</textarea>
                                        @error('parcel_order_note')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="parcel_extra_cover_id" class="col-md-12 col-form-label">{{ __('Parcel Extra Cover') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="parcel_extra_cover_id" style="width: 100%;" class="form-control @error('parcel_extra_cover_id') is-invalid @enderror" name="parcel_extra_cover_id" value="{{ old('parcel_extra_cover_id') }}" autocomplete="parcel_extra_cover_id" autofocus onchange="calExtra();">
                                            @if($orders->parcel_extra_cover_id==0 || $orders->parcel_extra_cover_id==null)
                                                <option value="000">{{ "Choose Parcel Extra Cover" }}</option>
                                                @foreach($extra as $value)
                                                    <option value="{{ $value->parcel_extra_cover_id }}/{{ $value->parcel_extra_cover_price }}"> Prices#( {{ $value->parcel_extra_cover_price }} )</option>
                                                @endforeach
                                            @else
                                                <option value="{{ $orders->parcel_extra_cover_id }}/{{ $orders->parcel_extra->parcel_extra_cover_price }}"> Prices#( {{ $orders->parcel_extra->parcel_extra_cover_price }} )</option>
                                                @foreach($extra as $value)
                                                    <option value="{{ $value->parcel_extra_cover_id }}/{{ $value->parcel_extra_cover_price }}"> Prices#( {{ $value->parcel_extra_cover_price }} )</option>
                                                    <option value="000">{{ "Choose Parcel Extra Cover" }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('parcel_extra_cover_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="delivery_fee" class="col-md-12 col-form-label">{{ __('Deli Fee') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="delivery_fee" type="text" class="form-control @error('delivery_fee') is-invalid @enderror" name="delivery_fee" value="{{ $orders->delivery_fee }}" autocomplete="category_image" autofocus onchange="calDeli();">
                                        @error('delivery_fee')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="extra_fee" class="col-md-12 col-form-label">{{ __('Extra Fee') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        @if($orders->parcel_extra_cover_id==0 || $orders->parcel_extra_cover_id==null)
                                            <input id="extra_fee" type="text" class="form-control @error('extra_fee') is-invalid @enderror" name="extra_fee" value="{{ 0 }}" autocomplete="category_image" autofocus>
                                        @else
                                            <input id="extra_fee" type="text" class="form-control @error('extra_fee') is-invalid @enderror" name="extra_fee" value="{{ $orders->parcel_extra->parcel_extra_cover_price }}" autocomplete="category_image" autofocus>
                                        @endif
                                        @error('extra_fee')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="total_estimated_fee" class="col-md-12 col-form-label">{{ __('Total Estimated Fee') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input id="total_estimated_fee" type="text" class="form-control @error('total_estimated_fee') is-invalid @enderror" name="total_estimated_fee" value="{{ $orders->bill_total_price }}" autocomplete="category_image" autofocus>
                                        @error('total_estimated_fee')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="rider_id" class="col-md-12 col-form-label">{{ __('Assign Rider') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select id="rider_id" style="width: 100%;" class="form-control @error('rider_id') is-invalid @enderror" name="rider_id" value="{{ old('rider_id') }}" autocomplete="rider_id" autofocus onchange="calExtra();">
                                            <option value="0"> All Rider</option>
                                            @foreach($riders as $value)
                                                <option value="{{ $value->rider_id }}"> {{ $value->rider_user_name }} ( @if($value->is_order)HasOrder @else Free @endif )</option>
                                            @endforeach
                                        </select>
                                        @error('rider_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- <div class="form-group row">
                                    <fieldset class="form-group">
                                        <label>Upload (Allow Multiple / no select each image) <a href="javascript:void(0)" class="custom-file-container__image-clear" id="clear_image" title="Clear Image"> ClearImage</a></label>
                                        <input type="file" id="parcel_image" name="parcel_image[]" style="display: block;height: auto;" class="form-control" multiple>
                                    </fieldset>
                                    <div class="preview-images-zone form-group" id=preview></div>
                                </div> --}}


                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/daily_parcel_orders/list')}}" class="btn btn-secondary btn-sm">
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
    //select2
    $('#from_parcel_city_id').select2();
    $('#to_parcel_city_id').select2();
    $('#parcel_type_id').select2();
    $('#parcel_extra_cover_id').select2();
    $('#rider_id').select2();
});
//Image Show
var loadFileImage= function(event) {
    var image = document.getElementById('imageOne');
    image.src = URL.createObjectURL(event.target.files[0]);
};

    function calExtra(){
        var extra = document.getElementById("parcel_extra_cover_id").value;
        var result = extra.substr(2, extra.length);
        document.getElementById("extra_fee").value=result;
        document.getElementById("total_estimated_fee").value=parseInt(document.getElementById("delivery_fee").value)+parseInt(result);
    }

    function calDeli(){
        document.getElementById("total_estimated_fee").value=parseInt(document.getElementById("delivery_fee").value)+parseInt(document.getElementById("extra_fee").value);
    }

</script>

<script type="text/javascript">
    $(document).ready(function() {
      document.getElementById('parcel_image').addEventListener('change', readImage, false);

      $( ".preview-images-zone" ).sortable();

      $(document).on('click', '.image-cancel', function() {
          let no = $(this).data('no');
          console.log('no');
          $(".preview-image.preview-show-"+no).remove();
          var $el = $('#parcel_image');
          $el.wrap('<form>').closest('form').get(0).reset();
          $el.unwrap();
      });
      $('#clear_image').on('click', function(e) {
          var $el = $('#parcel_image');
          $el.wrap('<form>').closest('form').get(0).reset();
          $el.unwrap();
          let no = $('.image-cancel').data('no');
          console.log(no);
          $(".preview-image.preview-show-").remove();

     });
  });
  var num = 4;
  function readImage() {
      if (window.File && window.FileList && window.FileReader) {
          var files = event.target.files; //FileList object
          var output = $(".preview-images-zone");
          for (let i = 0; i < files.length; i++) {
              var file = files[i];
              if (!file.type.match('image')) continue;

              var picReader = new FileReader();

              picReader.addEventListener('load', function (event) {
                  var picFile = event.target;
                  var html =  '<div class="preview-image preview-show-">' +
                              '<div class="image-cancel" data-no="">x</div>' +
                              '<div class="image-zone"><img id="parcel_image" src="' + picFile.result + '"></div>' +
                              '</div>';
                  output.append(html);
                  num = num + 1;
              });
              picReader.readAsDataURL(file);
          }
          // $("#parcel_image").val('');
      } else {
          console.log('Browser not support');
      }
  }
  </script>
@endsection
