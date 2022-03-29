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
                    <li class="breadcrumb-item active">Customers</li>
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
                                        <td>From Month:</td>
                                        <td><input type="text" id="min" name="min" autocomplete="off"></td>
                                    </tr>
                                    <tr>
                                        <td>To Month:</td>
                                        <td><input type="text" id="max" name="max" autocomplete="off"></td>
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
                                    {{--                                        
                                        @foreach($customers as $key=>$customer)
                                        <tr class="text-center">
                                            <td> {{ ($tno*15)+$key+1 }} </td>
                                            <td class="text-left">
                                                @if($customer->customer_name==null)
                                                <p style="color: red;">{{ "Empty" }}</p>
                                                @else
                                                {{ $customer->customer_name }}
                                                @endif
                                            </td>
                                            <td class="text-left">{{ $customer->customer_phone}}</td> 
                                            <td class="text-left">{{ $customer->created_at->format('d.m.Y') }}</td> 
                                            <td class="text-left">{{ $customer->order_count }}</td> 
                                            <td class="text-left">{{ $customer->order_amount }}</td>  --}}
                                            {{-- <td>
                                                @if($customer->image)
                                                <img src="../../../uploads/customer/{{$customer->image}}" class="img-rounded" style="width: 55px;height: 45px;" data-toggle="modal" data-target="#customer{{ $customer->customer_id }}">
                                                @else
                                                <img src="{{asset('../image/person.png')}}" class="img-rounded" style="width: 55px;height: 45px;">
                                                @endif
                                                <!-- Modal -->
                                                <div class="modal fade" id="customer{{ $customer->customer_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">{{ $customer->name }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </td> --}}
                                            {{-- <td class="btn-group text-center">
                                                <a href="{{route('fatty.admin.customers.edit',['customer_id'=>$customer->customer_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>
                                                
                                                <form action="{{route('fatty.admin.customers.destroy', $customer->customer_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach --}}
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
                    dateFormat: 'MM yy',
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
                var minDate = min.val();
                var minData = minDate.split('-');
                var minMonth = minData[0];
                var minYear = minData[1];
                
                var max = $('#max').datepicker({
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    dateFormat: 'MM yy',
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
                var maxDate = max.val();
                var maxData = maxDate.split('-');
                var maxMonth = maxData[0];
                var maxYear = maxData[1];
                
                
                
                var date = data[3].split('-');
                console.log(date[1] >= minMonth && minYear <= date[2] && date[1] <= maxMonth && maxYear >= date[2]);
                
                if ((isNaN(minDate) == false && isNaN(maxDate) == false) ||
                (date[1] == minMonth && minYear == date[2]) || 
                ((date[1] >= minMonth || date[1] <= minMonth && minYear < date[2]) && minYear <= date[2] && (date[1] <= maxMonth || date[1] >= maxMonth && maxYear > date[2]) && maxYear >= date[2]) 
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
                dateFormat: 'mm-yy',
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
                dateFormat: 'mm-yy',
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
            var table = $("#customers").DataTable({
                "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
                "paging": true, // Allow data to be paged
                "lengthChange": true,
                "searching": true, // Search box and search function will be actived
                "info": true,
                "autoWidth": true,
                "processing": true,  // Show processing
                ajax: "/fatty/main/admin/customers/datatable/monthlyorderedajax",
                columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' ,className: "number" , orderable: false, searchable: false},
                {data: 'customer_name', name:'customer_name'},
                {data: 'customer_phone', name:'customer_phone'},
                {data: 'register_date', name:'register_date',className: "register_date"},
                {data: 'order_count', name:'order_count',className: "order_count"},
                {data: 'order_amount', name:'order_amount',className: "order_amount"},
                {data: 'action', name: 'action', orderable: false, searchable: false,className: "action"},
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
    