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
                    <li class="breadcrumb-item active">Food</li>
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
                            <a href="{{url('fatty/main/admin/restaurants')}}" class="btn btn-danger btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                            <a href="{{route('fatty.admin.restaurants_food.create',$restaurants->restaurant_id)}}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus-circle"></i> Add Food</a>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <h4><b>" {{ $restaurants->restaurant_name_en."'s" }} " {{ "Food Information" }}</b></h4>
                            <input type="hidden" id="restaurant_id" value="{{ $restaurants->restaurant_id }}">
                        </div>
                    </div>
                </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <table id="restaurants" class="table table-hover border">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Status</th>
                                            <th>Image</th>
                                            <th>CreatedDate</th>
                                            <th>NameMyanmar</th>
                                            <th>NameEnglish</th>
                                            <th>NameChina</th>
                                            <th>FoodPrice</th>
                                            <th>FoodMenuName</th>
                                            <th>RestaurantName</th>
                                            <th>Description</th>
                                            <th>Food</th>
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

        $(document).ready(function() {
            var id = document.getElementById('restaurant_id').value;
            // DataTables initialisation
            var table = $("#restaurants").DataTable({
                "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
                "paging": true, // Allow data to be paged
                "lengthChange": true,
                "searching": true, // Search box and search function will be actived
                "info": true,
                "autoWidth": true,
                "processing": true,  // Show processing
                ajax: "/fatty/main/admin/restaurants/food/list/datatable/foodlistajax/"+id,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex' ,className: "number" , orderable: false, searchable: false},
                    {data: 'status', name: 'status', orderable: false, searchable: false,className: "btn-group"},
                    {data: 'food_image', name:'food_image',className: "food_image"},
                    {data: 'register_date', name:'register_date',className: "text-center"},
                    {data: 'food_name_mm', name:'food_name_mm'},
                    {data: 'food_name_en', name:'food_name_en'},
                    {data: 'food_name_ch', name:'food_name_ch'},
                    {data: 'food_price', name:'food_price'},
                    {data: 'food_menu_name', name:'food_menu_name'},
                    {data: 'restaurant_name', name:'restaurant_name'},
                    {data: 'food_description', name:'food_description'},
                    {data: 'food_add', name: 'food_add', orderable: false, searchable: false,className: "text-center"},
                    {data: 'action', name: 'action', orderable: false, searchable: false,className: "btn-group"},
                ],
                dom: 'lBfrtip',
                buttons: [
                'excel', 'pdf', 'print'
                ],
            });
        });
    </script>
    @endpush
