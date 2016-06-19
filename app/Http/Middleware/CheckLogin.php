<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;
use App\Model\Admin;

class CheckLogin
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
        if(!empty($request->cookie('token'))){
            $admin = Admin::where("username", "=", Crypt::decrypt($request->cookie('token')));
            if($admin->count()) {
                return $next($request);
            }
        }
        if(strpos($request->getPathInfo(), '/admin/login') !== false){
            return $next($request);
        }
        return redirect("/admin/login");
    }
}
