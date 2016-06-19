<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
    </h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="main-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Advertise Title</th>
                            <th>Started Date</th>
                            <th>Ended Date</th>
                            <th>Result</th>
                            <th>Playing Time <small style="color: darkred">(seconds)</small></th>
                            <th>Compass Rate <small style="color: darkred">(multiplier)</small></th>
                            <th>Key Obtained</th>
                            <th>Reward</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Full Name</th>
                            <th>Advertise Title</th>
                            <th>Started Date</th>
                            <th>Ended Date</th>
                            <th>Result</th>
                            <th>Playing Time <small style="color: darkred">(seconds)</small></th>
                            <th>Compass Rate <small style="color: darkred">(multiplier)</small></th>
                            <th>Key Obtained</th>
                            <th>Reward</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="box-footer text-center">
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <span style="font-size: large"><strong>Average Game Time: </strong>{{ round($time, 2) }} seconds</span>
</section>