<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckAddUserRole
{
    public function handle(Request $request, Closure $next)
  {
      if (Auth::user()->id == '1' || Auth::user()->id == '2')
      {
          $user_roles = DB::table('user_roles')->get();

          return response()->view('admin.pages.users.addusers',['user_roles'=>$user_roles]);
      }
      else
      {
         return redirect(''.env('ADMIN_PREFIX').'/users')->with('add_user_faliure',"You don't have the permission to add a User!");
      }

    return $next($request);
  }
}
