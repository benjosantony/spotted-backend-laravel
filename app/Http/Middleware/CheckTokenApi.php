<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;


class CheckTokenApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!empty($request->input('token'))){
            try {
                $token = Crypt::decrypt($request->input('token'));
                $val = explode('_', $token, 2);
                if(!session_id($val[0]))
                    session_start();
                if(!isset($_SESSION['token']) || $_SESSION['token'] != $request->input('token')){ //the $_SESSION['token'] was save at UserController > login > 39
                    return response()->json(['status'=>0, 'error'=>['code'=>8001, 'message'=>'session timeout']]);
                }
                $request->attributes->add(['userId' => $val[0], 'fbId'=>$val[1]]);
                return $next($request);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return response()->json(['status'=>0, 'error'=>['code'=>1, 'message'=>env(1)]]);
            }
        }
        if(strpos($request->getPathInfo(), '/api/user/login') !== false || strpos($request->getPathInfo(), '/api/version') !== false){
            return $next($request);
        }
        return response()->json(['status'=>0, 'error'=>['code'=>1, 'message'=>env(1)]]);
    }
}
