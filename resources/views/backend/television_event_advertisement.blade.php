<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
        <button class="btn btn-primary pull-right btn-sm" onclick="goToDetailTelevisionEventAdvertisement(0);">Add New Event Advertisement</button>
    </h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="main-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Stage 1</th>
                            <th>Stage 2</th>
                            <th>Stage 3</th>
                            <th>Stage 4</th>
                            <th>Created Date</th>
                            <th>Started Date</th>
                            <th>Ended Date</th>
                            <th>Status</th>
                            <th>Detail</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allData as $data)
                            <tr>
                                <td>{{ $data->name }}</td>
                                <td>
                                    @if(!empty($data->imageUrl01))
                                        <a href="{{ $data->imageUrl01 }}" target="_blank"><img src="{{ $data->imageUrl01 }}" alt="" width="40px" height="40px"></a>
                                        @if($data->difficult01 == 0)
                                            <span style="color: green;">Easy</span>
                                        @elseif($data->difficult01 == 1)
                                            <span style="color: blue;">Normal</span>
                                        @elseif($data->difficult01 == 2)
                                            <span style="color: red;">Hard</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($data->imageUrl02))
                                        <a href="{{ $data->imageUrl02 }}" target="_blank"><img src="{{ $data->imageUrl02 }}" alt="" width="40px" height="40px"></a>
                                        @if($data->difficult02 == 0)
                                            <span style="color: green;">Easy</span>
                                        @elseif($data->difficult02 == 1)
                                            <span style="color: blue;">Normal</span>
                                        @elseif($data->difficult02 == 2)
                                            <span style="color: red;">Hard</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($data->imageUrl03))
                                        <a href="{{ $data->imageUrl03 }}" target="_blank"><img src="{{ $data->imageUrl03 }}" alt="" width="40px" height="40px"></a>
                                        @if($data->difficult03 == 0)
                                            <span style="color: green;">Easy</span>
                                        @elseif($data->difficult03 == 1)
                                            <span style="color: blue;">Normal</span>
                                        @elseif($data->difficult03 == 2)
                                            <span style="color: red;">Hard</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($data->imageUrl04))
                                        <a href="{{ $data->imageUrl04 }}" target="_blank"><img src="{{ $data->imageUrl04 }}" alt="" width="40px" height="40px"></a>
                                        @if($data->difficult04 == 0)
                                            <span style="color: green;">Easy</span>
                                        @elseif($data->difficult04 == 1)
                                            <span style="color: blue;">Normal</span>
                                        @elseif($data->difficult04 == 2)
                                            <span style="color: red;">Hard</span>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $data->dateCreate }}</td>
                                <td>{{ $data->dateStart }}</td>
                                <td>{{ $data->dateEnd }}</td>
                                <td><input name="{{ $data->id }}" type="checkbox" class="js-switch" onchange="updateEventAdvertiseStatus(this);" {{ $data-> status == 1 ? "checked" : "" }} /> </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="goToDetailTelevisionEventAdvertisement('{{ $data->id }}');">Detail</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Stage 1</th>
                            <th>Stage 2</th>
                            <th>Stage 3</th>
                            <th>Stage 4</th>
                            <th>Created Date</th>
                            <th>Started Date</th>
                            <th>Ended Date</th>
                            <th>Status</th>
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
</section>