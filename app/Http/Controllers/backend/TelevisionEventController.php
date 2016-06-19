<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/14/2015
 * Time: 4:09 PM
 */

namespace App\Http\Controllers\backend;
use App\Model\TelevisionEventAdvertisement;
use App\Model\TelevisionEventLeaderboard;
use Input;
use Validator;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TelevisionEventController extends Controller
{
    public function eventAdvertisement(){
        $page_title = "Event Adverstise Management";
        $allData = TelevisionEventAdvertisement::all();
        return view('backend/television_event_advertisement', ['allData' => $allData])->with(['page_title'=>$page_title]);
    }

    public function eventAdvertisementDetail(){
        $id = Input::get("id");
        if($id){
            $page_title = "Selected Event Advertisement Information";
            $specificData = TelevisionEventAdvertisement::find($id);
            return view('backend/television_event_advertisement_detail', ['data' => $specificData])->with(['page_title' => $page_title]);
        }
        $page_title = "Add New Event Advertisement";
        return view('backend/television_event_advertisement_detail', ['data' => new TelevisionEventAdvertisement()])->with(['page_title' => $page_title]);
    }

    public function insertEventAdvertisement(){
        $specificData = new TelevisionEventAdvertisement();
        $specificData->name = Input::get("name");
        $specificData->dateCreate = date("Y-m-d H:i:s");
        $specificData->dateStart = Input::get("start");
        $specificData->dateEnd = Input::get("end");
        $specificData->status = Input::get("status");
        $specificData->shareKind = Input::get("kind");
        if ($specificData->shareKind != 0) $specificData->shareContent = Input::get("content");
        $specificData->imageUrl01 = Input::get("image-1");
        $specificData->imageUrl02 = Input::get("image-2");
        $specificData->imageUrl03 = Input::get("image-3");
        $specificData->imageUrl04 = Input::get("image-4");
        $specificData->difficult01 = Input::get("difficult-1");
        $specificData->difficult02 = Input::get("difficult-2");
        $specificData->difficult03 = Input::get("difficult-3");
        $specificData->difficult04 = Input::get("difficult-4");
        $this->validateAdvertisement($specificData);
        $totalStage = 0;
        if (!empty($specificData->imageUrl01) && $specificData->imageUrl01 != "remove") {
            $specificData->imageUrl01 = $this->saveUploadedImage($specificData->imageUrl01);
            $totalStage++;
        } else {
            $specificData->imageUrl01 = null;
        }
        if (!empty($specificData->imageUrl02) && $specificData->imageUrl02 != "remove") {
            $specificData->imageUrl02 = $this->saveUploadedImage($specificData->imageUrl02);
            $totalStage++;
        } else {
            $specificData->imageUrl02 = null;
        }
        if (!empty($specificData->imageUrl03) && $specificData->imageUrl03 != "remove") {
            $specificData->imageUrl03 = $this->saveUploadedImage($specificData->imageUrl03);
            $totalStage++;
        } else {
            $specificData->imageUrl03 = null;
        }
        if (!empty($specificData->imageUrl04) && $specificData->imageUrl04 != "remove") {
            $specificData->imageUrl04 = $this->saveUploadedImage($specificData->imageUrl04);
            $totalStage++;
        } else {
            $specificData->imageUrl04 = null;
        }
        $specificData->totalStage = $totalStage;
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function saveEventAdvertisement(){
        $specificData = TelevisionEventAdvertisement::find(Input::get("id"));
        $specificData->name = Input::get("name");
        $specificData->dateCreate = date("Y-m-d H:i:s");
        $specificData->dateStart = Input::get("start");
        $specificData->dateEnd = Input::get("end");
        $specificData->status = Input::get("status");
        $specificData->shareKind = Input::get("kind");
        if ($specificData->shareKind != 0) $specificData->shareContent = Input::get("content");
        $specificData->difficult01 = Input::get("difficult-1");
        $specificData->difficult02 = Input::get("difficult-2");
        $specificData->difficult03 = Input::get("difficult-3");
        $specificData->difficult04 = Input::get("difficult-4");
        $image1 = Input::get("image-1");
        $image2 = Input::get("image-2");
        $image3 = Input::get("image-3");
        $image4 = Input::get("image-4");
        if(!empty($image1)) {
            if ($image1 != "remove") {
                $specificData->imageUrl01 = "new";
            } else {
                $specificData->imageUrl01 = null;
            }
        }
        if(!empty($image2)) {
            if ($image2 != "remove") {
                $specificData->imageUrl02 = "new";
            } else {
                $specificData->imageUrl02 = null;
            }
        }
        if(!empty($image3)) {
            if ($image3 != "remove") {
                $specificData->imageUrl03 = "new";
            } else {
                $specificData->imageUrl03 = null;
            }
        }
        if(!empty($image4)) {
            if ($image4 != "remove") {
                $specificData->imageUrl04 = "new";
            } else {
                $specificData->imageUrl04 = null;
            }
        }
        $this->validateAdvertisement($specificData);
        if ($specificData->imageUrl01 == "new") $specificData->imageUrl01 = $this->saveUploadedImage($image1);
        if ($specificData->imageUrl02 == "new") $specificData->imageUrl02 = $this->saveUploadedImage($image2);
        if ($specificData->imageUrl03 == "new") $specificData->imageUrl03 = $this->saveUploadedImage($image3);
        if ($specificData->imageUrl04 == "new") $specificData->imageUrl04 = $this->saveUploadedImage($image4);
        $totalStage = 0;
        if (!empty($specificData->imageUrl01)) $totalStage++;
        if (!empty($specificData->imageUrl02)) $totalStage++;
        if (!empty($specificData->imageUrl03)) $totalStage++;
        if (!empty($specificData->imageUrl04)) $totalStage++;
        $specificData->totalStage = $totalStage;
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function updateAdvertiseStatus() {
        $specificData = TelevisionEventAdvertisement::find(Input::get("id"));
        $specificData->status = filter_var(Input::get("status"), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $specificData->save();
        echo json_encode(array("status" => 1));
    }

    public function saveUploadedImage($base64String){
        $timeArr = explode(" ", microtime());
        $fileName = $timeArr[1] . $timeArr[0] * 1000000 . ".png";
        $filePath = env("UPLOAD_DIR") . $fileName;
        file_put_contents($filePath, base64_decode($base64String));
        return env("UPLOAD_URL") . $fileName;
    }

    public function saveThumbnailImage($base64String){
        $timeArr = explode(" ", microtime());
        $fileName = "thumb_" . $timeArr[1] . $timeArr[0] * 1000000 . ".png";
        $filePath = env("THUMB_DIR") . $fileName;
        file_put_contents($filePath, base64_decode($base64String));
        return env("THUMB_URL") . $fileName;
    }

    public function validateAdvertisement($specificData){
        $validator = Validator::make(array("Name" => $specificData->name),
            array("Name" => "required|string|max:30"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Name'][0]));
            exit;
        }
        $validator = Validator::make(array("StartDate" => $specificData->dateStart),
            array("StartDate" => "required|date_format:Y-m-d H:i:s"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['StartDate'][0]));
            exit;
        }
        $validator = Validator::make(array("EndDate" => $specificData->dateEnd),
            array("EndDate" => "required|date_format:Y-m-d H:i:s"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['EndDate'][0]));
            exit;
        }
        $validator = Validator::make(array("Status" => $specificData->status),
            array("Status" => "required|integer|min:0|max:1"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Status'][0]));
            exit;
        }
        $validator = Validator::make(array("ShareKind" => $specificData->shareKind),
            array("ShareKind" => "required|integer|min:0|max:2"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['ShareKind'][0]));
            exit;
        }
        if ($specificData->shareKind != 0) {
            $validator = Validator::make(array("ShareContent" => $specificData->shareContent),
                array("ShareContent" => "required|string|max:255"));
            if($validator->fails()){
                echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['ShareContent'][0]));
                exit;
            }
        }
        if ((empty($specificData->imageUrl01) || $specificData->imageUrl01 == "remove") &&
            ((!empty($specificData->imageUrl02) && $specificData->imageUrl02 != "remove") ||
            (!empty($specificData->imageUrl03) && $specificData->imageUrl03 != "remove") ||
            (!empty($specificData->imageUrl04) && $specificData->imageUrl04 != "remove"))
        ) {
            echo json_encode(array("error" => 1, "message" => "Stage 1 must not be empty."));
            exit;
        }
        if ((empty($specificData->imageUrl02) || $specificData->imageUrl02 == "remove") &&
            ((!empty($specificData->imageUrl03) && $specificData->imageUrl03 != "remove") ||
            (!empty($specificData->imageUrl04) && $specificData->imageUrl04 != "remove"))
        ) {
            echo json_encode(array("error" => 1, "message" => "Stage 2 must not be empty."));
            exit;
        }
        if ((empty($specificData->imageUrl03) || $specificData->imageUrl03 == "remove") &&
            (!empty($specificData->imageUrl04) && $specificData->imageUrl04 != "remove")
        ) {
            echo json_encode(array("error" => 1, "message" => "Stage 3 must not be empty."));
            exit;
        }
        if ((empty($specificData->imageUrl01) || $specificData->imageUrl01 == "remove") &&
            (empty($specificData->imageUrl02) || $specificData->imageUrl02 == "remove") &&
            (empty($specificData->imageUrl03) || $specificData->imageUrl03 == "remove") &&
            (empty($specificData->imageUrl04) || $specificData->imageUrl04 == "remove")) {
            echo json_encode(array("error" => 1, "message" => "Event must have at least 1 stage."));
            exit;
        }
    }

    public function eventLeaderboard(){
        $page_title = "Event Leaderboard Management";
        $advertiseList = TelevisionEventAdvertisement::orderBy("id", "asc")->select("id", "name")->get();
        $id = 0;
        $entirePublish = false;
        if (sizeof($advertiseList) > 0) {
            $advertiseId = Input::get("advertiseId");
            if (empty($advertiseId)) {
                $id = $advertiseList[0]->id;
            } else {
                $id = $advertiseId;
            }
            $allData = TelevisionEventLeaderboard::where("advId", "=", $id)->orderBy("timePlayed", "asc")->orderBy("dateUpdate", "asc")->get();
            $checkPublish = TelevisionEventLeaderboard::where("advId", "=", $id)->where("publish", "=", 0)->first();
            if (sizeof($checkPublish) > 0) {
                $entirePublish = true;
            }
        } else {
            $allData = [];
        }
        return view('backend/television_event_leaderboard', ['allData' => $allData, 'entirePublish' => $entirePublish])->with(['page_title' => $page_title, 'advertiseList' => $advertiseList, 'currentId' => $id]);
    }

    public function saveEventLeaderboard(){
        $id = Input::get("id");
        $specificData = TelevisionEventLeaderboard::find($id);
        $specificData->win = Input::get("win");
        $specificData->winValue = Input::get("value");
        $specificData->publish = Input::get("publish");
        if($specificData->win == 1)
            $specificData->dateConfirmed = date('Y-m-d H:i:s');
        $this->validateEventLeaderboard($specificData);
        $specificData->save();
        $advertiseId = empty(Input::get("advertiseId")) ? 0 : Input::get("advertiseId");
        $entirePublish = TelevisionEventLeaderboard::where("advId", "=", $advertiseId)->where("publish", "=", 0)->first();
        echo json_encode(array("error" => 0, "entirePublish" => sizeof($entirePublish) > 0 ? true : false));
    }

    public function validateEventLeaderboard($specificData){
        $validator = Validator::make(array("Winner" => $specificData->win),
            array("Winner" => "required|integer|min:0|max:2"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Winner'][0]));
            exit;
        }
        $validator = Validator::make(array("Publish" => $specificData->publish),
            array("Publish" => "required|integer|min:0|max:1"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Publish'][0]));
            exit;
        }
        if ($specificData->win == 1) {
            $validator = Validator::make(array("WinValue" => $specificData->winValue),
                array("WinValue" => "required|integer"));
            if ($validator->fails()) {
                echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['WinValue'][0]));
                exit;
            }
        } elseif ($specificData->win == 2) {
            $validator = Validator::make(array("WinValue" => $specificData->winValue),
                array("WinValue" => "required|string"));
            if ($validator->fails()) {
                echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['WinValue'][0]));
                exit;
            }
        }
    }

    public function publishEntireEventLeaderboard(){
        DB::select('Call publishEventLeaderBoard(?)', array(empty(Input::get("advertiseId")) ? 0 : Input::get("advertiseId")));
        /*
        $rs = DB::select('Call publishEventLeaderBoard()');
        if(empty($rs)) return response()->json(['status' => 0, 'error' => ['code' => 4005, 'message' => 'Hiện không có sự kiện']]);
        if($rs[0]->error == 1){ //ingame
            return response()->json(['status' => 0, 'error' => ['code' => 4005, 'message' => 'Hiện không có sự kiện']]);
        }*/
        echo json_encode(array("error" => 0));
    }
}