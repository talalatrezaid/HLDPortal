<?php

namespace App\Http\Controllers;

use App\Models\PortalSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class PortalSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user() == null) {

            return view('admin.pages.account.login');
        }

        $data = PortalSettings::where("id", 1)->first();

        return view('admin.pages.portalsettings.index', [
            'settings' => $data,
            "content" => "settings",

        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PortalSettings  $portalSettings
     * @return \Illuminate\Http\Response
     */
    public function show(PortalSettings $portalSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PortalSettings  $portalSettings
     * @return \Illuminate\Http\Response
     */
    public function edit(PortalSettings $portalSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PortalSettings  $portalSettings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $v = Validator::make($request->all(), [
            //
            'testing_worldpay_client_id' => 'required',
            'testing_worldpay_secret_key' => 'required',
            'live_worldpay_client_id' => 'required',
            'live_worldpay_secret_key' => 'required',
            'welcome_charity_email_messsage' => 'required',
            'assigning_product_email_message' => 'required',
            'customer_order_email_message' => 'required',
            'charity_order_email_message' => 'required',
            'superadmin_email_message' => 'required',
            'website_notify_email' => 'required|email',
            'shipping_charges' => 'required'
        ]);

        $google_analytics_id = "";
        $google_tag_manger_id = "";
        $facebook_pixel_script = "";

        $is_live_worldpay = 0;
        if (isset($_POST['is_live_worldpay'])) {
            $is_live_worldpay = 1;
        }

        if (isset($_POST['google_analytics_id'])) {
            $google_analytics_id = addslashes(trim($_POST['google_analytics_id']));
        }

        if (isset($_POST['google_tag_manger_id'])) {
            $google_tag_manger_id = addslashes(trim($_POST['google_tag_manger_id']));
        }



        if (isset($_POST['facebook_pixel_script'])) {
            $facebook_pixel_script = addslashes(trim($_POST['facebook_pixel_script']));
        }


        $data = [
            'testing_worldpay_client_id' => $request->testing_worldpay_client_id,
            'testing_worldpay_secret_key' => $request->testing_worldpay_secret_key,
            'live_worldpay_client_id' => $request->live_worldpay_client_id,
            'live_worldpay_secret_key' => $request->live_worldpay_secret_key,
            'welcome_charity_email_messsage' => $request->welcome_charity_email_messsage,
            'assigning_product_email_message' => $request->assigning_product_email_message,
            'customer_order_email_message' => $request->customer_order_email_message,
            'charity_order_email_message' => $request->charity_order_email_message,
            'superadmin_email_message' => $request->superadmin_email_message,
            'website_notify_email' => $request->website_notify_email,
            'is_live_worldpay' => $is_live_worldpay,
            'shipping_charges' => $request->shipping_charges,
            'google_analytics_id' => $google_analytics_id,
            'google_tag_manger_id' => $google_tag_manger_id,
            'facebook_pixel_script' => $facebook_pixel_script
        ];
        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        PortalSettings::where('id', $id)->update($data);
        return redirect()->back()->with('message', 'Setting successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PortalSettings  $portalSettings
     * @return \Illuminate\Http\Response
     */
    public function destroy(PortalSettings $portalSettings)
    {
        //
    }
}
