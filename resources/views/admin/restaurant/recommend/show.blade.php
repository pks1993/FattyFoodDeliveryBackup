@extends('admin.layouts.master')

@section('css')
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
                <div class="col-md-12">
                            <form method="POST" action="{{ route('fatty.admin.recommend_restaurants.store') }}" autocomplete="off" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title" style="font-weight: 550;">All Restaurant</h3>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/recommend_restaurants/create')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane table-responsive active" id="Admin">
                                    <div class="pagination">
                                        {{-- {{ $restaurants->appends(request()->input())->links() }} --}}
                                    </div>
                                    <table id="recommend_restaurants" class="table table-bordered table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Image</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($restaurants as $restaurant)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td> 
                                                <td class="text-left">{{ $restaurant->restaurant_name_mm }}</td> 
                                                <td class="text-left">{{ $restaurant->city->city_name_mm }}</td> 
                                                <td class="text-left">{{ $restaurant->state->state_name_mm }}</td> 
                                                <td>
                                                    @if($restaurant->restaurant_image)
                                                        <img src="../../../../uploads/restaurant/{{$restaurant->restaurant_image}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                    @else
                                                        <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                    @endif
                                                </td>
                                                {{-- <td></td> --}}
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="card-body">
                                @csrf
                                <div class="row">
                                    @foreach($restaurants as $restaurant)
                                        <div class="col-md-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <input type="checkbox" name="restaurant_id[]" id="restaurant_id[]" value="{{ $restaurant->restaurant_id }}"> {{ $restaurant->restaurant_name }}

                                                    @foreach($recommend as $rec)
                                                        @if($restaurant->restaurant_id===$rec->restaurant_id)
                                                            <span style="color: red;"> *selected*</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="card-body">
                                                    @if($restaurant->restaurant_image)
                                                        <img src="../../../../uploads/restaurant/{{$restaurant->restaurant_image}}" class="img-rounded" style="width: 100%;height: 100px;">
                                                    @else
                                                        <img src="{{asset('../image/available.png')}}" class="img-rounded" style="width: 100%;height: 100px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>      
                                    @endforeach
                                </div>
                                
                        </div> --}}

                        <div class="card-footer">
                            <div class="form-group row mb-0 text-right">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <i class="fa fa-save"></i> {{ __('Create') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/recommend_restaurants/create')}}" class="btn btn-secondary btn-md">
                                            <i class="fa fa-ban"></i> {{ __('Back') }}
                                        </a>
                                    </div>
                                </div>
                        </div>
                    </div>
                            </form>
                </div>
            </div>
        </div>
        <br><br><br><br><br>
  </section>

@endsection
@section('script')
{{-- <script type="text/javascript">
    $('#restaurant_id').select2();
</script> --}}

@endsection
