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
use App\Model\AcademyConfig;
use App\Model\CompassRate;
use App\Model\DailyRewardConfig;
use App\Model\DockConfig;
use App\Model\FactoryConfig;
use App\Model\LevelConfig;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
class GameSettingsController extends Controller
{
    public function academyConfig(){
        $page_title = "Game Settings - Academy Config";
        $allData = AcademyConfig::all();
        return view('backend/academy_config', ['allData' => $allData])->with(['page_title'=>$page_title]);
    }

    public function insertAcademy(){
        $specificData = new AcademyConfig();
        $specificData->level = Input::get("level");
        $specificData->buildingToken = Input::get("token");
        $specificData->bonusRate = Input::get("rate");
        $this->validateAcademy($specificData);
        $specificData->save();
        echo json_encode($specificData);
    }

    public function updateAcademy(){
        $id = Input::get("id");
        $specificData = AcademyConfig::find($id);
        $specificData->level = Input::get("level");
        $specificData->buildingToken = Input::get("token");
        $specificData->bonusRate = Input::get("rate");
        $this->validateAcademy($specificData);
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function deleteAcademy($id){
        $specificData = AcademyConfig::find($id);
        $specificData->delete();
    }

    public function validateAcademy($specificData){
        $validator = Validator::make(array("Level" => $specificData->level),
            array("Level" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Level'][0]));
            exit;
        }
        $validator = Validator::make(array("BuildingToken" => $specificData->buildingToken),
            array("BuildingToken" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['BuildingToken'][0]));
            exit;
        }
        $validator = Validator::make(array("BonusRate" => $specificData->bonusRate),
            array("BonusRate" => "required|integer|min:0|max:100"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['BonusRate'][0]));
            exit;
        }
    }

    public function dockConfig(){
        $page_title = "Game Settings - Dock Config";
        $allData = DockConfig::all();
        return view('backend/dock_config', ['allData' => $allData])->with(['page_title'=>$page_title]);
    }

    public function insertDock(){
        $specificData = new DockConfig();
        $specificData->level = Input::get("level");
        $specificData->buildingToken = Input::get("token");
        $specificData->bonusShip = Input::get("ship");
        $this->validateDock($specificData);
        $specificData->save();
        echo json_encode($specificData);
    }

    public function updateDock(){
        $id = Input::get("id");
        $specificData = DockConfig::find($id);
        $specificData->level = Input::get("level");
        $specificData->buildingToken = Input::get("token");
        $specificData->bonusShip = Input::get("ship");
        $this->validateDock($specificData);
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function deleteDock($id){
        $specificData = DockConfig::find($id);
        $specificData->delete();
    }

    public function validateDock($specificData){
        $validator = Validator::make(array("Level" => $specificData->level),
            array("Level" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Level'][0]));
            exit;
        }
        $validator = Validator::make(array("BuildingToken" => $specificData->buildingToken),
            array("BuildingToken" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['BuildingToken'][0]));
            exit;
        }
        $validator = Validator::make(array("BonusShip" => $specificData->bonusShip),
            array("BonusShip" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['BonusShip'][0]));
            exit;
        }
    }

    public function factoryConfig(){
        $page_title = "Game Settings - Factory Config";
        $allData = FactoryConfig::all();
        return view('backend/factory_config', ['allData' => $allData])->with(['page_title'=>$page_title]);
    }

    public function insertFactory(){
        $specificData = new FactoryConfig();
        $specificData->level = Input::get("level");
        $specificData->buildingToken = Input::get("token");
        $specificData->reducePercent = Input::get("time");
        $this->validateFactory($specificData);
        $specificData->reduceTime = $specificData->reducePercent * 18;
        $specificData->save();
        echo json_encode($specificData);
    }

    public function updateFactory(){
        $id = Input::get("id");
        $specificData = FactoryConfig::find($id);
        $specificData->level = Input::get("level");
        $specificData->buildingToken = Input::get("token");
        $specificData->reducePercent = Input::get("time");
        $this->validateFactory($specificData);
        $specificData->reduceTime = $specificData->reducePercent * 18;
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function deleteFactory($id){
        $specificData = FactoryConfig::find($id);
        $specificData->delete();
    }

    public function validateFactory($specificData){
        $validator = Validator::make(array("Level" => $specificData->level),
            array("Level" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Level'][0]));
            exit;
        }
        $validator = Validator::make(array("BuildingToken" => $specificData->buildingToken),
            array("BuildingToken" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['BuildingToken'][0]));
            exit;
        }
        $validator = Validator::make(array("TimeReduction" => $specificData->reducePercent),
            array("TimeReduction" => "required|integer|min:0|max:100"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['TimeReduction'][0]));
            exit;
        }
    }

    public function levelConfig(){
        $page_title = "Game Settings - Level Config";
        $allData = LevelConfig::all();
        return view('backend/level_config', ['allData' => $allData])->with(['page_title'=>$page_title]);
    }

    public function insertLevel(){
        $specificData = new LevelConfig();
        $specificData->level = Input::get("level");
        $specificData->buildingToken = Input::get("token");
        $specificData->fromExp = Input::get("from");
        $specificData->toExp = Input::get("to");
        $this->validateLevel($specificData);
        $specificData->save();
        echo json_encode($specificData);
    }

    public function updateLevel(){
        $id = Input::get("id");
        $specificData = LevelConfig::find($id);
        $specificData->level = Input::get("level");
        $specificData->buildingToken = Input::get("token");
        $specificData->fromExp = Input::get("from");
        $specificData->toExp = Input::get("to");
        $this->validateLevel($specificData);
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function deleteLevel($id){
        $specificData = LevelConfig::find($id);
        $specificData->delete();
    }

    public function validateLevel($specificData){
        $validator = Validator::make(array("Level" => $specificData->level),
            array("Level" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Level'][0]));
            exit;
        }
        $validator = Validator::make(array("BuildingToken" => $specificData->buildingToken),
            array("BuildingToken" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['BuildingToken'][0]));
            exit;
        }
        $validator = Validator::make(array("FromExperience" => $specificData->fromExp),
            array("FromExperience" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['FromExperience'][0]));
            exit;
        }
        $validator = Validator::make(array("ToExperience" => $specificData->toExp),
            array("ToExperience" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['ToExperience'][0]));
            exit;
        }
        if($specificData->fromExp > $specificData->toExp){
            echo json_encode(array("error" => 1, "message" => "From experience can not be greater than to experience."));
            exit;
        }
    }

    public function compassRate(){
        $page_title = "Game Settings - Compass Rate";
        $allData = CompassRate::all();
        return view('backend/compass_rate', ['allData' => $allData])->with(['page_title'=>$page_title]);
    }

    public function insertCompass(){
        $specificData = new CompassRate();
        $specificData->multiple = Input::get("multiple");
        $specificData->rate = Input::get("rate");
        $this->validateCompass($specificData);
        $specificData->save();
        echo json_encode($specificData);
    }

    public function updateCompass(){
        $id = Input::get("id");
        $specificData = CompassRate::find($id);
        $specificData->multiple = Input::get("multiple");
        $specificData->rate = Input::get("rate");
        $this->validateCompass($specificData);
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function deleteCompass($id){
        $specificData = CompassRate::find($id);
        $specificData->delete();
    }

    public function validateCompass($specificData){
        $validator = Validator::make(array("Multiplier" => $specificData->multiple),
            array("Multiplier" => "required|integer|min:0|max:9"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Multiplier'][0]));
            exit;
        }
        $validator = Validator::make(array("WinningRate" => $specificData->rate),
            array("WinningRate" => "required|integer|min:0|max:100"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['WinningRate'][0]));
            exit;
        }
    }

    public function dailyRewardConfig(){
        $page_title = "Game Settings - Daily Reward Config";
        $allData = DailyRewardConfig::all();
        return view('backend/daily_reward_config', ['allData' => $allData])->with(['page_title'=>$page_title]);
    }

    /*public function insertReward(){
        $specificData = new DailyRewardConfig();
        $specificData->name = Input::get("name");
        $specificData->rate = Input::get("rate");
        $specificData->minValue = Input::get("min");
        $specificData->maxValue = Input::get("max");
        $this->validateReward($specificData);
        $specificData->save();
        echo json_encode($specificData);
    }*/

    public function updateReward(){
        $id = Input::get("id");
        $specificData = DailyRewardConfig::find($id);
        //$specificData->name = Input::get("name");
        $specificData->rate = Input::get("rate");
        $specificData->minValue = Input::get("min");
        $specificData->maxValue = Input::get("max");
        $this->validateReward($specificData);
        $specificData->save();
        echo json_encode(array("error" => 0));
    }

    public function deleteReward($id){
        $specificData = DailyRewardConfig::find($id);
        $specificData->delete();
    }

    public function validateReward($specificData){
        /*$validator = Validator::make(array("Name" => $specificData->name),
            array("Name" => "required|string|min:0|max:30"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['Name'][0]));
            exit;
        }*/
        $validator = Validator::make(array("AppearanceRate" => $specificData->rate),
            array("AppearanceRate" => "required|integer|min:0|max:100"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['AppearanceRate'][0]));
            exit;
        }
        $validator = Validator::make(array("MinimumValue" => $specificData->minValue),
            array("MinimumValue" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['MinimumValue'][0]));
            exit;
        }
        $validator = Validator::make(array("MaximumValue" => $specificData->maxValue),
            array("MaximumValue" => "required|integer|min:0"));
        if($validator->fails()){
            echo json_encode(array("error" => 1, "message" => $validator->messages()->getMessages()['MaximumValue'][0]));
            exit;
        }
        if($specificData->minValue > $specificData->maxValue){
            echo json_encode(array("error" => 1, "message" => "Minimum value can not be greater than maximum value."));
            exit;
        }
    }
}