<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/14/2015
 * Time: 4:09 PM
 */

namespace App\Http\Controllers\backend;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\SinglePlayRecord;
use App\Model\SSP;

class GameRecordController extends Controller
{
    public function gameRecord(){
        /*$allData = SinglePlayRecord::leftjoin("sp_user", "sp_user.id", "=", "sp_singerplayrecord.userId")
            ->leftjoin("sp_advertisement", "sp_advertisement.id", "=", "sp_singerplayrecord.imageId")
            ->select("sp_singerplayrecord.dateEnd", "sp_singerplayrecord.dateStart", "sp_singerplayrecord.win", "sp_singerplayrecord.timeActualPlay", "sp_singerplayrecord.compassRate",
                "sp_singerplayrecord.numberOfKey", "sp_singerplayrecord.gold", "sp_singerplayrecord.exp", "sp_singerplayrecord.buildingToken",
                "sp_user.fullname", "sp_advertisement.name")->get();*/
        //$lastGame = SinglePlayRecord::orderBy("id", "desc")->first();
        //return view('backend/game_record', ['allData' => $allData, 'time' => $averageTime, "lastGame" => $lastGame->id])->with(['page_title'=>$page_title]);

        $page_title = "Gameplay Record Management";
        $averageTime = SinglePlayRecord::where("win", "=", 1)->avg("timeActualPlay");
        return view('backend/game_record', ['time' => $averageTime])->with(['page_title'=>$page_title]);
    }

    public function getGameFromId($id){
        $games = SinglePlayRecord::leftjoin("sp_user", "sp_user.id", "=", "sp_singerplayrecord.userId")
            ->leftjoin("sp_advertisement", "sp_advertisement.id", "=", "sp_singerplayrecord.imageId")
            ->where("sp_singerplayrecord.id", ">", $id)
            ->select("sp_singerplayrecord.dateEnd", "sp_singerplayrecord.win", "sp_singerplayrecord.timeActualPlay", "sp_singerplayrecord.compassRate",
                "sp_singerplayrecord.numberOfKey", "sp_singerplayrecord.gold", "sp_singerplayrecord.exp", "sp_singerplayrecord.buildingToken",
                "sp_user.fullname", "sp_advertisement.name");
        $total = $games->count();
        if($total){
            $lastGame = SinglePlayRecord::orderBy("id", "desc")->first();
            echo json_encode(array("total" => $total, "data" => $games->get(), "lastId" => $lastGame->id));
        } else {
            echo json_encode(array("total" => 0));
        }
    }

    public function getNumberOfGameInTimeInterval($time){
        $fromTime = date("Y-m-d H:i:s", strtotime("-" . $time . " seconds"));
        $totalGame = SinglePlayRecord::where("dateStart", ">", $fromTime)->count();
        echo $totalGame;
    }

    public function gameRecordPagingAjax(){
        $table = 'sp_singerplayrecord';
        $primaryKey = 'id';
        $columns = array(
            array( 'db' => 'su.fullname', 'dt' => 0, 'field' => 'fullname' ),
            array( 'db' => 'sa.name', 'dt' => 1, 'field' => 'name' ),
            array( 'db' => 'spr.dateStart', 'dt' => 2, 'field' => 'dateStart' ),
            array( 'db' => 'spr.dateEnd', 'dt' => 3, 'field' => 'dateEnd' ),
            array(
                'db'        => 'spr.win',
                'dt'        => 4,
                'formatter' => function( $d, $row ) {
                    if ($d == 1) return '<span style="color: darkgreen">Success</span>';
                    if ($d == 0) return '<span style="color: lightgrey">Failed</span>';
                    return '';
                },
                'field' => 'win'
            ),
            array( 'db' => 'spr.timeActualPlay', 'dt' => 5, 'field' => 'timeActualPlay' ),
            array( 'db' => 'spr.compassRate', 'dt' => 6, 'field' => 'compassRate' ),
            array( 'db' => 'spr.numberOfKey', 'dt' => 7, 'field' => 'numberOfKey' ),
            array(
                'db'        => "concat(spr.gold, ' coins, ', spr.exp, ' experiences, ', spr.buildingToken, ' tokens')",
                'dt'        => 8,
                'formatter' => function( $d, $row ) {
                    if ($row['win'] == 1) return $d;
                    if ($row['win'] == 0) return 'Nothing';
                    return '';
                },
                'field' => 'reward',
                'as' => 'reward'
            )
        );
        $joinQuery = "FROM `{$table}` AS `spr` LEFT JOIN `sp_user` AS `su` ON (`spr`.`userId` = `su`.`id`) LEFT JOIN `sp_advertisement` AS `sa` ON (`spr`.`imageId` = `sa`.`id`)";
        $sql_details = array(
            'user' => env('DB_USERNAME', 'root'),
            'pass' => env('DB_PASSWORD', ''),
            'db'   => env('DB_DATABASE', 'spottedpuzzle'),
            'host' => env('DB_HOST', 'localhost')
        );
        echo json_encode(
            SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery )
        );
        exit;
    }
}