<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRoles extends Controller
{
    public function showUserRoles()
    {
    	$user_roles 				    = DB::table('user_roles')->get();
		$modules 						= DB::table('modules')->get();
			// $admin_modules 			= explode(',', $user_roles[1]->module);
			// $editor_modules			= explode(',', $user_roles[2]->module);
			
			return view('admin.pages.user_roles.index')
											->with('userroles',$user_roles)
											->with('modules',$modules);
											// ->with('admin_modules',$admin_modules)
											// ->with('editor_modules',$editor_modules);
    }

    public function change_user_access(Request $request)
    {
    	$user_id 		= $request->input('user_id');
    	$module_id 		= $request->input('module_id');
    	$module_name 	= $request->input('module_name');

    	$Arr[]			= $module_id;

    	$user_roles 	=	DB::table('user_roles')
    							->where('id','=',$user_id)->get();

    	$user_roles_modules 	= $user_roles[0]->module;
    	$role_modules_array  	= explode(',', $user_roles_modules);

    	$final_module_ids = '';
    	
    	if($user_id !='' && $module_id != '' && $module_name != '')
    	{
    		if (in_array($module_id, $role_modules_array))
    		{
    			$final_module_id_array = array_diff($role_modules_array, $Arr);

    			foreach ($final_module_id_array as $value) {
    				$final_module_ids = $final_module_ids.','.$value;	
    			}
    			
    			if(empty($final_module_id_array))
    			{
    				DB::table('user_roles')
    					->where('id','=',$user_id)
    					->update(['module'=>'','updated_at'=>date('Y-m-d H:i:s', time())]);
    			}
    			else
    			{
    			$final_module_ids = substr($final_module_ids, 1); 
    			DB::table('user_roles')
    					->where('id','=',$user_id)
    					->update(['module'=>$final_module_ids,'updated_at'=>date('Y-m-d H:i:s', time())]);
    			}
    			return response()->json([
	            'isSuccessful' => 'Disabled',
	          	'module'			 =>  $module_name,
	          ]);
    		}
    		else
    		{
    			
    			if(empty($user_roles_modules))
    			{
    				DB::table('user_roles')
    					->where('id','=',$user_id)
    					->update(['module'=>$module_id,'updated_at'=>date('Y-m-d H:i:s', time())]);
	    			return response()->json([
		            'isSuccessful' => 'Enabled',
		          	'module'			 =>  $module_name,
		          ]);
    			}

    			else
    			{

	    			array_push($role_modules_array, $module_id);
	    			foreach ($role_modules_array as $value) {
	    				$user_roles_modules_1 = $user_roles_modules.','.$value;
	    			}
	    			
	    			DB::table('user_roles')
	    					->where('id','=',$user_id)
	    					->update(['module'=>$user_roles_modules_1,'updated_at'=>date('Y-m-d H:i:s', time())]);
	    			return response()->json([
		            'isSuccessful' => 'Enabled',
		          	'module'			 =>  $module_name,
		          ]);
    			}

    		}
    	}
    	
    	else
    	{
    		return response()->json([
	            'isSuccessful' => 'No',
	          ]);
    	}
    }
}
