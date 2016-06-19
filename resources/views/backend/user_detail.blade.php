<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
        <div class="pull-right" style="padding-left: 5px">
            <button class="btn btn-default btn-sm" onclick="getDataByMenuTitle(null, 'user');">Back to User Management</button>
        </div>
        <div id="user-operation-edit" class="pull-right">
            <button class="btn btn-info btn-sm" onclick="editCurrentUser();">Edit</button>
        </div>
        <div id="user-operation-save" class="pull-right" style="display: none">
            <button class="btn btn-primary btn-sm" onclick="saveCurrentUser();">Save</button>
        </div>
    </h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary box-body">
                <!-- /.box-header -->
                <div class="col-xs-10">
                    <form id="user-form">
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input name="fullName" type="text" class="form-control" value="{{ $data->fullname }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Phone</label>
                                <input name="phone" type="text" class="form-control" value="{{ $data->phone }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>User Status</label>
                                <select name="userStatus" class="form-control" disabled>
                                    <option value="0"{{ $data->status == 0 ? " selected" : "" }}>Inactive</option>
                                    <option value="1"{{ $data->status == 1 ? " selected" : "" }}>Active</option>
                                    <option value="2"{{ $data->status == 2 ? " selected" : "" }}>Ban</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control" disabled>
                                    <option value="0"{{ $data->gender == 0 ? " selected" : "" }}>Female</option>
                                    <option value="1"{{ $data->gender == 1 ? " selected" : "" }}>Male</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Age</label>
                                <input name="age" type="text" class="form-control" value="{{ $data->age }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Bank Name</label>
                                <input name="bankName" type="text" class="form-control" value="{{ $bank->bankName or "" }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Bank Account</label>
                                <input name="bankAccount" type="text" class="form-control" value="{{ $bank->bankAccount or "" }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Bank Status</label>
                                <select name="bankStatus" class="form-control" disabled>
                                    <option value="">Undefined</option>
                                    <option value="0"{{ $bank->status == 0 ? " selected" : "" }}>Not Update</option>
                                    <option value="1"{{ $bank->status == 1 ? " selected" : "" }}>Approved</option>
                                    <option value="2"{{ $bank->status == 2 ? " selected" : "" }}>Pending ...</option>
                                    <option value="3"{{ $bank->status == 3 ? " selected" : "" }}>Denied</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Level</label>
                                <input name="level-disabled" type="text" class="form-control" value="{{ $data->level }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Experience</label>
                                <input name="exp-disabled" type="text" class="form-control" value="{{ $data->exp }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Building Token</label>
                                <input name="buildingToken-disabled" type="text" class="form-control" value="{{ $data->buildingToken }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Coin</label>
                                <input name="coin-disabled" type="text" class="form-control" value="{{ $data->coin }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Factory Level</label>
                                <input name="factoryLevel-disabled" type="text" class="form-control" value="{{ $data->factoryLevel }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Dock Level</label>
                                <input name="dockLevel-disabled" type="text" class="form-control" value="{{ $data->dockLevel }}" disabled>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Academy Level</label>
                                <input name="academyLevel-disabled" type="text" class="form-control" value="{{ $data->academyLevel or "" }}" disabled>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xs-2">
                    <div class="form-group">
                        <label>Avatar</label>
                    </div>
                    <img src="{{ $data->picture }}" alt="" width="auto" height="auto" style="max-width: 100%; max-height: 100%">
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <div class="row">
        <div class="col-xs-5">
            <h3>Average Game Time</h3>
            <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="statistic-1-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Advertise Title</th>
                            <th>Average Time <small style="color: darkred">(seconds)</small></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(sizeof($gameTime))
                            @foreach($gameTime as $game)
                                <tr>
                                    <td>{{ $game->name }}</td>
                                    <td>{{ round($game->averageTimePlay, 2) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td>There is no data available</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-xs-7">
            <h3>Average Prize</h3>
            <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="statistic-2-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Prize</th>
                            <th>Quantity</th>
                            <th>Value</th>
                            <th>Average</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $userPrize["exp"] = $userPrize["gold"] = $userPrize["token"] = 0;
                        $userPrize["totalExp"] = $userPrize["totalGold"] = $userPrize["totalToken"] = 0;
                        if(sizeof($prizeData)){
                            foreach($prizeData as $prize){
                                if($prize->firstRewardType != "troll"){
                                    switch($prize->firstRewardType){
                                        case "exp":
                                            $userPrize["exp"] += $prize->firstRewardValue;
                                            $userPrize["totalExp"]++;
                                            break;
                                        case "gold":
                                            $userPrize["gold"] += $prize->firstRewardValue;
                                            $userPrize["totalGold"]++;
                                            break;
                                        case "token":
                                            $userPrize["token"] += $prize->firstRewardValue;
                                            $userPrize["totalToken"]++;
                                            break;
                                    }
                                }
                                if($prize->secondRewardType != "troll"){
                                    switch($prize->secondRewardType){
                                        case "exp":
                                            $userPrize["exp"] += $prize->secondRewardValue;
                                            $userPrize["totalExp"]++;
                                            break;
                                        case "gold":
                                            $userPrize["gold"] += $prize->secondRewardValue;
                                            $userPrize["totalGold"]++;
                                            break;
                                        case "token":
                                            $userPrize["token"] += $prize->secondRewardValue;
                                            $userPrize["totalToken"]++;
                                            break;
                                    }
                                }if($prize->thirdRewardType != "troll"){
                                    switch($prize->thirdRewardType){
                                        case "exp":
                                            $userPrize["exp"] += $prize->thirdRewardValue;
                                            $userPrize["totalExp"]++;
                                            break;
                                        case "gold":
                                            $userPrize["gold"] += $prize->thirdRewardValue;
                                            $userPrize["totalGold"]++;
                                            break;
                                        case "token":
                                            $userPrize["token"] += $prize->thirdRewardValue;
                                            $userPrize["totalToken"]++;
                                            break;
                                    }
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td>Experience</td>
                            <td>{{ $userPrize["totalExp"] }}</td>
                            <td>{{ $userPrize["exp"] }}</td>
                            <td>{{ $userPrize["totalExp"] ? round($userPrize["exp"] / $userPrize["totalExp"], 2) : 0 }}</td>
                        </tr>
                        <tr>
                            <td>Coin</td>
                            <td>{{ $userPrize["totalGold"] }}</td>
                            <td>{{ $userPrize["gold"] }}</td>
                            <td>{{ $userPrize["totalGold"] ? round($userPrize["gold"] / $userPrize["totalGold"], 2) : 0 }}</td>
                        </tr>
                        <tr>
                            <td>Building Token</td>
                            <td>{{ $userPrize["totalToken"] }}</td>
                            <td>{{ $userPrize["token"] }}</td>
                            <td>{{ $userPrize["totalToken"] ? round($userPrize["token"] / $userPrize["totalToken"], 2) : 0 }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
</section>