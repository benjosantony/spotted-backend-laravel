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
                    <table id="sub-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Level</th>
                            <th>Building Token</th>
                            <th>From Experience</th>
                            <th>To Experience</th>
                            <th>Add New</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input id="cell-new-level" type="text" value=""></td>
                                <td><input id="cell-new-token" type="text" value=""></td>
                                <td><input id="cell-new-from" type="text" value=""></td>
                                <td><input id="cell-new-to" type="text" value=""></td>
                                <td><button class="btn btn-sm btn-primary" onclick="addNewLevelRow()" id="btn-add">Add New Level</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="main-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Level</th>
                            <th>Building Token</th>
                            <th>From Experience</th>
                            <th>To Experience</th>
                            <th>Edit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allData as $data)
                            <tr>
                                <td><input id="cell-{{ $data->id }}-level" type="text" value="{{ $data->level }}" disabled></td>
                                <td><input id="cell-{{ $data->id }}-token" type="text" value="{{ $data->buildingToken }}" disabled></td>
                                <td><input id="cell-{{ $data->id }}-from" type="text" value="{{ $data->fromExp }}" disabled></td>
                                <td><input id="cell-{{ $data->id }}-to" type="text" value="{{ $data->toExp }}" disabled></td>
                                <td>
                                    <div id="operation-{{ $data->id }}-edit" style="position: absolute">
                                        <button class="btn btn-sm btn-info" onclick="editSelectedLevelRow(this, '{{ $data->id }}');">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="showConfirmBox(this, '{{ $data->id }}', deleteSelectedLevelRow);">Delete</button>
                                    </div>
                                    <div id="operation-{{ $data->id }}-save" style="position: absolute; display: none">
                                        <button class="btn btn-sm btn-primary" onclick="saveSelectedLevelRow(this, '{{ $data->id }}');">Save</button>
                                        <button class="btn btn-sm btn-default" onclick="cancelSelectedLevelRow(this, '{{ $data->id }}');">Cancel</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Level</th>
                            <th>Building Token</th>
                            <th>From Experience</th>
                            <th>To Experience</th>
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