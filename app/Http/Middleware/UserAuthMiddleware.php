<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class UserAuthMiddleware
{

  public function handle($request, Closure $next)
    {
      if (session()->has('id'))
      {
          return redirect(''.env('ADMIN_PREFIX').'/dashboard');
      }
      else
      {
         return response()->view('admin.pages.account.login');
      }

    return $next($request);
  }

}
