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
                    <li class="breadcrumb-item active">Daily Active Customers</li>
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
                {{-- <div class="card-header">
                    <div class="row">
                        <div class="col-md-6 pagination">
                            {{ $customers->appends(request()->input())->links() }}
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <h4><b>{{ "Customers Information" }}</b></h4>
                        </div>
                    </div>
                </div> --}}
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane table-responsive active" id="Admin">
                            <table border="0" cellspacing="5" cellpadding="5">
                                <tbody>
                                    <tr>
                                        <td>Minimum date:</td>
                                        <td><input type="text" id="min" name="min"></td>
                                    </tr>
                                    <tr>
                                        <td>Maximum date:</td>
                                        <td><input type="text" id="max" name="max"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table id="customers" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th class="text-left">Customer Name</th>
                                        <th class="text-left">Customer Phone</th>
                                        <th class="text-left">Register Date</th>
                                        <th class="text-left">Order Count</th>
                                        <th class="text-left">Order Amount</th>
                                        {{-- <th>Image</th> --}}
                                        <th>Action</th>
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
        var minDate, maxDate;
    
    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = minDate.val();
        var max = maxDate.val();
        var date = new Date( data[3] );
        
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
        minDate = new DateTime($('#min'), {
            format: 'Do MMMM YYYY'
        });
        maxDate = new DateTime($('#max'), {
            format: 'Do MMMM YYYY'
        });
        
        // DataTables initialisation
        var table = $("#customers").DataTable({
                "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
                "paging": true, // Allow data to be paged
                "lengthChange": true,
                "searching": true, // Search box and search function will be actived
                "info": true,
                "autoWidth": true,
                "processing": true,  // Show processing
                ajax: "/fatty/main/admin/customers/datatable/dailyactiveajax",
                columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' ,className: "number" , orderable: false, searchable: false},
                {data: 'customer_name', name:'customer_name'},
                {data: 'customer_phone', name:'customer_phone'},
                {data: 'register_date', name:'register_date',className: "register_date"},
                {data: 'order_count', name:'order_count',className: "order_count"},
                {data: 'order_amount', name:'order_amount',className: "order_amount"},
                {data: 'action', name: 'action', orderable: false, searchable: false,className: "action"},
                ],
                dom: 'PlBfrtip',
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
    