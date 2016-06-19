<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/14/2015
 * Time: 4:09 PM
 */

namespace App\Http\Controllers\backend;
use Input;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Advertisement;
use App\Model\Question;
use App\Model\QuestionAnswer;
use DB;

class AdvertisementController extends Controller
{
    public function advertisement(){
        $page_title = "Adverstise Management";
        $allData = Advertisement::all();
        return view('backend/advertisement', ['allData' => $allData])->with(['page_title'=>$page_title]);
    }

    public function advertisementDetail(){
        $sub_page_title = "Quizzes Of This Advertisement";
        $id = Input::get("id");
        if($id){
            $page_title = "Selected Advertisement Information";
            $specificData = Advertisement::find($id);
            $allData = Question::where("advertiseId", "=", $id)->get();
            return view('backend/advertisement_detail', ['data' => $specificData, 'allData' => $allData])->with(['page_title' => $page_title, 'sub_page_title' => $sub_page_title]);
        }
        $page_title = "Add New Advertisement";
        return view('backend/advertisement_detail', ['data' => new Advertisement()])->with(['page_title' => $page_title, 'sub_page_title' => $sub_page_title]);
    }

    public function insertAdvertisement(){
        $specificData = new Advertisement();
        $specificData->name = Input::get("name");
        $specificData->date = date("Y-m-d H:i:s");
        $specificData->timeToPlay = Input::get("time");
        $specificData->firstKey = Input::get("firstKey");
        $specificData->secondKey = Input::get("secondKey");
        $specificData->thirdKey = Input::get("thirdKey");
        $specificData->expRate = Input::get("expRate");
        $specificData->expMin = Input::get("expMin");
        $specificData->expMax = Input::get("expMax");
        $specificData->cashRate = Input::get("cashRate");
        $specificData->cashMin = Input::get("cashMin");
        $specificData->cashMax = Input::get("cashMax");
        $specificData->tokenRate = Input::get("tokenRate");
        $specificData->tokenMin = Input::get("tokenMin");
        $specificData->tokenMax = Input::get("tokenMax");
        $specificData->trashRate = Input::get("trashRate");
        $specificData->dealType = Input::get("dealType");
        $specificData->dealValue = Input::get("dealValue");
        $specificData->dealExpiration = !empty(Input::get("dealExpiration")) ? Input::get("dealExpiration") : null;
        $specificData->status = Input::get("status");
        $specificData->imageUrl = Input::get("image");
        $this->validateAdvertisement($specificData);
        $specificData->imageUrl = $this->saveUploadedImage(Input::get("image"));
        $specificData->thumbUrl = $this->saveThumbnailImage(Input::get("thumbnail"));
        $specificData->save();
        DB::select('CALL randomCompetitor(:advId, :currentDate)', ['advId' =>$specificData->id, 'currentDate'=>date('Y-m-d H:i:s')]);
        echo json_encode(array("error" => 0));
    }

    public function saveAdvertisement(){
        $specificData = Advertisement::find(Input::get("id"));
        $specificData->name = Input::get("name");
        $specificData->lastUpdate = date("Y-m-d H:i:s");
        $specificData->timeToPlay = Input::get("time");
        $specificData->firstKey = Input::get("firstKey");
        $specificData->secondKey = Input::get("secondKey");
        $specificData->thirdKey = Input::get("thirdKey");
        $specificData->expRate = Input::get("expRate");
        $specificData->expMin = Input::get("expMin");
        $specificData->expMax = Input::get("expMax");
        $specificData->cashRate = Input::get("cashRate");
        $specificData->cashMin = Input::get("cashMin");
        $specificData->cashMax = Input::get("cashMax");
        $specificData->tokenRate = Input::get("tokenRate");
        $specificData->tokenMin = Input::get("tokenMin");
        $specificData->tokenMax = Input::get("tokenMax");
        $specificData->trashRate = Input::get("trashRate");
        $specificData->dealType = Input::get("dealType");
        $specificData->dealValue = Input::get("dealValue");
        $specificData->dealExpiration = !empty(Input::get("dealExpiration")) ? Input::get("dealExpiration") : null;
        $specificData->status = Input::get("status");
        $this->validateAdvertisement($specificData);
        $image = Input::get("image");
        if (!empty($image)) {
            $specificData->imageUrl = $this->saveUploadedImage($image);
            $specificData->thumbUrl = $this->saveThumbnailImage(Input::get("thumbnail"));
        }
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function updateAdvertiseStatus() {
        $specificData = Advertisement::find(Input::get("id"));
        $specificData->status = filter_var(Input::get("status"), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $specificData->save();
        echo json_encode(array("status" => 1));
    }

    public function saveUploadedImage($base64String){
        $fileName = date("YmdHis") . ".png";
        $filePath = env("UPLOAD_DIR") . $fileName;
        file_put_contents($filePath, base64_decode($base64String));
        return env("UPLOAD_URL") . $fileName;
    }

    public function saveThumbnailImage($base64String){
        $fileName = "thumb_" . date("YmdHis") . ".png";
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
        $validator = Validator::make(array("TimePlay" => $specificData->timeToPlay),
            array("TimePlay" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['TimePlay'][0]));
            exit;
        }
        $validator = Validator::make(array("Date" => $specificData->date),
            array("Date" => "date_format:Y-m-d H:i:s"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Date'][0]));
            exit;
        }
        $validator = Validator::make(array("FirstKey" => $specificData->firstKey),
            array("FirstKey" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['FirstKey'][0]));
            exit;
        }
        if($specificData->firstKey > $specificData->timeToPlay){
            echo json_encode(array("error" => 1, "message" => "Time drop first key must not be greater than time play."));
            exit;
        }
        $validator = Validator::make(array("SecondKey" => $specificData->secondKey),
            array("SecondKey" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['SecondKey'][0]));
            exit;
        }
        if($specificData->secondKey > $specificData->timeToPlay){
            echo json_encode(array("error" => 1, "message" => "Time drop second key must not be greater than time play."));
            exit;
        }
        $validator = Validator::make(array("ThirdKey" => $specificData->thirdKey),
            array("ThirdKey" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['ThirdKey'][0]));
            exit;
        }
        if($specificData->thirdKey > $specificData->timeToPlay){
            echo json_encode(array("error" => 1, "message" => "Time drop third key must not be greater than time play."));
            exit;
        }
        if($specificData->firstKey >= $specificData->secondKey){
            echo json_encode(array("error" => 1, "message" => "Time drop first key must be less than second key."));
            exit;
        }
        if($specificData->secondKey >= $specificData->thirdKey){
            echo json_encode(array("error" => 1, "message" => "Time drop second key must be less than third key."));
            exit;
        }
        $validator = Validator::make(array("ExperienceRate" => $specificData->expRate),
            array("ExperienceRate" => "required|integer|min:0|max:100"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['ExperienceRate'][0]));
            exit;
        }
        $validator = Validator::make(array("MinimumExperience" => $specificData->expMin),
            array("MinimumExperience" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['MinimumExperience'][0]));
            exit;
        }
        $validator = Validator::make(array("MaximumExperience" => $specificData->expMax),
            array("MaximumExperience" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['MaximumExperience'][0]));
            exit;
        }
        if($specificData->expMin > $specificData->expMax){
            echo json_encode(array("error" => 1, "message" => "Minimum experience must not be greater than maximum experience."));
            exit;
        }
        $validator = Validator::make(array("CashRate" => $specificData->cashRate),
            array("CashRate" => "required|integer|min:0|max:100"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['CashRate'][0]));
            exit;
        }
        $validator = Validator::make(array("MinimumCash" => $specificData->cashMin),
            array("MinimumCash" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['MinimumCash'][0]));
            exit;
        }
        $validator = Validator::make(array("MaximumCash" => $specificData->cashMax),
            array("MaximumCash" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['MaximumCash'][0]));
            exit;
        }
        if($specificData->cashMin > $specificData->cashMax){
            echo json_encode(array("error" => 1, "message" => "Minimum cash must not be greater than maximum cash."));
            exit;
        }
        $validator = Validator::make(array("TokenRate" => $specificData->tokenRate),
            array("TokenRate" => "required|integer|min:0|max:100"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['TokenRate'][0]));
            exit;
        }
        $validator = Validator::make(array("MinimumToken" => $specificData->tokenMin),
            array("MinimumToken" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['MinimumToken'][0]));
            exit;
        }
        $validator = Validator::make(array("MaximumToken" => $specificData->tokenMax),
            array("MaximumToken" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['MaximumToken'][0]));
            exit;
        }
        if($specificData->tokenMin > $specificData->tokenMax){
            echo json_encode(array("error" => 1, "message" => "Minimum token must not be greater than maximum token."));
            exit;
        }
        if($specificData->expRate + $specificData->cashRate + $specificData->tokenRate + $specificData->trashRate != 100){
            echo json_encode(array("error" => 1, "message" => "Total exp/cash/token/trash rate must be equal to 100."));
            exit;
        }
        $validator = Validator::make(array("DealType" => $specificData->dealType),
            array("DealType" => "required|integer|min:0|max:4"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['DealType'][0]));
            exit;
        }
        $validator = Validator::make(array("DealValue" => $specificData->dealValue),
            array("DealValue" => "string|max:255"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['DealValue'][0]));
            exit;
        }
        if(!isset($specificData->id)) {
            $validator = Validator::make(array("Image" => $specificData->imageUrl),
                array("Image" => "required"));
            if ($validator->fails()) {
                echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Image'][0]));
                exit;
            }
        }
        $validator = Validator::make(array("Status" => $specificData->status),
            array("Status" => "required|integer|min:0|max:1"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Status'][0]));
            exit;
        }
        if (!empty($specificData->dealExpiration)) {
            $validator = Validator::make(array("DealExpiration" => $specificData->dealExpiration),
                array("DealExpiration" => "date_format:Y-m-d H:i:s"));
            if ($validator->fails()) {
                echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['DealExpiration'][0]));
                exit;
            }
        }
    }

    public function question(){
        $sub_page_title = "Quizzes Of This Advertisement";
        $id = Input::get("id");
        $allData = Question::where("advertiseId", "=", $id)->get();
        return view('backend/question', ['allData' => $allData])->with(['sub_page_title' => $sub_page_title, 'advertiseId' => $id]);
    }

    public function insertQuestion(){
        $specificData = new Question();
        $specificData->title = Input::get("title");
        $specificData->dateFrom = Input::get("from");
        $specificData->dateTo = Input::get("to");
        $specificData->status = Input::get("status");
        $specificData->advertiseId = Input::get("advertiseId");
        $this->validateQuestion($specificData);
        $specificData->save();
        echo json_encode($specificData);
    }

    public function updateQuestion(){
        $id = Input::get("id");
        $specificData = Question::find($id);
        $specificData->title = Input::get("title");
        $specificData->dateFrom = Input::get("from");
        $specificData->dateTo = Input::get("to");
        $specificData->status = Input::get("status");
        $this->validateQuestion($specificData);
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function deleteQuestion($id){
        $specificData = Question::find($id);
        $specificData->delete();
    }

    public function validateQuestion($specificData){
        $validator = Validator::make(array("Title" => $specificData->title),
            array("Title" => "required|string|max:1000"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Title'][0]));
            exit;
        }
        $validator = Validator::make(array("DateFrom" => $specificData->dateFrom),
            array("DateFrom" => "required|date_format:Y-m-d H:i:s"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['DateFrom'][0]));
            exit;
        }
        $validator = Validator::make(array("DateTo" => $specificData->dateTo),
            array("DateTo" => "required|date_format:Y-m-d H:i:s"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['DateTo'][0]));
            exit;
        }
        $validator = Validator::make(array("Status" => $specificData->status),
            array("Status" => "required|integer|min:0|max:1"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Status'][0]));
            exit;
        }
        if(strtotime($specificData->dateFrom) > strtotime($specificData->dateTo)){
            echo json_encode(array("error" => 1, "message" => "From date must not be greater than to date."));
            exit;
        }
    }

    public function questionAnswer(){
        $sub_page_title = "Answers Of Selected Quiz";
        $questionId = Input::get("questionId");
        $allData = QuestionAnswer::where("questionId", "=", $questionId)->get();
        return view('backend/question_answer', ['allData' => $allData])->with(['sub_page_title' => $sub_page_title, 'questionId' => $questionId, 'advertiseId' => Input::get("advertiseId")]);
    }

    public function insertQuestionAnswer(){
        $specificData = new QuestionAnswer();
        $specificData->title = Input::get("title");
        $specificData->correct = Input::get("correct");
        $specificData->questionId = Input::get("questionId");
        $this->validateQuestionAnswer($specificData);
        $specificData->save();
        echo json_encode($specificData);
    }

    public function updateQuestionAnswer(){
        $id = Input::get("id");
        $specificData = QuestionAnswer::find($id);
        $specificData->title = Input::get("title");
        $specificData->correct = Input::get("correct");
        $this->validateQuestionAnswer($specificData);
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function deleteQuestionAnswer($id){
        $specificData = QuestionAnswer::find($id);
        $specificData->delete();
    }

    public function validateQuestionAnswer($specificData){
        $validator = Validator::make(array("Title" => $specificData->title),
            array("Title" => "required|string|max:1000"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Title'][0]));
            exit;
        }
        $validator = Validator::make(array("Correction" => $specificData->correct),
            array("Correction" => "required|integer|min:0|max:1"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Correction'][0]));
            exit;
        }
    }
}