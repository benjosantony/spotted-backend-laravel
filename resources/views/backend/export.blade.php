<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">Game Records <span style="color: darkred">(original)</span></h1>
    <div class="row">
        <div class="box box-primary box-body">
            <!-- /.box-header -->
            <div class="col-xs-3">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <input id="game-search" class="form-control" type="text" placeholder="Full Name">
                </div>
            </div>
            <div class="col-xs-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input id="game-date" class="form-control" type="text">
                </div>
            </div>
            <div class="col-xs-3">
                <div class="input-group">
                    <button class="btn btn-success" onclick="exportGameRecord();">CSV</button>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>

    <h1 style="padding-bottom: 10px">Game Records <span style="color: darkred">(events)</span></h1>
    <div class="row">
        <div class="box box-primary box-body">
            <!-- /.box-header -->
            <div class="col-xs-2">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-trophy"></i>
                    </div>
                    <select id="event-id" class="form-control" onchange="getEventTime(this);">
                        <option value="">--- Select ---</option>
                        @foreach($advertise as $adv)
                            <option value="{{ $adv->id }}">{{ $adv->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <input id="event-search" class="form-control" type="text" placeholder="Full Name">
                </div>
            </div>
            <div class="col-xs-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input id="event-date" class="form-control" type="text">
                </div>
            </div>
            <div class="col-xs-1">
                <div class="input-group">
                    <button class="btn btn-success" onclick="exportEventGameRecord();">CSV</button>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</section>