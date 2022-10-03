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
{{-- <link href="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.css" rel="stylesheet"/> --}}

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
                    <li class="breadcrumb-item">Benefit Peak Time</li>
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
                            <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#new_benefit_peak_time"><i class="fa fa-plus-circle"></i> Add Benefit Peak Time</a></button>
                            <form method="post" action="{{route('fatty.admin.benefit_peak_time.store')}}" id="form">
                                @csrf
                                <div class="modal fade" id="new_benefit_peak_time" tabindex="-1" role="dialog" aria-labelledby="new_benefit_peak_time" aria-hidden="true" style="text-align: left;">
                                    <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="new_benefit_peak_time">"Add Benefit Peak Time"</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="start_time_one" class="col-form-label">Start Time One :</label>
                                                <input type="time" placeholder="--:--" id="time" class="form-control" name="start_time_one">
                                            </div>
                                            <div class="form-group">
                                                <label for="end_time_one" class="col-form-label">End Time One :</label>
                                                <input type="time" placeholder="--:--" id="time1" class="form-control" name="end_time_one">
                                            </div>
                                            <div class="form-group">
                                                <label for="start_time_two" class="col-form-label">Start Time Two :</label>
                                                <input type="time" placeholder="--:--" id="time2" class="form-control" name="start_time_two">
                                            </div>
                                            <div class="form-group">
                                                <label for="end_time_two" class="col-form-label">End Time Two :</label>
                                                <input type="time" placeholder="--:--" id="time3" class="form-control" name="end_time_two">
                                            </div>
                                            <div class="form-group">
                                                <label for="peak_time_percentage" class="col-form-label">Peak Time Percentage :</label>
                                                <input type="text" value="0" class="form-control" name="peak_time_percentage">
                                            </div>
                                            <div class="form-group">
                                                <label for="peak_time_amount" class="col-form-label">Peak Time Amount :</label>
                                                <input type="number" value="0" class="form-control" name="peak_time_amount">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="peak_time_start_date" class="col-form-label">Peak Time Start Date :</label>
                                                <input type="date" value="{{ \Carbon\Carbon::parse($peak_time_start_date)->format('Y-m-d') }}" class="form-control" name="peak_time_start_date">
                                                
                                            </div>
                                            <div class="form-group">
                                                <label for="peak_time_end_date" class="col-form-label">Peak Time End Date :</label>
                                                <input type="date" value="{{ \Carbon\Carbon::parse($peak_time_end_date)->format('Y-m-d') }}" class="form-control" name="peak_time_end_date">
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
                                    <th style="text-align: center">StartTimeOne</th>
                                    <th style="text-align: center">EndTimeOne</th>
                                    <th style="text-align: center">StartTimeTwo</th>
                                    <th style="text-align: center">EndTimeTwo</th>
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
                                    <td>{{ $item->start_time_one }}</td>
                                    <td>{{ $item->end_time_one }}</td>
                                    <td>{{ $item->start_time_two }}</td>
                                    <td>{{ $item->end_time_two }}</td>
                                    <td>{{ $item->peak_time_percentage }}</td>
                                    <td>{{ $item->peak_time_amount }}</td>
                                    <td>{{ $item->peak_time_start_date }}</td>
                                    <td>{{ $item->peak_time_end_date }}</td>
                                    <td class="btn btn-group">
                                        <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#q{{ $item->benefit_peak_time_id }}"><i class="fa fa-edit"></i></button>
                                        <form method="post" action="{{route('fatty.admin.benefit_peak_time.update',$item->benefit_peak_time_id)}}" id="form">
                                            @csrf
                                            <div class="modal fade" id="q{{ $item->benefit_peak_time_id }}" tabindex="-1" role="dialog" aria-labelledby="new_deli_fee" aria-hidden="true" style="text-align: left;">
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
                                                            <label for="start_time_one" class="col-form-label">Start Time One :</label>
                                                            <input type="time" value="{{ $item->start_time_one }}" id="time" class="form-control" name="start_time_one">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="end_time_one" class="col-form-label">End Time One :</label>
                                                            <input type="time" value="{{ $item->end_time_one }}" id="time1" class="form-control" name="end_time_one">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="start_time_two" class="col-form-label">Start Time Two :</label>
                                                            <input type="time" value="{{ $item->start_time_two }}" id="time2" class="form-control" name="start_time_two">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="end_time_two" class="col-form-label">End Time Two :</label>
                                                            <input type="time" value="{{ $item->end_time_two }}" id="time3" class="form-control" name="end_time_two">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="peak_time_percentage" class="col-form-label">Peak Time Percentage :</label>
                                                            <input type="text" value="{{ $item->peak_time_percentage }}" class="form-control" name="peak_time_percentage">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="peak_time_amount" class="col-form-label">Peak Time Amount :</label>
                                                            <input type="number" value="{{ $item->peak_time_amount }}" class="form-control" name="peak_time_amount">
                                                        </div>
                                                       
                                                        <div class="form-group">
                                                            <label for="peak_time_start_date" class="col-form-label">Peak Time Start Date :</label>
                                                            <input type="date" value="{{ \Carbon\Carbon::parse($item->peak_time_start_date)->format('Y-m-d') }}" class="form-control" name="peak_time_start_date">
                                                            
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="peak_time_end_date" class="col-form-label">Peak Time End Date :</label>
                                                            <input type="date" value="{{ \Carbon\Carbon::parse($item->peak_time_end_date)->format('Y-m-d') }}" class="form-control" name="peak_time_end_date">
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

                                        {{-- <form action="{{route('fatty.admin.benefit_peak_time.destroy', $item->benefit_peak_time_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </form> --}}
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
{{-- <script src="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js"></script> --}}

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

        // var timepicker = new TimePicker('time', {
        //     lang: 'en',
        //     theme: 'dark'
        // });
        // timepicker.on('change', function(evt) {
        //     var value = (evt.hour || '00') + ':' + (evt.minute || '00');
        //     evt.element.value = value;
        // });
    });
</script>
@endpush
