<?php

namespace App\Http\Controllers;

use App\Models\Orders\Orders;
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
