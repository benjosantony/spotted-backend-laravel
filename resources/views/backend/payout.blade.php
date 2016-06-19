<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
        <button class="btn btn-sm btn-success pull-right" onclick="refreshPayout(this);">Refresh</button>
    </h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="box-body col-xs-12">
                        <div class="col-xs-2">
                            <select id="payout-filter-string-column" class="form-control">
                                <option value="username">User Full Name</option>
                                <option value="bankName">Bank Name</option>
                                <option value="bankAccount">Bank Account</option>
                                <option value="phone">Phone</option>
                            </select>
                        </div>
                        <div class="col-xs-1">
                            <select id="payout-filter-string-operation" class="form-control">
                                <option value="is">is</option>
                                <option value="contain">contain</option>
                            </select>
                        </div>
                        <div class="col-xs-3">
                            <input id="payout-filter-string-value" class="form-control">
                        </div>
                    </div>
                    <div class="box-body col-xs-12">
                        <div class="col-xs-2">
                            <!--select id="filter-date-column-1" class="form-control">
                                <option value="date">Joined Date</option>
                            </select-->
                            <input type="text" value="Requested Date" class="form-control" disabled>
                        </div>
                        <div class="col-xs-1">
                            <!--select id="filter-date-operation-1" class="form-control">
                                <option value="gtae">>=</option>
                            </select-->
                            <input type="text" value=">=" class="form-control" disabled>
                        </div>
                        <div class="col-xs-3">
                            <input id="payout-filter-date-value-1" class="form-control">
                        </div>
                        <div class="col-xs-2">
                            <!--select id="filter-date-column-2" class="form-control">
                                <option value="date">Joined Date</option>
                            </select-->
                            <input type="text" value="Requested Date" class="form-control" disabled>
                        </div>
                        <div class="col-xs-1">
                            <!--select id="filter-date-operation-2" class="form-control">
                                <option value="ltae"><=</option>
                            </select-->
                            <input type="text" value="<=" class="form-control" disabled>
                        </div>
                        <div class="col-xs-3">
                            <input id="payout-filter-date-value-2" class="form-control">
                        </div>
                    </div>
                    <div class="box-body col-xs-12">
                        <div class="col-xs-2">
                            <!--select id="filter-numeric-column-1" class="form-control">
                                <option value="level">Level</option>
                            </select-->
                            <input type="text" value="Amount" class="form-control" disabled>
                        </div>
                        <div class="col-xs-1">
                            <!--select id="filter-numeric-operation-1" class="form-control">
                                <option value="gtae">>=</option>
                            </select-->
                            <input type="text" value=">=" class="form-control" disabled>
                        </div>
                        <div class="col-xs-3">
                            <input id="payout-filter-numeric-value-1" class="form-control">
                        </div>
                        <div class="col-xs-2">
                            <!--select id="filter-numeric-column-2" class="form-control">
                                <option value="level">Level</option>
                            </select-->
                            <input type="text" value="Amount" class="form-control" disabled>
                        </div>
                        <div class="col-xs-1">
                            <!--select id="filter-numeric-operation-2" class="form-control">
                                <option value="ltae"><=</option>
                            </select-->
                            <input type="text" value="<=" class="form-control" disabled>
                        </div>
                        <div class="col-xs-3">
                            <input id="payout-filter-numeric-value-2" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
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
                            <th>User Full Name</th>
                            <th>Bank Name</th>
                            <th>Bank Account</th>
                            <th>Phone</th>
                            <th>Requested Date</th>
                            <th>Amount <small style="color: darkred">($)</small></th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allData as $data)
                            <tr>
                                <td>{{ $data->fullname }}</td>
                                <td>{{ $data->bankName }}</td>
                                <td>{{ $data->bankAccount }}</td>
                                <td>{{ $data->phone }}</td>
                                <td>{{ $data->dateRequest }}</td>
                                <td>{{ $data->amount/100 }}SGD</td>
                                @if($data->status == "WAIT")
                                    <td>
                                        <span id="status-{{ $data->id }}" style="color: purple">Wait</span>
                                        <input type="hidden" value="0">
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="showConfirmPaymentBox(this, '{{ $data->id }}', manualPayment);">Manual Payment</button>
                                        <button class="btn btn-sm btn-primary" onclick="showConfirmPaymentBox(this, '{{ $data->id }}', processPayment);">Process Payment</button>
                                        <button class="btn btn-sm btn-danger" onclick="showConfirmPaymentBox(this, '{{ $data->id }}', cancelPayment);">Cancel</button>
                                    </td>
                                @elseif($data->status == "SUCCESS")
                                    <td>
                                        <span id="status-{{ $data->id }}" style="color: darkgreen">Success</span>
                                        <input type="hidden" value="5">
                                    </td><td></td>
                                @elseif($data->status == "CANCEL")
                                    <td>
                                        <span id="status-{{ $data->id }}" style="color: darkred">Denied</span>
                                        <input type="hidden" value="2">
                                    </td><td></td>
                                @elseif($data->status == "PAYOUT")
                                    <td><span id="status-{{ $data->id }}" style="color: deepskyblue">Payout ...</span></td><td></td>
                                @elseif($data->status == "cancelled")
                                    <td>
                                        <span id="status-{{ $data->id }}" style="color: red">Cancelled(xfer.io)</span>
                                        <input type="hidden" value="3">
                                    </td><td></td>
                                @elseif($data->status == "unclaimed")
                                    <td>
                                        <span id="status-{{ $data->id }}" style="color: deepskyblue">Unclaimed</span>
                                        <input type="hidden" value="1">
                                    </td><td></td>
                                @elseif($data->status == "completed")
                                    <td>
                                        <span id="status-{{ $data->id }}" style="color: darkgreen">Completed</span>
                                        <input type="hidden" value="4">
                                    </td><td></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>User Full Name</th>
                            <th>Bank Name</th>
                            <th>Bank Account</th>
                            <th>Phone</th>
                            <th>Requested Date</th>
                            <th>Amount <small style="color: darkred">($)</small></th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                    </table>

                    <ul>
                        <li><span style="color: purple">Wait: </span>User request payout.</li>
                        <li><span style="color: deepskyblue">Unclaimed: </span>Payout has not been accepted by recipient.</li>
                        <li><span style="color: darkred">Denied: </span>Payout has been cancelled by admin.</li>
                        <li><span style="color: red">Cancelled(xfer.io): </span>Payout has been cancelled.</li>
                        <li><span style="color: darkgreen">Completed: </span>Payout has been completed.</li>
                        <li><span style="color: darkgreen">Success: </span>Manual payment success</li>
                    </ul>

                </div>
                <div class="box-footer text-center">
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</section>