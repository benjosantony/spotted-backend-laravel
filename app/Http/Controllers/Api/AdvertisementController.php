<?php

/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 7/6/2015
 * Time: 4:10 PM
 */
namespace App\Http\Controllers\Api;

use App\Model\AcademyConfig;
use App\Model\User;
use DB;
use Illuminate\Http\Request;
use App\Model\SinglePlayRecord;
use App\Model\Advertisement;

class AdvertisementController extends APIController
{
    public function bookmarkAdv(Request $request)
    {
        $advId = $request->input('advId');
        $rs = DB::select('call bookmarkAdv(:userId, :advId, :date)', ['userId' => $request->attributes->get('userId'), 'advId' => $advId, 'date' => date('Y-m-d H:i:s')]);
        if(!empty($rs) && !empty($rs[0]->error)){
            if($rs[0]->error == -1)
                return response()->json(['status'=>1]);
            if ($rs[0]->error == 1) {
                return response()->json(['status'=>0, 'error'=>['code'=>2030,'message'=>env(2030)]]);
            }
            if ($rs[0]->error == 2) {
                return response()->json(['status'=>0, 'error'=>['code'=>2031,'message'=>env(2031)]]);
            }
        }
        return response()->json(['status'=>0, 'error'=>['code'=>2,'message'=>env(2)]]);
    }

    public function listAdv(Request $request)
    {
        $rs = DB::select('SELECT adverId as id, name, imageUrl, thumbUrl, dealValue as url, dealType as type, dealExpiration as expire, case when isUsed is null then 0 else 1 end as isUsed FROM sp_loveadvertisement WHERE adverId in (select id from sp_advertisement where status=1) and userId = :userId', ['userId' => $request->attributes->get('userId')]);
        return response()->json([
            'status'=>1,
            'data'=>[
                'advertisements'=>$rs
            ]
        ]);
    }

    public function useAdv(Request $request)
    {
        $advId = $request->input('adId');
        DB::select('UPDATE sp_loveadvertisement set isUsed = :date WHERE userId = :userId and adverId = :advId and isUsed is NULL ', ['date' => date('Y-m-d H:i:s'), 'userId' => $request->attributes->get('userId'), 'advId' => $advId]);
        return response()->json(['status'=>1]);
    }

    public function deleteAdv(Request $request)
    {
        $advId = $request->input('adId');
        $rs = DB::select('SELECT * FROM sp_loveadvertisement WHERE userId = :userId AND adverId = :advId', ['userId' => $request->attributes->get('userId'), 'advId' => $advId]);
        if (empty($rs)) {
            return response()->json(['status' => 0, 'error' => ['code' => 2031, 'message' => env(2032)]]);
        } else {
            DB::select('DELETE FROM sp_loveadvertisement WHERE userId = :userId AND adverId = :advId', ['userId' => $request->attributes->get('userId'), 'advId' => $advId]);
            return response()->json(['status' => 1]);
        }
    }
}