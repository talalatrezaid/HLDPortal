<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
  public function sendPasswordResetToken(Request $request)
    {

    $data             = $request->all();
    $validator        = Validator::make($request->all(), [
        'forgotemail'       => ['required'],
    ]);

    if ($validator->fails()) {
        return redirect(''.env('ADMIN_PREFIX').'/forget-password')
                      ->withErrors($validator)
                      ->with('forgot_error1',$data['forgotemail']);
    }

    else{
      $user =DB::table('rezaid_users')
                    ->where('email',  $data['forgotemail'] )->get();
      $user1 =DB::table('rezaid_users')
                    ->where('user_name', $data['forgotemail'] )->get();

      if (!$user->isEmpty()) {

        DB::table('rezaid_users')
                        ->where('email', '=',  $data['forgotemail'] )
                        ->update(array(
                                  'token' => md5(Str::random(50)),
                                  'updated_at'=>date('Y-m-d H:i:s', time())
                          ));
        $tokenData      = DB::table('rezaid_users')
                         ->where('email', $data['forgotemail'])->get();
        $token          = $tokenData[0]->token;
        // $link           = "<a href='/reset-password/?t=".$token."'>Click To Reset password</a>";
        $link1          = url('/')."/reset-password/?t=".$token;


        $name = $tokenData[0]->name;
        $email = $tokenData[0]->email;

        $user =
        [
          'name'  => $name,
          'email' => $email,
          'link'  => $link1
        ];

        Mail::to($email)
            ->send(new ForgotPassword($user));

        return redirect(''.env('ADMIN_PREFIX').'/forget-password')
                  ->with('forgot_success1','An Email has been sent to you!!');

      }
      elseif (!$user1->isEmpty())
      {
        DB::table('rezaid_users')
                        ->where('user_name', '=',  $data['forgotemail'] )
                        ->update(array(
                                  'token' => md5(Str::random(50)),
                                  'updated_at'=>date('Y-m-d H:i:s', time())
                          ));
        $tokenData      = DB::table('rezaid_users')
                         ->where('user_name', $data['forgotemail'])->get();
        $token          = $tokenData[0]->token;
        // $link           = "<a href='/reset-password/?t=".$token."'>Click To Reset password</a>";
        $link1          = url('/')."/reset-password/?t=".$token;

        $name = $tokenData[0]->name;
        $email = $tokenData[0]->email;

        $user =
        [
          'name'  => $name,
          'email' => $email,
          'link'  => $link1
        ];

        Mail::to($email)
            ->send(new ForgotPassword($user));
        return redirect(''.env('ADMIN_PREFIX').'/forget-password')
                          ->with('forgot_success1','An Email has been sent to you!!');
      }
      else
      {
        return redirect(''.env('ADMIN_PREFIX').'/forget-password')
                          ->with('forgot_error12','User Not Found! Try Again!!');
      }
      }
    }

  public function showPasswordResetForm(Request $request)
    {
      $tokenVal = $_GET['t'];
      $tokenData = DB::table('rezaid_users')
                          ->where('token', $tokenVal)->first();

     if ( !$tokenData ){return redirect()->to(''.env('ADMIN_PREFIX').'/login');}
     return view('admin.pages.account.recover-password')->with('tokenval',$tokenData->token);

    }

  public function resetPassword(Request $request)
    {
      $data             = $request->all();
      $validator        = Validator::make($request->all(), [
          'password'            => ['required','min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/','regex:/[@$!%*#?&]/',],
          'retype_password'     => ['required','same:password'],
      ]);

      if ($validator->fails()) {
          return view('admin.pages.account.recover-password')
                        ->withErrors($validator)
                        ->with('tokenval',$data['tokenval']);
      }

      else{


          $dbresult1 = DB::table('rezaid_users')
                            ->where('token', '=',   $data['tokenval'])
                            ->where('password', '=',   Hash::make($data['password']))->get();

        if(!$dbresult1->isEmpty()){
          return view('admin.pages.account.recover-password')
                          ->with('recover_error2','You cannot use the same password again.')
                          ->withErrors($validator)
                          ->with('tokenval',$data['tokenval']);
        }

        elseif ($dbresult1->isEmpty()) {
          DB::table('rezaid_users')
                        ->where('token', '=',   $data['tokenval'])
                        ->update(array(
                              'password' => Hash::make($data['password']),
                              'updated_at'=>date('Y-m-d H:i:s', time())
                          ));
          DB::table('rezaid_users')
                        ->where('token', $data['tokenval'])
                        ->update(array(
                            'token' => md5(Str::random(60)),
                            'updated_at'=>date('Y-m-d H:i:s', time())
                        ));
            return redirect(''.env('ADMIN_PREFIX').'/login')
                            ->with('recover_success','Password Successfully Updated!');
        }
        else{
          return view('admin.pages.account.recover-password')
                          ->withErrors($validator)
                          ->with('tokenval',$data['tokenval']);
        }

      }
    }
}
