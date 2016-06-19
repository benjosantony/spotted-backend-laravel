<?php

/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/6/2015
 * Time: 4:10 PM
 */
namespace App\Http\Controllers\Api;

use App\Model\AcademyConfig;
use App\Model\AskForShip;
use App\Model\User;
use DB;
use Log;
use Illuminate\Http\Request;
use App\Model\SinglePlayRecord;
use App\Model\Advertisement;

class GameController extends APIController
{
    public function startSinglePlay(Request $request)
    {
        $rs = DB::select('CALL generateSinglePlay(:userId, :kind, :date)', ['userId'=>$request->attributes->get('userId'), 'kind'=>0, 'date'=>date('Y-m-d H:i:s')]);

        if(empty($rs)){
            return response()->json(['status'=>0,'error'=>['code'=>2006, 'message'=>env(2006)]]);
        }

        if(!empty($rs[0]->error)){
            Log::info('startSinglePlay error: ' . $request->attributes->get('userId'));
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
                    'timeToDropKey'=>[$rs[0]->firstKey, $rs[0]->secondKey, $rs[0]->thirdKey],
                    'url'=>$adv[0]->dealValue,
                    'type'=>$adv[0]->dealType
                ]
            ]
        ]);
    }

    public function endSinglePlay(Request $request)
    {
        $matchId = $request->input('matchId', null);
        $playHistory = $request->input('playHistory', null);
        $checksum = $request->input('checksum', null);
        if(empty($matchId) || empty($playHistory) || empty($checksum))
            return response()->json(['status'=>0,'error'=>['code'=>2,'message'=>env(2)]]);
        Log::info("EndSinglePlay: matchId: " . $matchId . ' -checksum: ' . $checksum . ' - playHistory: ' . $playHistory);
        Log::info("MD5 " . $playHistory. (10 + $matchId));
        Log::info("After MD5: " . md5($playHistory.(10 + $matchId)));
        if($checksum != md5($playHistory.(10 + $matchId))){
            return response()->json([
                'status'=>0,
                'error'=>['code'=>2,'message'=>env(2)]
            ]);
        }
        Log::info('endSinglePlay: $matchId: ' . $matchId . ' -$playHistory: ' . $playHistory );
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


        $match = DB::select('call preFinishSinglePlay(:user, :game, :date)', ['user'=>$request->attributes->get('userId'), 'game'=>$matchId, 'date'=>date('Y-m-d H:i:s')]);
        if(empty($match))
            return response()->json(['status'=>0,'error'=>['code'=>2,'message'=>env(2)]]);
        if(!empty($match->error))
            return response()->json(['status'=>0,'error'=>['code'=>2010,'message'=>env(2010)]]); //this game was finished
        $match = $match[0];
        $match->history = $playHistory;
        $match->timeActualPlay = $timePlay;

        if($tiles == ($match->row * $match->col))
            $match->win = 1;
        elseif ($tiles > ($match->row * $match->col)) {
            return response()->json([
                'status'=>0,
                'error'=>['code'=>2,'message'=>env(2)]
            ]);
        }

        $key = 0;
        if($match->win == 1){
            if($timePlay <= $match->thirdKey) ++$key;
            if($timePlay <= $match->secondKey) ++$key;
            if($timePlay <= $match->firstKey) ++$key;
        }
        $match->numberOfKey = $key;

        $adv = Advertisement::find($match->imageId);
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
        $match->exp = $exp ;
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

        $user = DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', ['user'=>$request->attributes->get('userId'), 'exp'=>$exp, 'token'=>$token, 'gold'=>$gold, 'reason'=>'Earn by single play game '.$matchId, 'date'=>date('Y-m-d H:i:s')]);
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

    public function submitQuiz(Request $request){
        $gameId = $request->input('gameId', null);
        $quizId = $request->input('quizId', null);
        $answerId = $request->input('answerId', null);

        if(empty($quizId) || empty($answerId))
            return response()->json(['status'=>0,'error'=>['code'=>2,'message'=>env(2)]]);
        $rs = DB::select('Call submitQuiz(:user, :game, :question, :answer, :date)',['user'=>$request->attributes->get('userId'), 'game'=>$gameId, 'question'=>$quizId, 'answer'=>$answerId, 'date'=>date('Y-m-d H:i:s')]);
        if(empty($rs) || !empty($rs[0]->error))
            return response()->json(['status'=>0,'error'=>['code'=>$rs[0]->error,'message'=>env(2002)]]);
        if($rs[0]->correct == 1){//random reward for this user
            $advId = DB::select('SELECT advertiseId from sp_question WHERE id = :question', ['question'=>$quizId]);
            $adv = Advertisement::find($advId[0]->advertiseId);
            $gameRecord = SinglePlayRecord::find($gameId);
            $sharePrize = $this->randomPrize($adv);

            $prizeType = $sharePrize['type'];
            $prizeValue = $sharePrize['value'];
            $gameRecord->quizRewardType = $prizeType;
            $gameRecord->quizRewardValue = $prizeValue;
            $gameRecord->save();

            if($prizeType == 'exp') {
                $expBonusRate = DB::select('SELECT a.bonusRate FROM sp_academyconfig a LEFT JOIN sp_user u ON a.level = u.`academyLevel` WHERE u.id = :userId', ['userId'=>$request->attributes->get('userId')]);
                $expBonusRate = $expBonusRate[0]->bonusRate;
                $prizeValue = ($prizeValue * $gameRecord->compassRate) + ceil(($prizeValue * $expBonusRate / 100));
                DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                    'user' => $request->attributes->get('userId'),
                    'exp' => $prizeValue,
                    'token' => 0,
                    'gold' => 0,
                    'reason' => 'Earned by answer quiz '. $quizId,
                    'date' => date('Y-m-d H:i:s')
                ]);
            } elseif($prizeType == 'token') {
                $prizeValue = $prizeValue * $gameRecord->compassRate;
                DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                    'user' => $request->attributes->get('userId'),
                    'exp' => 0,
                    'token' => $prizeValue,
                    'gold' => 0,
                    'reason' => 'Earned by answer quiz '. $quizId,
                    'date' => date('Y-m-d H:i:s')
                ]);
            } elseif($prizeType == 'gold') {
                $prizeValue = $prizeValue * $gameRecord->compassRate;
                DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                    'user' => $request->attributes->get('userId'),
                    'exp' => 0,
                    'token' => 0,
                    'gold' => $prizeValue,
                    'reason' => 'Earned by answer quiz '. $quizId,
                    'date' => date('Y-m-d H:i:s')
                ]);
            }
            return response()->json([
                'status'=>1,
                'data'=>[
                    'result'=>$rs[0]->correct,
                    'rightAnswer'=>$rs[0]->rightAnswer,
                    'reward'=>[
                        'type'=>$prizeType,
                        'value'=>$prizeValue
                    ]
                ]
            ]);
        }
        return response()->json([
            'status'=>1,
            'data'=>[
                'result'=>$rs[0]->correct,
                'rightAnswer'=>$rs[0]->rightAnswer,
            ]
        ]);
    }

    public function getDailyReward(Request $request)
    {
        $info = DB::select('call prepareDailyReward(?, ?)', array($request->attributes->get('userId'), date('Y-m-d H:i:s')));
        if($info[0]->_didReceivedDailyReward == 1){ //ready received daily reward
            return response()->json(['status'=>0,'error'=>['code'=>2020, 'message'=>env(2020)]]);
        }
        $dailyRewardConfig = DB::select('call getDailyRewardRate(:random)', ['random'=>rand(0, 100)]);
        $reward['type'] = $dailyRewardConfig[0]->name;
        $reward['value'] = rand($dailyRewardConfig[0]->minValue, $dailyRewardConfig[0]->maxValue) * $info[0]->_dailyRewardMultiplier;
        DB::select('UPDATE sp_dailyrewardreceived set type=:type, value=:value where id = :id', ['type'=>$reward['type'], 'value'=>$reward['value'], 'id'=>$info[0]->_dailyRewardId]);
        if($reward['type'] == 'exp'){
            $user = DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', ['user'=>$request->attributes->get('userId'), 'exp'=>$reward['value'], 'token'=>0, 'gold'=>0, 'reason'=>'Earn by daily reward ', 'date'=>date('Y-m-d H:i:s')]);
        }elseif($reward['type'] == 'token'){
            $user = DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', ['user'=>$request->attributes->get('userId'), 'exp'=>0, 'token'=>$reward['value'], 'gold'=>0, 'reason'=>'Earn by daily reward ', 'date'=>date('Y-m-d H:i:s')]);
        }else{//gold
            $user = DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', ['user'=>$request->attributes->get('userId'), 'exp'=>0, 'token'=>0, 'gold'=>$reward['value'], 'reason'=>'Earn by daily reward ', 'date'=>date('Y-m-d H:i:s')]);
        }
        Log::info('getDailyReward: User ID: '. $request->attributes->get('userId') . ' - type: ' . $reward['type'] . ' - value: '.  $reward['value']);
        $user = $user[0];
        $shipInfo = DB::select('call getCurrentShipAndTimeNextShipEx(:user, :date)', ['user'=>$user->id, 'date'=>date('Y-m-d H:i:s')]);
        return response()->json([
            'status'=>1,
            'data'=>[
                'numberOfContinuousDay'=>$info[0]->_dailyRewardMultiplier,
                'reward'=>[
                    'type'=>$reward['type'],
                    'value'=>$reward['value']
                ],
                'userInfo'=>[
                    'username'=>$user->fullname,
                    'level'=>$user->level,
                    'gold'=>$user->coin,
                    'exp'=>$user->exp,
                    'buildingTokens'=>$user->buildingToken,
                    'ship'=>$shipInfo[0]->ship,
                    'nextShip'=>$shipInfo[0]->nextShip
                ]
            ]
        ]);
    }

    public function upgradeAbilities(Request $request){
        $userId = $request->attributes->get('userId');
        if(empty($userId)){
            return response()->json(['status'=>0, 'error'=>['code'=>3010, 'message'=>env(3010)]]);
        }
        $upgradeType = $request->input('type');
        if(!isset($upgradeType)){
            return response()->json(['status'=>0, 'error'=>['code'=>3011, 'message'=>env(3011)]]);
        }
        $upgrade = DB::select('CALL upgradeAbilities(?, ?)', array($userId, $upgradeType));
        $upgrade = $upgrade[0];
        $currentDate = date('Y-m-d H:i:s');
        if($upgrade->_error == 1){ // current user is on another process
            return response()->json(['status'=>0, 'error'=>['code'=>3000, 'message'=>env(3000)]]);
        } elseif($upgrade->_error == 2){ // try to upgrade factory over max level
            return response()->json(['status'=>0, 'error'=>['code'=>3001, 'message'=>env(3001)]]);
        } elseif($upgrade->_error == 3){ // try to upgrade dock over max level
            return response()->json(['status'=>0, 'error'=>['code'=>3002, 'message'=>env(3002)]]);
        } elseif($upgrade->_error == 4){ // try to upgrade academy over max level
            return response()->json(['status'=>0, 'error'=>['code'=>3003, 'message'=>env(3003)]]);
        } elseif($upgrade->_error == 5){ // not enough token
            return response()->json(['status'=>0, 'error'=>['code'=>3004, 'message'=>env(3004)]]);
        } elseif($upgrade->_error == 0) {
            $user = "";
            if($upgradeType == 0) {
                $user = DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                    'user' => $userId,
                    'exp' => 0,
                    'token' => 0 - $upgrade->_currentFactoryToken,
                    'gold' => 0,
                    'reason' => 'Upgrade factory level',
                    'date' => $currentDate
                ]);
            } elseif($upgradeType == 1) {
                $user = DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                    'user' => $userId,
                    'exp' => 0,
                    'token' => 0 - $upgrade->_currentDockToken,
                    'gold' => 0,
                    'reason' => 'Upgrade dock level',
                    'date' => $currentDate
                ]);
            } elseif($upgradeType == 2) {
                $user = DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                    'user' => $userId,
                    'exp' => 0,
                    'token' => 0 - $upgrade->_currentAcademyToken,
                    'gold' => 0,
                    'reason' => 'Upgrade academy level',
                    'date' => $currentDate
                ]);
            }
            $user = $user[0];
            $maxShip = DB::select('SELECT getMaxShip(?) AS maxShip', array($userId));
            $shipInfo = DB::select('call getCurrentShipAndTimeNextShipEx(:user, :date)', ['user'=>$userId, 'date'=>$currentDate]);
            if($upgrade->_nextFactoryToken == -1){
                $canUpgradeFactory = 0;
                $nextFactoryToken = 0;
            } else {
                $canUpgradeFactory = 1;
                $nextFactoryToken = $upgrade->_nextFactoryToken;
            }
            if($upgrade->_nextDockToken == -1){
                $canUpgradeDock = 0;
                $nextDockToken = 0;
            } else {
                $canUpgradeDock = 1;
                $nextDockToken = $upgrade->_nextDockToken;
            }
            if($upgrade->_nextAcademyToken == -1){
                $canUpgradeAcademy = 0;
                $nextAcademyToken = 0;
            } else {
                $canUpgradeAcademy = 1;
                $nextAcademyToken = $upgrade->_nextAcademyToken;
            }
            return response()->json([
                'status'=>1,
                'data'=>[
                    'userInfo'=>[
                        'username'=>$user->fullname,
                        'level'=>$user->level,
                        'gold'=>$user->coin,
                        'exp'=>$user->exp,
                        'buildingTokens'=>$user->buildingToken
                    ],
                    'shipInfo'=>[
                        'ship'=>$shipInfo[0]->ship,
                        'timeForNextShip'=>$shipInfo[0]->nextShip
                    ],
                    'upgradeInfo'=>[
                        'factory'=>[
                            'level'=>$user->factoryLevel,
                            'canUpgrade'=>$canUpgradeFactory,
                            'tokensToUpgrade'=>$nextFactoryToken
                        ],
                        'dock'=>[
                            'level'=>$user->dockLevel,
                            'canUpgrade'=>$canUpgradeDock,
                            'tokensToUpgrade'=>$nextDockToken
                        ],
                        'academy'=>[
                            'level'=>$user->academyLevel,
                            'canUpgrade'=>$canUpgradeAcademy,
                            'tokensToUpgrade'=>$nextAcademyToken
                        ],
                        'timeToGain1Ship'=>$shipInfo[0]->timeForOneShip,
                        'maxShip'=>$maxShip[0]->maxShip
                    ]
                ]
            ]);
        }
    }

    public function shareForKey(Request $request){
        $userId = $request->attributes->get('userId');
        if (empty($userId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3010, 'message'=>env(3010)]]);
        }
        $userInfo = User::find($userId);
        $currentDate = date('Y-m-d');
        $currentDateTime = date('Y-m-d H:i:s');
        if ($userInfo->shareForKey == $currentDate) {
            return response()->json(['status'=>0, 'error'=>['code'=>3020, 'message'=>env(3020)]]);
        }
        $gameId = $request->input("id");
        if (empty($gameId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3012, 'message'=>env(3012)]]);
        }
        try {
            $gameRecord = SinglePlayRecord::find($gameId);
            if ($gameRecord->userId != $userId) {
                return response()->json(['status'=>0, 'error'=>['code'=>3013, 'message'=>env(3013)]]);
            }
            $gameAdvertise = Advertisement::find($gameRecord->imageId);
            $sharePrize = $this->randomPrize($gameAdvertise);
            $prizeType = $sharePrize['type'];
            $prizeValue = $sharePrize['value'];
            $gameRecord->shareRewardType = $prizeType;
            $gameRecord->shareRewardValue = $prizeValue;
            $gameRecord->save();
            $userInfo->shareForKey = $currentDate;
            $userInfo->save();
            if($prizeType == 'exp') {
                DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                    'user' => $userId,
                    'exp' => $prizeValue,
                    'token' => 0,
                    'gold' => 0,
                    'reason' => 'Earned by share for key',
                    'date' => $currentDateTime
                ]);
            } elseif($prizeType == 'token') {
                DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                    'user' => $userId,
                    'exp' => 0,
                    'token' => $prizeValue,
                    'gold' => 0,
                    'reason' => 'Earned by share for key',
                    'date' => $currentDateTime
                ]);
            } elseif($prizeType == 'gold') {
                DB::select('CALL updateUser(:user, :exp, :token, :gold, :reason, :date)', [
                    'user' => $userId,
                    'exp' => 0,
                    'token' => 0,
                    'gold' => $prizeValue,
                    'reason' => 'Earned by share for key',
                    'date' => $currentDateTime
                ]);
            }
        } catch (\PDOException $e) {
            return response()->json(['status'=>0, 'error'=>['code'=>0, 'message'=>$e->getMessage()]]);
        }
        return response()->json([
            'status'=>1,
            'data'=>[
                'type'=>$sharePrize['type'],
                'value'=>$sharePrize['value']
            ]
        ]);
    }

    public function askForHelp(Request $request){
        $userId = $request->attributes->get('userId');
        if (empty($userId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3010, 'message'=>env(3010)]]);
        }
        $askShip = DB::select('CALL askFacebookForShip(:userId, :currentDate)', ['userId'=>$userId, 'currentDate'=>date('Y-m-d H:i:s')]);
        if(empty($askShip) || !empty($askShip[0]->error)){
            return response()->json(['status'=>0, 'error'=>['code'=>3033, 'message'=>env(3033)]]);
        }
        return response()->json(['status'=>1]);
    }

    public function sendRewardFacebook(Request $request){
        $userId = $request->attributes->get('userId');
        if (empty($userId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3010, 'message'=>env(3010)]]);
        }
        $requestId = $request->input('requestId');
        if (empty($requestId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3030, 'message'=>env(3030)]]);
        }
        $senderId = $request->input('senderId');
        if (empty($senderId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3031, 'message'=>env(3031)]]);
        }
        $receiverId = $request->input('receiverId');
        if (empty($receiverId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3031, 'message'=>env(3031)]]);
        }
        try {
            $askShip = new AskForShip();
            $askShip->requestId = $requestId;
            $askShip->senderId = $senderId;
            $askShip->receiverId = $receiverId;
            $askShip->dateAccept = null;
            $askShip->status = 1;
            $askShip->save();
        } catch (\PDOException $e){
            return response()->json(['status'=>0, 'error'=>['code'=>0, 'message'=>$e->getMessage()]]);
        }
        return response()->json(['status'=>1]);
    }

    public function getReward(Request $request){
        $userId = $request->attributes->get('userId');
        if (empty($userId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3010, 'message'=>env(3010)]]);
        }
        $requestId = $request->input('requestId');
        if (empty($requestId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3030, 'message'=>env(3030)]]);
        }
        $currentDate = date('Y-m-d H:i:s');
        $getReward = DB::select('CALL getRewardFacebook(?, ?, ?)', array($requestId, $userId, $currentDate));
        if ($getReward[0]->_error == 1) {
            return response()->json(['status'=>0, 'error'=>['code'=>3031, 'message'=>env(3031)]]);
        } else if ($getReward[0]->_error == 2) {
            return response()->json(['status'=>0, 'error'=>['code'=>3032, 'message'=>env(3032)]]);
        } else {
            $user = User::find($userId);
            $shipInfo = DB::select('call getCurrentShipAndTimeNextShipEx(:user, :date)', ['user'=>$userId, 'date'=>$currentDate]);
            return response()->json([
                'status'=>1,
                'data'=>[
                    'userInfo'=>[
                        'username'=>$user->fullname,
                        'level'=>$user->level,
                        'gold'=>$user->coin,
                        'exp'=>$user->exp,
                        'buildingTokens'=>$user->buildingToken
                    ],
                    'shipInfo'=>[
                        'ship'=>$shipInfo[0]->ship,
                        'timeForNextShip'=>$shipInfo[0]->nextShip
                    ]
                ]
            ]);
        }
    }

    public function getListReward(Request $request){
        $userId = $request->attributes->get('userId');
        if (empty($userId)) {
            return response()->json(['status'=>0, 'error'=>['code'=>3010, 'message'=>env(3010)]]);
        }
        /*$facebookRequest = FacebookRequest::where("senderId", "=", $userId)->where("status", "=", 1)->get();
        $type = "ship";
        $value = 1;
        $reward = [];
        foreach ($facebookRequest as $fbRequest) {
            $message = $fbRequest->receiverName . ' sent to you ' . $value . ' ' . $type;
            $inboxValue = [
                'id' => $fbRequest->id,
                'type' => $type,
                'value' => $value,
                'description' => $message
            ];
            array_push($reward, ['inboxs' => $inboxValue]);
        }
        return response()->json([
            'status' => 1,
            'data' => $reward
        ]);*/
    }

    public function getVersion(Request $request){
        $os = $request->input('os');
        $version = $request->input('version');
        $rs = DB::select('SELECT link, forceUpdate, api from sp_version where os=:os and version=:version limit 1', ['os'=>$os, 'version'=>$version]);
        if(empty($rs)) return response()->json(['status'=>0, 'error'=>['code'=>5000, 'message'=>env(5000)]]);
        return response()->json(['status'=>1, 'data'=>['link'=>$rs[0]->link, 'forceUpdate'=>$rs[0]->forceUpdate == 1 ? true : false, 'api'=>$rs[0]->api]]);
    }
}