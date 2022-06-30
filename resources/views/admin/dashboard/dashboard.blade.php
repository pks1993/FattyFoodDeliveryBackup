@extends('admin.layouts.master')

@section('css')


@endsection

@section('content')

    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
              <h4><b>Dashboard</b></h4>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Home</li>
              <li class="breadcrumb-item active"><a href="{{url('admin/dashboard')}}">Dashboard</a> </li>
              <li class="breadcrumb-item">All Lists</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $all_orders }}</h3></span></div>
                        <p>Total Customers Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ url('fatty/main/admin/customers') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $all_food_orders }}</h3></span></div>
                        <p>Total Foods Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ url('fatty/main/admin/customers') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $all_parcel_orders }}</h3></span></div>
                        <p>Total Parcel Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ url('fatty/main/admin/customers') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $total_customer }}</h3></span></div>
                        <p>Total Customers</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ url('fatty/main/admin/customers') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box bg-primary">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $total_all_admin }}</h3></span></div>
                        <p>Total All Admin</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ url('fatty/main/admin/user') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $total_main_admin }}</h3></span></div>
                        <p>Total Main Admin</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ url('fatty/main/admin/user') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $total_branch_admin }}</h3></span></div>
                        <p>Total Branch Admin</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ url('fatty/main/admin/user') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box" style="background-color: #000350;color: white;">
                    <div class="inner">
                        <div id="shiva"><span class="count"><h3>{{ $total_zone }}</h3></span></div>
                        <p>Total Zone</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ url('fatty/main/admin/zones') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('script')

@endsection



