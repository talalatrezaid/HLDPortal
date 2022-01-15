<?php

namespace App\Http\Controllers;

use App\Jobs\AddCharityJob;
use App\Mail\AddCharityEmail;
use App\Mail\AssignProductToCharity;
use Illuminate\Http\Request;
use DB;
use App\Models\charity;
use App\Models\AssignedCharitiesProducts;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Auth;
use Exception;
use Illuminate\Support\Facades\Mail;
use Validator;
use Redirect;
use Response;


class CharitiesController extends Controller
{
    /**
     * Method 
     * Method responsible just show view where all charities will be displayed
     * Method: GET
     * @param request
     * @return view
     */
    public function index(Request $request)
    {
        if (Auth::user() == null) {

            return view('admin.pages.account.login');
        }
        return view('admin.pages.charities.index', [
            'products' => [],
            'storefront_categories_count' => 0
        ]);
    }

    /**
     * Method 
     * Method responsible to store new charity and email to charity email
     * Method: POST
     * @param request
     * @return view
     */
    public function store(Request $request)
    {

        $validator                  = Validator::make($request->all(), [
            'charity_name'                => 'required',
            'user_name'           => ['required', 'unique:user'],
            'email'               => ['required', 'regex:/(.*)@(.*)\.(.*)/i', 'unique:user'],
        ]);
        $data                       = $request->all();

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->with('charity_name', $data['charity_name'])
                ->with('user_name', $data['user_name'])
                ->with('email', $data['email'])->withInput();
        }

        $charity  = charity::create([
            'charity_name' => $request->charity_name,
            'user_type_id' => 1,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => '8adf36eeb26a3f68d51bd636bbb51559' //default password 
        ]);

        if ($charity) {
            //send email here first

            // create a home page for this charity 
            $values = array('page_template' => 'home', 'page_template' => 'Home', "charity_id" => $charity->id);
            $page_id = DB::table('page')->insertGetId($values);
            $newvalues[0] = array(
                'page_id' => $page_id,
                'field_key' => 'hero_section_logo',
                'field_title'  => 'Website logo',
                'field_value' => '',
                'field_type' => 'file',
            );

            $newvalues[1] = array(
                'page_id' => $page_id,
                'field_key' => 'page_heading',
                'field_title'  => 'Home',
                'field_value' => 'home',
                'field_type' => 'text',
            );

            $newvalues[0] = array(
                'page_id' => $page_id,
                'field_key' => 'hero_section_logo',
                'field_title'  => 'Website logo',
                'field_value' => '',
                'field_type' => 'file',
            );





            $page_id = DB::table('pages')->insert($newvalues);

            $setting = [];


            $setting[0]['setting_title'] = "Address";
            $setting[0]['setting_description'] = "Office address";
            $setting[0]['key'] = "address";
            $setting[0]['value'] = "";
            $setting[0]['settings_name'] = "";
            $setting[0]['settings_value'] = "";
            $setting[0]['settings_type'] = "";
            $setting[0]['setting_group'] = "";
            $setting[0]['charity_id'] = $charity->id;

            $setting[1]['setting_title'] = "Email Address";
            $setting[1]['setting_description'] = "Office address";
            $setting[1]['key'] = "contact_email";
            $setting[1]['value'] = "";
            $setting[1]['settings_name'] = "";
            $setting[1]['settings_value'] = "";
            $setting[1]['settings_type'] = "";
            $setting[1]['setting_group'] = "";
            $setting[1]['charity_id'] = $charity->id;

            $setting[2]['setting_title'] = "Phone Number";
            $setting[2]['setting_description'] = "Office address";
            $setting[2]['key'] = "phone_number";
            $setting[2]['value'] = "";
            $setting[2]['settings_name'] = "";
            $setting[2]['settings_value'] = "";
            $setting[2]['settings_type'] = "";
            $setting[2]['setting_group'] = "";
            $setting[2]['charity_id'] = $charity->id;

            $setting[3]['setting_title'] = "Facebook Link";
            $setting[3]['setting_description'] = "Office address";
            $setting[3]['key'] = "facebook_link";
            $setting[3]['value'] = "";
            $setting[3]['settings_name'] = "";
            $setting[3]['settings_value'] = "";
            $setting[3]['settings_type'] = "";
            $setting[3]['setting_group'] = "";
            $setting[3]['charity_id'] = $charity->id;

            $setting[4]['setting_title'] = "Twitter Link";
            $setting[4]['setting_description'] = "Office address";
            $setting[4]['key'] = "twitter_link";
            $setting[4]['value'] = "";
            $setting[4]['settings_name'] = "";
            $setting[4]['settings_value'] = "";
            $setting[4]['settings_type'] = "";
            $setting[4]['setting_group'] = "";
            $setting[4]['charity_id'] = $charity->id;

            $setting[5]['setting_title'] = "Instagram Link";
            $setting[5]['setting_description'] = "Office address";
            $setting[5]['key'] = "instagram_link";
            $setting[5]['value'] = "";
            $setting[5]['settings_name'] = "";
            $setting[5]['settings_value'] = "";
            $setting[5]['settings_type'] = "";
            $setting[5]['setting_group'] = "";
            $setting[5]['charity_id'] = $charity->id;

            $setting[6]['setting_title'] = "Charity Reg.No";
            $setting[6]['setting_description'] = "Office address";
            $setting[6]['key'] = "charity_reg_no";
            $setting[6]['value'] = "";
            $setting[6]['settings_name'] = "";
            $setting[6]['settings_value'] = "";
            $setting[6]['settings_type'] = "";
            $setting[6]['setting_group'] = "";
            $setting[6]['charity_id'] = $charity->id;

            $setting[7]['setting_title'] = "Whatsapp Number";
            $setting[7]['setting_description'] = "Office address";
            $setting[7]['key'] = "whatsapp";
            $setting[7]['value'] = "";
            $setting[7]['settings_name'] = "";
            $setting[7]['settings_value'] = "";
            $setting[7]['settings_type'] = "";
            $setting[7]['setting_group'] = "";
            $setting[7]['charity_id'] = $charity->id;

            $setting[8]['setting_title'] = "YouTube";
            $setting[8]['setting_description'] = "Office address";
            $setting[8]['key'] = "youtube_link";
            $setting[8]['value'] = "";
            $setting[8]['settings_name'] = "";
            $setting[8]['settings_value'] = "";
            $setting[8]['settings_type'] = "";
            $setting[8]['setting_group'] = "";
            $setting[8]['charity_id'] = $charity->id;
            $setting[9]['setting_title'] = "linkedIn";
            $setting[9]['setting_description'] = "Office address";
            $setting[9]['key'] = "linkedin_link";
            $setting[9]['value'] = "";
            $setting[9]['settings_name'] = "";
            $setting[9]['settings_value'] = "";
            $setting[9]['settings_type'] = "";
            $setting[9]['setting_group'] = "";
            $setting[9]['charity_id'] = $charity->id;
            $setting[10]['setting_title'] = "Marketing Email";
            $setting[10]['setting_description'] = "Office address";
            $setting[10]['key'] = "marketing_email";
            $setting[10]['value'] = "";
            $setting[10]['settings_name'] = "";
            $setting[10]['settings_value'] = "";
            $setting[10]['settings_type'] = "";
            $setting[10]['setting_group'] = "";
            $setting[10]['charity_id'] = $charity->id;
            $setting[11]['setting_title'] = "Donation Limi";
            $setting[11]['setting_description'] = "Office address";
            $setting[11]['key'] = "donation_limit";
            $setting[11]['value'] = "";
            $setting[11]['settings_name'] = "";
            $setting[11]['settings_value'] = "";
            $setting[11]['settings_type'] = "";
            $setting[11]['setting_group'] = "";
            $setting[11]['charity_id'] = $charity->id;
            $setting[12]['setting_title'] = "BrainTree Environment";
            $setting[12]['setting_description'] = "Office address";
            $setting[12]['key'] = "address";
            $setting[12]['value'] = "";
            $setting[12]['settings_name'] = "";
            $setting[12]['settings_value'] = "";
            $setting[12]['settings_type'] = "";
            $setting[12]['setting_group'] = "";
            $setting[12]['charity_id'] = $charity->id;
            $setting[13]['setting_title'] = "Mic";
            $setting[13]['setting_description'] = "Office address";
            $setting[13]['key'] = "mic_icon";
            $setting[13]['value'] = "";
            $setting[13]['settings_name'] = "";
            $setting[13]['settings_value'] = "";
            $setting[13]['settings_type'] = "";
            $setting[13]['setting_group'] = "";
            $setting[13]['charity_id'] = $charity->id;
            $setting[14]['setting_title'] = "Broadcast Link";
            $setting[14]['setting_description'] = "Office address";
            $setting[14]['key'] = "broadcast_icon";
            $setting[14]['value'] = "";
            $setting[14]['settings_name'] = "";
            $setting[14]['settings_value'] = "";
            $setting[14]['settings_type'] = "";
            $setting[14]['setting_group'] = "";
            $setting[14]['charity_id'] = $charity->id;


            $settings = DB::table('settings')->insert($setting);

            //            dispatch(new AddCharityJob($request->email, $charity));
            $email = new  AddCharityEmail($charity);
            Mail::to($request->email)->send($email);

            return back()->with('success', 'Charity added successfuly.');
        } else {
            return back()->with('error', 'Error in inserting record, please try again');
        }
    }

    /**
     * Method 
     * Method responsible to show edit form for particular charity
     * Method: GET
     * @param request
     * @return view
     */
    public function edit(Request $request, $id)
    {

        if (CheckuserPermissions('register'))
            return redirect('' . env('ADMIN_PREFIX') . '/users')->with('add_user_faliure', "You don't have the permission to add a User!");
        $data = array();
        $charity = charity::findOrFail($id);
        $data['user_roles'] = DB::table('user_roles')->get();
        $data['charity'] =  $charity;

        return view('admin.pages.charities.edit', $data);
    }

    /**
     * Method 
     * Method responsible to show charity products and product add form to particular charity
     * Method it should email quantity of that product to charity and holy land dates both
     * Method: GET
     * @param request
     * @return view
     */
    public function assignproducts(Request $request, $id)
    {
        //get this charity detail
        $chrity = charity::where("id", $id)->first();

        if (Auth::user() == null) {
            return view('admin.pages.account.login');
        }
        $data['charity_id'] = $id;
        $data['charity'] = $chrity;
        return view('admin.pages.charities.assignproducts', $data);
    }

    function assignedproductdestroy($id)
    {
        $findId = AssignedCharitiesProducts::find($id);

        //get quantity from db 
        if ($findId <> null) {
            if ($findId->qty > 0) {
                $product = new Product();
                $adjustOrderOnShopifyStore = $product->adjustOrderOnShopifyStore($findId->variantId, $findId->qty);
            }

            $findId->delete();
            return redirect()->back()->with('success', 'Product deleted and ajusted quantity to shopify sotre.');
        }
    }

    function getAssignedProductsForDataTable(Request $request, $id)
    {
        return datatables()->of(AssignedCharitiesProducts::with('products')->where("charity_id", $id))->addColumn('action', function ($row) {

            $btn = '';

            $btn = $btn . '&nbsp; 
            <form action="' . route('assignedproductdestroy', $row["id"]) . '" method="POST"> 
            ' . method_field("DELETE") . '
            <input type="hidden" name="_token" id="csrf-token" value="' . csrf_token() . '" />

            <button type="submit" class="btn btn-danger" onclick="return confirm(\'Are You Sure Want to Delete?\')">delete</form>';

            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    /**
     * Method 
     * Method responsible to verify product quantity from shopify 
     * Method using product variant id and update the new value in our database
     * Method check also that this product already being assigned this charity or not
     * Method if both cases are fine then assign product to charity
     * Method and send email to charity email and super admin email 
     * Method: GET
     * @param request
     * @return view
     */
    public function assignthisproducttocharity(Request $request)
    {

        //first of all validate all 6 variables coming or not

        $validator = Validator::make($request->all(), [
            'charity_id'                => 'required:numeric',
            "local_product_id" => "required:numeric",
            "local_product_variant_id" => "required:numeric",
            "shopify_product_id" => "required:numeric",
            "shopify_product_variant_id" => "required:numeric",
            "quantity" => "required:numeric",
            "product_name" => "required",
            "total_quantity" => "required:numeric",
        ]);
        $data                       = $request->all();

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator);
        }

        // variables 
        // total_quantity of product from our local database
        $total_quantity = $request->total_quantity;
        // quantity superadmin want to assign this quantity to a particular chairty
        $quantity = $request->quantity;
        $product = new Product();
        //if all fine now check to shopify store and check latest quantity
        $live_shopify_quantity = $product->checkProductQuantityFromShopifyStore($request->shopify_product_id, $request->shopify_product_variant_id);

        $live_shopify_quantity . " - live shopify count";
        //compare live quantity with our total quantity of database
        if ($live_shopify_quantity >= $total_quantity) {
            //update new quantity to db 

            $productvariant = ProductVariant::findorfail((int) $request->local_product_variant_id);

            //update new quantity 
            //now assign quantity to charity and decreast 
            $live_shopify_quantity = $live_shopify_quantity - $quantity;
            $productvariant->quantity = $live_shopify_quantity;
            $productvariant->save();

            //now insert quantity product to charity table which is 
            $assigning = AssignedCharitiesProducts::create([
                'product_id' => $request->local_product_id,
                'charity_id' => $request->charity_id,
                'variantId' => $request->shopify_product_variant_id,
                'qty' => $quantity
            ]);

            //generate email 

            $data = [
                "status" => 1,
                "message" => "Product Successfully Assigned"
            ];

            //send email to charity and superadmin
            $data['charity_name'] = "islamic relief";
            $data['product_name'] = $request->product_name;
            $data['product_quantity'] = $request->quantity;


            $product_make = array();
            $product_make[0]['title'] =  $request->product_name;
            $product_make[0]['quantity'] =  $request->quantity;
            $product_make[0]['variant_id'] =  $request->shopify_product_variant_id;
            $product_make[0]['grams'] =  0;
            $product_make[0]['price'] = $productvariant->price;
            try {
                //call shopify api and make an order on shopify store
                $customer = "Charity Order Do not Proceed";
                $product->craeteOrderOnShopifyStore($product_make, $customer);
            } catch (Exception $x) {
                return ["error", $x];
            }
            // get product image here = 
            $product_img = ProductImage::where("product_id", $request->local_product_id)->first();
            $image = $product_img->source;
            $data['product_image_here'] = $image;
            $qry = DB::table("page")
                ->selectRaw('page.*,pages.page_id,pages.field_key,pages.field_value,pages.field_type,pages.field_title')
                ->join('pages', 'pages.page_id', "=", "page.id")
                ->where('charity_id', $request->charity_id)
                ->orderBy('pages.id', 'asc')->get();
            $page_content = $qry;
            $logo = $page_content[0]->field_value;
            //      echo $this->db->last_query();die();

            $data['charity_logo_here']  = "http://hldcharity.test/" . 'uploads/pages/' . $logo;

            $email = new  AssignProductToCharity($data);
            Mail::to("charityemail@gmail.com")->send($email);

            return Response::json(array("success" => 1, "message" => "Product has been assigned to charity successfully"));

            //generate notification 
        } else if ($live_shopify_quantity < $total_quantity) {
            //update new quantity to db 

            if ($live_shopify_quantity > $quantity) {
                $productvariant = ProductVariant::findorfail($request->local_product_variant_id);
                //update new quantity 
                //now assign quantity to charity and decreast 
                $live_shopify_quantity = $live_shopify_quantity - $quantity;
                $productvariant->quantity = $live_shopify_quantity;
                $productvariant->save();

                //now insert quantity product to charity table which is 
                $assigning = AssignedCharitiesProducts::create([
                    'product_id' => $request->local_product_id,
                    'charity_id' => $request->charity_id,
                    'variantId' => $request->local_product_variant_id,
                    'qty' => $quantity
                ]);
                $data = [
                    "status" => 1,
                    "message" => "Product Successfully Assigned"
                ];

                //send email to charity and superadmin
                $data['charity_name'] = "islamic relief";
                $data['product_name'] = $request->product_name;
                $data['product_quantity'] = $request->quantity;

                $product_make = array();
                $product_make[0]['title'] =  $request->product_name;
                $product_make[0]['price'] = $productvariant->price;
                $product_make[0]['variant_id'] =  $request->shopify_product_variant_id;
                $product_make[0]['quantity'] =  $request->quantity;
                $product_make[0]['grams'] =  0;
                $customer = "Charity Order Do not Proceed";
                try {
                    //call shopify api and make an order on shopify store
                    $retrun =   $product->craeteOrderOnShopifyStore($product_make, $customer);
                    if ($retrun == 1) {
                    } else {
                        $data_error = [
                            "status" => 0,
                            "message" => "We can't assign the product because shopify not responding at the moment please try again."
                        ];
                        return Response::json($data_error);
                    }
                } catch (Exception $x) {
                    $data_error = [
                        "status" => 0,
                        "message" => "We can't assign the product because shopify not responding at the moment please try again."
                    ];
                    return Response::json($data_error);
                }


                $email = new  AssignProductToCharity($data);
                Mail::to($this->send_mail)->send($email);

                return Response::json(array("success" => 1, "message" => "Product has been assigned to charity successfully"));
            } else {
                $data_error = [
                    "status" => 0,
                    "message" => "We can't assign this product to charity because"
                ];
                return Response::json($data_error);
            }
        }
    }



    /**
     * Method 
     * Method responsible to get all charities and show json to data table
     * Method: GET
     * @param request
     * @return view
     */
    public function show(Request $request)
    {
        return datatables()->of(charity::all())->addColumn('action', function ($row) {

            $btn = '';

            $btn = $btn . '<a href="' . Adminurl('productToCharity/') . $row['id'] . '" class="edit btn btn-primary btn-sm">assign products</a><a href="' . route('charities.edit', $row["id"]) . '" class="edit btn btn-primary btn-sm">edit</a>';
            $btn = $btn . '&nbsp; 
            <form action="' . route('charities.destroy', $row["id"]) . '" method="POST"> 
            ' . method_field("DELETE") . '
            <input type="hidden" name="_token" id="csrf-token" value="' . csrf_token() . '" />

            <button type="submit" class="btn btn-danger" onclick="return confirm(\'Are You Sure Want to Delete?\')">delete</form>';

            return $btn;
        })->rawColumns(['action'])->make(true);
    }


    /**
     * Method 
     * Method responsible to update charity
     * Method: PUT
     * @param request
     * @return view
     */

    public function update(Request $request, $id)
    {
        $validator                  = Validator::make($request->all(), [
            'charity_name'                => 'required',
            'user_name'           => ['required', 'unique:user,user_name,' . $id],
            'email'               => ['required', 'regex:/(.*)@(.*)\.(.*)/i', 'unique:user,email,' . $id],
        ]);
        $data                       = $request->all();

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->with('charity_name', $data['charity_name'])
                ->with('user_name', $data['user_name'])
                ->with('email', $data['email']);
        }
        if (CheckuserPermissions('register'))
            return redirect('' . env('ADMIN_PREFIX') . '/users')->with('add_user_faliure', "You don't have the permission to add a User!");

        $charity = charity::findOrFail($id);
        $charity->charity_name = $request->charity_name;
        $charity->user_name = $request->user_name;
        $charity->email = $request->email;
        $charity->is_active = $request->is_active;
        $charity->save();

        return back()->with('success', 'Charity updated successfuly.');
    }

    /**
     * Method 
     * Method responsible to delete charity
     * Method: DELETE
     * @param request
     * @return view
     */
    public function destroy(Request $request, $id)
    {
        if (CheckuserPermissions('register'))
            return redirect('' . env('ADMIN_PREFIX') . '/users')->with('add_user_faliure', "You don't have the permission to add a User!");

        $charity = charity::findOrFail($id);
        $charity->delete(); //doesn't delete permanently

        return back()->with('success', 'Charity deleted successfuly.');
    }

    /**
     * Method 
     * Method responsible to show form of new charity 
     * Method: GET
     * @param request
     * @return view
     */
    public function create(Request $request)
    {
        if (CheckuserPermissions('register'))
            return redirect('' . env('ADMIN_PREFIX') . '/users')->with('add_user_faliure', "You don't have the permission to add a User!");
        $data = array();
        $data['user_roles'] = DB::table('user_roles')->get();
        return view('admin.pages.charities.create', $data);
    }

    /**
     * Method 
     * Method responsible to fetch products for particular charity for assigning for selct2
     * Page Link where select 2 is displayed domain.com/productToCharity/charity_id
     * Method: GET
     * @param request
     * @return view
     */
    public function getProuctsForCharity(Request $request)
    {
        $charity_id = 0;

        if ($request->charity_id <> null) {
            $charity_id = $request->charity_id;
        }

        //call model to get products and old quantity

        $q = "";
        if ($request->q <> null) {
            $q = $request->q;
        }
        $qry = Product::select("id", "productId", "title")->with(array('productVariants' => function ($query) use ($q) {
            $query->where('quantity', '>', 0);
        }))->whereNotIn("id", AssignedCharitiesProducts::select("product_id")->where("charity_id", $charity_id))->with(array('productImages' => function ($query) use ($q) {
            //   $query->select('id')->where('title', $q);
            //    $query->limit(1);
        }));

        $qry->where('title', "like", $q . "%");

        $products =  $qry->get();

        echo json_encode(array("products" => $products));
    }
}
