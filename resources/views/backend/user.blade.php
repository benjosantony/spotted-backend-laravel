<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
        <span style="font-size: medium" class="label label-info pull-right">Update new users automatically in 10 seconds</span>
    </h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="box-body col-xs-12">
                        <div class="col-xs-2">
                            <select id="filter-string-column" class="form-control">
                                <option value="name">Full Name</option>
                                <option value="bank">Bank Name</option>
                                <option value="account">Bank Account</option>
                            </select>
                        </div>
                        <div class="col-xs-1">
                            <select id="filter-string-operation" class="form-control">
                                <option value="is">is</option>
                                <option value="contain">contain</option>
                            </select>
                        </div>
                        <div class="col-xs-3">
                            <input id="filter-string-value" class="form-control">
                        </div>
                    </div>
                    <div class="box-body col-xs-12">
                        <div class="col-xs-2">
                            <!--select id="filter-date-column-1" class="form-control">
                                <option value="date">Joined Date</option>
                            </select-->
                            <input type="text" value="Joined Date" class="form-control" disabled>
                        </div>
                        <div class="col-xs-1">
                            <!--select id="filter-date-operation-1" class="form-control">
                                <option value="gtae">>=</option>
                            </select-->
                            <input type="text" value=">=" class="form-control" disabled>
                        </div>
                        <div class="col-xs-3">
                            <input id="filter-date-value-1" class="form-control">
                        </div>
                        <div class="col-xs-2">
                            <!--select id="filter-date-column-2" class="form-control">
                                <option value="date">Joined Date</option>
                            </select-->
                            <input type="text" value="Joined Date" class="form-control" disabled>
                        </div>
                        <div class="col-xs-1">
                            <!--select id="filter-date-operation-2" class="form-control">
                                <option value="ltae"><=</option>
                            </select-->
                            <input type="text" value="<=" class="form-control" disabled>
                        </div>
                        <div class="col-xs-3">
                            <input id="filter-date-value-2" class="form-control">
                        </div>
                    </div>
                    <div class="box-body col-xs-12">
                        <div class="col-xs-2">
                            <!--select id="filter-numeric-column-1" class="form-control">
                                <option value="level">Level</option>
                            </select-->
                            <input type="text" value="Level" class="form-control" disabled>
                        </div>
                        <div class="col-xs-1">
                            <!--select id="filter-numeric-operation-1" class="form-control">
                                <option value="gtae">>=</option>
                            </select-->
                            <input type="text" value=">=" class="form-control" disabled>
                        </div>
                        <div class="col-xs-3">
                            <input id="filter-numeric-value-1" class="form-control">
                        </div>
                        <div class="col-xs-2">
                            <!--select id="filter-numeric-column-2" class="form-control">
                                <option value="level">Level</option>
                            </select-->
                            <input type="text" value="Level" class="form-control" disabled>
                        </div>
                        <div class="col-xs-1">
                            <!--select id="filter-numeric-operation-2" class="form-control">
                                <option value="ltae"><=</option>
                            </select-->
                            <input type="text" value="<=" class="form-control" disabled>
                        </div>
                        <div class="col-xs-3">
                            <input id="filter-numeric-value-2" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1 style="padding-bottom: 10px">
        {{ $page_title_main or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
        <div class="pull-right">
            <div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" onchange="toggleAutoApproveBankStatus(this);"{{ $status }}>
                <label class="onoffswitch-label" for="myonoffswitch">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div>
        <small class="pull-right" style="padding-top: 8px; padding-right: 5px; font-weight: bold">Auto approve bank status</small>
    </h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="main-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Facebook ID</th>
                            <th>Full Name</th>
                            <th>Avatar</th>
                            <th>Facebook Email</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Bank Name</th>
                            <th>Bank Account</th>
                            <th>Level</th>
                            <th>Experience</th>
                            <th>Building Token</th>
                            <th>Coin</th>
                            <th>Cash Out <small style="color: darkred">($)</small></th>
                            <th>Joined Date</th>
                            <th>User Status</th>
                            <th>Bank Status</th>
                            <th>Detail</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allData as $data)
                            <tr>
                                <td>{{ $data->fbId }}</td>
                                <td>{{ $data->fullname }}</td>
                                <td><a href="{{ $data->picture }}" target="_blank"><img src="{{ $data->picture }}" alt="" width="40px" height="40px"></a></td>
                                <td>{{ $data->fbEmail }}</td>
                                <td>{{ $data->age }}</td>
                                <td>{{ $data->gender == 1 ? "Male" : "Female" }}</td>
                                <td>{{ $data->phone }}</td>
                                <td>{{ $data->bankName }}</td>
                                <td>{{ $data->bankAccount }}</td>
                                <td>{{ $data->level }}</td>
                                <td>{{ $data->exp }}</td>
                                <td>{{ $data->buildingToken }}</td>
                                <td>{{ $data->coin }}</td>
                                <td>{{ $data->cashOut }}</td>
                                <td>{{ $data->dateCreate }}</td>
                                <td>
                                    @if($data->status == 0)
                                        <span style="color: grey">Inactive</span>
                                    @elseif($data->status == 1)
                                        <span style="color: blue">Active</span>
                                    @elseif($data->status == 2)
                                        <span style="color: red">Ban</span>
                                    @else
                                        Undefined
                                    @endif
                                </td>
                                <td>
                                    @if($data->bankStatus === 0)
                                        <span style="color: grey">Not Update</span>
                                        <input type="hidden" value="2">
                                    @elseif($data->bankStatus == 1)
                                        <span style="color: blue">Approved</span>
                                        <input type="hidden" value="4">
                                    @elseif($data->bankStatus == 2)
                                        <span style="color: green">Pending ...</span>
                                        <input type="hidden" value="0">
                                    @elseif($data->bankStatus == 3)
                                        <span style="color: red">Denied</span>
                                        <input type="hidden" value="3">
                                    @else
                                        Undefined
                                        <input type="hidden" value="1">
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="goToDetailUser('{{ $data->id }}');">Detail</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Facebook ID</th>
                            <th>Full Name</th>
                            <th>Avatar</th>
                            <th>Facebook Email</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Bank Name</th>
                            <th>Bank Account</th>
                            <th>Level</th>
                            <th>Experience</th>
                            <th>Building Token</th>
                            <th>Coin</th>
                            <th>Cash Out <small style="color: darkred">($)</small></th>
                            <th>Joined Date</th>
                            <th>User Status</th>
                            <th>Bank Status</th>
                            <th>Detail</th>
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
    <input type="hidden" id="last-user-id" value="{{ $lastUserId }}">
</section>