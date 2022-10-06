@extends('admin.layouts.master')

@section('css')
<style>
    form>.fa {
        display: none;
    }
    .dt-buttons>button{
        border-radius: revert;
        margin-top: 15px;
        margin-right: 5px;
    }
    .dataTables_length >label {
        margin-right: 15px !important;
        margin-top: 15px;
    }
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-7" style="height: 20px;">
                <div class="flash-message" id="successMessage">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item">Rider Benefit</li>
                    <li class="breadcrumb-item">Lists</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#new_rider_benefit"><i class="fa fa-plus-circle"></i> Add Rider Benefit</a></button>
                            <form method="post" action="{{route('fatty.admin.rider_benefit.store')}}" id="form">
                                @csrf
                                <div class="modal fade" id="new_rider_benefit" tabindex="-1" role="dialog" aria-labelledby="new_rider_benefit" aria-hidden="true" style="text-align: left;">
                                    <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="new_rider_benefit">"Add Rider Benefit"</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="benefit_start_date" class="col-form-label">Benefit Start Date :</label>
                                                <input type="date" value="{{ \Carbon\Carbon::parse($benefit_start_date)->format('Y-m-d') }}" class="form-control" name="benefit_start_date">
                                                
                                            </div>
                                            <div class="form-group">
                                                <label for="benefit_end_date" class="col-form-label">Benefit End Date :</label>
                                                <input type="date" value="{{ \Carbon\Carbon::parse($benefit_end_date)->format('Y-m-d') }}" class="form-control" name="benefit_end_date">
                                            </div>
                                            <div class="form-group">
                                                <label for="start_benefit_count" class="col-form-label">Start Benefit Order Count :</label>
                                                <input type="number" value="0" class="form-control" name="start_benefit_count">
                                            </div>
                                            <div class="form-group">
                                                <label for="end_benefit_count" class="col-form-label">End Benefit Order Count :</label>
                                                <input type="number" value="0" class="form-control" name="end_benefit_count">
                                            </div>
                                            <div class="form-group">
                                                <label for="benefit_percentage" class="col-form-label">Benefit Percentage :</label>
                                                <input type="text" value="0" class="form-control" name="benefit_percentage">
                                            </div>
                                            <div class="form-group">
                                                <label for="benefit_amount" class="col-form-label">Benefit Amount :</label>
                                                <input type="number" value="0" class="form-control" name="benefit_amount">
                                            </div>
                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-sm">Create</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-6" style="text-align: right">
                            <h4><b>Benefit Information</b></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="food_fees" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">No.</th>
                                    <th style="text-align: center">OrderCount</th>
                                    <th style="text-align: center">Percentage</th>
                                    <th style="text-align:center">Amount</th>
                                    <th style="text-align:center">StartDate</th>
                                    <th style="text-align:center">EndDate</th>
                                    <th style="text-align:center">Action</th>
                                </tr>
                            </thead>
                                @foreach ($rider_benefit as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->start_benefit_count }}   <span style="font-weight: 800;font-size:20px;">&nbsp;- &nbsp;</span>   {{ $item->end_benefit_count }}</td>
                                    <td>{{ $item->benefit_percentage }}</td>
                                    <td>{{ $item->benefit_amount }}</td>
                                    <td>{{ $item->benefit_start_date }}</td>
                                    <td>{{ $item->benefit_end_date }}</td>
                                    <td class="btn btn-group">
                                        <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#q{{ $item->rider_benefit_id }}"><i class="fa fa-edit"></i></button>
                                        <form method="post" action="{{route('fatty.admin.rider_benefit.update',$item->rider_benefit_id)}}" id="form">
                                            @csrf
                                            <div class="modal fade" id="q{{ $item->rider_benefit_id }}" tabindex="-1" role="dialog" aria-labelledby="new_deli_fee" aria-hidden="true" style="text-align: left;">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title" id="new_deli_fee">Benefit Edit</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="benefit_start_date" class="col-form-label">Benefit Start Date :</label>
                                                            <input type="date" value="{{ \Carbon\Carbon::parse($item->benefit_start_date)->format('Y-m-d') }}" class="form-control" name="benefit_start_date">
                                                            
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="benefit_end_date" class="col-form-label">Benefit End Date :</label>
                                                            <input type="date" value="{{ \Carbon\Carbon::parse($item->benefit_end_date)->format('Y-m-d') }}" class="form-control" name="benefit_end_date">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="start_benefit_count" class="col-form-label">Start Benefit Order Count :</label>
                                                            <input type="number" value="{{ $item->start_benefit_count }}" class="form-control" name="start_benefit_count">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="end_benefit_count" class="col-form-label">End Benefit Order Count :</label>
                                                            <input type="number" value="{{ $item->end_benefit_count }}" class="form-control" name="end_benefit_count">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="benefit_percentage" class="col-form-label">Benefit Percentage :</label>
                                                            <input type="text" value="{{ $item->benefit_percentage }}" class="form-control" name="benefit_percentage">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="benefit_amount" class="col-form-label">Benefit Amount :</label>
                                                            <input type="number" value="{{ $item->benefit_amount }}" class="form-control" name="benefit_amount">
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </form>

                                        <form action="{{route('fatty.admin.rider_benefit.destroy', $item->rider_benefit_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                    
                                </tr>
                                @endforeach
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
<script>
    $(function () {
        $('#benefit_type').select2({
            theme: 'bootstrap4'
        });
        $('#benefit_type_edit').select2({
            theme: 'bootstrap4'
        });
    });
</script>
@endpush
