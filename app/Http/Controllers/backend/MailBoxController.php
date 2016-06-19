<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 4/20/2016
 * Time: 3:31 PM
 */

namespace App\Http\Controllers\backend;


use App\Http\Controllers\Controller;
use App\Model\User;
use DB;
use Illuminate\Http\Request;

class MailBoxController extends Controller
{
    public function index(){
        $rs = DB::select('SELECT id, fullname FROM sp_user');
        return view('backend.mailbox')
            ->with('users', json_encode($rs, JSON_UNESCAPED_UNICODE));
    }

    public function sendMessage(Request $request){
        $to = $request['to'];
        $request['title'];
        $request['content'];
        if(empty($to)){
            DB::select('call sendMailBox(:userId, :title, :content, :now)',['userId'=>0, 'title'=>$request['title'], 'content'=>$request['content'], 'now'=>date('Y-m-d H:i:s')]);
        }else{
            $user = User::find($to);
            if(empty($user)){
                return response()->json(['s'=>0, 'msg'=>'User does not exist']);
            }else{
                DB::select('call sendMailBox(:userId, :title, :content, :now)',['userId'=>$user->id, 'title'=>$request['title'], 'content'=>$request['content'], 'now'=>date('Y-m-d H:i:s')]);
            }
        }
        return response()->json(['s'=>1, 'msg'=>'Sent']);
    }
}