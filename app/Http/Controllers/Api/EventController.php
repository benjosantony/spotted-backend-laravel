<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 11/23/2015
 * Time: 11:19 AM
 */

namespace App\Http\Controllers\Api;

use App\Model\TelevisionEventAdvertisement;
use DB;
use Log;
use Illuminate\Http\Request;
class EventController extends APIController
{
    public function playGame(Request $request){
        $currentDate = date('Y-m-d H:i:s');
        Log::info('Play Event at ' . $currentDate . " userId: " . $request->attributes->get('userId'));
        $rs = DB::select('CALL generateEventSinglePlay(:userId, :date)', ['userId'=>$request->attributes->get('userId'), 'date'=>$currentDate]);
        if(empty($rs) || !empty($rs[0]->error)){
            Log::info('generateEventSinglePlay error: ' . $request->attributes->get('userId'));
            if($rs[0]->error === 1){ //these are no event
                return response()->json(['status' => 0, 'error' => ['code' => 4001, 'message' => env(4001)]]);
            }elseif($rs[0]->error === 2){//you can't play more for this event
                return response()->json(['status' => 0, 'error' => ['code' => 4002, 'message' => env(4002)]]);
            }
            //you don't have enough ship to play more, please share
            return response()->json(['status' => 0, 'error' => ['code' => 4003, 'message' => env(4003)]]);
        }
        return response()->json([
            'status'=>1,
            'data'=>[
                'matchId'=>$rs[0]->matchId,
                'gameplayInfo'=>[
                    'adImageUrl'=>$rs[0]->adImageUrl,
                    'adId'=>$rs[0]->adId,
                    'mapInfo'=>[
                        'row'=>$rs[0]->row,
                        'col'=>$rs[0]->col
                    ]
                ],
                'stageInfo'=>[
                    'indexStage'=>$rs[0]->indexStage,
                    'maxStage'=>$rs[0]->maxStage
                ]
            ]
        ]);
    }

    public function endGame(Request $request){
        $matchId = $request->input('matchId', null);
        $playHistory = $request->input('playHistory', null);
        $checksum = $request->input('checksum', null);
        Log::info("EndEventGame userId: " . $request->attributes->get('userId') . " MatchId: ".$matchId);
        if(empty($matchId) || empty($playHistory) || empty($checksum))
            return response()->json(['status'=>0,'error'=>['code'=>2,'message'=>env(2)]]);
        $jsonPlay = json_decode($playHistory);
        if($jsonPlay == false)
            return response()->json(['status'=>0,'error'=>['code'=>2,'message'=>env(2)]]);

        if($checksum != md5($playHistory.(10 + $matchId))){
            return response()->json([
                'status'=>0,
                'error'=>['code'=>2,'message'=>env(2)]
            ]);
        }

        $len = count($jsonPlay->steps);
        if($len > 0){
            $timePlay = $jsonPlay->steps[$len-1]->time; //in seconds
            //$tiles = $jsonPlay->steps[$len-1]->correctTile;
        }else{
            $timePlay = 999999999;
            //$tiles = 0;
        }
        $leaderBoard = DB::select('call finishEventSinglePlay(:userId, :matchId, :timeActualPlay, :currentDate, :history)',
            ['userId'=>$request->attributes->get('userId'), 'matchId'=>$matchId, 'timeActualPlay'=>$timePlay,
                'currentDate'=>date('Y-m-d H:i:s'), 'history'=>$playHistory]);
        //$leaderBoard = DB::select('CALL getLeaderBoard(:userId)', ['userId'=>$request->attributes->get('userId')]);
        if(empty($leaderBoard) || !empty($leaderBoard[0]->error)){
            if($leaderBoard[0]->error === 1){//these are no event
                return response()->json(['status' => 0, 'error' => ['code' => 4001, 'message' => env(4001)]]);
            }
            if($leaderBoard[0]->error === 2){//incorrect input data
                return response()->json(['status' => 0, 'error' => ['code' => 2, 'message' => env(2)]]);
            }
        }

        $status = DB::select('Call getEventStatus(:date)', ['date'=>date('Y-m-d H:i:s')]);
        $turnInfo = DB::select('Call getMoreTurnInfo(:userId, :currentDate)', ['userId'=>$request->attributes->get('userId'), 'currentDate'=>date('Y-m-d H:i:s')]);
        if(!empty($leaderBoard[0]->notLastStage)){
            return response()->json([
                'status'=>1,
                'data'=>[
                    'leaderboard'=>null,
                    'eventStatus'=>$status[0]->error,
                    'getMoreTurnInfo'=>[
                        'type'=>$turnInfo[0]->type,
                        'url'=>$turnInfo[0]->url,
                        'valueRemain'=>$turnInfo[0]->valueRemain
                    ]
                ]
            ]);
        }
        $hasPrize = 0;
        $top3 = [];
        $myBoard = [];
        $rank = 1;
        foreach($leaderBoard as $row){
            if($row->rank == -1){ //
                $hasPrize = $row->userId === null? 0 : $row->userId; //không tham gia thì không có giải
            }elseif($row->rank == 0){// top3
                $row->rank = $rank++;
                if($row->userId == $request->attributes->get('userId'))
                    $row->me = true;
                else
                    $row->me = false;
                $top3[] = $row;
            }else{ //my leader board
                if($row->userId == $request->attributes->get('userId'))
                    $row->me = true;
                else
                    $row->me = false;
                $myBoard[] = $row;
            }
        }
        return response()->json([
            'status'=>1,
            'data'=>[
                'leaderboard'=>[
                    'top3'=>$top3,
                    'myLeaderboard'=>$myBoard,
                    'hasPrize'=>$hasPrize
                ],
                'eventStatus'=>$status[0]->error,
                'getMoreTurnInfo'=>[
                    'type'=>$turnInfo[0]->type,
                    'url'=>$turnInfo[0]->url,
                    'valueRemain'=>$turnInfo[0]->valueRemain
                ]
            ]
        ]);
    }

    public function getLastStage(Request $request){
        $rs = DB::select('Call getLastStage(:userId, :date)', ['userId'=> $request->attributes->get('userId'), 'date'=>date('Y-m-d H:i:s')]);
        if(empty($rs) || !empty($rs[0]->error)){
            return response()->json(['status' => 0, 'error' => ['code' => 4001, 'message' => env(4001)]]);
        }
        if(!empty($rs[0]->firstTime)){
            return response()->json([
                'status'=>1,
                'data'=>[
                    'lastStageInfo'=>null,
                    'lastAdsImageUrl'=>null
                ]
            ]);
        }
        return response()->json([
            'status'=>1,
            'data'=>[
                'lastStageInfo'=>[
                    'indexStage'=>$rs[0]->stage,
                    'maxStage'=>$rs[0]->total
                ],
                'lastAdsImageUrl'=>$rs[0]->image,
                'timePlayed'=>$rs[0]->timePlayed
            ]
        ]);
    }

    public function shareFB(Request $request){
        $rs = DB::select('Call insertShare(:userId, :kind, :date)', ['userId'=> $request->attributes->get('userId'), 'kind'=>$request->attributes->get('kind'), 'date'=>date('Y-m-d H:i:s')]);
        if(empty($rs) || !empty($rs[0]->error)){
            return response()->json(['status' => 0, 'error' => ['code' => 4004, 'message' => env(4004)]]);
        }
        return response()->json([
            'status'=>1,
            'data'=>['valueRemain'=>$rs[0]->sharedFB]
        ]);
    }

    public function getPrize(Request $request){
        $rs = DB::select('Call getEventPrize(:userId)', ['userId'=> $request->attributes->get('userId')]);
        if(empty($rs)) return response()->json(['status' => 0, 'error' => ['code' => 4005, 'message' => env(4005)]]);
        if($rs[0]->win == 1){ //ingame
            $rs[0]->winValue = (int)$rs[0]->winValue;
            DB::select('UPDATE sp_event_leadboard SET received = 1 WHERE userId = :userId', ['userId'=>$request->attributes->get('userId')]);
            DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', ['user'=>$request->attributes->get('userId'), 'exp'=>0, 'token'=>0, 'gold'=>$rs[0]->winValue, 'reason'=>'Earn by play event', 'date'=>date('Y-m-d H:i:s')]);
        }
        return response()->json([
            'status'=>1,
            'data'=>[
                'type'=>$rs[0]->win,
                'value'=>''.$rs[0]->winValue.''
            ]
        ]);
    }

    public function confirmPrize(Request $request){
        DB::select('Call confirmReceiveReward(:userId, :date)', ['userId'=> $request->attributes->get('userId'), 'date'=>date('Y-m-d H:i:s')]);
        return response()->json([
            'status'=>1
        ]);
    }

    public function getStatus(){
        $status = DB::select('Call getEventStatus(:date)', ['date'=>date('Y-m-d H:i:s')]);
        return response()->json([
            'status'=>1,
            'data' => ['status' => $status[0]->error, 'id' => isset($status[0]->advId) ? $status[0]->advId : 0, 'name' => isset($status[0]->name) ? $status[0]->name : '0', 'logo' => isset($status[0]->logo) ? $status[0]->logo : '', 'dateEnd' => isset($status[0]->dateEnd) ? $status[0]->dateEnd : null]
        ]);
    }

    public function getLeaderBoard(Request $request){
        $leaderBoard = DB::select('call getLeaderBoard(:userId)',['userId'=> $request->attributes->get('userId')]);
        if(empty($leaderBoard) || !empty($leaderBoard[0]->error)){
            if($leaderBoard[0]->error === 1){//these are no event
                return response()->json(['status' => 0, 'error' => ['code' => 4001, 'message' => env(4001)]]);
            }
            if($leaderBoard[0]->error === 2){//incorrect input data
                return response()->json(['status' => 0, 'error' => ['code' => 2, 'message' => env(2)]]);
            }
        }
        $turnInfo = DB::select('Call getMoreTurnInfo(:userId, :currentDate)', ['userId'=>$request->attributes->get('userId'), 'currentDate'=>date('Y-m-d H:i:s')]);
        $hasPrize = 0;
        $top3 = [];
        $myBoard = [];
        $rank = 1;
        foreach($leaderBoard as $row){
            if($row->rank == -1){ //
                $hasPrize = $row->userId === null?0:$row->userId; //không tham gia thì trả về 0, không có giải
            }elseif($row->rank == 0){// top3
                $row->rank = $rank++;
                if($row->userId == $request->attributes->get('userId'))
                    $row->me = true;
                else
                    $row->me = false;
                $top3[] = $row;
            }else{ //my leader board
                if($row->userId == $request->attributes->get('userId'))
                    $row->me = true;
                else
                    $row->me = false;
                $myBoard[] = $row;
            }
        }
        return response()->json([
            'status'=>1,
            'data'=>[
                'leaderboard'=>[
                    'top3'=>$top3,
                    'myLeaderboard'=>$myBoard,
                    'hasPrize'=>$hasPrize
                ],
                'getMoreTurnInfo'=>[
                    'type'=>$turnInfo[0]->type,
                    'url'=>$turnInfo[0]->url,
                    'valueRemain'=>$turnInfo[0]->valueRemain
                ]
            ]
        ]);
    }

    public function getTermAndCondition() {
        $currentDate = date("Y-m-d H:i:s");
        $eventAds = TelevisionEventAdvertisement::where("status", "=", 1)->where("dateStart", "<=", $currentDate)->first();
        if (empty($eventAds)) {
            return response()->json(['status' => 0, 'error' => ['code' => 4001, 'message' => env(4001)]]);
        } else {
            return response()->json(['status' => 1, 'data' => ['content' => $eventAds->termAndCondition]]);
        }
    }
}