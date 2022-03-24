<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Mail\OrderCompleteEmail;
use App\Models\charity;
use App\Models\Orders\Orders;
use App\Models\PortalSettings;
use App\Models\Product;
use Illuminate\Http\Request;
use Auth;
use Exception;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use DB;
use Spreadsheet_Excel_Writer;

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
            if (isset($_POST['from_date'])) {
                $date_from = $request->from_date;
                if (strlen($date_from) > 0)
                    $date_from = \DateTime::createFromFormat('Y-m-d', $date_from)->format('Y-m-d');
            }
            if (isset($_POST['to_date'])) {
                $date_to = $request->to_date;
                if (strlen($date_to) > 0)
                    $date_to = \DateTime::createFromFormat('Y-m-d', $date_to)->format('Y-m-d');
            }


            if ($status == "unfulfilled") {
                $status = "Unfulfilled";
            }

            if ($status == "fulfilled") {
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
            "content" => "orders",
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

        if ($status == "unfulfilled") {
            $status = "Unfulfilled";
        }

        if ($status == "fulfilled") {
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
        $columns = [
            "Order #",
            "Customer Name",
            "Customer Email",
            "Customer Contact",
            "Charity Name",
            "Total Price",
            "Payment Status",
            "Order Status",
            "Date"
        ];

        $callback = function () use (
            $aorders,
            $columns
        ) {
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

    //this function will generate
    public function csvexportordersOnlyCharityProducts(Request $request)
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

        if ($status == "unfulfilled") {
            $status = "Unfulfilled";
        }

        if ($status == "fulfilled") {
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
            "Content-Disposition" => "attachment; filename=Product Orders.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = [
            "Order #",
            "Customer Name",
            "Customer Email",
            "Customer Contact",
            "Charity Name",
            "Total Products Quantity",
            "Total Products Price",
            "Fullfilment Charges",
            "Net. Total",
            "Zakat",
            "Sadqah",
            "Lilah",
            "Other",
            "Total Donation",
            "Grand Total",
            "Payment Status",
            "Order Status",
            "Date"
        ];

        $callback = function () use ($aorders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($aorders as $row) {
                $total_charity_products_quanity = 0; //count after foreach
                $total_charity_products_price = 0; // sum after foreach
                $fullfilment_charges_per_unit = 0.5; //i will make it dynamic this time it is 0.5 pound
                $total_zakat = 0;
                $total_sadqah = 0;
                $total_lillah = 0;
                $total_others = 0;
                $donation_total = 0;
                //start loop here list_items

                foreach ($row->list_items as $product) {
                    //all calculation have to perform here
                    //  if($row)   
                    if ($product->is_charity_product == 1) {
                        $total_charity_products_quanity += $product->quantity;
                    } else {
                        continue;
                    }
                    $thisproductprice = $product->quantity * $product->price;
                    $total_charity_products_price += $thisproductprice;
                }

                if ($row->donations !== null) {

                    foreach ($row->donations as $donation) {
                        //all calculation have to perform here
                        $charity_type = $donation->charity_type;
                        switch ($charity_type) {
                                //general charity
                            case 1:
                                $total_others += $donation->amount;
                                break;
                            case 2: //sadqah
                                $total_sadqah += $donation->amount;
                                break;
                            case 3: //zakat
                                $total_zakat += $donation->amount;
                                break;
                            case 4: //lillah
                                $total_lillah += $donation->amount;
                                break;
                            default:
                                break;
                        }
                    }
                }
                $donation_total = $total_others + $total_sadqah + $total_zakat + $total_lillah;
                $fullfilment_charges = ($total_charity_products_quanity * $fullfilment_charges_per_unit); // sum after foreach
                $net_total = $total_charity_products_price - $fullfilment_charges;
                $grand_total = $net_total + $donation_total;


                $fields = [
                    $row->id,
                    $row->name,
                    $row->email,
                    $row->phone,
                    $row->charities->charity_name,
                    $total_charity_products_quanity,
                    $total_charity_products_price,
                    $fullfilment_charges,
                    $net_total,
                    $total_zakat,
                    $total_sadqah,
                    $total_lillah,
                    $total_others,
                    $donation_total,
                    $grand_total,

                    $row->financial_status,
                    $row->fulfillment_status,
                    $row->created_at
                ];

                if ($total_charity_products_quanity > 0)
                    fputcsv($file, $fields);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    //this function will generate
    public function exportordersOnlyCharityProducts(Request $request)
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

        if ($status == "unfulfilled") {
            $status = "Unfulfilled";
        }

        if ($status == "fulfilled") {
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
        // $headers = array(
        //     "Content-type" => "text/csv",
        //     "Content-Disposition" => "attachment; filename=Product Orders.csv",
        //     "Pragma" => "no-cache",
        //     "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires" => "0"
        // );

        //  require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->send('Products Orders.xls');
        //        $xls->send('Pro'.date("Y-m-d__H:i:s").'.xls');

        $worksheet = $workbook->addWorksheet('Orders Detail');
        $worksheet2 = $workbook->addWorksheet('Line Items Detail');


        //order details sheet header
        $worksheet->write(0, 0,  "Order #");
        $worksheet->write(0, 1,  "Customer Name");
        $worksheet->write(0, 2,  "Customer Email");
        $worksheet->write(0, 3,  "Customer Contact");
        $worksheet->write(0, 4,  "Charity Name");
        $worksheet->write(0, 5,  "Total Products Quantity");
        $worksheet->write(0, 6,  "Total Products Price");
        $worksheet->write(0, 7,  "Fullfilment Charges");
        $worksheet->write(0, 8,  "Net. Total");
        $worksheet->write(0, 9,  "Zakat");
        $worksheet->write(0, 10, "Sadqah");
        $worksheet->write(0, 11, "Lilah");
        $worksheet->write(0, 12, "Other");
        $worksheet->write(0, 13, "Total Donation");
        $worksheet->write(0, 14, "Grand Total");
        $worksheet->write(0, 15, "Gift Aid");
        $worksheet->write(0, 16, "25% of Total Donation");
        $worksheet->write(0, 17, "Payment Status");
        $worksheet->write(0, 18, "Order Status");
        $worksheet->write(0, 19, "Date");
        $worksheet->write(0, 20, "Shipping Address");
        $worksheet->write(0, 21, "Billing Address");



        //list item details sheet header

        $worksheet2->write(0, 0, "Order #");
        $worksheet2->write(0, 1, "Product Name");
        $worksheet2->write(0, 2, "Quantity");
        $worksheet2->write(0, 3, "Price Per Unit");
        $worksheet2->write(0, 4, "Total");
        $worksheet2->write(0, 5, "Fullfilment Charges");
        $worksheet2->write(0, 6, "Net. Total");

        //  $file = fopen('php://output', 'w');
        // fputcsv($file, $columns);
        $i = 1;
        $j = 1;
        foreach ($aorders as $row) {
            $total_charity_products_quanity = 0; //count after foreach
            $total_charity_products_price = 0; // sum after foreach
            $fullfilment_charges_per_unit = 0.5; //i will make it dynamic this time it is 0.5 pound
            $total_zakat = 0;
            $total_sadqah = 0;
            $total_lillah = 0;
            $total_others = 0;
            $donation_total = 0;
            //start loop here list_items
            foreach ($row->list_items as $product) {
                //all calculation have to perform here
                if ($product->is_charity_product == 1) {

                    $total_charity_products_quanity += $product->quantity;
                    $product_quantity = $product->quantity;
                    $product_price = $product->price;
                    $product_total_price = $product_quantity * $product_price;
                    $fullfilment_charges_product = $fullfilment_charges_per_unit * $product_quantity;
                    $net_total_product = $product_total_price - $fullfilment_charges_product;
                    $worksheet2->write($j, 0, $row->id);
                    $worksheet2->write($j, 1, $product->name);
                    $worksheet2->write($j, 2, $product_quantity);
                    $worksheet2->write($j, 3, $product_price);
                    $worksheet2->write($j, 4, $product_total_price);
                    $worksheet2->write($j, 5, $fullfilment_charges_product);
                    $worksheet2->write($j, 6, $net_total_product);
                    $j++;
                } else {
                    continue;
                }
                $thisproductprice = $product->quantity * $product->price;
                $total_charity_products_price += $thisproductprice;
            }

            if ($row->donations !== null) {

                foreach ($row->donations as $donation) {
                    //all calculation have to perform here
                    $charity_type = $donation->charity_type;
                    switch ($charity_type) {
                            //general charity
                        case 1:
                            $total_others += $donation->amount;
                            break;
                        case 2: //sadqah
                            $total_sadqah += $donation->amount;
                            break;
                        case 3: //zakat
                            $total_zakat += $donation->amount;
                            break;
                        case 4: //lillah
                            $total_lillah += $donation->amount;
                            break;
                        default:
                            break;
                    }
                }
            }
            $donation_total = $total_others + $total_sadqah + $total_zakat + $total_lillah;
            $fullfilment_charges = ($total_charity_products_quanity * $fullfilment_charges_per_unit); // sum after foreach
            $net_total = $total_charity_products_price - $fullfilment_charges;
            $grand_total = $net_total + $donation_total;
            $donation_total_25_percent = 0.25 * $donation_total;

            if ($total_charity_products_quanity > 0) {
                $worksheet->write($i, 0, $row->id);
                $worksheet->write($i, 1, $row->name);
                $worksheet->write($i, 2, $row->email);
                $worksheet->write($i, 3, (string) strval($row->phone));
                $worksheet->write($i, 4, $row->charities->charity_name);
                $worksheet->write($i, 5, $total_charity_products_quanity);
                $worksheet->write($i, 6, $total_charity_products_price);
                $worksheet->write($i, 7, $fullfilment_charges);
                $worksheet->write($i, 8, $net_total);
                $worksheet->write($i, 9, $total_zakat);
                $worksheet->write($i, 10, $total_sadqah);
                $worksheet->write($i, 11, $total_lillah);
                $worksheet->write($i, 12, $total_others);
                $worksheet->write($i, 13, $donation_total);
                $worksheet->write($i, 14, $grand_total);
                $gift_add = "NO";

                if ($row->uktaxpayer == 1) {
                    $gift_add = "YES";
                } else if ($row->uktaxpayer == 2) {
                    $gift_add = "Not Sure";
                }
                $worksheet->write($i, 15, $gift_add);
                $worksheet->write($i, 16, $donation_total_25_percent);
                $worksheet->write($i, 17, $row->financial_status);

                if ($row->shipping <> null) {
                    if (isset($row->shipping[0]->address1))
                        $worksheet->write($i, 18, $row->shipping[0]->address1);
                } else {
                    $worksheet->write($i, 18, "");
                }

                if ($row->billing <> null)
                    $worksheet->write($i, 19, $row->billing[0]->address1);
                else {
                    $worksheet->write($i, 19, "");
                }
                $i++;
            }
        }

        $workbook->close();
    }
    //this function will generate
    public function csvexportordersAdditionalProducts(Request $request)
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

        if ($status == "unfulfilled") {
            $status = "Unfulfilled";
        }

        if ($status == "fulfilled") {
            $status = "Completed";
        }


        if (isset($_GET['from_date'])) {
            $date_from = $request->from_date;
            if (strlen($date_from) > 0)
                $date_from = \DateTime::createFromFormat('Y-m-d', $date_from)->format('Y-m-d');
        }


        if (isset($_GET['to_date'])) {
            $date_to = $request->to_date;
            if (strlen($date_to) > 0)
                $date_to = \DateTime::createFromFormat('Y-m-d', $date_to)->format('Y-m-d');
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
            "Content-Disposition" => "attachment; filename=Product Orders.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = [
            "Order #",
            "Customer Name",
            "Customer Email",
            "Customer Contact",
            "Charity Name",
            "Total Products Quantity",
            "Total Products Price",
            "Fullfilment Charges",
            "Net. Total",
            "Payment Status",
            "Order Status",
            "Date"
        ];

        $callback = function () use ($aorders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($aorders as $row) {
                $total_addional_products = 0; //count after foreach
                $total_charity_products_price = 0; // sum after foreach
                $fullfilment_charges_per_unit = 0.5; //i will make it dynamic this time it is 0.5 pound
                $total_zakat = 0;
                $total_sadqah = 0;
                $total_lillah = 0;
                $total_others = 0;
                $donation_total = 0;
                //start loop here list_items

                foreach ($row->list_items as $product) {
                    //all calculation have to perform here
                    //  if($row)   
                    if ($product->is_charity_product == 0) {
                        $total_addional_products += $product->quantity;
                    } else {
                        continue;
                    }
                    $thisproductprice = $product->quantity * $product->price;
                    $total_charity_products_price += $thisproductprice;
                }

                $fullfilment_charges = ($total_addional_products * $fullfilment_charges_per_unit); // sum after foreach
                $net_total = $total_charity_products_price - $fullfilment_charges;


                $fields = [
                    $row->id,
                    $row->name,
                    $row->email,
                    $row->phone,
                    $row->charities->charity_name,
                    $total_addional_products,
                    $total_charity_products_price,
                    $fullfilment_charges,
                    $net_total,
                    $row->financial_status,
                    $row->fulfillment_status,
                    $row->created_at
                ];

                if ($total_addional_products > 0)
                    fputcsv($file, $fields);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    //this function will generate additonal products orders csv
    public function exportordersAdditionalProducts(Request $request)
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

        if ($status == "unfulfilled") {
            $status = "Unfulfilled";
        }

        if ($status == "fulfilled") {
            $status = "Completed";
        }


        if (isset($_GET['from_date'])) {
            $date_from = $request->from_date;
            if (strlen($date_from) > 0)
                $date_from = \DateTime::createFromFormat('Y-m-d', $date_from)->format('Y-m-d');
        }


        if (isset($_GET['to_date'])) {
            $date_to = $request->to_date;
            if (strlen($date_to) > 0)
                $date_to = \DateTime::createFromFormat('Y-m-d', $date_to)->format('Y-m-d');
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
        // $headers = array(
        //     "Content-type" => "text/csv",
        //     "Content-Disposition" => "attachment; filename=Product Orders.csv",
        //     "Pragma" => "no-cache",
        //     "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires" => "0"
        // );

        //  require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->send('Additional Products Orders.xls');
        //        $xls->send('Pro'.date("Y-m-d__H:i:s").'.xls');

        $worksheet = $workbook->addWorksheet('Orders Detail');
        $worksheet2 = $workbook->addWorksheet('Line Items Detail');


        //order details sheet header
        $worksheet->write(0, 0, "Order #");
        $worksheet->write(0, 1, "Customer Name");
        $worksheet->write(0, 2, "Customer Email");
        $worksheet->write(0, 3, "Customer Contact");
        $worksheet->write(0, 4, "Charity Name");
        $worksheet->write(0, 5, "Total Products Quantity");
        $worksheet->write(0, 6, "Total Products Price");
        $worksheet->write(0, 7, "Fullfilment Charges");
        $worksheet->write(0, 8, "Net. Total");
        $worksheet->write(0, 9, "Payment Status");
        $worksheet->write(0, 10, "Order Status");
        $worksheet->write(0, 11, "Date");


        //list item details sheet header

        $worksheet2->write(0, 0, "Order #");
        $worksheet2->write(0, 1, "Product Name");
        $worksheet2->write(0, 2, "Quantity");
        $worksheet2->write(0, 3, "Price Per Unit");
        $worksheet2->write(0, 4, "Total");
        $worksheet2->write(0, 5, "Fullfilment Charges");
        $worksheet2->write(0, 6, "Net. Total");

        //  $file = fopen('php://output', 'w');
        // fputcsv($file, $columns);
        $i = 0;
        foreach ($aorders as $row) {
            $total_additional_products = 0; //count after foreach
            $total_charity_products_price = 0; // sum after foreach
            $fullfilment_charges_per_unit = 0.5; //i will make it dynamic this time it is 0.5 pound
            $total_zakat = 0;
            $total_sadqah = 0;
            $total_lillah = 0;
            $total_others = 0;
            $donation_total = 0;
            //start loop here list_items
            foreach ($row->list_items as $product) {
                //all calculation have to perform here
                if ($product->is_charity_product == 0) {

                    $total_additional_products += $product->quantity;
                    $product_quantity = $product->quantity;
                    $product_price = $product->price;
                    $product_total_price = $product_quantity * $product_price;
                    $fullfilment_charges_product = $fullfilment_charges_per_unit * $product_quantity;
                    $net_total_product = $product_total_price - $fullfilment_charges_product;
                    $worksheet2->write($i, 0, $row->id);
                    $worksheet2->write($i, 1, $product->name);
                    $worksheet2->write($i, 2, $product_quantity);
                    $worksheet2->write($i, 3, $product_price);
                    $worksheet2->write($i, 4, $product_total_price);
                    $worksheet2->write($i, 5, $fullfilment_charges_product);
                    $worksheet2->write($i, 6, $net_total_product);
                } else {
                    continue;
                }
                $thisproductprice = $product->quantity * $product->price;
                $total_charity_products_price += $thisproductprice;
            }

            if ($row->donations !== null) {

                foreach ($row->donations as $donation) {
                    //all calculation have to perform here
                    $charity_type = $donation->charity_type;
                    switch ($charity_type) {
                            //general charity
                        case 1:
                            $total_others += $donation->amount;
                            break;
                        case 2: //sadqah
                            $total_sadqah += $donation->amount;
                            break;
                        case 3: //zakat
                            $total_zakat += $donation->amount;
                            break;
                        case 4: //lillah
                            $total_lillah += $donation->amount;
                            break;
                        default:
                            break;
                    }
                }
            }
            $donation_total = $total_others + $total_sadqah + $total_zakat + $total_lillah;
            $fullfilment_charges = ($total_additional_products * $fullfilment_charges_per_unit); // sum after foreach
            $net_total = $total_charity_products_price - $fullfilment_charges;
            $grand_total = $net_total + $donation_total;

            if ($total_additional_products > 0) {
                $worksheet->write($i, 0, $row->id);
                $worksheet->write($i, 1, $row->name);
                $worksheet->write($i, 2, $row->email);
                $worksheet->write($i, 3, (string) $row->phone);
                $worksheet->write($i, 4, $row->charities->charity_name);
                $worksheet->write($i, 5, $total_additional_products);
                $worksheet->write($i, 6, $total_charity_products_price);
                $worksheet->write($i, 7, $fullfilment_charges);
                $worksheet->write($i, 8, $net_total);
                $worksheet->write($i, 9, $row->financial_status);
                $worksheet->write($i, 10, $row->fulfillment_status);
                $worksheet->write($i, 11, $row->created_at);
                $i++;
            }

            //    if ($total_additional_products > 0)
            //       fputcsv($file, $fields);
            //}

            //     fclose($file);
            // };
            // return Response::stream($callback, 200, $headers);
        }

        $workbook->close();
    }


    //this function will generate a sheet for hermes delivery
    public function exportordersForHermes(Request $request)
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
        $order_ids = [];

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

        if ($status == "unfulfilled") {
            $status = "Unfulfilled";
        }

        if ($status == "fulfilled") {
            $status = "Completed";
        }

        if (isset($_GET['from_date'])) {
            $date_from = $request->from_date;
            if (strlen($date_from) > 0)
                $date_from = \DateTime::createFromFormat('Y-m-d', $date_from)->format('Y-m-d');
        }
        if (isset($_GET['to_date'])) {
            $date_to = $request->to_date;
            if (strlen($date_to) > 0)
                $date_to = \DateTime::createFromFormat('Y-m-d', $date_to)->format('Y-m-d');
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
        $csv = 'Hermes File.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Hermes Orders.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = [
            "Address_line_1",
            "Address_line_2",
            "Address_line_3",
            "Address_line_4",
            "Postcode",
            "First_name",
            "Last_name",
            "Email",
            "Weight(Kg)",
            "Compensation(£)",
            "Signature(y/n)",
            "Reference",
            "Contents",
            "Parcel_value(£)",
            "Delivery_phone",
            "Delivery_safe_place",
            "Delivery_instructions",
            "Service",
        ];

        $callback = function () use ($aorders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $i = 0;
            foreach ($aorders as $row) {

                $order_ids[$i++] = $row->id;

                $fields = [
                    $row->shipping[0]->address1,
                    $row->shipping[0]->address2,
                    $row->shipping[0]->city,
                    "", //address 4
                    $row->shipping[0]->zip,
                    $row->customer->first_name,
                    $row->customer->last_name,
                    $row->email,
                    $row->total_weight, //weight
                    utf8_decode("20£"), //compensation
                    "N", //signatiure
                    "Holy Land Dates", //reference
                    "Dates", //contents
                    utf8_decode($row->total_products_amount . "£"),
                    $row->phone,
                    "", "", ""
                ];

                fputcsv($file, $fields);
            }

            DB::table("orders")->whereIn("id", $order_ids)->update(['is_exported_for_hermes' => 1]);



            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }


    //this function will generate a sheet for hermes delivery
    public function exportordersForHermesAndUpdateFlag(Request $request)
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
        $order_ids = [];
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

        if ($status == "unfulfilled") {
            $status = "Unfulfilled";
        }

        if ($status == "fulfilled") {
            $status = "Completed";
        }

        if (isset($_GET['from_date'])) {
            $date_from = $request->from_date;
            if (strlen($date_from) > 0)
                $date_from = \DateTime::createFromFormat('Y-m-d', $date_from)->format('Y-m-d');
        }
        if (isset($_GET['to_date'])) {
            $date_to = $request->to_date;
            if (strlen($date_to) > 0)
                $date_to = \DateTime::createFromFormat('Y-m-d', $date_to)->format('Y-m-d');
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
        $csv = 'Hermes File.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Hermes Orders.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = [
            "Address_line_1",
            "Address_line_2",
            "Address_line_3",
            "Address_line_4",
            "Postcode",
            "First_name",
            "Last_name",
            "Email",
            "Weight(Kg)",
            utf8_decode("Compensation(£)"),
            "Signature(y/n)",
            "Reference",
            "Contents",
            utf8_decode("Parcel_value(£)"),
            "Delivery_phone",
            "Delivery_safe_place",
            "Delivery_instructions",
            "Service",
        ];

        $callback = function () use ($aorders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($aorders as $row) {

                array_push($order_ids, $row->id);

                $fields = [
                    $row->shipping[0]->address1,
                    $row->shipping[0]->address2,
                    $row->shipping[0]->city,
                    "", //address 4
                    $row->shipping[0]->zip,
                    $row->customer->first_name,
                    $row->customer->last_name,
                    $row->email,
                    $row->total_weight, //weight
                    utf8_decode("20£"), //compensation
                    "N", //signatiure
                    "Holy Land Dates", //reference
                    "Dates", //contents
                    utf8_decode($row->total_products_amount . "£"),
                    $row->phone,
                    "", "", ""
                ];

                fputcsv($file, $fields);
            }
            DB::table("orders")->whereIn("id", $order_ids)->update(['is_exported_for_hermes' => 1]);
            fclose($file);
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
        $hash = password_hash($id . "Rezaid" . $orders->email, PASSWORD_DEFAULT);
        if (strtolower($orders->fulfillment_status) == "completed") {
        }
        $orders->order_verification_link = $hash;
        $orders->fulfillment_status = "Completed";
        $orders->save();
        $charity_user_name = "";
        $charity_url = "";
        //get this charity email
        if ($orders->charity_id > 0) {
            //charity email 
            $charity = charity::where("id", $orders->charity_id)->first();
            $charity_user_name = $charity->user_name;
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
        if (strlen($orders->email) > 0) {
            //get customer email
            $email = $orders->email;
            $data['order_no'] = $id;
            //charity url feedback/orderid encrypted/ email encrypted/
            $data['order_feedback_link'] = "https://" . $charity_user_name . ".datesfrompalestine.com/orderfeedback/" . base64_encode($hash);
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
            'order' => $data, "content" => "orders"
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
