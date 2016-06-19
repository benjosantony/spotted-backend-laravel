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
                        <td><input id="cell-new-title" type="text" value="" maxlength="90"></td>
                        <td><input id="cell-new-from" type="text" value=""></td>
                        <td><input id="cell-new-to" type="text" value=""></td>
                        <td>
                            <select id="cell-new-status">
                                <option value="0">Inactive</option>
                                <option value="1">Active</option>
                            </select>
                        </td>
                        <td><button class="btn btn-sm btn-primary" onclick="addNewQuestionRow('{{ $advertiseId }}')" id="btn-add">Add New Question</button></td>
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
                                    <button class="btn btn-sm btn-primary" onclick="goToDetailQuestion('{{ $specificdata->id }}', '{{ $advertiseId }}');">Detail</button>
                                </div>
                                <div id="operation-{{ $specificdata->id }}-save" style="position: absolute; display: none">
                                    <button class="btn btn-sm btn-primary" onclick="saveSelectedQuestionRow(this, '{{ $specificdata->id }}');">Save</button>
                                    <button class="btn btn-sm btn-default" onclick="cancelSelectedQuestionRow(this, '{{ $specificdata->id }}');">Cancel</button>
                                    <button class="btn btn-sm btn-primary" onclick="goToDetailQuestion('{{ $specificdata->id }}', '{{ $advertiseId }}');">Detail</button>
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