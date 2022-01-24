<?php

namespace App\Http\Controllers;

use App\Models\PortalSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'settings' => $data
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
    public function update(Request $request, PortalSettings $portalSettings)
    {
        //
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
