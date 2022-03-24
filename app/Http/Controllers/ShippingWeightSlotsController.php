<?php

namespace App\Http\Controllers;

use App\Models\PortalSettings;
use App\Models\ShippingWeightSlots;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Redirect;
use Response;
use Carbon\Carbon;

class ShippingWeightSlotsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Auth::user() == null) {
            return view('admin.pages.account.login');
        }

        $all_slots = ShippingWeightSlots::paginate();
        $data['slots'] = $all_slots;
        $data['content'] = "shippingslots";
        $data['settings'] = PortalSettings::select("shipping_charges")->where("id", 1)->first();

        return view('admin.pages.shippingslots.index', $data);
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
        $validator  = Validator::make($request->all(), [
            'min_weight' => 'required',
            'max_weight' => 'required',
            'charges'    => 'required',
        ]);
        $data = $request->all();

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $data['min_weight'] = $request->min_weight;
        $data['max_weight'] = $request->max_weight;
        $data['charges'] = $request->charges;
        $id = ShippingWeightSlots::create($data);
        return ["success" => 1, "message" => "Slot added successfully"];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    }

    public function updateShippingFreeAmount(Request $request)
    {
        $settings = PortalSettings::find(1);

        $settings->shipping_charges = $request->amount;
        $settings->save();
        return ["success" => 1, "message" => "Amount updated successfully"];
    }
    public function updateSlot(Request $request)
    {
        $validator  = Validator::make($request->all(), [
            'id' => 'required',
            'min_weight' => 'required',
            'max_weight' => 'required',
            'charges'    => 'required',
        ]);
        $data = $request->all();

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }
        $findslot = ShippingWeightSlots::find($request->id);
        $findslot->min_weight = $request->min_weight;
        $findslot->max_weight = $request->max_weight;
        $findslot->charges = $request->charges;
        $findslot->save();
        return ["success" => 1, "message" => "Slot updated successfully"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Slot = ShippingWeightSlots::find($id);
        $Slot->delete();

        return redirect()->back()->with('success', 'Slot deleted successfully');
    }
}
