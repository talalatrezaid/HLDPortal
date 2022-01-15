<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckEditUserMiddleware
{

    public function handle(Request $request, Closure $next)
    {
      $user = DB::table('rezaid_users')
            ->where('id', Auth::user()->id)
            ->get();


      $users = DB::table('rezaid_users')
                    ->join('user_roles', 'rezaid_users.role_id', '=', 'user_roles.id')
                    ->where('rezaid_users.id',Auth::user()->id)
                    ->get();


      if($user->isEmpty())
      {
        return redirect(''.env('ADMIN_PREFIX').'/users')->with('update_user_error1',"User Does not Exist!!");
      }

      elseif (Auth::user()->id == '1'|| (Auth::user()->id == '2' && ($users[0]->id != '1' && $users[0]->id != '2')) || (Auth::user()->user_name == $users[0]->user_name ))
      {
          $user = DB::table('rezaid_users')
            ->where('id', Auth::user()->id)
            ->get();
          $user_roles = DB::table('user_roles')
                        ->get();
          return response()->view('admin.pages.users.editusers',['users' => $user,'user_roles'=>$user_roles]);
      }
      else
      {
         return redirect(''.env('ADMIN_PREFIX').'/users')->with('update_user_error',"You Dont Have Permission to Update This User");
      }
        return $next($request);
    }
}
