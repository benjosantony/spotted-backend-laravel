<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/14/2015
 * Time: 4:09 PM
 */

namespace App\Http\Controllers\backend;
use App\Model\TelevisionEventAdvertisement;
use Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class ExportController extends Controller
{
    public function index(){
        $advertise = TelevisionEventAdvertisement::all();
        return view('backend/export')->with(["advertise" => $advertise]);
    }

    /*public function gameRecord(){
        ini_set('max_execution_time', 0);
        Excel::create('abc', function($excel) {
            $sheetNumber = 1;
            Payout::chunk(20, function ($users) use ($excel, &$sheetNumber) {
                $excel->sheet('' . $sheetNumber, function($sheet) use ($users) {
                    $sheet->rows(json_decode(json_encode($users), true), null, 'A1', false);
                });
                //$sheetNumber++;
            });
        })->download('xls');
    }*/

    public function gameRecord()
    {
        ini_set('max_execution_time', 0);
        $search = trim(Input::get('search'));
        $dateRange = !empty(Input::get('date')) ? Input::get('date') : env('GAME_START_DATE') . " ~ " . date("Y-m-d");
        $dateArray = explode(" ~ ", $dateRange);
        $from = !empty(strtotime($dateArray[0])) ? $dateArray[0] : env('GAME_START_DATE');
        $to = !empty(strtotime($dateArray[1])) ? $dateArray[1] : date("Y-m-d");
        $pageSize = 1000;

        $filename = env('EXPORT_DIR') . "game-records.csv";
        if (realpath($filename) === FALSE) {
            touch($filename);
            chmod($filename, 0755);
        }
        $filename = realpath($filename);
        $handle = fopen($filename, "w");
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($handle, array("Facebook ID", "Full Name", "Facebook Email", "Gender", "Registered Date",
            "Advertisement Name", "Started Date", "Ended Date", "Result", "Playing Time (seconds)", "Compass Rate (x)", "Keys Obtained", "Coins", "Experiences", "Tokens"), ",");

        if (empty($search)) {
            $total = DB::select(DB::raw('SELECT COUNT(*) AS total FROM sp_singerplayrecord WHERE dateStart >= :fromDate AND dateStart <= :toDate AND userId <> 0'),
                array('fromDate' => $from, 'toDate' => $to . ' 23:59:59'));
            $totalPage = ceil($total[0]->total / $pageSize);

            for ($i = 0; $i < $totalPage; $i++) {
                $data = DB::select(DB::raw('SELECT a.name, u.fbId, u.fullname, u.fbEmail, u.gender, u.dateCreate,
                    spr.dateStart, spr.dateEnd, spr.win, spr.timeActualPlay, spr.compassRate, spr.numberOfKey, spr.gold, spr.exp, spr.buildingToken
                FROM (SELECT id, userId, imageId, dateStart, dateEnd, win, timeActualPlay, compassRate, numberOfKey, gold, `exp`, buildingToken
                    FROM sp_singerplayrecord WHERE dateStart >= :fromDate AND dateStart <= :toDate AND userId <> 0 LIMIT :skip, :take) AS spr
                LEFT JOIN (SELECT id, fbId, fullname, fbEmail, gender, dateCreate FROM sp_user) AS u ON spr.userId = u.id
                LEFT JOIN (SELECT id, `name` FROM sp_advertisement) AS a ON spr.imageId = a.id'),
                    array('fromDate' => $from, 'toDate' => $to . ' 23:59:59', 'skip' => $i * $pageSize, 'take' => $pageSize));

                foreach ($data AS $row) {
                    $finalRow = array($row->fbId, mb_convert_encoding($row->fullname, "UTF-8", "UTF-8"), $row->fbEmail, $row->gender == 0 ? 'Female' : ($row->gender == 1 ? 'Male' : 'Undefined'), $row->dateCreate,
                        mb_convert_encoding($row->name, "UTF-8", "UTF-8"), $row->dateStart, $row->dateEnd, $row->win == 1 ? 'Success' : 'Fail', $row->timeActualPlay,
                        $row->compassRate, $row->numberOfKey, $row->gold, $row->exp, $row->buildingToken);
                    fputcsv($handle, $finalRow, ",");
                }
            }
        } else {
            $total = DB::select(DB::raw("SELECT COUNT(*) AS total
                FROM (SELECT userId FROM sp_singerplayrecord WHERE dateStart >= :fromDate AND dateStart <= :toDate) AS spr
                LEFT JOIN (SELECT id, fullname FROM sp_user) AS u ON spr.userId = u.id
                WHERE u.fullname LIKE '%$search%'"),
                array('fromDate' => $from, 'toDate' => $to . ' 23:59:59'));
            $totalPage = ceil($total[0]->total / $pageSize);

            for ($i = 0; $i < $totalPage; $i++) {
                $data = DB::select(DB::raw("SELECT a.name, spru.fbId, spru.fullname, spru.fbEmail, spru.gender, spru.dateCreate,
                    spru.dateStart, spru.dateEnd, spru.win, spru.timeActualPlay, spru.compassRate, spru.numberOfKey, spru.gold, spru.exp, spru.buildingToken
                FROM (SELECT u.fbId, u.fullname, u.fbEmail, u.gender, u.dateCreate,
                    spr.dateStart, spr.dateEnd, spr.win, spr.timeActualPlay, spr.compassRate, spr.numberOfKey, spr.gold, spr.exp, spr.buildingToken, spr.imageId
                    FROM (SELECT id, userId, imageId, dateStart, dateEnd, win, timeActualPlay, compassRate, numberOfKey, gold, `exp`, buildingToken
                    FROM sp_singerplayrecord WHERE dateStart >= :fromDate AND dateStart <= :toDate) AS spr
                    LEFT JOIN (SELECT id, fbId, fullname, fbEmail, gender, dateCreate FROM sp_user) AS u ON spr.userId = u.id
                    WHERE u.fullname LIKE '%$search%' LIMIT :skip, :take) AS spru
                LEFT JOIN (SELECT id, `name` FROM sp_advertisement) AS a ON spru.imageId = a.id"),
                    array('fromDate' => $from, 'toDate' => $to . ' 23:59:59', 'skip' => $i * $pageSize, 'take' => $pageSize));

                foreach ($data AS $row) {
                    $finalRow = array($row->fbId, mb_convert_encoding($row->fullname, "UTF-8", "UTF-8"), $row->fbEmail, $row->gender == 0 ? 'Female' : ($row->gender == 1 ? 'Male' : 'Undefined'), $row->dateCreate,
                        mb_convert_encoding($row->name, "UTF-8", "UTF-8"), $row->dateStart, $row->dateEnd, $row->win == 1 ? 'Success' : 'Fail', $row->timeActualPlay,
                        $row->compassRate, $row->numberOfKey, $row->gold, $row->exp, $row->buildingToken);
                    fputcsv($handle, $finalRow, ",");
                }
            }
        }

        fclose($handle);
        $headers = ["Content-Type: text/csv; charset=UTF-8",
            "Content-Disposition: attachment; filename=Game_records_from_" . $from . "_to_" . $to . ".csv",
            "Content-Transfer-Encoding: binary", "Expires: 0", "Cache-Control: must-revalidate, post-check=0, pre-check=0",
            "Pragma: public", "Content-Length: " . filesize($filename)];
        return response()->download($filename, "Game_records_from_" . $from . "_to_" . $to . ".csv", $headers);
    }

    public function getEventTime() {
        $id = Input::get('id');
        if (!empty($id)) {
            $advertise = TelevisionEventAdvertisement::find($id);
            if (!empty($advertise)) {
                return json_encode(array("status" => 1, "date" => date("Y-m-d", strtotime($advertise->dateStart)) . " ~ " . date("Y-m-d", strtotime($advertise->dateEnd))));
            }
        }
        return json_encode(array("status" => 0, "message" => "Error"));
    }

    public function eventGameRecord() {
        ini_set('max_execution_time', 0);
        $filename = env('EXPORT_DIR') . "event-game-records.csv";
        if (realpath($filename) === FALSE) {
            touch($filename);
            chmod($filename, 0755);
        }
        $filename = realpath($filename);
        $handle = fopen($filename, "w");
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($handle, array("Facebook ID", "Full Name", "Facebook Email", "Gender", "Registered Date",
            "Started Date", "Ended Date", "Stage", "Playing Time (seconds)"), ",");

        $id = Input::get('id');
        $from = $to = $advertiseName = '?';

        if (!empty($id)) {
            $advertise = TelevisionEventAdvertisement::find($id);
            if (!empty($advertise)) {
                $advertiseName = $advertise->name;
                $dateRange = !empty(Input::get('date')) ? Input::get('date') : date("Y-m-d", strtotime($advertise->dateStart)) . " ~ " . date("Y-m-d", strtotime($advertise->dateEnd));
                $dateArray = explode(" ~ ", $dateRange);
                $from = !empty(strtotime($dateArray[0])) ? $dateArray[0] : date("Y-m-d", strtotime($advertise->dateStart));
                $to = !empty(strtotime($dateArray[1])) ? $dateArray[1] : date("Y-m-d", strtotime($advertise->dateEnd));
                $search = trim(Input::get('search'));
                $pageSize = 1000;

                if (empty($search)) {
                    $total = DB::select(DB::raw('SELECT COUNT(*) AS total FROM sp_event_singleplay WHERE advId = :advId AND dateStart >= :fromDate AND dateStart <= :toDate'),
                        array('advId' => $advertise->id, 'fromDate' => $from, 'toDate' => $to . ' 23:59:59'));
                    $totalPage = ceil($total[0]->total / $pageSize);

                    for ($i = 0; $i < $totalPage; $i++) {
                        $data = DB::select(DB::raw('SELECT u.fbId, u.fullname, u.fbEmail, u.gender, u.dateCreate,
                            ses.dateStart, ses.dateEnd, ses.stage, ses.timePlayed
                            FROM (SELECT id, userId, dateStart, dateEnd, stage, timePlayed
                                FROM sp_event_singleplay WHERE advId = :advId AND dateStart >= :fromDate AND dateStart <= :toDate ORDER BY userId, id LIMIT :skip, :take) AS ses
                            LEFT JOIN (SELECT id, fbId, fullname, fbEmail, gender, dateCreate FROM sp_user) AS u ON ses.userId = u.id'),
                            array('advId' => $advertise->id, 'fromDate' => $from, 'toDate' => $to . ' 23:59:59', 'skip' => $i * $pageSize, 'take' => $pageSize));

                        foreach ($data AS $row) {
                            $finalRow = array($row->fbId, mb_convert_encoding($row->fullname, "UTF-8", "UTF-8"), $row->fbEmail, $row->gender == 0 ? 'Female' : ($row->gender == 1 ? 'Male' : 'Undefined'), $row->dateCreate,
                                $row->dateStart, $row->dateEnd, $row->stage, $row->timePlayed);
                            fputcsv($handle, $finalRow, ",");
                        }
                    }
                } else {
                    $total = DB::select(DB::raw("SELECT COUNT(*) AS total
                        FROM (SELECT userId FROM sp_event_singleplay WHERE advId = :advId AND dateStart >= :fromDate AND dateStart <= :toDate) AS ses
                        LEFT JOIN (SELECT id, fullname FROM sp_user) AS u ON ses.userId = u.id
                        WHERE u.fullname LIKE '%$search%'"),
                        array('advId' => $advertise->id, 'fromDate' => $from, 'toDate' => $to . ' 23:59:59'));
                    $totalPage = ceil($total[0]->total / $pageSize);

                    for ($i = 0; $i < $totalPage; $i++) {
                        $data = DB::select(DB::raw("SELECT u.fbId, u.fullname, u.fbEmail, u.gender, u.dateCreate,
                            ses.dateStart, ses.dateEnd, ses.stage, ses.timePlayed
                            FROM (SELECT id, userId, dateStart, dateEnd, stage, timePlayed
                                FROM sp_event_singleplay WHERE advId = :advId AND dateStart >= :fromDate AND dateStart <= :toDate) AS ses
                                LEFT JOIN (SELECT id, fbId, fullname, fbEmail, gender, dateCreate FROM sp_user) AS u ON ses.userId = u.id
                                WHERE u.fullname LIKE '%$search%' LIMIT :skip, :take"),
                            array('advId' => $advertise->id, 'fromDate' => $from, 'toDate' => $to . ' 23:59:59', 'skip' => $i * $pageSize, 'take' => $pageSize));

                        foreach ($data AS $row) {
                            $finalRow = array($row->fbId, mb_convert_encoding($row->fullname, "UTF-8", "UTF-8"), $row->fbEmail, $row->gender == 0 ? 'Female' : ($row->gender == 1 ? 'Male' : 'Undefined'), $row->dateCreate,
                                $row->dateStart, $row->dateEnd, $row->stage, $row->timePlayed);
                            fputcsv($handle, $finalRow, ",");
                        }
                    }
                }
            }
        }

        fclose($handle);
        $headers = ["Content-Type: text/csv; charset=UTF-8",
            "Content-Disposition: attachment; filename=Game_records_from_" . $from . "_to_" . $to . ".csv",
            "Content-Transfer-Encoding: binary", "Expires: 0", "Cache-Control: must-revalidate, post-check=0, pre-check=0",
            "Pragma: public", "Content-Length: " . filesize($filename)];
        return response()->download($filename, "Event_" . $advertiseName . "_game_records_from_" . $from . "_to_" . $to . ".csv", $headers);
    }
}