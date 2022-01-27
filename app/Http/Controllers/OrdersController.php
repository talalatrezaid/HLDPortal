<?php

namespace App\Http\Controllers;

use App\Mail\OrderCompleteEmail;
use App\Models\charity;
use App\Models\Orders\Orders;
use App\Models\PortalSettings;
use Illuminate\Http\Request;
use Auth;
use Exception;
use Illuminate\Support\Facades\Mail;

class OrdersController extends Controller
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
        return view('admin.pages.orders.index', [
            'products' => [],
            'storefront_categories_count' => 0
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
     * @param  \App\Models\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function show(Orders $orders)
    {
        //
        return datatables()->of(Orders::with('billing', 'shipping', "payment", "list_items", "fullfilments", "charities"))->make(true);
    }
    public function orderComplete($id)
    {
        //
        if (Auth::user() == null) {

            return view('admin.pages.account.login');
        }

        $orders = Orders::findorfail($id);
        $orders->fulfillment_status = "Completed";
        $orders->save();

        //get this charity email
        if ($orders->charity_id > 0) {
            //charity email 
            $charity = charity::where("id", $orders->charity_id)->first();

            $charityemail = $charity->email;
            $data['order_no'] = $id;
            $email = new  OrderCompleteEmail($data);
            Mail::to($charityemail)->send($email);
        }
        //get settings email 
        $setttings = PortalSettings::find(1);
        $superadmin = $setttings->website_notify_email;
        $data['order_no'] = $id;
        $email = new  OrderCompleteEmail($data);
        Mail::to($superadmin)->send($email);

        //get customer email
        if ($orders->customer_id > 0) {
            //get customer email
            $email = $orders->customers_email;
            $data['order_no'] = $id;
            $email = new  OrderCompleteEmail($data);
            Mail::to($email)->send($email);
        }
        return   redirect(Adminurl("orderdetail/" . $id));
    }
    public function orderDetail($id)
    {
        //
        if (Auth::user() == null) {

            return view('admin.pages.account.login');
        }

        $data = Orders::where("id", $id)->with('billing', 'shipping', "payment", "list_items", "fullfilments", "charities")->first();

        return view('admin.pages.orders.detail', [
            'order' => $data
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function destroy(Orders $orders)
    {
        //
    }
}
