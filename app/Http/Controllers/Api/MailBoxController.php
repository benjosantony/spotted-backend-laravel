<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 4/20/2016
 * Time: 1:19 PM
 */

namespace App\Http\Controllers\Api;


use DateTime;
use DateTimeZone;
use DB;
use Log;
use Illuminate\Http\Request;

class MailBoxController extends APIController
{
    public function listMailBoxForClient(Request $request){
        $userId = $request->attributes->get('userId');
        $dateTimeZoneSingapore = new DateTimeZone(date_default_timezone_get());
        $dateTimeSingapore = new DateTime("now", $dateTimeZoneSingapore);
        $timezone = (-1*$dateTimeZoneSingapore->getOffset($dateTimeSingapore)/3600) . ':00';
        $rs = DB::select('call getMailBox(:userId, :time)', ['userId'=>$userId, 'time'=>$timezone]);
        return response()->json(['status'=>1, 'data'=>['mails'=>$rs]]);
    }

    public function markRead(Request $request){
        $mailId = $request['mailId'];
        Log::info('markMailRead ' . $mailId . ' user: ' . $request->attributes->get('userId'));
        DB::select('call markMailRead(:userId, :mailId, :now);',['userId'=>$request->attributes->get('userId'), 'mailId'=>$mailId, 'now'=>date('Y-m-d H:i:s')]);
        return response()->json(['status'=>1]);
    }
}