<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
    </h1>
    <!--div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <!-- /.box-header --
                <div class="box-body">
                    <table id="sub-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Appearance Rate <small style="color: darkred">(percentage)</small></th>
                            <th>Minimum Value</th>
                            <th>Maximum Value</th>
                            <th>Add New</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input id="cell-new-name" type="text" value=""></td>
                                <td><input id="cell-new-rate" type="text" value=""></td>
                                <td><input id="cell-new-min" type="text" value=""></td>
                                <td><input id="cell-new-max" type="text" value=""></td>
                                <td><button class="btn btn-sm btn-primary" onclick="addNewDailyRewardRow()" id="btn-add">Add New Daily Reward</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body --
            </div>
            <!-- /.box --
        </div>
    </div-->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="main-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Appearance Rate <small style="color: darkred">(percentage)</small></th>
                            <th>Minimum Value</th>
                            <th>Maximum Value</th>
                            <th>Edit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allData as $data)
                            <tr>
                                <td>{{ $data->name }}</td>
                                <td><input id="cell-{{ $data->id }}-rate" type="text" value="{{ $data->rate }}" disabled></td>
                                <td><input id="cell-{{ $data->id }}-min" type="text" value="{{ $data->minValue }}" disabled></td>
                                <td><input id="cell-{{ $data->id }}-max" type="text" value="{{ $data->maxValue }}" disabled></td>
                                <td>
                                    <div id="operation-{{ $data->id }}-edit" style="position: absolute">
                                        <button class="btn btn-sm btn-info" onclick="editSelectedDailyRewardRow(this, '{{ $data->id }}');">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="showConfirmBox(this, '{{ $data->id }}', deleteSelectedDailyRewardRow);">Delete</button>
                                    </div>
                                    <div id="operation-{{ $data->id }}-save" style="position: absolute; display: none">
                                        <button class="btn btn-sm btn-primary" onclick="saveSelectedDailyRewardRow(this, '{{ $data->id }}');">Save</button>
                                        <button class="btn btn-sm btn-default" onclick="cancelSelectedDailyRewardRow(this, '{{ $data->id }}');">Cancel</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Appearance Rate <small style="color: darkred">(percentage)</small></th>
                            <th>Minimum Value</th>
                            <th>Maximum Value</th>
                            <th>Edit</th>
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
</section>