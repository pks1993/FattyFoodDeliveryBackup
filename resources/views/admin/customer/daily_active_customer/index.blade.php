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
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <form action="{{ url('fatty/main/admin/daily_active_customers') }}">
                        <input class="col-5 col-md-2" type="date" name="start_date" value="{{ \Carbon\Carbon::parse($date_start)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                        <input class="col-5 col-md-2" type="date" name="end_date" value="{{ \Carbon\Carbon::parse($date_end)->format('Y-m-d') }}" class="btn mb-1" style="background-color:#FFFFFF;width: 100%;border-color:#00dfc2;border-style:solid;border-width:2px;color: #1c1a1a;font-size:15px;font-weight:510;border-radius:5px">
                        <button class="col-1 col-md-1" type="submit" class="btn mb-1" style="height:100%;background:#00dfc2;color:white;font-size:15px;border-radius:5px;"><i class="fa fa-search"></i></button>
                    </form>
                    <div class="mt-3">
                        {{ $customers->appends(request()->input())->links() }}
                    </div>
                    <table id="customers" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th class="text-left">CustomerName</th>
                                <th class="text-left">CustomerPhone</th>
                                <th class="text-left">OrderDate</th>
                                <th class="text-left">OrderCount</th>
                                <th class="text-left">OrderAmount</th>
                                <th class="text-left">CustomerType</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $value)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">
                                    @if($value->customer->customer_name)
                                        {{ $value->customer->customer_name }}
                                    @else
                                        <span style="color: red">{{ "Empty" }}</span>
                                    @endif
                                </td>
                                <td>{{ $value->customer->customer_phone }}</td>
                                <td>{{ date('d/M/Y',strtotime($value->created_at)) }}</td>
                                <td>{{ $value->customer->order_count }}</td>
                                <td>{{ $value->customer->order_amount }}</td>
                                <td>
                                    @if($value->customer->customer_type_id==1)
                                        <a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>
                                    @elseif($value->customer->customer_type_id==2)
                                        <a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>
                                    @else
                                        <a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>
                                    @endif
                                </td>
                                <td class="btn-group">
                                    <a href="/fatty/main/admin/customers/view/ {{ $value->customer_id }}" title="View Detail" class="btn btn-info btn-sm mr-2"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        // DataTables initialisation
        var table = $("#customers").DataTable({
            "lengthMenu": [[15,25,50, 100, 250,500, -1], [15,25,50,100, 250, 500, "All"]],
            "paging": false, // Allow data to be paged
            "lengthChange": false,
            "searching": false, // Search box and search function will be actived
            "info": false,
            "autoWidth": false,
            "processing": false,  // Show processing
        });
    });
</script>
@endpush
