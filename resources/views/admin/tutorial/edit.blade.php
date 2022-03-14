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
              <li class="breadcrumb-item active">Tutorial</li>
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
                          <h2 class="card-title" style="font-size: 25px;">Edit<b>" Id# {{ $tutorials->tutorial_id }} "</b></h2>
                        </div>
                        <div class="col-md-6" style="text-align: right">
                            <a href="{{url('fatty/main/admin/tutorials')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{route('fatty.admin.tutorials.update',$tutorials)}}" method="POST" autocomplete="off" enctype="multipart/form-data">
                  @csrf
                  <div class="card-body">
                      <div class="form-group row">
                          <label for="name" class="col-md-12 col-form-label">{{ __('Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                          <div class="col-md-12">
                              <input type="text" style="height: auto;" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$tutorials->name}}" autocomplete="name">
                              @error('name')
                                <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                                </span>
                              @enderror
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="photo" class="col-md-12 col-form-label">{{ __('Cover Photo') }} </label>
                          <div class="col-md-6">
                              <input type="file" style="height: auto;" id="photo" class="form-control @error('photo') is-invalid @enderror" name="photo" autocomplete="photo" onchange="loadFileImage(event)">
                              @error('photo')
                                <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                                </span>
                              @enderror
                          </div>
                          <div class="col-md-6 mt-2">
                              <div class="form-group">
                                <image src="../../../../../uploads/tutorial_coverphoto/{{$tutorials->photo}}" id="image" style="width: 100%;height: 150px;"></image>
                            </div>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="video" class="col-md-12 col-form-label">{{ __('Video') }} </label>
                          <div class="col-md-6">
                              <input type="file" style="height: auto;" id="video" class="form-control @error('video') is-invalid @enderror" name="video" autocomplete="video" onchange="loadFileVideo(event)">
                              @error('video')
                                <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                                </span>
                              @enderror
                          </div>
                          <div class="col-md-6 mt-2">
                            <div class="form-group">
                              <video id="video_one" style="width: 100%;height: 150px;" controls>
                                <source src="../../../../../uploads/tutorial/{{$tutorials->video}}" type="video/mp4">
                                <source src="../../../../../uploads/tutorial/{{$tutorials->video}}" type="video/ogg">
                                Your browser does not support the video tag.
                              </video>
                            </div>
                          </div>
                      </div>
                      <div class="form-group row mb-0">
                          <div class="col-md-12">
                              <button type="submit" class="btn btn-primary btn-sm">
                                  <i class="fa fa-save"></i> {{ __('Update Tutorial') }}
                              </button>
                              <a href="{{url('fatty/main/admin/carriers')}}" class="btn btn-secondary btn-sm">
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
<script type="text/javascript">
var loadFileVideo= function(event) {
    var video = document.getElementById('video_one');
    video.src = URL.createObjectURL(event.target.files[0]);
};
var loadFileImage= function(event) {
    var image = document.getElementById('image');
    image.src = URL.createObjectURL(event.target.files[0]);
};
</script>
@endsection
