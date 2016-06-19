<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
        <div class="pull-right" style="padding-left: 5px">
            <button class="btn btn-default btn-sm" onclick="getDataByMenuTitle(null, 'eventAdvertisement');">Back to Event Advertise Management</button>
        </div>
        @if(!isset($data->id))
            <div id="advertise-operation-new" class="pull-right">
                <button class="btn btn-primary btn-sm" onclick="insertCurrentTelevisionEventAdvertisement();">Save</button>
            </div>
        @else
            <div id="advertise-operation-edit" class="pull-right">
                <button class="btn btn-info btn-sm" onclick="editCurrentTelevisionEventAdvertisement();">Edit</button>
            </div>
            <div id="advertise-operation-save" class="pull-right" style="display: none">
                <button class="btn btn-primary btn-sm" onclick="saveCurrentTelevisionEventAdvertisement();">Save</button>
            </div>
        @endif
    </h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary box-body">
                <!-- /.box-header -->
                <div class="col-xs-12">
                    <form id="detail-form">
                        <input type="hidden" name="id" value="{{ $data->id or 0 }}">
                        <input type="hidden" name="logo" id="send-logo">
                        <input type="hidden" name="image-1" id="send-image-1">
                        <input type="hidden" name="image-2" id="send-image-2">
                        <input type="hidden" name="image-3" id="send-image-3">
                        <input type="hidden" name="image-4" id="send-image-4">
                        <div class="col-xs-6" style="padding: 0px">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input name="name" type="text" class="form-control" value="{{ $data->name or "" }}"{{ isset($data->id) ? " disabled" : "" }}>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Date Start</label>
                                    <input name="start" type="text" class="form-control" value="{{ $data->dateStart }}"{{ isset($data->id) ? " disabled" : "" }}>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Date End</label>
                                    <input name="end" type="text" class="form-control" value="{{ $data->dateEnd }}"{{ isset($data->id) ? " disabled" : "" }}>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control"{{ isset($data->id) ? " disabled" : "" }}>
                                        <option value="0"{{ $data->status == 0 ? " selected" : "" }}>Inactive</option>
                                        <option value="1"{{ $data->status == 1 ? " selected" : "" }}>Active</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Share Kind</label>
                                    <select name="kind" class="form-control"{{ isset($data->id) ? " disabled" : "" }}>
                                        <option value="0"{{ $data->shareKind == 0 ? " selected" : "" }}>Share to Replay</option>
                                        <option value="1"{{ $data->shareKind == 1 ? " selected" : "" }}>Website</option>
                                        <option value="2"{{ $data->shareKind == 2 ? " selected" : "" }}>View Video</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label title="When 'Share kind' is website or video">Share Content</label> <small style="color: darkred">(Text/URL)</small>
                                    <input name="content" type="text" class="form-control" value="{{ $data->shareContent }}"{{ isset($data->id) ? " disabled" : "" }}>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6" style="padding: 0px">
                            <div class="col-xs-10">
                                <div class="form-group">
                                    <label>Icon</label>
                                    <input id="browse-logo" type="file"{{ isset($data->id) ? " disabled" : "" }}>
                                    <br/><span class="alert alert-success" style="padding: 5px">Please use image with size <strong style="color: darkblue">300x300px</strong> and less than <strong style="color: darkblue">1MB</strong></span>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <img id="show-logo" src="{{ $data->logo or "" }}" alt="" width="auto" height="auto" style="width: 100px; height: 100px">
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>Term & Condition</label>
                                    <textarea name="condition" class="form-control"{{ isset($data->id) ? " disabled" : "" }}>{{ $data->termAndCondition }}</textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-12">
                        <br><span class="alert alert-success" style="padding: 5px">Please use image with size <strong style="color: darkblue">1050x1260px</strong> and less than <strong style="color: darkblue">2MB</strong></span><br><br>
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label>Stage 1</label>
                            <input id="browse-image-1" type="file"{{ isset($data->id) ? " disabled" : "" }}><br>
                            <label>Difficulty</label>
                            <select name="difficult-1" class="form-control" disabled>
                                <option value="0"{{ $data->difficult01 == 0 ? " selected" : "" }}>Easy</option>
                                <option value="1"{{ $data->difficult01 == 1 ? " selected" : "" }}>Normal</option>
                                <option value="2"{{ $data->difficult01 == 2 ? " selected" : "" }}>Hard</option>
                            </select><br>
                            <button id="remove-image-1" class="btn btn-primary" onclick="removeEventAdvertiseImage(1);" disabled>Remove</button>
                        </div>
                        <img id="show-image-1" src="{{ $data->imageUrl01 or "" }}" alt="" width="auto" height="auto" style="max-width: 100%; max-height: 100%">
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label>Stage 2</label>
                            <input id="browse-image-2" type="file"{{ isset($data->id) ? " disabled" : "" }}><br>
                            <label>Difficulty</label>
                            <select name="difficult-2" class="form-control" disabled>
                                <option value="0"{{ $data->difficult02 == 0 ? " selected" : "" }}>Easy</option>
                                <option value="1"{{ $data->difficult02 == 1 ? " selected" : "" }}>Normal</option>
                                <option value="2"{{ $data->difficult02 == 2 ? " selected" : "" }}>Hard</option>
                            </select><br>
                            <button id="remove-image-2" class="btn btn-primary" onclick="removeEventAdvertiseImage(2);" disabled>Remove</button>
                        </div>
                        <img id="show-image-2" src="{{ $data->imageUrl02 or "" }}" alt="" width="auto" height="auto" style="max-width: 100%; max-height: 100%">
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label>Stage 3</label>
                            <input id="browse-image-3" type="file"{{ isset($data->id) ? " disabled" : "" }}><br>
                            <label>Difficulty</label>
                            <select name="difficult-3" class="form-control" disabled>
                                <option value="0"{{ $data->difficult03 == 0 ? " selected" : "" }}>Easy</option>
                                <option value="1"{{ $data->difficult03 == 1 ? " selected" : "" }}>Normal</option>
                                <option value="2"{{ $data->difficult03 == 2 ? " selected" : "" }}>Hard</option>
                            </select><br>
                            <button id="remove-image-3" class="btn btn-primary" onclick="removeEventAdvertiseImage(3);" disabled>Remove</button>
                        </div>
                        <img id="show-image-3" src="{{ $data->imageUrl03 or "" }}" alt="" width="auto" height="auto" style="max-width: 100%; max-height: 100%">
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label>Stage 4</label>
                            <input id="browse-image-4" type="file"{{ isset($data->id) ? " disabled" : "" }}><br>
                            <label>Difficulty</label>
                            <select name="difficult-4" class="form-control" disabled>
                                <option value="0"{{ $data->difficult04 == 0 ? " selected" : "" }}>Easy</option>
                                <option value="1"{{ $data->difficult04 == 1 ? " selected" : "" }}>Normal</option>
                                <option value="2"{{ $data->difficult04 == 2 ? " selected" : "" }}>Hard</option>
                            </select><br>
                            <button id="remove-image-4" class="btn btn-primary" onclick="removeEventAdvertiseImage(4);" disabled>Remove</button>
                        </div>
                        <img id="show-image-4" src="{{ $data->imageUrl04 or "" }}" alt="" width="auto" height="auto" style="max-width: 100%; max-height: 100%">
                    </div>
                <!-- /.box-body -->
                </div>
            </div>
            <!-- /.box -->
        </div>
    </div>
</section>