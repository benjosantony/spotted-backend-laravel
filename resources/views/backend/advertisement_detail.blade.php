<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
        <div class="pull-right" style="padding-left: 5px">
            <button class="btn btn-default" onclick="getDataByMenuTitle(null, 'advertisement');">Back to Advertise Management</button>
        </div>
        @if(!isset($data->id))
            <div id="advertise-operation-new" class="pull-right">
                <button class="btn btn-primary" onclick="insertCurrentAdvertisement();">Save</button>
            </div>
        @else
            <div id="advertise-operation-edit" class="pull-right">
                <button class="btn btn-info" onclick="editCurrentAdvertisement();">Edit</button>
            </div>
            <div id="advertise-operation-save" class="pull-right" style="display: none">
                <button class="btn btn-primary" onclick="saveCurrentAdvertisement();">Save</button>
            </div>
        @endif
    </h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary box-body">
                <!-- /.box-header -->
                <div class="col-xs-8">
                    <form id="detail-form">
                        <input type="hidden" name="id" value="{{ $data->id or 0 }}">
                        <input type="hidden" name="image" id="send-image">
                        <input type="hidden" name="thumbnail" id="thumb-image">
                        <div class="col-xs-8">
                            <div class="form-group">
                                <label>Name</label>
                                <input name="name" type="text" class="form-control" value="{{ $data->name or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Time Play <small style="color: darkred">(seconds)</small></label>
                                <input name="time" type="text" class="form-control" value="{{ $data->timeToPlay }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>First Key <small style="color: darkred">(seconds)</small></label>
                                <input name="firstKey" type="text" class="form-control" value="{{ $data->firstKey or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Second Key <small style="color: darkred">(seconds)</small></label>
                                <input name="secondKey" type="text" class="form-control" value="{{ $data->secondKey or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Third Key <small style="color: darkred">(seconds)</small></label>
                                <input name="thirdKey" type="text" class="form-control" value="{{ $data->thirdKey or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Experience Rate <small style="color: darkred">(%)</small></label>
                                <input name="expRate" type="text" class="form-control" value="{{ $data->expRate or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Minimum Experience</label>
                                <input name="expMin" type="text" class="form-control" value="{{ $data->expMin or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Maximum Experience</label>
                                <input name="expMax" type="text" class="form-control" value="{{ $data->expMax or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Cash Rate <small style="color: darkred">(%)</small></label>
                                <input name="cashRate" type="text" class="form-control" value="{{ $data->cashRate or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Minimum Cash</label>
                                <input name="cashMin" type="text" class="form-control" value="{{ $data->cashMin or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Maximum Cash</label>
                                <input name="cashMax" type="text" class="form-control" value="{{ $data->cashMax or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Token Rate <small style="color: darkred">(%)</small></label>
                                <input name="tokenRate" type="text" class="form-control" value="{{ $data->tokenRate or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Minimum Token</label>
                                <input name="tokenMin" type="text" class="form-control" value="{{ $data->cashMin or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Maximum Token</label>
                                <input name="tokenMax" type="text" class="form-control" value="{{ $data->tokenMax or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Trash Rate <small style="color: darkred">(%)</small></label>
                                <input name="trashRate" type="text" class="form-control" value="{{ $data->trashRate or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Deal Type</label>
                                <select name="dealType" class="form-control"{{ isset($data->id) ? " disabled" : "" }}>
                                    <option value="0"{{ $data->dealType == 0 ? " selected" : "" }}>Undefined</option>
                                    <option value="1"{{ $data->dealType == 1 ? " selected" : "" }}>Video</option>
                                    <option value="2"{{ $data->dealType == 2 ? " selected" : "" }}>Website</option>
                                    <option value="3"{{ $data->dealType == 3 ? " selected" : "" }}>Only One Deal/Offer</option>
                                    <option value="4"{{ $data->dealType == 4 ? " selected" : "" }}>Unlimited Deal/Offer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control"{{ isset($data->id) ? " disabled" : "" }}>
                                    <option value="0"{{ $data->status == 0 ? " selected" : "" }}>Inactive</option>
                                    <option value="1"{{ $data->status == 1 ? " selected" : "" }}>Active</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <div class="form-group">
                                <label>Deal Value <small style="color: darkred">(URL/Code)</small></label>
                                <input name="dealValue" type="text" class="form-control" value="{{ $data->dealValue or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Deal Expiration</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input placeholder="Leave blank to set no expiration" name="dealExpiration" type="text" class="form-control" value="{{ $data->dealExpiration or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Image</label>
                        <input id="browse-image" type="file"{{ isset($data->id) ? " disabled" : "" }}>
                        <br/><span class="alert alert-success" style="padding: 5px">Please use image with size 1050x1260 px and less than 2MB</span>
                    </div>
                    <img id="show-image" src="{{ $data->imageUrl or "" }}" alt="" width="auto" height="auto" style="max-width: 100%; max-height: 100%">
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    @if(isset($allData))
    <div style="position: relative">
        <div id="sub-content-loading-screen" class="overlay" style="display: none; background: rgba(255, 255, 255, 0.5) none repeat scroll 0 0; border-radius: 3px; z-index: 9999; height: 100%; position: absolute; width: 100%;">
            <i class="fa fa-refresh fa-spin" style="color: #000; font-size: 50px; left: 50%; margin-left: -25px; margin-top: -25px; position: absolute; top: 50%;"></i>
        </div>
        <div class="sub-content" style="position: relative">
            <h3>
                {{ $sub_page_title or "Welcome to Spotted Puzzle Administration Page" }}
                <small>{{ $sub_page_description or null }}</small>
            </h3>
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="sub-table" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Status</th>
                                    <th>Add New</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><input id="cell-new-title" type="text" value="" maxlength="90" class="form-control"></td>
                                    <td><input id="cell-new-from" type="text" value="" class="form-control"></td>
                                    <td><input id="cell-new-to" type="text" value="" class="form-control"></td>
                                    <td>
                                        <select id="cell-new-status" class="form-control">
                                            <option value="0">Inactive</option>
                                            <option value="1">Active</option>
                                        </select>
                                    </td>
                                    <td><button class="btn btn-primary" onclick="addNewQuestionRow('{{ $data->id }}')" id="btn-add">Add New Question</button></td>
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
                                    <th>Title</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Status</th>
                                    <th>Operation</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($allData as $specificdata)
                                    <tr>
                                        <td><input id="cell-{{ $specificdata->id }}-title" type="text" value="{{ $specificdata->title }}" disabled></td>
                                        <td><input id="cell-{{ $specificdata->id }}-from" type="text" value="{{ $specificdata->dateFrom }}" disabled></td>
                                        <td><input id="cell-{{ $specificdata->id }}-to" type="text" value="{{ $specificdata->dateTo }}" disabled></td>
                                        <td>
                                            <select id="cell-{{ $specificdata->id }}-status" disabled>
                                                <option value="0"{{ $specificdata->status == 0 ? " selected" : "" }}>Inactive</option>
                                                <option value="1"{{ $specificdata->status == 1 ? " selected" : "" }}>Active</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div id="operation-{{ $specificdata->id }}-edit" style="position: absolute">
                                                <button class="btn btn-sm btn-info" onclick="editSelectedQuestionRow(this, '{{ $specificdata->id }}');">Edit</button>
                                                <button class="btn btn-sm btn-danger" onclick="showConfirmBox(this, '{{ $specificdata->id }}', deleteSelectedQuestionRow);">Delete</button>
                                                <button class="btn btn-sm btn-primary" onclick="goToDetailQuestion('{{ $specificdata->id }}', '{{ $data->id }}');">Detail</button>
                                            </div>
                                            <div id="operation-{{ $specificdata->id }}-save" style="position: absolute; display: none">
                                                <button class="btn btn-sm btn-primary" onclick="saveSelectedQuestionRow(this, '{{ $specificdata->id }}');">Save</button>
                                                <button class="btn btn-sm btn-default" onclick="cancelSelectedQuestionRow(this, '{{ $specificdata->id }}');">Cancel</button>
                                                <button class="btn btn-sm btn-primary" onclick="goToDetailQuestion('{{ $specificdata->id }}', '{{ $data->id }}');">Detail</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Title</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Status</th>
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
        </div>
    </div>
    @endif
</section>