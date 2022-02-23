<?php

namespace App\Http\Controllers;

use App\Mail\NewSellerNotification;
use App\Mail\NewSellerRegistration;
use App\Models\User;
use App\UsersInsert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Mail\AdminPasswordReset;
use App\Mail\RoleChange;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Settings;

class AdminController extends Controller
{

  public function ShowLogin()
  {
    if (Auth::user() == null)
      return view('admin.pages.account.login');
    else
      return redirect('' . env('ADMIN_PREFIX') . '/dashboard');
  }
  public function registerUser()
  {
    if (CheckuserPermissions('register'))
      return redirect('' . env('ADMIN_PREFIX') . '/users')->with('add_user_faliure', "You don't have the permission to add a User!");
    $data = array();
    $data['user_roles'] = DB::table('user_roles')->get();
    return view('admin.pages.users.addusers', $data);
  }

  public function insert(Request $request)
  {
    if (CheckuserPermissions('register'))
      return redirect('' . env('ADMIN_PREFIX') . '/users')->with('add_user_faliure', "You don't have the permission to add a User!");
    $data                       = $request->all();
    $validator                  = Validator::make($request->all(), [
      'fname'                => 'required',
      'user_name'           => ['required', 'unique:rezaid_users'],
      'email'               => ['required', 'regex:/(.*)@(.*)\.(.*)/i', 'unique:rezaid_users'],
      'password'            => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/',],
      'retype_password'     => ['required', 'same:password'],
    ]);

    if ($validator->fails()) {
      return redirect('' . env('ADMIN_PREFIX') . '/users/register')
        ->withErrors($validator)
        ->with('insert_error1', $data['fname'])
        ->with('insert_error6', $data['role'])
        ->with('insert_error2', $data['user_name'])
        ->with('insert_error3', $data['email'])
        ->with('insert_error4', $data['password'])
        ->with('insert_error5', $data['retype_password']);
    } else {
      $password                         = Hash::make($data['password']);
      $rezaid_users                     = new UsersInsert;
      $rezaid_users->name               = $data['fname'];
      $rezaid_users->user_name          = $data['user_name'];
      $rezaid_users->email              = $data['email'];
      $rezaid_users->role_id            = $data['role'];
      $rezaid_users->password           = $password;
      $rezaid_users->save();
      return redirect('' . env('ADMIN_PREFIX') . '/users')->with('user_added', 'User Successfully Created');
    }
  }

  public function authenticate(Request $request)
  {
    $data             = $request->all();
    $validator        = Validator::make($request->all(), [
      'email'       => ['required'],
      'password'    => ['required'],
    ]);

    if ($validator->fails()) {
      return redirect('' . env('ADMIN_PREFIX') . '/login')
        ->withErrors($validator)
        ->with('login_error1', $data['email'])
        ->with('login_error2', $data['password']);
    } else {
      // Attempt to log the user in
      if (Auth::attempt(['user_name' => $data['email'], 'password' => $data['password']]) || Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
        // if successful, then redirect to their intended location
        return redirect('' . env('ADMIN_PREFIX') . '/dashboard');
      } else {
        session()->flash('Error Message', 'Incorrect email or password. Please provide valid credentials to login.');
        return redirect('' . env('ADMIN_PREFIX') . '/login');
      }
    }
  }

  public function logout(Request $request)
  {
    $request->session()->flush();
    return redirect('' . env('ADMIN_PREFIX') . '/login');
  }

  public function showusers()
  {

    $role_id = Auth::user()->role_id;
    $user_id = Auth::user()->id;
    $user_roles = DB::table('user_roles')->get();
    $users = DB::table('rezaid_users')
      ->where('id', $user_id)
      ->paginate(10);
    return view('admin.pages.users.index', ['users' => $users,  "content" => "users", 'user_roles' => $user_roles]);
  }

  public function edituser($id)
  {
    if (CheckuserPermissions('edit', $id))
      return redirect('' . env('ADMIN_PREFIX') . '/users')->with('update_user_error', "You Dont Have Permission to Update This User");
    $data = array();
    $exitId = User::where('id', $id)->first();
    if ($exitId) {
      $data['users'] = DB::table('rezaid_users')
        ->where('id', '=', $id)->get();
      $data['user_roles'] = DB::table('user_roles')->get();

      return view('admin.pages.users.editusers', $data);
    } else {
      return redirect()->back();
    }
  }

  public function updateuserdata(Request $request, $id)
  {
    if (CheckuserPermissions('edit', $id))
      return redirect('' . env('ADMIN_PREFIX') . '/users')->with('update_user_error', "You Dont Have Permission to Update This User");
    $data          = $request->all();
    $validator     = Validator::make($request->all(), [
      'name'  => 'required'
    ]);
    if ($validator->fails()) {
      return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)
        ->withErrors($validator);
    } else {
      if ($request->role) {
        $name       = $data['name'];
        $role_id    = $data['role'];
        $status     = $data['status_value'];
        DB::table('rezaid_users')
          ->where('id', $id)
          ->update(['name' => $name, 'role_id' => $role_id, 'status' => $status, 'updated_at' => date('Y-m-d H:i:s', time())]);
        return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)->with('user_update', "User Data Updated successfully");
      } else {
        $name   = $data['name'];
        $status = $data['status_value'];
        DB::table('rezaid_users')
          ->where('id', $id)
          ->update(['name' => $name, 'status' => $status, 'updated_at' => date('Y-m-d H:i:s', time())]);
        return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)->with('user_update', "User Data Updated successfully");
      }
    }
  }

  public function updateuserpassword(Request $request, $id)
  {
    if (CheckuserPermissions('edit', $id))
      return redirect('' . env('ADMIN_PREFIX') . '/users')->with('update_user_error', "You Dont Have Permission to Update This User");
    $users      = DB::table('rezaid_users')
      ->where('id', '=', $id)->get();

    $user_name  = session()->get('user_name');

    if ($users[0]->user_name == $user_name) {
      $data                   = $request->all();
      $validator              = Validator::make($request->all(), [
        'old_password'        => 'required',
        'password'            => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/',],
        'retype_password'     => ['required', 'same:password'],
      ]);

      if ($validator->fails()) {
        return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)
          ->withErrors($validator)
          ->with('user_password_error1', $data['old_password'])
          ->with('user_password_error2', $data['password'])
          ->with('user_password_error3', $data['retype_password']);
      } else {
        $query = DB::table('rezaid_users')
          ->where('id', '=', $id)
          ->where('password', '=', Hash::make($data['old_password']))->get();
        if (!$query->isEmpty()) {
          $name = $query[0]->name;
          $email = $query[0]->email;

          $user =
            [
              'name'  => $name,
              'email' => $email,
            ];

          Mail::to($email)
            ->send(new AdminPasswordReset($user));
          DB::table('rezaid_users')
            ->where('id', '=', $id)
            ->update(array(
              'password' => Hash::make($data['password']),
              'updated_at' => date('Y-m-d H:i:s', time())
            ));
          return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)
            ->with('user_password_reset', 'Password Successfully Updated!');
        } elseif ($query->isEmpty()) {
          return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)
            ->with('user_password_error0', 'You have provided wrong current password! Try Again!!')
            ->with('user_password_error1', $data['old_password'])
            ->with('user_password_error2', $data['password'])
            ->with('user_password_error3', $data['retype_password']);
        } else {
          return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)
            ->with('user_password_error', 'Password could not be updated! Try Again!!')
            ->with('user_password_error1', $data['old_password'])
            ->with('user_password_error2', $data['password'])
            ->with('user_password_error3', $data['retype_password']);
        }
      }
    } else {
      $data                   = $request->all();
      $validator              = Validator::make($request->all(), [
        'password'            => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/',],
        'retype_password'     => ['required', 'same:password'],
      ]);

      if ($validator->fails()) {
        return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)
          ->withErrors($validator)
          ->with('user_password_error2', $data['password'])
          ->with('user_password_error3', $data['retype_password']);
      } else {
        try {
          $query  = DB::table('rezaid_users')
            ->where('id', '=', $id)->get();
          $name   = $query[0]->name;
          $email  = $query[0]->email;

          $user =
            [
              'name'  => $name,
              'email' => $email,
            ];

          Mail::to($email)
            ->send(new AdminPasswordReset($user));

          DB::table('rezaid_users')
            ->where('id', '=', $id)
            ->update(array(
              'password' => Hash::make($data['password']),
              'updated_at' => date('Y-m-d H:i:s', time())
            ));
          return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)
            ->with('user_password_reset', 'Password Successfully Updated!');
        } catch (\Throwable $e) {
          return redirect('' . env('ADMIN_PREFIX') . '/users/edit/' . $id)
            ->with('user_password_error', 'Password Couldnot be Updated! Try Again!!')
            ->with('user_password_error1', $data['old_password'])
            ->with('user_password_error2', $data['password'])
            ->with('user_password_error3', $data['retype_password']);
        }
      }
    }
  }

  public function deleteuser($id)
  {
    if (CheckuserPermissions('delete', $id))
      return redirect('' . env('ADMIN_PREFIX') . '/users')->with('delete_users_faliure3', "You Dont Have Permission to Delete This User");
    try {
      DB::table('rezaid_users')
        ->where('id', $id)
        ->delete();
      return redirect('' . env('ADMIN_PREFIX') . '/users')->with('delete_user_success', "User Deleted successfully");
    } catch (\Throwable $e) {
      return redirect('' . env('ADMIN_PREFIX') . '/users')->with('delete_users_faliure', "User Deletion failed");
    }
  }

  public function updateStatus(Request $request)
  {


    $id     = $request->input('id');
    $status = $request->input('status');


    //if (CheckuserPermissions('edit',$id))
    //return redirect(''.env('ADMIN_PREFIX').'/users')->with('update_user_error',"You Dont Have Permission to Update This User");

    if ($id != '' && $status != '') {
      $query = DB::table('rezaid_users')
        ->where('id', $id)->get();

      DB::table('rezaid_users')
        ->where('id', '=', $id)
        ->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s', time())]);

      if ($status == '1') {

        $token =  md5(Str::random(50));
        $pass_reset_link = url('/') . "/reset-password/?t=" . $token;

        DB::table('rezaid_users')
          ->where('id', '=', $id)
          ->update(['token' => $token, 'updated_at' => date('Y-m-d H:i:s', time())]);

        echo 'The User has been successfully Enabled.';
        $name   = $query[0]->name;
        $email  = $query[0]->email;



        $user =
          [
            'name'    => $name,
            'email'   => $email,
            'token'   => $pass_reset_link,
            'body' => 'Your Account has been approved. You can set your password using this link!',
          ];

        Mail::to($email)
          ->send(new RoleChange($user));
      }

      /*else {
        echo 'The User has been successfully Disabled.';
        $name   = $query[0]->name;
        $email  = $query[0]->email;

        $user =
          [
            'name'    => $name,
            'email'   => $email,
            'subject' => 'Your Account has been blocked. Please contact Admin',
          ];

        Mail::to($email)
          ->send(new RoleChange($user));
      }*/
    } else {
      echo 'Status Update Error.';
    }
  }


  /**
   * Display seller registration form.
   * @param void
   * @return void
   */
  public function ShowSellerRegisterForm()
  {

    if (Auth::user() == null)
      return view('admin.pages.account.seller-registration');
    else
      return redirect('' . env('ADMIN_PREFIX') . '/dashboard');
  }


  /**
   * Seller registration form submission.
   * Method specifically used for seller registration
   * @param Request $request
   * @return void
   */
  public function insertNewSellerUser(Request $request)
  {

    $data = $request->all();
    $validator = Validator::make($request->all(), [

      'user_name'      => ['required', 'unique:rezaid_users'],
      'seller_plan'      => 'required',
      'email'     => ['required', 'regex:/(.*)@(.*)\.(.*)/i', 'unique:rezaid_users'],
      'seller_phone'     => 'required',
      'seller_address'   => 'required',
      //'seller_address2'  => 'required',
      'seller_city'      => 'required',
      'seller_state'     => 'required',
      'seller_country'   => 'required',
      'seller_zipcode'   => 'required',
      //'seller_vatnumber' => 'required',

    ]);

    if ($validator->fails()) {
      return redirect('' . env('ADMIN_PREFIX') . '/seller-register')
        ->withErrors($validator->errors())

        ->with('user_name_err',       $data['user_name'])
        ->with('seller_plan_err',     $data['seller_plan'])
        ->with('email_err',           $data['email'])
        ->with('seller_phone_err',    $data['seller_phone'])
        ->with('seller_address_err',  $data['seller_address'])
        ->with('seller_address2_err', $data['seller_address2'])
        ->with('seller_city_err',     $data['seller_city'])
        ->with('seller_state_err',    $data['seller_state'])
        ->with('seller_country_err',  $data['seller_country'])
        ->with('seller_zipcode_err',  $data['seller_zipcode'])
        ->with('seller_vatnumber_err', $data['seller_vatnumber']);
    } else {

      // Adding dummy password for DB insertion, actual password will be generated when admin approve this account.
      $password                       = Hash::make('Strongpassfhg');

      $rezaid_users                   = new UsersInsert;
      $rezaid_users->name             = 'FHG Seller';
      $rezaid_users->user_name        = $data['user_name'];
      $rezaid_users->password         = $password;
      $rezaid_users->seller_plan      = $data['seller_plan'];
      $rezaid_users->email            = $data['email'];
      $rezaid_users->seller_phone     = $data['seller_phone'];
      $rezaid_users->seller_address   = $data['seller_address'];
      $rezaid_users->seller_address2  = $data['seller_address2'];
      $rezaid_users->seller_city      = $data['seller_city'];
      $rezaid_users->seller_state     = $data['seller_state'];
      $rezaid_users->seller_country   = $data['seller_country'];
      $rezaid_users->seller_zipcode   = $data['seller_zipcode'];
      $rezaid_users->seller_vatnumber = $data['seller_vatnumber'];

      // In DB we have specific roleID(5) for sellers
      $rezaid_users->role_id            = '5';

      // Set user account status to in-active as admin will change status after approval
      $rezaid_users->status            = 0;

      $rezaid_users->save();

      // get website admin's email address from DB to send new seller notification
      $setting = DB::table('settings')->where('id', '=', 2)->get();
      $admin_email = $setting[0]->settings_value;

      $seller_email = $data['email'];

      $userinfo =
        [
          'name'    => $data['user_name'],
          'email'   => $data['email'],
          'body' => 'You have received new seller registration request and user\'s account is on hold for approval. ',
        ];

      Mail::to($admin_email)
        ->send(new NewSellerNotification($userinfo));

      $seller_mail =
        [
          'name'    => $data['user_name'],
          'email'   => $data['email'],
          'body' => 'Your account registration request has been received and pending for approval, We will let you know soon once approved.',
        ];


      Mail::to($seller_email)
        ->send(new NewSellerRegistration($seller_mail));

      return redirect('' . env('ADMIN_PREFIX') . '/seller-register')->with('user_added', 'Registration has been completed successfully, Access details will be share with you via email shortly');
    }
  }
}
