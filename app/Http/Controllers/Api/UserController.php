<?php

/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/6/2015
 * Time: 4:10 PM
 */
namespace App\Http\Controllers\Api;

use App\Model\CashoutInfo;
use App\Model\Config;
use DB;
use Crypt;
use Log;
use Validator;
use Illuminate\Http\Request;
use Mattbrown\Laracurl\Facades\Laracurl;

class UserController extends APIController
{
    public function login(Request $request){
        Log::info("Current datetime: " . date('Y-m-d H:i:s') . $request->input('accesstoken'));
        //check if the old version (< 1.8), we force client update to new version.
        if(empty($request->input('version'))){
            return  response()->json(
                [   'status'=>0,
                    'error'=>['code'=> 5000, 'message'=>env('5000')]
                ]);
        }
        $fbResult = $this->checkTokenWithFacebook($request->input('accesstoken'));
        if($fbResult != null){
            $user = \App\Model\User::where('fbId', $fbResult['id'])->first();
            if(empty($user) ){ //new user
                DB::select('call createNewUser(?, ?, ?, ?, ?, ?)', array($fbResult['id'], $fbResult['name'], isset($fbResult['email']) ? $fbResult['email'] : null, $fbResult['gender'] == 'female' ? 0 : 1, $fbResult['age_range']['min'], date('Y-m-d H:i:s')));
                $user = \App\Model\User::where('fbId', $fbResult['id'])->first();
            }
            if(empty($user->fbEmail) && !empty($fbResult['email'])){
                $user->fbEmail = $fbResult['email'];
                $user->save();
            }
            if($user->status == 2){
                return response()->json(['status'=>0, 'error'=>['code'=>1001, 'message'=>env(1001)]]);
            }
            $info = DB::select('call getUserInfo(?, ?)', array($fbResult['id'], date('Y-m-d H:i:s')));
            $levelConfig = DB::select('SELECT toExp as maxExp, level, buildingToken as levelUpReward FROM sp_levelconfig ORDER BY level');
            $cashOutInfo = DB::select('SELECT bankName as bank, bankAccount as bankNumber, age, gender, status as cashOutInformationStatus FROM sp_cashoutinfo WHERE userId = :id limit 0, 1', ['id'=>$user->id]);
            $upgrade = DB::select('call getUpgradeInfo(?)', array($user->id));
            $token = Crypt::encrypt($user->id.'_'.$user->fbId.'_'.time());
            if(!session_id($user->id))
                session_start();
            $_SESSION['token'] = $token;
            return response()->json(
                ['status'=>1,
                    'data'=>[
                        'token'=>$token,
                        'didReceiveDailyReward'=>$info[0]->_didReceivedDailyReward == 0 ? false : true,
                        'dailyRewardMultiplier'=>$info[0]->_dailyRewardMultiplier,
                        'askForShipNumber'=>$info[0]->_askForShipNumber,
                        'didShareForKey'=>$info[0]->_shareForKeyBool == 0 ? false : true,
                        'userInfo'=>[
                            'username'=>$user->fullname,
                            'isNewUser'=> $user->status == 0 ? true : false,
                            'level'=>$user->level,
                            'gold'=>$user->coin,
                            'exp'=>$user->exp,
                            'buildingTokens'=>$user->buildingToken
                        ],
                        'shipInfo'=>[
                            'ship'=>$info[0]->_ship,
                            'timeForNextShip'=>$info[0]->_timeForNextShip
                        ],
                        'upgradeInfo'=>[
                            'factory'=>[
                                'level'=>$upgrade[0]->factory,
                                'canUpgrade'=>$upgrade[0]->canUpgradeFactory,
                                'tokensToUpgrade'=>$upgrade[0]->_buildingTokenFactory
                            ],
                            'dock'=>[
                                'level'=>$upgrade[0]->dock,
                                'canUpgrade'=>$upgrade[0]->canUpgradeDock,
                                'tokensToUpgrade'=>$upgrade[0]->_buildingTokenDock
                            ],
                            'academy'=>[
                                'level'=>$upgrade[0]->academy,
                                'canUpgrade'=>$upgrade[0]->canUpgradeAcademy,
                                'tokensToUpgrade'=>$upgrade[0]->_buildingTokenAcademy
                            ],
                            'timeToGain1Ship'=>$upgrade[0]->timeToGain1Ship,
                            'maxShip'=>$upgrade[0]->maxShip
                        ],
                        'gameInfo'=>[
                            'levelInfos'=>$levelConfig
                        ],
                        'cashOutInfo'=>empty($cashOutInfo[0])?null:$cashOutInfo[0]
                    ]
                ]);
        }
        Log::info('login facebook error with access token: ' . $request->input("accesstoken"));
        return response()->json(['status'=>0, 'error'=>['code'=>1000, 'message'=>env('1000')]]);
    }

    /*return fbUser if access token is valid otherwise return null*/
    private function checkTokenWithFacebook($accessToken){

        $url = Laracurl::buildUrl('https://graph.facebook.com/app', ['access_token'=>$accessToken]);
        $response = Laracurl::get($url);
        if(!empty($response) && !empty($response->body)){
            $fbResult = json_decode($response->body, true);
            if(!empty($fbResult['id']) && $fbResult['id'] == env('FB_APP_KEY')){//this user come from our facebook app
                $url = Laracurl::buildUrl('https://graph.facebook.com/me', ['fields'=> 'id,name,gender,email,age_range', 'access_token'=>$accessToken]);
                $response = Laracurl::get($url);
                if(!empty($response) && !empty($response->body)) {
                    $fbResult = json_decode($response->body, true);
                    if (!empty($fbResult['id'])) {
                        return $fbResult;
                    }
                }
            }
        }
        return null;
    }

    public function sendActivationCode(Request $request)
    {
        $phone = $request->input('phonenumber');
        $country = $request->input("countrycode");
        //check phone's format
        $phone_number = $this->getPhoneNumber("+".$country.$phone);

        if(empty($phone) || empty($country) || $phone_number === null){
            return response()->json(['status'=>0, 'error'=>['code'=> 2000, 'message'=>env('2000')]]);
        }

        //check this phone is used in our game
        $rs = DB::select('SELECT count(id) as noOfPhone from sp_user where phone = :phone and id <> :userId', ['phone'=>$phone_number, 'userId'=>$request->attributes->get('userId')]);
        if($rs[0]->noOfPhone > 0)
            return response()->json(['status'=>0, 'error'=>['code'=> 2001, 'message'=>env('2001')]]);

        //check this user is active or not
        $rs = DB::select('SELECT count(id) as id from sp_user where status=0 and id = :userId limit 1', ['userId'=>$request->attributes->get('userId')]);
        if($rs[0]->id == 0) //this user is actived
            return response()->json(['status'=>0, 'error'=>['code'=> 2003, 'message'=>env('2003')]]);

        //Sending the verification code
        $response = Laracurl::post('https://api.authy.com/protected/json/phones/verification/start?api_key=OCsDpRKoXWAWv73VvFB8TPJN3Hg7uWpA',[
            'via'=>'sms',
            'country_code'=> $country,
            'phone_number'=> $phone,
            'locale' => 'en'
        ]);

        $js = json_decode($response->body);
        if($js->success === false){
            return response()->json(['status'=>0, 'error'=>['code'=>2001, 'message'=>$js->message]]);
        }

        DB::select('INSERT INTO sp_authysending (userId, request, response, phone, status, country, phone_number, carrier, is_cellphone, dateCreate) VALUES (:userId, :request, :response, :phone, :status, :country, :phone_number, :carrier, :is_cellphone, :dateCreate)',
            ['userId'=>$request->attributes->get('userId'),
                'request'=> 'https://api.authy.com/protected/json/phones/verification/start?api_key=' . env('app_key') . ' via=>sms,country_code=> ' . $country . 'phone_number=> ' . $phone,
                'response'=>$response->body,
                'phone'=>$phone,
                'status'=>$js->success,
                'country'=>$country,
                'phone_number'=>$phone_number,
                'carrier'=>$js->carrier,
                'is_cellphone'=>$js->is_cellphone,
                'dateCreate'=>date('Y-m-d H:i:s')
            ]);

        return response()->json(['status'=>1]);
    }

    public function activateByPhone(Request $request)
    {
        $code = $request->input('activationCode');
        if(empty($code) || strlen($code) != 4){
            return response()->json(['status'=>0, 'code'=> 2002, 'message'=>env('2002')]);
        }
        //check this user is active or not
        $rs = DB::select('SELECT count(id) as id from sp_user where status=0 and id = :userId limit 1', ['userId'=>$request->attributes->get('userId')]);
        if($rs[0]->id == 0) //this user is active
            return response()->json(['status'=>0, 'code'=> 2003, 'message'=>env('2003')]);

        $sending = DB::select('SELECT * FROM sp_authysending WHERE userId = :userId and status=1 ORDER BY dateCreate DESC LIMIT 1', ['userId'=>$request->attributes->get('userId')]);
        if(empty($sending))
            return response()->json(['status'=>0, 'code'=>2002, 'message'=>env('2002')]);

        //Verifying the verification code
        $response = Laracurl::get('https://api.authy.com/protected/json/phones/verification/check?api_key=OCsDpRKoXWAWv73VvFB8TPJN3Hg7uWpA&phone_number='.$sending[0]->phone.'&country_code='.$sending[0]->country.'&verification_code='.$code,[
        ]);

        $js = json_decode($response->body);
        if($js->success === false){
            return response()->json(['status'=>0, 'code'=>2001, 'message'=>$js->message]);
        }
        DB::select('UPDATE sp_user set phone = :phone, status = :status WHERE id = :userId', ['phone'=>$sending[0]->phone_number, 'status'=>1, 'userId'=>$request->attributes->get('userId')]);

        return response()->json(['status'=>1]);
    }

    public function getPhoneNumber($phone){
        $username='AC1f4f252ba3d4e17ad6d00c6c62d5d58b';
        $password='e9551bd3ab0b3b26a4984988d624a238';
        $URL='https://lookups.twilio.com/v1/PhoneNumbers/' . $phone;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        $result=curl_exec ($ch);
        if($result === false){
            $error = curl_error($ch);
            curl_close ($ch);
            Log::info('call '. $URL . ' __ get: ' . $error);
            return null;
        }
        curl_close ($ch);
        Log::info('call '. $URL . ' __ get: ' . $result);
        $rs = json_decode($result);
        if(!empty($rs->phone_number)){
            return $rs->phone_number;
        }
        return null;
    }

    public function upgradeCashoutInformation(Request $request){
        $userId = $request->attributes->get('userId');
        if(empty($userId)){
            return response()->json(['status'=>0, 'error'=>['code'=>3010, 'message'=>env(3010)]]);
        }
        try {
            $config = Config::where("key", "=", "DefaultBankStatus")->first();
            $cashoutInfo = CashoutInfo::where("userId", "=", $userId)->first();
            $cashoutInfo->bankName = $request->input("bankName");
            $cashoutInfo->bankAccount = $request->input("bankNumber");
            $cashoutInfo->gender = $request->input("gender");
            $cashoutInfo->age = $request->input("age");
            $cashoutInfo->status = $config->value;
            $validateCashout = $this->validateBank($cashoutInfo);
            if($validateCashout['error']){
                return response()->json(['status'=>0, 'error'=>['code'=>0, 'message'=>$validateCashout['message']]]);
            }
            $cashoutInfo->save();
        } catch (\PDOException $e) {
            return response()->json(['status'=>0, 'error'=>['code'=>0, 'message'=>$e->getMessage()]]);
        }
        return response()->json(
            ['status'=>1,
                'data'=>[
                    'cashOutInfo'=>[
                        'bank'=>$cashoutInfo->bankName,
                        'bankNumber'=>$cashoutInfo->bankAccount,
                        'age'=>(int)$cashoutInfo->age,
                        'gender'=>(int)$cashoutInfo->gender,
                        'cashOutInformationStatus'=>2 //PENDING
                    ]
                ]
            ]);
    }

    public function validateBank($specificData){
        $validator = Validator::make(array("BankName" => $specificData->bankName),
            array("BankName" => "string|max:150"));
        if($validator->fails()){
            return array('error'=>1, 'message'=>$validator->messages()->getMessages()['BankName'][0]);
        }
        $validator = Validator::make(array("BankAccount" => $specificData->bankAccount),
            array("BankAccount" => "string|max:20"));
        if($validator->fails()){
            return array('error'=>1, 'message'=>$validator->messages()->getMessages()['BankAccount'][0]);
        }
        $validator = Validator::make(array("Gender" => $specificData->gender),
            array("Gender" => "required|numeric|min:0|max:1"));
        if($validator->fails()){
            return array('error'=>1, 'message'=>$validator->messages()->getMessages()['Gender'][0]);
        }
        $validator = Validator::make(array("Age" => $specificData->age),
            array("Age" => "numeric"));
        if($validator->fails()){
            return array('error'=>1, 'message'=>$validator->messages()->getMessages()['Age'][0]);
        }
        return array('error'=>0);
    }
}