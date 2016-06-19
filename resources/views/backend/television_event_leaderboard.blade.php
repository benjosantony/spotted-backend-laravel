<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
        <select id="advertiseList" class="form-control" style="display: inline; width: auto" onchange="redirectToGetDataByMenuTitle('menuEventLeaderboard', 'eventLeaderboard');">
            @foreach($advertiseList as $advertise)
                <option value="{{$advertise->id}}"{{$advertise->id == $currentId ? " selected" : "" }}>{{$advertise->name}}</option>
            @endforeach
        </select>
        <button id="entirePublish" class="btn btn-success pull-right btn-sm" onclick="publishEntireEventLeaderboard();"{{ $entirePublish ? "" : " disabled"}}>Publish Entire Leaderboard</button>
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
                            <th>Time Played <small style="color: darkred">(milliseconds)</small></th>
                            <th>Date Updated</th>
                            <th>Win</th>
                            <th>Win Value / Congratulation Text</th>
                            <th>Confirm</th>
                            <th>Publish</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allData as $data)
                        <tr>
                            <td>{{ $data->fullname }}</td>
                            <td>{{ number_format($data->timePlayed) }}</td>
                            <td>{{ $data->dateUpdate }}</td>
                            <td>
                                <select id="cell-{{ $data->id }}-win" disabled>
                                    <option value="0"{{ $data->win == 0 ? " selected" : "" }}>Not Winnner</option>
                                    <option value="1"{{ $data->win == 1 ? " selected" : "" }}>Ingame Winner</option>
                                    <option value="2"{{ $data->win == 2 ? " selected" : "" }}>Outgame Winner</option>
                                </select>
                            </td>
                            <td><input id="cell-{{ $data->id }}-value" type="text" value="{{ $data->winValue }}" disabled></td>
                            <td>
                                @if($data->confirm == 1)
                                <span style="color: darkgreen">Confirmed</span>
                                @elseif($data->confirm == 0)
                                <span style="color: deepskyblue">Waiting</span>
                                @endif
                            </td>
                            <td>
                                <select id="cell-{{ $data->id }}-publish" disabled>
                                    <option value="0"{{ $data->publish == 0 ? " selected" : "" }}>Pending</option>
                                    <option value="1"{{ $data->publish == 1 ? " selected" : "" }}>Published</option>
                                </select>
                            </td>
                            <td>
                                <div id="operation-{{ $data->id }}-edit" style="position: absolute">
                                    <button class="btn btn-sm btn-info" onclick="editSelectedLeaderboardRow(this, '{{ $data->id }}');">Edit</button>
                                </div>
                                <div id="operation-{{ $data->id }}-save" style="position: absolute; display: none">
                                    <button class="btn btn-sm btn-primary" onclick="saveSelectedLeaderboardRow(this, '{{ $data->id }}');">Save</button>
                                    <button class="btn btn-sm btn-default" onclick="cancelSelectedLeaderboardRow(this, '{{ $data->id }}');">Cancel</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Full Name</th>
                            <th>Time Played <small style="color: darkred">(milliseconds)</small></th>
                            <th>Date Updated</th>
                            <th>Win</th>
                            <th>Win Value / Congratulation Text</th>
                            <th>Confirm</th>
                            <th>Publish</th>
                            <th>Operation</th>
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