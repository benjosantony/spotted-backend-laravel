<h3>
    {{ $sub_page_title or "Welcome to Spotted Puzzle Administration Page" }}
    <small>{{ $sub_page_description or null }}</small>
    <button class="btn btn-default btn-sm pull-right" onclick="goBackToDetailAdvertisement('{{ $advertiseId }}');">Back to Previous Page</button>
</h3>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <!-- /.box-header -->
            <div class="box-body">
                <table id="sub-table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Answer</th>
                        <th>Correction</th>
                        <th>Add New</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input id="cell-new-title" type="text" value=""></td>
                            <td>
                                <select id="cell-new-correct">
                                    <option value="0">False</option>
                                    <option value="1">True</option>
                                </select>
                            </td>
                            <td><button class="btn btn-sm btn-primary" onclick="addNewQuestionAnswerRow('{{ $questionId }}')" id="btn-add">Add New Answer</button></td>
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
                        <th>Answer</th>
                        <th>Correction</th>
                        <th>Edit</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($allData as $data)
                        <tr>
                            <td><input id="cell-{{ $data->id }}-title" type="text" value="{{ $data->title }}" disabled></td>
                            <td>
                                <select id="cell-{{ $data->id }}-correct" disabled>
                                    <option value="0"{{ $data->correct == 0 ? " selected" : "" }}>False</option>
                                    <option value="1"{{ $data->correct == 1 ? " selected" : "" }}>True</option>
                                </select>
                            </td>
                            <td>
                                <div id="operation-{{ $data->id }}-edit" style="position: absolute">
                                    <button class="btn btn-sm btn-info" onclick="editSelectedQuestionAnswerRow(this, '{{ $data->id }}');">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="showConfirmBox(this, '{{ $data->id }}', deleteSelectedQuestionAnswerRow);">Delete</button>
                                </div>
                                <div id="operation-{{ $data->id }}-save" style="position: absolute; display: none">
                                    <button class="btn btn-sm btn-primary" onclick="saveSelectedQuestionAnswerRow(this, '{{ $data->id }}');">Save</button>
                                    <button class="btn btn-sm btn-default" onclick="cancelSelectedQuestionAnswerRow(this, '{{ $data->id }}');">Cancel</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Answer</th>
                        <th>Correction</th>
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