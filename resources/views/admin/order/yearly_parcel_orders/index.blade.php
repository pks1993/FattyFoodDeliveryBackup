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
                    <li class="breadcrumb-item active">Monthly Parcel Ordes</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
            {{-- <div class="col-md-12">
                <form method='post' action="{{ route('fatty.admin.backup.customers') }}">
                    @csrf
                    <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportexcel" value='Excel Export'>
                    <input type="submit" class="btn btn-sm" style="background-color: #000335;color: #FFFFFF;" name="exportcsv" value='CSV Export'>
                </form>
            </div> --}}
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane table-responsive active" id="Admin">
                            <table border="0" cellspacing="5" cellpadding="5">
                                <tbody>
                                    <tr>
                                        <td>From Year:</td>
                                        <td><input type="text" id="min" name="min" autocomplete="off"></td>
                                    </tr>
                                    <tr>
                                        <td>To Year:</td>
                                        <td><input type="text" id="max" name="max" autocomplete="off"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table id="orders" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>CustomerOrderId</th>
                                        <th>CustomerBookingId</th>
                                        <th>OrdereDate</th>
                                        <th>OrderTime</th>
                                        <th>OrderStatus</th>
                                        <th>CustomerName</th>
                                        <th>RiderName</th>
                                        <th>PaymentMethod</th>
                                        <th>TotalPrice</th>
                                        <th>Detail</th>
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
            
            var date = data[3].split('-');
            
            // console.log(minData);
            
            if ((minData == '' && maxData == '') ||
            (date[2] == minYear) ||
            ((date[2] <= minYear || date[2] > minYear) && maxYear >= date[2])
            
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
        var table = $("#orders").DataTable({
            "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
            "paging": true, // Allow data to be paged
            "lengthChange": true,
            "searching": true, // Search box and search function will be actived
            "info": true,
            "autoWidth": true,
            "processing": true,  // Show processing
            ajax: "/fatty/main/admin/orders/datatable/yearlyparcelorderajax",
            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
            {data: 'customer_order_id', name:'customer_order_id'},
            {data: 'customer_booking_id', name:'customer_booking_id'},
            {data: 'ordered_date', name:'ordered_date'},
            {data: 'order_time', name:'order_time'},
            {data: 'order_status_name', name:'order_status_name'},
            {data: 'customer_name', name:'customer_name'},
            {data: 'rider_name', name:'rider_name'},
            {data: 'payment_method_name', name:'payment_method_name'},
            {data: 'bill_total_price', name:'bill_total_price'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            dom: 'lBfrtip',
            buttons: [
            'excel', 'pdf', 'print'
            ],
        });
        $('#min, #max').on('change', function () {
            table.draw();
        });
    });
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush
