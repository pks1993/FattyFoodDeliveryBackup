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
                    <li class="breadcrumb-item active">Restaurant</li>
                    <li class="breadcrumb-item active">Lists</li>
                </ol>
            </div>
            <div class="col-md-12">

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
                            <a href="{{route('fatty.admin.restaurants_user.create')}}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add Restaurant</a>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <h4><b>{{ "Restaurant Information" }}</b></h4>
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
                                            <td>Minimum date:</td>
                                            <td><input type="text" id="min" name="min"></td>
                                        </tr>
                                        <tr>
                                            <td>Maximum date:</td>
                                            <td><input type="text" id="max" name="max"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table id="restaurants" class="table table-hover">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Status</th>
                                            <th>Image</th>
                                            <th>RegisterDate</th>
                                            <th>NameMyanmar</th>
                                            <th>NameEnglish</th>
                                            <th>NameChina</th>
                                            <th>CategoryName</th>
                                            <th>Address</th>
                                            <th>CityName</th>
                                            <th>StateName</th>
                                            <th>UserPhone</th>
                                            <th>UserPassword</th>
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

    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 3000);

        // Custom filtering function which will search data in column four between two values
        $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var min = $('#min').datepicker("getDate");
            var max = $('#max').datepicker("getDate");
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
            $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,dateFormat: 'dd-M-yy' });

            $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat: 'dd-M-yy' });
            // DataTables initialisation
            var table = $("#restaurants").DataTable({
                "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
                "paging": true, // Allow data to be paged
                "lengthChange": true,
                "searching": true, // Search box and search function will be actived
                "info": true,
                "autoWidth": true,
                "processing": true,  // Show processing
                ajax: "/fatty/main/admin/restaurants/datatable/restaurantajax",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex' ,className: "number" , orderable: false, searchable: false},
                    {data: 'status', name: 'status', orderable: false, searchable: false,className: "btn-group"},
                    {data: 'restaurant_image', name:'restaurant_image',className: "restaurant_image"},
                    {data: 'register_date', name:'register_date',className: "register_date"},
                    {data: 'restaurant_name_mm', name:'restaurant_name_mm'},
                    {data: 'restaurant_name_en', name:'restaurant_name_en'},
                    {data: 'restaurant_name_ch', name:'restaurant_name_ch'},
                    {data: 'restaurant_category_name_mm', name:'restaurant_category_name_mm',className: "restaurant_category_name_mm"},
                    {data: 'restaurant_address', name:'restaurant_address'},
                    {data: 'city_name_mm', name:'city_name_mm',className: "text-center"},
                    {data: 'state_name_mm', name:'state_name_mm',className: "text-center"},
                    {data: 'restaurant_user_password', name:'restaurant_user_phone',className: "restaurant_user_phone"},
                    {data: 'restaurant_user_password', name:'restaurant_user_password',className: "restaurant_user_password"},
                    {data: 'action', name: 'action', orderable: false, searchable: false,className: "btn-group"},
                    // {data: 'restaurant_emergency_status', name:'restaurant_emergency_status',className: "text-center"},
                    // {data: 'is_admin_approved', name:'is_admin_approved',className: "text-center"},
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
