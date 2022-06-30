@extends('admin.layouts.master')

@section('css')
@endsection
@section('content')
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Customer</li>
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
                                    <h2 class="card-title"><b>" Edit {{ $customers->name }} ( ID# {{ $customers->customer_id }} ) "</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/customers')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.customers.update',$customers->customer_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="customer_name" class="col-md-12 col-form-label">{{ __('Customer Name') }} </label>
                                    <div class="col-md-12">
                                        <input id="customer_name" type="text" class="form-control @error('customer_name') is-invalid @enderror" name="customer_name" value="{{ $customers->customer_name }}" autocomplete="customer_name" autofocus>
                                        @error('customer_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="customer_phone" class="col-md-12 col-form-label">{{ __('Phone Number') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <input type="text" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" name="customer_phone" value="{{ $customers->customer_phone }}" autocomplete="customer_phone" autofocus>
                                        @error('customer_phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="is_restricted" class="col-md-12 col-form-label">{{ __('Restricted') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select type="text" id="is_restricted" class="form-control @error('is_restricted') is-invalid @enderror" name="is_restricted" value="{{ $customers->is_restricted }}" autocomplete="is_restricted" autofocus>
                                            @if($customers->is_restricted==0)
                                                <option value="0">Not Ban Customer</option>
                                                <option value="1">Ban Customer</option>
                                            @else
                                            <option value="1">Ban Customer</option>
                                            <option value="0">Not Ban Customer</option>
                                            @endif
                                        </select>
                                        @error('is_restricted')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="customer_type_id" class="col-md-12 col-form-label">{{ __('Customer Type') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                                    <div class="col-md-12">
                                        <select type="text" id="customer_type_id" class="form-control @error('customer_type_id') is-invalid @enderror" name="customer_type_id" value="{{ $customers->customer_type_id }}" autocomplete="customer_type_id" autofocus>
                                            @if($customers->customer_type_id==1)
                                                <option value="1">Normal</option>
                                                <option value="2">VIP</option>
                                                <option value="3">Admin</option>
                                            @elseif ($customers->customer_type_id==2)
                                                <option value="2">VIP</option>
                                                <option value="1">Normal</option>
                                                <option value="3">Admin</option>
                                            @else
                                                <option value="3">Admin</option>
                                                <option value="1">Normal</option>
                                                <option value="2">VIP</option>
                                            @endif
                                        </select>
                                        @error('customer_type_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="image" class="col-md-12 col-form-label">{{ __('Profile Image') }} </label>
                                    <div class="col-md-6">
                                        <input type="file" style="height: auto;" id="image" class="form-control @error('image') is-invalid @enderror" name="image" autocomplete="image" onchange="loadFileImage(event)">
                                        @error('image')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            @if($customers->image==null)
                                                <image src="{{asset('../image/available.png')}}" id="image_one" style="width: 100%;height: 150px;"></image>
                                            @else
                                                <image src="../../../../../uploads/customer/{{$customers->image}}" id="image_one" style="width: 100%;height: 150px;"></image>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/customers')}}" class="btn btn-secondary btn-sm">
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
    var loadFileImage= function(event) {
        var image = document.getElementById('image_one');
        image.src = URL.createObjectURL(event.target.files[0]);
    };

    $('#is_restricted').select2();
    $('#customer_type_id').select2();

</script>
@endsection
