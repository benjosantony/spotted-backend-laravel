<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="padding-bottom: 10px">
        {{ $page_title or "Welcome to Spotted Puzzle Administration Page" }}
        <small>{{ $page_description or null }}</small>
        <button class="btn btn-primary pull-right" onclick="goToDetailAdvertisement(0);">Add New Advertisement</button>
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
                            <th>Image</th>
                            <th>Upload Date</th>
                            <th>Time Play <small style="color: darkred">(seconds)</small></th>
                            <th>First Key <small style="color: darkred">(seconds)</small></th>
                            <th>Second Key <small style="color: darkred">(seconds)</small></th>
                            <th>Third Key <small style="color: darkred">(seconds)</small></th>
                            <th>Experience Rate <small style="color: darkred">(%)</small></th>
                            <th>Cash Rate <small style="color: darkred">(%)</small></th>
                            <th>Token Rate <small style="color: darkred">(%)</small></th>
                            <th>Trash Rate <small style="color: darkred">(%)</small></th>
                            <th>Deal Type</th>
                            <th>Deal URL</th>
                            <th>Deal Expiration</th>
                            <th>Status</th>
                            <th>Detail</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allData as $data)
                            <tr>
                                <td>{{ $data->name }}</td>
                                <td><a href="{{ $data->imageUrl }}" target="_blank"><img src="{{ $data->imageUrl }}" alt="" width="40px" height="40px"></a></td>
                                <td>{{ $data->date }}</td>
                                <td>{{ $data->timeToPlay}}</td>
                                <td>{{ $data->firstKey }}</td>
                                <td>{{ $data->secondKey }}</td>
                                <td>{{ $data->thirdKey }}</td>
                                <td>{{ $data->expRate }}</td>
                                <td>{{ $data->cashRate }}</td>
                                <td>{{ $data->tokenRate }}</td>
                                <td>{{ $data->trashRate }}</td>
                                <td>
                                    @if(intval($data->dealType) == 1) {{ "Video" }}
                                        @elseif(intval($data->dealType) == 2) {{ "Website" }}
                                        @elseif(intval($data->dealType) == 3) {{ "Only One Deal/Offer " }}
                                        @elseif(intval($data->dealType) == 4) {{ "Unlimited Deal/Offer" }}
                                        @else {{ "Undefined" }}
                                    @endif
                                </td>
                                <td>{{ strlen($data->dealValue) > 20 ? substr($data->dealValue, 0, 20) . "..." : $data->dealValue }}</td>
                                <td>{{ $data->dealExpiration }}</td>
                                <td><input name="{{ $data->id }}" type="checkbox" class="js-switch" onchange="updateAdvertiseStatus(this);" {{ $data-> status == 1 ? "checked" : "" }} /> </td>
                                <td>
                                    <button class="btn btn-primary" onclick="goToDetailAdvertisement('{{ $data->id }}');">Detail</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Upload Date</th>
                            <th>Time Play <small style="color: darkred">(seconds)</small></th>
                            <th>First Key <small style="color: darkred">(seconds)</small></th>
                            <th>Second Key <small style="color: darkred">(seconds)</small></th>
                            <th>Third Key <small style="color: darkred">(seconds)</small></th>
                            <th>Experience Rate <small style="color: darkred">(%)</small></th>
                            <th>Cash Rate <small style="color: darkred">(%)</small></th>
                            <th>Token Rate <small style="color: darkred">(%)</small></th>
                            <th>Trash Rate <small style="color: darkred">(%)</small></th>
                            <th>Deal Type</th>
                            <th>Deal URL</th>
                            <th>Deal Expiration</th>
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