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
                    <li class="breadcrumb-item active">Menu</li>
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
                            <a href="{{url('fatty/main/admin/restaurants')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>lists</span></a>
                            <!-- Button trigger modal -->
                            <a href="#" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#foodmenu" style="color: white;" title="Restaurant Menu Create"><i class="fas fa-plus-circle"></i> Add Menu</a>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <h4><b>" {{ $restaurants->restaurant_name_en }} " {{ "Menu Information" }}</b></h4>
                        </div>
                    </div>
                </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane table-responsive active" id="Admin">
                                <input type="hidden" id="restaurant_id" value="{{ $restaurants->restaurant_id }}">
                                {{-- <table border="1" cellspacing="5" cellpadding="5">
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
                                </table> --}}
                                <table id="restaurants" class="table table-hover border">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>NameMyanmar</th>
                                            <th>NameEnglish</th>
                                            <th>NameChina</th>
                                            <th>Created_at</th>
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
    <!-- Modal -->
    <div class="modal fade" id="foodmenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Restaurant Type</h5>
                <a class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
            </div>
            <form method="POST" action="{{ route('fatty.admin.restaurants_menu.store') }}" autocomplete="off" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="form-group row">
                        <label for="food_menu_name_mm" class="col-md-12 col-form-label">{{ __('Food Menu Name Myanmar') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                        <div class="col-md-12">
                            <input id="food_menu_name_mm" type="text" class="form-control @error('food_menu_name_mm') is-invalid @enderror" name="food_menu_name_mm" value="{{ old('food_menu_name_mm') }}" placeholder="{{ "Enter Restaurant Type Name" }}" autocomplete="food_menu_name_mm" autofocus required='true'>
                            @error('food_menu_name_mm')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="food_menu_name_en" class="col-md-12 col-form-label">{{ __('Food Menu Name English') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                        <div class="col-md-12">
                            <input id="food_menu_name_en" type="text" class="form-control @error('food_menu_name_en') is-invalid @enderror" name="food_menu_name_en" value="{{ old('food_menu_name_en') }}" placeholder="{{ "Enter Restaurant Type Name" }}" autocomplete="food_menu_name_en" autofocus required='true'>
                            @error('food_menu_name_en')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="food_menu_name_ch" class="col-md-12 col-form-label">{{ __('Food Menu Name China') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                        <div class="col-md-12">
                            <input id="food_menu_name_ch" type="text" class="form-control @error('food_menu_name_ch') is-invalid @enderror" name="food_menu_name_ch" value="{{ old('food_menu_name_ch') }}" placeholder="{{ "Enter Restaurant Type Name" }}" autocomplete="food_menu_name_ch" autofocus required='true'>
                            @error('food_menu_name_ch')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="restaurant_id" class="col-md-12 col-form-label">{{ __('Restaurant Name') }} <span  style="color: #990000;font-weight:700;">*</span></label>
                        <div class="col-md-12">
                            <select id="restaurant_id" class="form-control @error('restaurant_id') is-invalid @enderror" name="restaurant_id" value="{{ old('restaurant_id') }}" autocomplete="restaurant_id" autofocus required='true'>
                                <option value="{{ $restaurants->restaurant_id }}">{{ $restaurants->restaurant_name_mm }} ( {{ $restaurants->restaurant_name_en }} )</option>
                            </select>
                            @error('restaurant_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create</button>
                </div>
            </form>
        </div>
      </div>
    </div>
    @endsection
    @push('scripts')
    <script>

    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 3000);

        // // Custom filtering function which will search data in column four between two values
        // $.fn.dataTable.ext.search.push(
        // function( settings, data, dataIndex ) {
        //     var min = $('#min').datepicker("getDate");
        //     var max = $('#max').datepicker("getDate");
        //     var date = new Date( data[3] );

        //     if (
        //     ( min === null && max === null ) ||
        //     ( min === null && date <= max ) ||
        //     ( min <= date   && max === null ) ||
        //     ( min <= date   && date <= max )
        //     ) {
        //         return true;
        //     }
        //     return false;
        // }
        // );


        $(document).ready(function() {
            // // Create date inputs
            // $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,dateFormat: 'dd-M-yy' });

            // $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat: 'dd-M-yy' });
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
                ajax: "/fatty/main/admin/restaurants/menu/list/datatable/menuajax/"+id,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex' ,className: "number text-center" , orderable: false, searchable: false},
                    {data: 'food_menu_name_mm', name:'food_menu_name_mm'},
                    {data: 'food_menu_name_en', name:'food_menu_name_en'},
                    {data: 'food_menu_name_ch', name:'food_menu_name_ch'},
                    {data: 'register_date', name:'register_date',className: "register_date"},
                    {data: 'action', name: 'action', orderable: false, searchable: false,className:"btn-group"},
                ],
                dom: 'lBfrtip',
                buttons: [
                'excel', 'pdf', 'print'
                ],
            });

            // // Refilter the table
            // $('#min, #max').on('change', function () {
            //     table.draw();
            // });
        });
    </script>
    @endpush
