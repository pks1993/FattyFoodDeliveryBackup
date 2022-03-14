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
              <li class="breadcrumb-item active">Notification Templates</li>
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
                            <h3 class="card-title" style="font-weight: 600;">Notification Templates Edit</h3>
                        </div>
                        <div class="col-md-6" style="text-align: right">
                            <a href="{{url('fatty/main/admin/notification_templates')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{route('fatty.admin.notification_templates.update',$notification_templates)}}" method="POST" autocomplete="off" enctype="multipart/form-data">
                  @csrf
                  <div class="card-body">
                      <div class="form-group row">
                          <label for="notification_title" class="col-md-12 col-form-label">{{ __('Title') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                          <div class="col-md-12">
                              <input id="notification_title" type="text" class="form-control @error('notification_title') is-invalid @enderror" name="notification_title" value="{{ $notification_templates->notification_title }}" autocomplete="notification_title" autofocus>
                              @error('notification_title')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                              @enderror
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="notification_body" class="col-md-12 col-form-label">{{ __('Body') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                          <div class="col-md-12">
                              <textarea id="notification_body" class="form-control @error('notification_body') is-invalid @enderror" name="notification_body" autocomplete="notification_body" autofocus>
                                  {!! $notification_templates->notification_body !!}
                              </textarea>
                              @error('notification_body')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                              @enderror
                          </div>
                      </div>
                      <div class="form-group row">
                        <label for="notification_image" class="col-md-12 col-form-label">{{ __('Notification Image') }} </label>
                        <div class="col-md-6">
                            <input type="file" style="height: auto;" id="notification_image" class="form-control @error('notification_image') is-invalid @enderror" name="notification_image" autocomplete="notification_image" onchange="loadFileImage(event)">
                                @error('notification_image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="form-group">
                                @if($notification_templates->notification_image==null)
                                    <image src="{{asset('../../../image/available.png')}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                @else
                                    <image src="{{asset('../../../uploads/notification/'.$notification_templates->notification_image )}}" id="imageOne" style="width: 100%;height: 150px;"></image>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                              <button type="submit" class="btn btn-primary btn-sm">
                                  <i class="fa fa-save"></i> {{ __('Update') }}
                              </button>
                              <a href="{{url('fatty/main/admin/notification_templates')}}" class="btn btn-secondary btn-sm">
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
    $(function () {
        $('#notification_body').summernote({
            height: 300,   //set editable area's height
            fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana', 'Roboto','Poppins'],
            fontNamesIgnoreCheck: ['Poppins']
        });
    });
    //Image Show
    var loadFileImage= function(event) {
        var image = document.getElementById('imageOne');
        image.src = URL.createObjectURL(event.target.files[0]);
};
</script>
@endsection
