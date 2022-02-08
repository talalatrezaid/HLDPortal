<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Mail\OrderCompleteEmail;
use App\Models\charity;
use App\Models\Orders\Orders;
use App\Models\PortalSettings;
use Illuminate\Http\Request;
use Auth;
use Exception;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use DB;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user() == null) {

            return view('admin.pages.account.login');
        }

        $charities = charity::where("is_active", 1)->get();
        //filters options 
        $order_by = "created_at"; // column name
        $sort = "desc"; // ascending desending order
        $search_keyword = ""; // any keyword
        $charity = -1; // 0 will be used for shopify orders
        $payment = "";
        $status = "";
        $per_page = 10;
        $date_from = ""; //date range
        $date_to = ""; //date to

        if (isset($_POST['submit'])) {

            $search_keyword = $request->search;
            $charity = $request->charity;
            $payment = $request->payment;
            $per_page = $request->per_page;
            $status = $request->status;

            if ($status == "unfullfilled") {
                $status = "Unfullfilled";
            }

            if ($status == "fullfilled") {
                $status = "Completed";
            }
        }
        $data['search_keyword'] = $search_keyword;
        $data['charity'] = $charity;
        $data['payment'] = $payment;
        $data['per_page'] = $per_page;
        $data['status'] = $status;


        $orders = new Orders();
        //   DB::enableQueryLog();

        $aorders = $orders->get_all_orders($order_by, $sort, $search_keyword, $charity, $payment, $status, $date_from, $date_to, $per_page);
        //  $query = DB::getQueryLog();
        //  dd($query);

        return view('admin.pages.orders.index', [
            'orders' => $aorders,
            'charities' => $charities,
            'search_keyword' => $search_keyword,
            'charity' => $charity,
            'payment' => $payment,
            'per_page' => $per_page,
            'status' => $request->status,
            "no" => 1,

            'storefront_categories_count' => 0
        ]);
    }


    public function exportorders(Request $request)
    {
        if (Auth::user() == null) {

            return view('admin.pages.account.login');
        }

        $charities = charity::where("is_active", 1)->get();
        //filters options 
        $order_by = "created_at"; // column name
        $sort = "desc"; // ascending desending order
        $search_keyword = ""; // any keyword
        $charity = -1; // 0 will be used for shopify orders
        $payment = "";
        $status = "";
        $per_page = 10;
        $date_from = ""; //date range
        $date_to = ""; //date to

        if (isset($_GET['search']))
            $search_keyword = $request->search;
        if (isset($_GET['charity']))
            $charity = $request->charity;
        if (isset($_GET['payment']))
            $payment = $request->payment;
        if (isset($_GET['per_page']))
            $per_page = $request->per_page;
        if (isset($_GET['status']))
            $status = $request->status;

        if ($status == "unfullfilled") {
            $status = "Unfullfilled";
        }

        if ($status == "fullfilled") {
            $status = "Completed";
        }

        $data['search_keyword'] = $search_keyword;
        $data['charity'] = $charity;
        $data['payment'] = $payment;
        $data['per_page'] = $per_page;
        $data['status'] = $status;


        $orders = new Orders();
        //   DB::enableQueryLog();

        $aorders = $orders->get_all_orders($order_by, $sort, $search_keyword, $charity, $payment, $status, $date_from, $date_to, $per_page);
        $fields = [];
        $index = 0;
        $csv = 'Orders.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = ["Order #", "Customer Name", "Customer Email", "Customer Contact", "Charity Name", "Total Price", "Payment Status", "Order Status", "Date"];

        $callback = function () use ($aorders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($aorders as $row) {

                $fields = [
                    $row->id,
                    $row->name,
                    $row->email,
                    $row->phone,
                    $row->charities->charity_name,
                    $row->total_price,
                    $row->financial_status,
                    $row->fulfillment_status,
                    $row->created_at
                ];
                fputcsv($file, $fields);
            }
            //  dd($fields);
            fclose($file);
            // die();
        };
        return Response::stream($callback, 200, $headers);
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
    public function show(Request $req)
    {
        //

        return Datatables()::of(
            Orders::with(
                'billing',
                'shipping',
                "payment",
                "list_items",
                "fullfilments",
                "charities",
                "customer"
            )

        )->addColumn('charity_name', '{{(isset($charities["charity_name"])? $charities["charity_name"] : "Shopify Sotre HLD")}}')
            ->filterColumn('financial_status', function ($query, $keyword) {
                $query->where("financial_status", "=", "$keyword");
            })->filterColumn('fulfillment_status', function ($query, $keyword) {
                $query->where("fulfillment_status", "=", "$keyword");
            })->filterColumn('fulfillment_status', function ($query, $keyword) {
                $query->where("fulfillment_status", "=", "$keyword");
            })->make(true);
    }
    public function orderComplete($id)
    {
        //
        if (Auth::user() == null) {

            return view('admin.pages.account.login');
        }

        $orders = Orders::findorfail($id);
        if (strtolower($orders->fulfillment_status) == "completed") {
        }

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
            $email = $orders->email;

            $data['order_no'] = $id;
            $emaildata = new  OrderCompleteEmail($data);
            Mail::to($email)->send($emaildata);
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
