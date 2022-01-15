<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{

    /**
     * Show store connection page / form
     * @param null
     * @return null
     */
    public function show(){

        // check if user is authenticate and login
        if (Auth::user() == null){

            return view('admin.pages.account.login');
        }
        else{

            // get store connection data using relationship between User and Store
            $store_data = User::find(Auth::user()->id)->userStores()->get();

            // if user already has store data then $store_data may have info array otherwise $store_data is empty and will be treated as new record
            return view('admin.pages.store_connection.index', [
                'store_data' => $store_data
            ]);
        }
    }

    /**
     * Update or add store information
     * @param $request
     * @return null
     */
    public function update( Request $request){
        $data              = $request->all();
        $validator         = Validator::make($request->all(), [
            'name'         => 'required',
            'api_key'      => 'required',
            'api_password' => 'required',
            'api_domain'   => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('' . env('ADMIN_PREFIX') . '/store_connection')->withErrors($validator);
        }else{

            // if request variable contain store_id means its update request
            if ($request->store_id){

                $store_data = Store::find($request->store_id);
                $store_data->name = $request->name;
                $store_data->api_key = $request->api_key;
                $store_data->api_password = $request->api_password;
                $store_data->api_domain = $request->api_domain;
                $store_data->is_active = "0";
                $store_data->save();

            }
            // no store_id in request variable mean its new record request
            else{

                $store_data = new Store;
                $store_data->user_id = Auth::user()->id;
                $store_data->name = $request->name;
                $store_data->api_key = $request->api_key;
                $store_data->api_password = $request->api_password;
                $store_data->api_domain = $request->api_domain;
                $store_data->base_url = 'admin/api/2021-04';
                $store_data->save();
            }

            // verify the provided information
            $is_varified = $this->verify_connection();

            if ($is_varified){
                return redirect('' . env('ADMIN_PREFIX') . '/store_connection')->with('store_update', 'Information Successfully Updated');
            }else{
                return redirect('' . env('ADMIN_PREFIX') . '/store_connection')->with('invalid_connection', 'Provided information is not valid');
            }
        }

    }

    /**
     * Verify store connectivity on add or update info
     * Method calls from update function
     * @param null
     * @return boolean
     */
    private function verify_connection(){

        // get store connection data using relationship between User and Store
        $store_data = User::find(Auth::user()->id)->userStores()->first();


        // Shopify store credentials
        $api_key         = $store_data->api_key;
        $api_password    = $store_data->api_password;
        $api_domain_name = $store_data->api_domain;

        // "admin/api/2021-04"
        $base_url        = $store_data->base_url;

        // endpoint to verify shop
        $api_endpoint = '/shop.json';

        try{

            // sample url
            //$response = Http::get('https://api_key:api_password@shop.myshopify.com/admin/api/2021-04/shop.json');

            $response = Http::get('https://'.$api_key.':'.$api_password.'@'.$api_domain_name.'/'.$base_url.$api_endpoint);
            if ($response->successful()){

                // connection is verified update status in DB
                $store_data->is_active = "1";
                $store_data->save();

                return true;

            }
            return false;
        }
        catch(\Throwable $e){
            return false;
        }

    }
}
