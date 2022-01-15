<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckDeleteUserRole
{
    public function handle(Request $request, Closure $next)
  {
      $user = DB::table('rezaid_users')
                  ->where('id', Auth::user()->id)
                  ->get();

      $users = DB::table('rezaid_users')
                    ->join('user_roles', 'rezaid_users.role_id', '=', 'user_roles.user_role')
                    ->where('rezaid_users.id', Auth::user()->id)
                    ->get();

      if($user->isEmpty())
      {
        return redirect(''.env('ADMIN_PREFIX').'/users')->with('update_user_error1',"User Doesnot Exist!");
      }
      elseif (Auth::user()->id != '1' && (Auth::user()->id == '1' || (Auth::user()->id == '2' && ($users[0]->id != '1' && $users[0]->id != '2') )))
      {
          try
          {
            DB::table('rezaid_users')
              ->where('id', Auth::user()->id)
              ->delete();
            return redirect(''.env('ADMIN_PREFIX').'/users')->with('delete_user_success',"User Deleted successfully");
          }
          catch(\Throwable $e)
          {
            return redirect(''.env('ADMIN_PREFIX').'/users')->with('delete_users_faliure',"User Deletion failed");
          }
      }
      else
      {
        return redirect(''.env('ADMIN_PREFIX').'/users')->with('delete_users_faliure3',"You Dont Have Permission to Delete This User");
      }

    return $next($request);
  }
}
