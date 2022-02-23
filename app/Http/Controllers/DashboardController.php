<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardController extends Controller
{
  public function index()
  {
    $data = array();
    $data['users'] = User::count();
    $data["content"] = "dashboard";

    return view('admin.pages.dashboard.index', $data);
  }
}
