@extends('vendor/adminLTE/admin_template')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="text-center">
            {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}<small>{{ $page_description or null }}</small>
        </h1>
        <!-- Main content -->
        <section class="content">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-light-blue">
                        <div class="inner">
                            <h3>{{ $totalGame }}</h3>
                            <p>Gameplay Records</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-ios-game-controller-b"></i>
                        </div>
                        <a href="javascript:" onclick="redirectToGetDataByMenuTitle('menuGameRecord', 'gameRecord');" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div><!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{ $totalPayout }}</h3>
                            <p>Total Payments</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="javascript:" onclick="redirectToGetDataByMenuTitle('menuPayout', 'payout');" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div><!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{ $totalUser }}</h3>
                            <p>User Registrations</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="javascript:" onclick="redirectToGetDataByMenuTitle('menuUser', 'user');" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div><!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{ $totalAdvertise }}</h3>
                            <p>Registered Advertisements</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-speakerphone"></i>
                        </div>
                        <a href="javascript:" onclick="redirectToGetDataByMenuTitle('menuAdvertisement', 'advertisement');" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div><!-- ./col -->
            </div><!-- /.row -->
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-7 connectedSortable">
                    <!-- BAR CHART -->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <i class="fa fa-bar-chart-o"></i>
                            <h3 class="box-title">Cashout & User Chart</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="chart">
                                <canvas id="barChart" style="height: 245px"></canvas>
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->

                    <!-- interactive chart -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-bar-chart-o"></i>
                            <h3 class="box-title">Game Record Chart</h3>
                            <div class="box-tools pull-right" style="margin-top: 3px">
                                Real time
                                <div class="btn-group" id="realtime" data-toggle="btn-toggle">
                                    <button type="button" class="btn btn-default btn-xs active" data-toggle="on">On</button>
                                    <button type="button" class="btn btn-default btn-xs" data-toggle="off">Off</button>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div id="interactive" style="height: 245px;"></div>
                        </div><!-- /.box-body-->
                    </div><!-- /.box -->
                </section><!-- /.Left col -->
                <!-- right col (We are only adding the ID to make the widgets sortable)-->
                <section class="col-lg-5 connectedSortable">
                    <!-- Map box -->
                    <div class="box box-solid bg-light-blue-gradient">
                        <div class="box-header">
                            <!-- tools box -->
                            <div class="pull-right box-tools">
                                <button class="btn btn-primary btn-sm daterange pull-right" data-toggle="tooltip" title="Date range"><i class="fa fa-calendar"></i></button>
                                <button class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="Collapse" style="margin-right: 5px;"><i class="fa fa-minus"></i></button>
                            </div><!-- /. tools -->

                            <i class="fa fa-map-marker"></i>
                            <h3 class="box-title">
                                Visitors
                            </h3>
                        </div>
                        <div class="box-body">
                            <div id="world-map" style="height: 250px; width: 100%;"></div>
                        </div><!-- /.box-body-->
                        <div class="box-footer no-border">
                            <div class="row">
                                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                    <div id="sparkline-1"></div>
                                    <div class="knob-label">Visitors</div>
                                </div><!-- ./col -->
                                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                    <div id="sparkline-2"></div>
                                    <div class="knob-label">Online</div>
                                </div><!-- ./col -->
                                <div class="col-xs-4 text-center">
                                    <div id="sparkline-3"></div>
                                    <div class="knob-label">Exists</div>
                                </div><!-- ./col -->
                            </div><!-- /.row -->
                        </div>
                    </div>
                    <!-- /.box -->

                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <h3 class="box-title">Browser Usage</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="chart-responsive">
                                        <canvas id="pieChart" height="150"></canvas>
                                    </div><!-- ./chart-responsive -->
                                </div><!-- /.col -->
                                <div class="col-md-4">
                                    <ul class="chart-legend clearfix">
                                        <li><i class="fa fa-circle-o text-red"></i> Chrome</li>
                                        <li><i class="fa fa-circle-o text-green"></i> IE</li>
                                        <li><i class="fa fa-circle-o text-yellow"></i> FireFox</li>
                                        <li><i class="fa fa-circle-o text-aqua"></i> Safari</li>
                                        <li><i class="fa fa-circle-o text-light-blue"></i> Opera</li>
                                        <li><i class="fa fa-circle-o text-gray"></i> Navigator</li>
                                    </ul>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </section><!-- right col -->
            </div><!-- /.row (main row) -->
        </section><!-- /.content -->
    </section>
@endsection