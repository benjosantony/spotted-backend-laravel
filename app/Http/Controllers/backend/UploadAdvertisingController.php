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

class UploadAdvertisingController extends Controller
{
    public function index(){
        $page_title = "This is a page";
        return view('backend/test')->with(['page_title'=>$page_title]);
    }
}