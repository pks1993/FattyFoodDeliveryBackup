@extends('admin.layouts.master')

@section('css')
<style>
    nav>.nav-tabs .nav-link {
        color: #000000 !important;
}
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-7">
                
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Parcel Ordes</li>
                    <li class="breadcrumb-item active">Charts</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<nav>
    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Daily Parcel Orders Report</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Monthly Parcel Orders Report</a>
        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Yearly Parcel Orders Report</a>
    </div>
</nav>
<div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                
                                <div class="col-md-12 col-sm-12">
                                    <div id="daily" style="height: 400px"></div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                
                                <div class="col-md-12 col-sm-12">
                                    <div id="monthly" style="height: 400px"></div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                
                                <div class="col-md-12 col-sm-12">
                                    <div id="yearly" style="height: 400px"></div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{asset('js/highcharts.js')}}"></script>
<script>
    $(document).ready(function () {
        Highcharts.chart('daily', {
            
            title: {
                text: 'Daily Parcel Orders Report'
            },
            
            yAxis: {
                allowDecimals: false,
                title: {
                    text: 'Number of orders'
                }
            },
            
            xAxis: {
                categories: [
                <?php echo json_encode($days[9])?>,
                <?php echo json_encode($days[8])?>,
                <?php echo json_encode($days[7])?>,
                <?php echo json_encode($days[6])?>,
                <?php echo json_encode($days[5])?>,
                <?php echo json_encode($days[4])?>,
                <?php echo json_encode($days[3])?>,
                <?php echo json_encode($days[2])?>,
                <?php echo json_encode($days[1])?>,
                <?php echo json_encode($days[0])?>,
                ],
            },
            
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            series: [{
                name: 'Parcel orders',
                data: [<?php echo json_encode($daily_orders[9]) ?>,<?php echo json_encode($daily_orders[8]) ?>,<?php echo json_encode($daily_orders[7]) ?>,<?php echo json_encode($daily_orders[6]) ?>,<?php echo json_encode($daily_orders[5]) ?>,<?php echo json_encode($daily_orders[4]) ?>,<?php echo json_encode($daily_orders[3]) ?>,<?php echo json_encode($daily_orders[2]) ?>,<?php echo json_encode($daily_orders[1]) ?>,<?php echo json_encode($daily_orders[0]) ?>]
            }],
            
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
            
        });

        Highcharts.chart('monthly', {
            
            title: {
                text: 'Monthly Parcel Orders Report'
            },
            
            
            yAxis: {
                allowDecimals: false,
                title: {
                    text: 'Number of orders'
                }
            },
            
            xAxis: {
                categories: [
                <?php echo json_encode($months[9])?>,
                <?php echo json_encode($months[8])?>,
                <?php echo json_encode($months[7])?>,
                <?php echo json_encode($months[6])?>,
                <?php echo json_encode($months[5])?>,
                <?php echo json_encode($months[4])?>,
                <?php echo json_encode($months[3])?>,
                <?php echo json_encode($months[2])?>,
                <?php echo json_encode($months[1])?>,
                <?php echo json_encode($months[0])?>,
                ],
            },
            
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            series: [{
                name: 'Parcel orders',
                data: [<?php echo json_encode($monthly_orders[9]) ?>,<?php echo json_encode($monthly_orders[8]) ?>,<?php echo json_encode($monthly_orders[7]) ?>,<?php echo json_encode($monthly_orders[6]) ?>,<?php echo json_encode($monthly_orders[5]) ?>,<?php echo json_encode($monthly_orders[4]) ?>,<?php echo json_encode($monthly_orders[3]) ?>,<?php echo json_encode($monthly_orders[2]) ?>,<?php echo json_encode($monthly_orders[1]) ?>,<?php echo json_encode($monthly_orders[0]) ?>]
            }],
            
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
            
        });

        Highcharts.chart('yearly', {
            
            title: {
                text: 'Yearly Parcel Orders Report'
            },
            
            
            yAxis: {
                allowDecimals: false,
                title: {
                    text: 'Number of orders'
                }
            },
            
            xAxis: {
                categories: [
                <?php echo json_encode($years[2])?>,
                <?php echo json_encode($years[1])?>,
                <?php echo json_encode($years[0])?>,
                ],
            },
            
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            series: [{
                name: 'Parcel orders',
                data: [<?php echo json_encode($yearly_orders[2]) ?>,<?php echo json_encode($yearly_orders[1]) ?>,<?php echo json_encode($yearly_orders[0]) ?>]
            }],
            
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
            
        });
    });
</script>
@endpush
