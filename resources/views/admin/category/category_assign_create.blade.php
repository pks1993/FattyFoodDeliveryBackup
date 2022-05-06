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
              <li class="breadcrumb-item active">Category</li>
              <li class="breadcrumb-item active">Assign</li>
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
                                    <h2 class="card-title" style="font-size: 20px;"><b>Add NewCategory Assign</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/restaurant/categories')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.assign_categories.store') }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="restaurant_category_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} </label>
                                    <div class="col-md-12">
                                        <select style="height: auto;" id="restaurant_category_id" class="form-control @error('restaurant_category_id') is-invalid @enderror" name="restaurant_category_id" autocomplete="restaurant_category_id">
                                            <option value="{{ $restaurant_category->restaurant_category_id }}">{{ $restaurant_category->restaurant_category_name_mm }} ( {{ $restaurant_category->restaurant_category_name_en }} )</option>
                                        </select>
                                        @error('restaurant_category_id')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="category_type_id" class="col-md-12 col-form-label">{{ __('Category Type Name') }} </label>
                                    <div class="col-md-12">
                                        <select style="height: auto;" id="category_type_id" class="form-control @error('category_type_id') is-invalid @enderror" name="category_type_id" autocomplete="category_type_id">
                                            <option value="">Choose Category Type</option>
                                            @foreach ($category_type as $value)
                                                <option value="{{ $value->category_type_id }}">{{ $value->category_type_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_type_id')
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
                                        <a href="{{url('fatty/main/admin/restaurant/categories')}}" class="btn btn-secondary btn-sm">
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
    $('#category_type_id').select2();
    $('#restaurant_category_id').select2();
//Image Show
var loadFileImage= function(event) {
    var image = document.getElementById('imageOne');
    image.src = URL.createObjectURL(event.target.files[0]);
};
</script>
@endsection
