<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Session;
use DB;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    $user_modules = DB::table('user_roles')
      ->where('id', Auth::user()->role_id)
      ->get();
    $allowed_modules = explode(',', $user_modules[0]->module);
    if($request->segment(1) == env('ADMIN_PREFIX'))
    $module_id = DB::table('modules')->where('slug', $request->segment(2))->pluck('id')->first();
    else
    $module_id = DB::table('modules')->where('slug', $request->segment(1))->pluck('id')->first();
    if (!in_array($module_id, $allowed_modules) && $module_id <> NULL)
      return redirect(''.env('ADMIN_PREFIX').'/dashboard')->with('not_allowed', 'Sorry! You don\'t have permission to access the module.');
    return $next($request);
  }
}
