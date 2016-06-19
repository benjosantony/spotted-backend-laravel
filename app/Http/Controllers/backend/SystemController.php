<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/14/2015
 * Time: 4:09 PM
 */

namespace App\Http\Controllers\backend;
use App\Model\Advertisement;
use App\Model\Config;
use App\Model\Payout;
use App\Model\RingCaptchaSending;
use App\Model\SinglePlayRecord;
use App\Model\User;
use Crypt;
use Input;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function index(Request $request){
        if(!empty($request->cookie('token'))){
            $admin = Admin::where("username", "=", Crypt::decrypt($request->cookie('token')));
            if($admin->count()) {
                $totalUser = User::count();
                $totalGame = SinglePlayRecord::count();
                $totalAdvertise = Advertisement::count();
                $totalPayout = Payout::count();
                return view('backend/index')->with(["username" => $admin->first()->username, "totalUser" => $totalUser,
                    "totalGame" => $totalGame, "totalAdvertise" => $totalAdvertise, "totalPayout" => $totalPayout]);
            }
        }
        return view('backend/index');
    }

    public function login(){
        $admin = Admin::where("password", "=", md5(Input::get("password")));
        $username = $admin->first()->username;
        if($username == Input::get("username")){
            $response = new \Illuminate\Http\Response(json_encode(array("error" => 0)));
            $response->withCookie(cookie("token", Crypt::encrypt($username) , 0));
            return $response;
        } else {
            echo json_encode(array("error" => 1));
            exit;
        }
    }

    public function logout(){
        $response = new \Illuminate\Http\Response();
        $response->withCookie(cookie("token", "" , -1));
        return $response;
    }

    public function adminChangePassword(Request $request){
        if(!empty($request->cookie('token'))){
            $admin = Admin::where("username", "=", Crypt::decrypt($request->cookie('token')));
            if($admin->count()) {
                $admin = $admin->first();
                $admin->password = md5(Input::get("adminNewPassword"));
                $admin->save();
                echo json_encode(array("error" => 0));
                exit;
            } else {
                echo json_encode(array("error" => 1, "message" => "This admin is unavailable"));
                exit;
            }
        }
        return view('backend/index');
    }

    public function autoApproveBankStatus(){
        try {
            $config = Config::where("key", "=", "DefaultBankStatus")->first();
            $config->value = Input::get("status");
            $config->save();
            echo json_encode(array("error" => 0));
            exit;
        } catch (\Exception $e) {
            echo json_encode(array("error" => 1, "message" => $e->getMessage()));
            exit;
        }
    }

    public function visitorCountry(){
        $ringSending = RingCaptchaSending::groupBy("country")->select("country", DB::raw("COUNT(country) as total"))->get();
        echo json_encode($ringSending);
    }

    public function visitorPerDay(){
        $currentDate = date("Y-m-d H:i:s");
        $lastTwoWeeks = date("Y-m-d", strtotime($currentDate . "-2 weeks"));
        $userPerDay = User::where("dateCreate", ">", $lastTwoWeeks)
            ->groupBy(DB::raw("DATE(dateCreate)"))
            ->select(DB::raw("DATE(dateCreate) as CreatedDate, COUNT(*) as UserPerDay"))->get();
        $data = [];
        for ($i = 0; $i < 15; $i++) {
            $flag = true;
            $date = date("Y-m-d", strtotime($lastTwoWeeks . " +" . $i . " days"));
            foreach ($userPerDay as $user) {
                if ($date == $user->CreatedDate) {
                    array_push($data, $user->UserPerDay);
                    $flag = false;
                }
            }
            if ($flag) array_push($data, 0);
        }
        echo json_encode($data);
    }

    public function cashoutAndUser(){
        $currentDate = date("Y-m-d H:i:s");
        $currentMonth = date("Y-m-01", strtotime($currentDate));
        $previousYear = date("Y-m-01", strtotime($currentDate . "-1 year"));
        $cashoutInfo = Payout::where("status", "=", "SUCCESS")->where("dateSuccess", ">", $previousYear)->where("dateSuccess", "<", $currentMonth)
            ->groupBy(DB::raw("MONTH(dateSuccess)"))->select(DB::raw("MONTHNAME(dateSuccess) as Month, COUNT(*) as PayoutPerMonth"))->get();
        $userInfo = User::where("status", "=", 1)->where("dateCreate", ">", $previousYear)->where("dateCreate", "<", $currentMonth)
            ->groupBy(DB::raw("MONTH(dateCreate)"))->select(DB::raw("MONTHNAME(dateCreate) as Month, COUNT(*) as UserPerMonth"))->get();
        $data = [];
        for ($i = -12; $i < 0; $i++) {
            $date = date("F", strtotime($currentDate . " " . $i . " month"));
            $tmp["Month"] = $date;
            $cashFlag = true;
            foreach ($cashoutInfo as $cashout) {
                if ($cashout->Month == $date) {
                    $tmp["Cashout"] = $cashout->PayoutPerMonth;
                    $cashFlag = false;
                }
            }
            if ($cashFlag) $tmp["Cashout"] = 0;
            $userFlag = true;
            foreach ($userInfo as $user) {
                if ($user->Month == $date) {
                    $tmp["User"] = $user->UserPerMonth;
                    $userFlag = false;
                }
            }
            if ($userFlag) $tmp["User"] = 0;
            array_push($data, $tmp);
        }
        echo json_encode($data);
    }
}