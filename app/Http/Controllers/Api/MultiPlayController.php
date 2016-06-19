<?php

/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/6/2015
 * Time: 4:10 PM
 */
namespace App\Http\Controllers\Api;

use App\Model\AcademyConfig;
use App\Model\Advertisement;
use App\Model\User;
use DB;
use Log;
use Illuminate\Http\Request;

class MultiPlayController extends APIController
{
    public function startMultiPlay(Request $request)
    {
        $rs = DB::select('CALL generateSinglePlay(:userId, :kind, :date)', ['userId'=>$request->attributes->get('userId'), 'kind'=>1, 'date'=>date('Y-m-d H:i:s')]);
        if(empty($rs) || !empty($rs[0]->error)){
            if($rs[0]->error == 5){
                return response()->json(['status'=>0,'error'=>['code'=>2006,'message'=>env(2006)]]);
            }
            return response()->json(['status'=>0,'error'=>['code'=>2004,'message'=>env(2004)]]);
        }
        $adv = DB::select("SELECT dealType, dealValue from sp_advertisement WHERE id = :advId", ['advId'=>$rs[0]->imageId]);
        return response()->json([
            'status'=>1,
            'data'=>[
                'matchId'=>$rs[0]->id,
                'compassInfo'=>$rs[0]->compassRate,
                'gameplayInfo'=>[
                    'adImageUrl'=>$rs[0]->imageUrl,
                    'adId'=>$rs[0]->imageId,
                    'mapInfo'=>[
                        'row'=>$rs[0]->row,
                        'col'=>$rs[0]->col
                    ],
                    'limitTimeToPlay'=>$rs[0]->timeToPlay,
                    'url'=>$adv[0]->dealValue,
                    'type'=>$adv[0]->dealType
                ],
                'opponentPlayHistory'=>json_decode($rs[0]->history, true)
            ]
        ]);
    }

    public function endMultiPlay(Request $request)
    {
        $matchId = $request->input('matchId', null);
        $playHistory = $request->input('playHistory', null);
        $checksum = $request->input('checksum', null);
        if(empty($matchId) || empty($playHistory) || empty($checksum))
            return response()->json(['status'=>0,'error'=>['code'=>2,'message'=>env(2)]]);
        if($checksum != md5($playHistory.(10 + $matchId))){
            return response()->json([
                'status'=>0,
                'error'=>['code'=>2,'message'=>env(2)]
            ]);
        }
        $jsonPlay = json_decode($playHistory);
        if($jsonPlay == false)
            return response()->json(['status'=>0,'error'=>['code'=>2,'message'=>env(2)]]);

        $len = count($jsonPlay->steps);
        if($len > 0){
            $timePlay = $jsonPlay->steps[$len-1]->time; //in seconds
            $tiles = $jsonPlay->steps[$len-1]->correctTile;
        }else{
            $timePlay = 999999999;
            $tiles = 0;
        }
        LOG::info('userId: ' . $request->attributes->get('userId') . ' game: ' . $matchId );
        $match = DB::select('call preFinishSinglePlay(:user, :game, :date)', ['user'=>$request->attributes->get('userId'), 'game'=>$matchId, 'date'=>date('Y-m-d H:i:s')]);
        if(empty($match))
            return response()->json(['status'=>0,'error'=>['code'=>2,'message'=>env(2)]]);
        if(!empty($match->error))
            return response()->json(['status'=>0,'error'=>['code'=>2010,'message'=>env(2010)]]); //this game was finished
        $match = $match[0];
        //$competitorActualPlay = DB::select('SELECT timeActualPlay FROM sp_singerplayrecord WHERE userId = :userId AND id = :playWith', ['userId'=>$request->attributes->get('userId'), 'playWith'=>$match->playWith]);
        $key = 0;
        if($tiles == ($match->row * $match->col)){
            $match->win = 1;
            $match->numberOfKey = $key = 1;
        }
        $match->history = $playHistory;
        $match->timeActualPlay = $timePlay;

        if ($tiles > ($match->row * $match->col)) {
            return response()->json([
                'status'=>0,
                'error'=>['code'=>2,'message'=>env(2)]
            ]);
        }

        $adv = Advertisement::find($match->imageId);
        //$expBonusRate = AcademyConfig::getBonusRateByUserId($request->attributes->get('userId'));
        $expBonusRate = DB::select('SELECT a.bonusRate FROM sp_academyconfig a LEFT JOIN sp_user u ON a.level = u.`academyLevel` WHERE u.id = :userId', ['userId'=>$request->attributes->get('userId')]);
        $expBonusRate = $expBonusRate[0]->bonusRate;
        $token = $exp = $gold = 0;
        $rewards = array();
        for($i = 0; $i < $key; ++$i){
            $reward = $this->randomPrize($adv);
            if ($i == 0) {
                $match->firstRewardType = $reward['type'];
                $match->firstRewardValue = $reward['value'];
            } elseif ($i == 1) {
                $match->secondRewardType = $reward['type'];
                $match->secondRewardValue = $reward['value'];
            } elseif ($i == 2) {
                $match->thirdRewardType = $reward['type'];
                $match->thirdRewardValue = $reward['value'];
            }
            //specification on compass Rate: https://docs.google.com/document/d/19kK4gVD_Z2-XcqvpAErfFrqU8job1WeRQpADal7aSFw/edit#
            if ($reward['type'] == 'exp') {
                $reward['value'] = ($reward['value'] * $match->compassRate) + ceil(($reward['value'] * $expBonusRate / 100));
                $exp += $reward['value'];
            } elseif ($reward['type'] == 'token') {
                $reward['value'] = $reward['value'] * $match->compassRate;
                $token += $reward['value'];
            } elseif ($reward['type'] == 'gold') {
                $reward['value'] = $reward['value'] * $match->compassRate;
                $gold += $reward['value'];
            } else {//troll

            }
            array_push($rewards, $reward);
        }
        $match->gold = $gold;
        $match->exp = $exp;
        $match->buildingToken = $token;

        DB::select('UPDATE sp_singerplayrecord set history = :history, timeActualPlay = :timeActualPlay, win = :win, numberOfKey = :numberOfKey, firstRewardType = :firstRewardType, firstRewardValue = :firstRewardValue, secondRewardType = :secondRewardType, secondRewardValue = :secondRewardValue, thirdRewardType = :thirdRewardType, thirdRewardValue = :thirdRewardValue, gold = :gold, exp = :exp, buildingToken = :buildingToken WHERE id = :id',
            [
                'history'=>$match->history,
                'timeActualPlay'=>$match->timeActualPlay,
                'win'=>$match->win,
                'numberOfKey'=>$match->numberOfKey,
                'firstRewardType'=>$match->firstRewardType,
                'firstRewardValue'=>$match->firstRewardValue,
                'secondRewardType'=>$match->secondRewardType,
                'secondRewardValue'=>$match->secondRewardValue,
                'thirdRewardType'=>$match->thirdRewardType,
                'thirdRewardValue'=>$match->thirdRewardValue,
                'gold'=>$match->gold,
                'exp'=>$match->exp,
                'buildingToken'=>$match->buildingToken,
                'id'=>$match->id
            ]);

        $user = DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', ['user'=>$request->attributes->get('userId'), 'exp'=>$exp, 'token'=>$token, 'gold'=>$gold, 'reason'=>'Earn by multi play game '.$matchId, 'date'=>date('Y-m-d H:i:s')]);
        $user = $user[0];
        $firstGameOfDay = DB::select('SELECT isFirstGameOfDay(:user, :date) as firstGame', ['user'=>$request->attributes->get('userId'), 'date'=>date('Y-m-d H:i:s')]);
        $shipInfo = DB::select('call getCurrentShipAndTimeNextShipEx(:user, :date)', ['user'=>$user->id, 'date'=>date('Y-m-d H:i:s')]);
        $quizzes = DB::select('CALL getRandomQuizzes(:user, :match)', ['user'=>$request->attributes->get('userId'), 'match'=>$matchId]);
        $q = sizeof($quizzes);
        if($q > 1){
            $i = 0;
            $quiz = array();
            foreach($quizzes as $row){
                if($i == 0){//question
                    $quiz['id'] = $row->id;
                    $quiz['question'] = $row->title;
                }else{ //answer
                    if($i == 1){
                        $quiz['a'] = array('id'=>$row->id, 'answer'=>$row->title);
                    } elseif ($i == 2) {
                        $quiz['b'] = array('id'=>$row->id, 'answer'=>$row->title);
                    } elseif ($i == 3) {
                        $quiz['c'] = array('id'=>$row->id, 'answer'=>$row->title);
                    } elseif ($i == 4) {
                        $quiz['d'] = array('id'=>$row->id, 'answer'=>$row->title);
                    }
                }
                ++$i;
            }
            return response()->json([
                'status'=>1,
                'data'=>[
                    'win'=>$match->win,
                    'key'=>$key,
                    'rewards'=>$rewards,
                    'userInfo'=>[
                        'username'=>$user->fullname,
                        'level'=>$user->level,
                        'gold'=>$user->coin,
                        'exp'=>$user->exp,
                        'buildingTokens'=>$user->buildingToken
                    ],
                    'shipInfo'=>[
                        'ship'=>$shipInfo[0]->ship,
                        'nextShip'=>$shipInfo[0]->nextShip
                    ],
                    'isFirstGameOfDay'=>$firstGameOfDay[0]->firstGame,
                    'quiz'=>$quiz
                ]
            ]);
        } else {
            return response()->json([
                'status'=>1,
                'data'=>[
                    'win'=>$match->win,
                    'key'=>$key,
                    'rewards'=>$rewards,
                    'userInfo'=>[
                        'username'=>$user->fullname,
                        'level'=>$user->level,
                        'gold'=>$user->coin,
                        'exp'=>$user->exp,
                        'buildingTokens'=>$user->buildingToken
                    ],
                    'shipInfo'=>[
                        'ship'=>$shipInfo[0]->ship,
                        'nextShip'=>$shipInfo[0]->nextShip
                    ],
                    'isFirstGameOfDay'=>$firstGameOfDay[0]->firstGame
                ]
            ]);
        }
    }

    /*random reward depend on advertisement*/
    function randomPrize($adv){
        $ran =  rand(1, 100);
        if ($ran <= $adv->trashRate) {
            return array('type' => 'troll', 'value' => rand(0, 1));
        } elseif ($ran <= $adv->trashRate + $adv->expRate) {
            return array('type' => 'exp', 'value' => rand($adv->expMin, $adv->expMax));
        } elseif ($ran <= $adv->trashRate + $adv->expRate + $adv->tokenRate) {
            return array('type' => 'token', 'value' => rand($adv->tokenMin, $adv->tokenMax));
        } else {
            return array('type' => 'gold', 'value' => rand($adv->cashMin, $adv->cashMax));
        }
    }
}