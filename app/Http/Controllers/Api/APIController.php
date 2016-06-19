<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 9/12/2015
 * Time: 2:02 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Crypt;
use DB;

class APIController extends Controller
{
    public function getToken($fbId){
        return Crypt::encrypt($fbId);
    }

    public function checkToken($token){
        try {
            $fbId = Crypt::decrypt($token);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return false;
        }
        $user = DB::select("SELECT id FROM sp_user WHERE fbId = :fbId limit 0, 1", ['fbId'=>$fbId]);
        return is_numeric($fbId) && !empty($user);
    }
}