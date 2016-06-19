<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/14/2015
 * Time: 4:09 PM
 */

namespace App\Http\Controllers\backend;
use App\Model\Config;
use DB;
use Input;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\CashoutInfo;
use App\Model\SinglePlayRecord;
use App\Model\User;

class UserController extends Controller
{
    public function user(){
        $page_title = "User Filter";
        $page_title_main = "User Management";
        $allData = User::leftjoin("sp_cashoutinfo", "sp_user.id", "=", "sp_cashoutinfo.userId")
            ->leftjoin("sp_payout", function($join){
                $join->on("sp_user.id", "=", "sp_payout.userId")
                    ->where("sp_payout.status", "=", "SUCCESS");})
            ->groupBy("sp_user.id")
            ->select("sp_user.*", "sp_cashoutinfo.bankName", "sp_cashoutinfo.bankAccount", "sp_cashoutinfo.status as bankStatus",
                DB::raw("SUM(sp_payout.amount) as cashOut"))->get();
        $lastUser = User::orderBy("id", "desc")->first();
        $statusConfig = Config::where("key", "=", "DefaultBankStatus")->first();
        $status = "";
        if ($statusConfig->value == 1) {
            $status = " checked";
        }
        return view('backend/user', ['allData' => $allData, 'status' => $status])->with(['page_title' => $page_title, 'page_title_main' => $page_title_main, 'lastUserId' => $lastUser->id]);
    }

    public function userDetail(){
        $page_title = "Selected User Information";
        $id = Input::get("id");
        $userData = User::find($id);
        $bankData = CashoutInfo::where("userId", "=", $id)->first();
        $gameTime = SinglePlayRecord::leftjoin("sp_advertisement", "sp_advertisement.id", "=", "sp_singerplayrecord.imageId")->where("userId", "=", $id)
            ->select("sp_advertisement.name", DB::raw("AVG(sp_singerplayrecord.timeActualPlay) as averageTimePlay"))->groupBy("imageId")->get();
        $prizeData = SinglePlayRecord::where("userId", "=", $id)->get();
        return view('backend/user_detail', ['data' => $userData, 'bank' => $bankData, 'gameTime' => $gameTime, 'prizeData' => $prizeData])->with(['page_title'=>$page_title]);
    }

    public function updateUser(){
        $id = Input::get("id");
        $specificData = User::find($id);
        $specificData->fullname = Input::get("fullName");
        $specificData->phone = Input::get("phone");
        $specificData->status = Input::get("userStatus");
        $specificData->gender = Input::get("gender");
        $specificData->age = Input::get("age");
        $this->validateUser($specificData);
        $bankData = CashoutInfo::where("userId", "=", $id)->first();
        $bankData->bankName = Input::get("bankName");
        $bankData->bankAccount = Input::get("bankAccount");
        $bankData->status = Input::get("bankStatus");
        $this->validateBank($bankData);
        $specificData->save();
        $bankData->save();
        echo json_encode(array("error" => 0));
    }

    public function validateUser($specificData){
        $validator = Validator::make(array("FullName" => $specificData->fullname),
            array("FullName" => "required|string|max:300"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['FullName'][0]));
            exit;
        }
        $validator = Validator::make(array("Status" => $specificData->status),
            array("Status" => "required|numeric|min:0|max:2"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Status'][0]));
            exit;
        }
        $validator = Validator::make(array("Gender" => $specificData->gender),
            array("Gender" => "required|numeric|min:0|max:1"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Gender'][0]));
            exit;
        }
        $validator = Validator::make(array("Age" => $specificData->age),
            array("Age" => "numeric"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Age'][0]));
            exit;
        }
    }

    public function validateBank($specificData){
        $validator = Validator::make(array("BankName" => $specificData->bankName),
            array("BankName" => "string|max:150"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['BankName'][0]));
            exit;
        }
        $validator = Validator::make(array("BankAccount" => $specificData->bankAccount),
            array("BankAccount" => "string|max:20"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['BankAccount'][0]));
            exit;
        }
        $validator = Validator::make(array("Status" => $specificData->status),
            array("Status" => "required|integer|min:0|max:3"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Status'][0]));
            exit;
        }
    }

    public function getUserFromId($id){
        $users = User::leftjoin("sp_cashoutinfo", "sp_user.id", "=", "sp_cashoutinfo.userId")->where("sp_user.id", ">", $id)->select("sp_user.*", "sp_cashoutinfo.bankName", "sp_cashoutinfo.bankAccount", "sp_cashoutinfo.status as bankStatus");
        $total = $users->count();
        if($total){
            $lastUser = User::orderBy("id", "desc")->first();
            echo json_encode(array("total" => $total, "data" => $users->get(), "lastId" => $lastUser->id));
        } else {
            echo json_encode(array("total" => 0));
        }
    }
}