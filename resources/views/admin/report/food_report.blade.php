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
                        <li class="breadcrumb-item active">Food Order</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
                </div>
                <div class="col-md-12">
                    {{-- <form method='post' action="{{ route('fatty.admin.backup.restaurants') }}">
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
                            {{-- <div class="col-md-6">
                                <a href="{{url('fatty/main/admin/all_riders_location')}}" class="btn btn-info btn-sm">
                                    <i class="fa fa-location-arrow"></i> All Rider Location
                                </a>
                            </div> --}}
                            <div class="col-md-12" style="text-align: right;">
                                <h4><b>{{ "Food Orders Information" }}</b></h4>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <table border="0" cellspacing="7" cellpadding="7">
                                    <tbody>
                                        <tr>
                                            <td>Minimum date:</td>
                                            <td><input type="text" id="min" value="{{ date('d/m/Y') }}" name="min"></td>
                                        </tr>
                                        <tr>
                                            <td>Maximum date:</td>
                                            <td><input type="text" id="max" value="{{ date('d/m/Y') }}" name="max"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table id="foods_orders" class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th>Date</th>
                                        <th>RiderName</th>
                                        <th>OrderId</th>
                                        <th>TranstationAmount</th>
                                        <th>Income(%)</th>
                                        <th>RiderBill(DeliFee)</th>
                                        <th>Profit</th>
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
    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
   function( settings, data, dataIndex ) {
       var min = $('#min').datepicker("getDate");
       var max = $('#max').datepicker("getDate");
       var date = new Date( data[1] );

       if (
       ( min === null && max === null ) ||
       ( min === null && date <= max ) ||
       ( min <= date   && max === null ) ||
       ( min <= date   && date <= max )
       ) {
           return true;
       }
       return false;
   }
   );


   $(document).ready(function() {
       // Create date inputs
       $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,dateFormat: 'dd/mm/yy' });

       $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat: 'dd/mm/yy' });

       // DataTables initialisation
       var table = $("#foods_orders").DataTable({
           "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
           "paging": true, // Allow data to be paged
           "lengthChange": true,
           "searching": true, // Search box and search function will be actived
           "info": true,
           "autoWidth": true,
           "processing": true,  // Show processing
           ajax: "/fatty/main/admin/riders/food_order/datatable/report/foodorderajax",
           columns: [
           {data: 'DT_RowIndex', name: 'DT_RowIndex',className:"text-center" , orderable: false, searchable: false},
           {data: 'ordered_date', name:'ordered_date'},
           {data: 'rider_name', name:'rider_name'},
           {data: 'customer_order_id', name:'customer_order_id'},
           {data: 'bill_total_price', name:'bill_total_price'},
           {data: 'income', name:'income'},
           {data: 'rider_delivery_fee', name:'rider_delivery_fee'},
           {data: 'profit', name:'profit'},
        //    {data: 'action', name: 'action',className:'btn-group', orderable: false, searchable: false},
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
   setTimeout(function() {
       $('#successMessage').fadeOut('fast');
   }, 2000);
</script>
@endpush
