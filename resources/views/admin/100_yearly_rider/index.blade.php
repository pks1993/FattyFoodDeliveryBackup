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
    .number {
        text-align: center;
    }
    .order_amount {
        text-align: center;
    }
    .order_count {
        text-align: center;
    }
    .action {
        text-align: center;
    }
    .register_date {
        text-align: center;
    }
    .ui-datepicker-month{
        display: none !important;
    }
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-7">
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
                    <li class="breadcrumb-item active">Yearly Top 100 Riders</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
            <div class="col-md-12">
                {{-- <form method='post' action="{{ route('fatty.admin.backup.riders') }}">
                    @csrf
                    <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportexcel" value='Excel Export'>
                    <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportcsv" value='CSV Export'>
                </form> --}}
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
                        <div class="col-md-6">
                            {{-- <a href="{{route('fatty.admin.riders.create')}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add rider</a> --}}
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h4><b>{{ "Rider Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <table border="0" cellspacing="5" cellpadding="5">
                                    <tbody>
                                        <tr>
                                            <td>From Year:</td>
                                            <td><input type="text" id="min" value="{{ now()->format('Y') }}" name="min" autocomplete="off"></td>
                                        </tr>
                                        <tr>
                                            <td>To Year:</td>
                                            <td><input type="text" id="max" value="{{ now()->format('Y') }}" name="max" autocomplete="off"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table id="riders" class="table table-bordered table-striped table-hover display nowrap" border="0" cellspacing="5" cellpadding="5">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Action</th>
                                            <th>Image</th>
                                            <th class="text-left">Rider Name</th>
                                            <th class="text-left">Rider Phone</th>
                                            <th class="text-left">StateName</th>
                                            <th class="text-left">RegisterDate</th>
                                            <th class="text-left">Latitude</th>
                                            <th class="text-left">Longitude</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
    @push('scripts')
    <script>
      $(document).ready(function() {
        $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var min = $('#min').datepicker({
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'yy',
                onChangeMonthYear: function(year, month, widget) {
                    setTimeout(function() {
                        $('.ui-datepicker-calendar').hide();
                    });
                },
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                    table.draw();
                },
            }).click(function(){
                $('.ui-datepicker-calendar').hide();
            });
            var minData = min.val();
            var minYear = min.val();
            // var minData = minDate.split('-');
            // var minYear = minData[1];

            var max = $('#max').datepicker({
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'yy',
                onChangeMonthYear: function(year, month, widget) {
                    setTimeout(function() {
                        $('.ui-datepicker-calendar').hide();
                    });
                },
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                    table.draw();
                },
            }).click(function(){
                $('.ui-datepicker-calendar').hide();
            });
            var maxData = max.val();
            var maxYear = max.val();
            // var maxData = maxDate.split('-');
            // var maxYear = maxData[1];

            var date = data[6].split('-');

            // console.log(minData);

            if ((minData == '' && maxData == '') ||
            (date[2] == minYear) ||
            ((date[2] >= minYear || date[2] > minYear) && maxYear >= date[2])

            )  {
                return true;
            }
            return false;
        }
        );

        // Create date inputs
        $("#min").datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'yy',
            onChangeMonthYear: function(year, month, widget) {
                setTimeout(function() {
                    $('.ui-datepicker-calendar').hide();
                });
            },
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
                table.draw();
            },
        }).click(function(){
            $('.ui-datepicker-calendar').hide();
        });

        $("#max").datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'yy',
            onChangeMonthYear: function(year, month, widget) {
                setTimeout(function() {
                    $('.ui-datepicker-calendar').hide();
                });
            },
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
                table.draw();
            },
        }).click(function(){
            $('.ui-datepicker-calendar').hide();
        });

            // DataTables initialisation
            var table = $("#riders").DataTable({
                "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
                "paging": true, // Allow data to be paged
                "lengthChange": true,
                "searching": true, // Search box and search function will be actived
                "info": true,
                "autoWidth": true,
                "processing": true,  // Show processing
                ajax: "/fatty/main/admin/riders/datatable/yearlyhundredriderajax",
                columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' ,className: "number" , orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false,className: "action"},
                {data: 'rider_image', name:'rider_image',className: "rider_image"},
                {data: 'rider_user_name', name:'rider_user_name'},
                {data: 'rider_user_phone', name:'rider_user_phone'},
                {data: 'state', name:'state'},
                {data: 'register_date', name:'register_date',className: "register_date"},
                {data: 'rider_latitude', name:'rider_latitude'},
                {data: 'rider_longitude', name:'rider_longitude'},

                ],
                dom: 'lBfrtip',
                buttons: [
                'excel', 'pdf', 'print'
                ],
            });

            // Refilter the table
            $('#min, #max').on('change', function () {
                table.draw();
            });
        });
    </script>
    @endpush
