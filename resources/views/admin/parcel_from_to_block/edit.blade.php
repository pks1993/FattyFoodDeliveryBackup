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
                                    <h2 class="card-title"><b>" Edit FromTo Parcel"</b></h2>
                                </div>
                                <div class="col-md-6" style="text-align: right">
                                    <a href="{{url('fatty/main/admin/parcel_from_to_block')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('fatty.admin.parcel_from_to_block.update',$parcel_from_to_block->parcel_from_to_block_id) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="parcel_from_block_id" class="col-form-label">From Block Name:</label>
                                    <select type="text" class="form-control" id="parcel_from_block_id_edit" name="parcel_from_block_id" disabled>
                                        <option value="{{ $parcel_from_to_block->parcel_from_block_id }}">{{ $parcel_from_to_block->from_block->block_name }}</option>
                                        @foreach ($blocks as $value)
                                            @if($value->parcel_block_id!=$parcel_from_to_block->parcel_from_block_id)
                                                <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                  </div>
                                  <div class="form-group">
                                    <label for="parcel_to_block_id" class="col-form-label">To Block Name:</label>
                                    <select type="text" class="form-control" id="parcel_to_block_id_edit" name="parcel_to_block_id" disabled>
                                        <option value="{{ $parcel_from_to_block->parcel_to_block_id }}">{{ $parcel_from_to_block->to_block->block_name }}</option>
                                        @foreach ($blocks as $value)
                                            @if($value->parcel_block_id!=$parcel_from_to_block->parcel_to_block_id)
                                                <option value="{{ $value->parcel_block_id }}">{{ $value->block_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                  </div>
                                  <div class="form-group">
                                    <label for="delivery_fee" class="col-form-label">Deli Fee</label>
                                    <input type="number" value="{{ $parcel_from_to_block->delivery_fee }}" class="form-control" name="delivery_fee">
                                  </div>
                                  <div class="form-group">
                                    <label for="remark" class="col-form-label">Remark</label>
                                    <textarea type="text" class="form-control" name="remark">{{ $parcel_from_to_block->remark }}</textarea>
                                  </div>
                                  <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> {{ __('Update') }}
                                        </button>
                                        <a href="{{url('fatty/main/admin/parcel_from_to_block')}}" class="btn btn-secondary btn-sm">
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

</script>
@endsection
