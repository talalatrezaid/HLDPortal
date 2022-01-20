<?php

namespace App\Http\Controllers\shopify;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\MappedCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCustomInformation;
use App\Models\ProductExtraDetail;
use App\Models\ProductImage;
use App\Models\ProductInfoHexCode;
use App\Models\ProductOption;
use App\Models\ProductVariant;
use App\Models\Settings;
use App\Models\Store;
use App\Models\StoreFrontCategory;
use App\Models\User;
use App\Models\VariantMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Validator;



class ProductController extends Controller
{

    /**
     * Display product page with listing
     *
     * @param null
     * @return null
     */
    public function index()
    {
        // check if user is authenticate and login
        if (Auth::user() == null) {

            return view('admin.pages.account.login');
        }

        $store_products = array();
        $storefront_categories_count = 0;

        // get store connection data using relationship between User and Store
        $store_data = User::find(Auth::user()->id)->userStores()->get();

        // If user do not have store credentials yet then send an empty product array as we have added conditions based on  $store_products($products)
        if ($store_data->isEmpty()) {
            return view('admin.pages.product.index', [
                'products' => $store_products,
                'storefront_categories_count' => $storefront_categories_count
            ]);
        }

        // get storefront categories data from DB, as we have to add check in view file based on it
        $storefront_categories_count = StoreFrontCategory::get()->count();

        // If storefront categories are not being added in DB by admin yet then send an empty product array and storefront categories count as we have added conditions based on these
        if ($storefront_categories_count <= 0) {

            return view('admin.pages.product.index', [
                'products' => $store_products,
                'storefront_categories_count' => $storefront_categories_count
            ]);
        }

        // get data from setting table to check if pagination setting exists
        $settings = Settings::where('settings_name', 'Pagination')->get();

        // if setting table do not have pagination setting value then set it to 10 by default
        if ($settings->isEmpty()) {

            $per_page = 10;
        } else {

            $per_page = $settings[0]->settings_value;
        }

        // get products data using relationship store and Product with pagination
        $store_products = Store::find($store_data[0]->id)->storeProducts()->with('productVariants')->with('productImages')->with('productOptions')->paginate($per_page);

        return view('admin.pages.product.index', [
            'products' => $store_products,
            'storefront_categories_count' => $storefront_categories_count
        ]);
    }

    /**
     * Initiate sync process and trigger Shopify products API
     * Method called by ajax
     * @param null
     * @return null
     */
    public function syncProducts()
    {

        // get store connection data using relationship between User and Store
        $store_data = User::find(Auth::user()->id)->userStores()->get();

        if ($store_data->isEmpty()) {
            echo "store_not_exists";
            die();
        }

        if ($store_data[0]->is_active == '0') {

            //return redirect('' . env('ADMIN_PREFIX') . '/products')->with('invalid_store', 'Your store is not connected either credentials are not valid or not verified yet');
            echo "invalid_store";
            die();
        }

        // call method to get products data from Shopify products api and save in DB
        $this->callShopifyProductsAPI($store_data, $pagination_link = '');

        //return redirect('' . env('ADMIN_PREFIX') . '/products')->with('sync_process_complete', 'Syncing process has been successfully completed');
        echo "sync_process_complete";
        die();
    }

    /**
     * Get products data from shopify products API
     * @param array, string
     * @return null
     */
    private function callShopifyProductsAPI($store_data, $pagination_link)
    {

        // Shopify store credentials
        $api_key            = $store_data[0]->api_key;
        $api_password       = $store_data[0]->api_password;
        $api_domain_name    = $store_data[0]->api_domain;
        $base_url           = $store_data[0]->base_url;   // "admin/api/2021-04"
        $api_endpoint = '/products.json?limit=250';
        $pagination = '&page_info=' . $pagination_link;

        //$url = 'https://' . $api_key . ':' . $api_password . '@' . $api_domain_name.'/'.$base_url . $api_endpoint . $pagination;

        $response = Http::get('https://' . $api_key . ':' . $api_password . '@' . $api_domain_name . '/' . $base_url . $api_endpoint . $pagination);

        $products = json_decode($response->body(), true);

        // check if products array has some data

        if ($products) {

            // call method to insert data in products DB table
            $this->addProduct($products['products'], $store_data[0]->id);
        }

        // check if response header contains a pagination link
        //  ["Link"]=>[0]=><https://pixusnodoubtshoes.myshopify.com/admin/api/2021-04/products.json?limit=250&page_info=eyJsYXN0X2lkIjo2MzA1LCJkaXJlY3Rpb24iOiJuZXh0In0>; rel="next""
        if (isset($response->headers()['Link'])) {

            // call method from helper to get next and previous page links from response header link array
            $pagination_links =  parsePaginationLinkHeader($response->headers()['Link'][0]);

            // check if $pagination_links variable has some data, means we have pagination request from shopify product API response
            if (isset($pagination_links['next'])) {

                $this->callShopifyProductsAPI($store_data, $pagination_link = $pagination_links['next']);
            }
        }
    }

    /**
     * Get products information from DB
     * Method used in ajax call
     * @param null
     * @return null
     */
    public function getStoreProducts()
    {

        // get store connection data using relationship between User and Store
        $store_data = User::find(Auth::user()->id)->userStores()->get();

        // If user do not have store credentials yet
        if ($store_data->isEmpty()) {

            echo "store_not_exists";
        }

        // get products data using relationship store and Product
        $store_products = Store::find($store_data[0]->id)->storeProducts()->with('productVariants')->with('productImages')->with('productOptions')->get()->toArray();

        return Response::json($store_products);
    }

    /**
     * Step-1 save product basic data in DB to get MySQL product_id
     * Within method called three another methods to save data based on product_id
     *
     * @param array,int
     * @return null
     */
    private function addProduct($products, $store_id)
    {

        foreach ($products as $key => $single_product) {

            //if ($key==20) {
            $product = Product::create([
                'productId' => $single_product['id'],
                'user_id' => Auth::user()->id,
                'store_id' => $store_id,
                'title' => $single_product['title'],
                'description' => $single_product['body_html'],
                'brand' => $single_product['vendor'],
                'type' => $single_product['product_type'],
                'handle' => $single_product['handle'],
                'status' => $single_product['status'],
                'tags' => $single_product['tags'],
            ]);

            $this->addProductVariant($single_product['variants'], $product->id);
            $this->addProductVariantMeta($single_product['variants'], $product->id, $single_product['id']);

            $this->addProductImages($single_product['images'], $product->id);
            $this->addProductOptions($single_product['variants'], $single_product['options'], $product->id);
            $this->addProductExtraDetails($product->id);
            $this->addProductCustomInformation($product->id);
            //}
        }
    }

    /**
     * Step-2 save product variants data in DB
     *
     * @param array,int
     * @return null
     */
    private function addProductVariant($variants, $product_id)
    {

        foreach ($variants as $key => $variant) {

            ProductVariant::create([
                'variantId' => $variant['id'],
                'product_id' => $product_id,
                'title' => $variant['title'],
                'price' => $variant['price'],
                'sku' => $variant['sku'],
                'quantity' => $variant['inventory_quantity'],
                'weight' => $variant['weight'],
                'weight_unit' => $variant['weight_unit'],
                'shipping' => $variant['requires_shipping'],
                'taxable' => $variant['taxable'],
                'inventory_item_id' => $variant['inventory_item_id']
            ]);
        }
    }

    /**
     * Step-2-a save product variants meta data in DB
     *
     * @param array,int
     * @return null
     */
    private function addProductVariantMeta($variants, $product_id, $shopify_product_id)
    {

        foreach ($variants as $key => $variant) {

            VariantMeta::create([
                'product_id' => $product_id,
                'variantId' => $variant['id'],
                'productId' => $shopify_product_id,
                'sale_price' => $variant['price'],
            ]);
        }
    }

    /**
     * Step-3 save product images data in DB
     *
     * @param array,int
     * @return null
     */
    private function addProductImages($images, $product_id)
    {

        foreach ($images as $key => $image) {

            $variantId = null;

            // check if image array has variant_ids, means image is associate with specific variant/variation
            // if variant_ids exist then we have to store MySQL variant_id in images table, else it will be null means image belong to product image
            if (!empty($image['variant_ids'])) {

                // In API response we are getting variant_ids array so loop through it
                foreach ($image['variant_ids'] as $key => $variant_id) {

                    // get variant data from DB where variant_id in image array equals to DB's variantId
                    $variantData = ProductVariant::where('variantId', $variant_id)->first();
                    $variantId = $variantData->id;
                }
            }

            // image url from vendor store / API
            $url = $image['src'];

            $contents = file_get_contents($url);

            // get file name
            $name = substr($url, strrpos($url, '/') + 1);

            // in shopify url it shows ? remove text after it //https://cdn.shopify.com/s/files/1/0505/7442/6300/products/466BEIGEA.jpg?v=1621857612
            $trimmed_name = substr($name, 0, strpos($name, '?'));

            // save image in storage/app/public/product_images
            // we use "php artisan storage:link" command so this will create copy in public/storage/product_images
            // The [D:\laragon\www\fhg\public\storage] link has been connected to [D:\laragon\www\fhg\storage\app/public].
            // Storage::put('public/product_images/' . $trimmed_name, $contents);

            // get url of stored image
            // $image_url = Storage::url('public/product_images/' . $trimmed_name);



            ProductImage::create([
                'product_id' => $product_id,
                'product_variant_id' => $variantId,
                'imageId' => $image['id'],
                'source' => $url,
            ]);
        }
    }

    /**
     * Step-4 save product options data in DB
     *
     * @param array,array,int
     * @return null
     */
    private function addProductOptions($variants, $options, $product_id)
    {

        // initialize array for batch insertion
        $insert_options = array();
        foreach ($variants as $key => $variant) {

            if ($variant['option1'] && $variant['option1'] != 'Default Title') {

                $insert_option = array();

                $option_name = $options[0]['name'];
                $option_value = $variant['option1'];

                // get variant data from DB where shopify-api(variant_id) in image array equals to DB's shopify-db(variantId)
                $variantData = ProductVariant::where('variantId', $variant['id'])->first();
                $variant_id = $variantData->id;

                $insert_option['product_id']         = $product_id;
                $insert_option['product_variant_id'] = $variant_id;
                $insert_option['optionId']           = $options[0]['id'];
                $insert_option['name']               = $option_name;
                $insert_option['value']              = $option_value;

                array_push($insert_options, $insert_option);
            }
            if ($variant['option2']) {

                $insert_option = array();

                $option_name = $options[1]['name'];
                $option_value = $variant['option2'];

                // get variant data from DB where shopify-api(variant_id) in image array equals to DB's shopify-db(variantId)
                $variantData = ProductVariant::where('variantId', $variant['id'])->first();
                $variant_id = $variantData->id;

                $insert_option['product_id']         = $product_id;
                $insert_option['product_variant_id'] = $variant_id;
                $insert_option['optionId']           = $options[1]['id'];
                $insert_option['name']               = $option_name;
                $insert_option['value']              = $option_value;

                array_push($insert_options, $insert_option);
            }
            if ($variant['option3']) {

                $insert_option = array();

                $option_name = $options[2]['name'];
                $option_value = $variant['option3'];

                // get variant data from DB where shopify-api(variant_id) in image array equals to DB's shopify-db(variantId)
                $variantData = ProductVariant::where('variantId', $variant['id'])->first();
                $variant_id = $variantData->id;

                $insert_option['product_id']         = $product_id;
                $insert_option['product_variant_id'] = $variant_id;
                $insert_option['optionId']           = $options[2]['id'];
                $insert_option['name']               = $option_name;
                $insert_option['value']              = $option_value;

                array_push($insert_options, $insert_option);
            }
        }

        ProductOption::insert($insert_options);
    }

    /**
     * Step-5 save product extra details data in DB
     * At this step we are going to save product_id only for product reference, as we're not getting information from API but user can add info from portal
     * @param int
     * @return null
     */
    private function addProductExtraDetails($product_id)
    {

        ProductExtraDetail::create([
            'product_id' => $product_id,
        ]);
    }

    /**
     * Step-6 save product custom information data in DB
     * At this step we are going to save product_id only for product reference, as we're not getting information from API but user can add info from portal
     * @param int
     * @return null
     */
    private function addProductCustomInformation($product_id)
    {

        ProductCustomInformation::create([
            'product_id' => $product_id,
        ]);
    }

    /**
     * Display Edit product screen / pages
     *
     * @param int
     * @return null
     */
    public function editProduct($product_id)
    {

        // get store connection data using relationship between User and Store
        $store_data = User::find(Auth::user()->id)->userStores()->get();

        // If user do not have store credentials yet
        if ($store_data->isEmpty()) {

            return redirect('' . env('ADMIN_PREFIX') . '/products')->with('edit_invalid_store', 'Your store is not connected either credentials are not valid or not verified yet');
        }

        // get product data to verify requested product is belongs to current user
        $verify_product_data = Product::find($product_id);

        if ($verify_product_data->user_id != Auth::user()->id) {
            return redirect('' . env('ADMIN_PREFIX') . '/products')->with('not_allowed', 'permission denied');
        }

        // get requested product data using relationships
        // with categories
        //$product_data = Product::where('id', $product_id)->with('productVariants')->with('productImages')->with('productOptions')->with('productCategories')->get()->toArray();

        // without categories
        $product_data = Product::where('id', $product_id)
            ->with('productVariants')
            ->with('productImages')
            ->with('productOptions')
            ->with('productExtraDetail')

            // getting nested relation
            ->with(array('productCustomInformation' => function ($q) {
                $q->with('productCustomInformationHexCodes');
            }))
            ->get()->toArray();

        // append user_name in $product_data array as we're using it as a customer's brand name
        $product_data[0]['user_brand_name'] = Auth::user()->user_name;

        // get categories associated with requested product
        $product_categories = Product::where('id', $product_id)->with('productCategories')->get()->toArray();

        // check if requested product have categories
        if ($product_categories[0]['product_categories']) {

            // categories associated with current product
            $categories = $product_categories[0]['product_categories'];

            // start finding storefront/FHG categories associated with current product categories
            // store all mapped fhg categories by pushing below code logic
            $categories_mapped_with_fhg = array();

            // product may have more than one category so loop through it
            foreach ($product_categories[0]['product_categories'] as $single_product_category) {

                // get data from mapped categories table using relation with ProductCategory and productCategoryMappedCategories, to get storefront category id
                $mapped_categories = ProductCategory::where('id', $single_product_category)->with('productCategoryMappedCategories')->get()->toArray();

                // we may have more than one category in mapping table
                foreach ($mapped_categories as $single_mapped_category) {

                    // check if mapping exists with product category, otherwise means current product have categories but not mapped with storefront/FHG categories so skip this process
                    if ($single_mapped_category['product_category_mapped_categories']) {

                        foreach ($single_mapped_category['product_category_mapped_categories'] as $single_mapped_storefront_category) {

                            // get storefront category info
                            $storefront_categories = StoreFrontCategory::find($single_mapped_storefront_category['store_front_category_id'])->toArray();

                            // prepare array to push in $categories_mapped_with_fhg
                            $category_mapped_with_fhg = array(
                                //'product_category_id' => $single_mapped_category['id'],
                                //'product_category_name' => $single_mapped_category['name'],
                                'storefront_category_id' => $single_mapped_storefront_category['store_front_category_id'],
                                'storefront_category_name' => $storefront_categories['name'],
                                'storefront_category_type' => $storefront_categories['categoryParentId'],
                            );

                            array_push($categories_mapped_with_fhg, $category_mapped_with_fhg);
                        }
                    }
                }
            }

            // storefront/FHG categories associated with current product's categories
            $storefront_mapped_categories = $categories_mapped_with_fhg;
        }

        // if current product do not any category then set variables to empty as we are passing these to view
        else {

            $categories = "";
            $storefront_mapped_categories = array();
        }

        // get variant meta
        $variant_meta_data = VariantMeta::where('product_id', $product_id)->get()->toArray();

        // create custom array and get only matched variation data
        $filtered_variant_meta = array();
        foreach ($product_data[0]['product_variants'] as $key => $single_product_variant) {
            foreach ($variant_meta_data as $single_variant_meta) {
                if ($single_product_variant['variantId'] == $single_variant_meta['variantId']) {

                    $filtered_variant_meta[$key]['variantId'] = $single_variant_meta['variantId'];
                    $filtered_variant_meta[$key]['sale_price'] = $single_variant_meta['sale_price'];
                    $filtered_variant_meta[$key]['product_id'] = $single_variant_meta['product_id'];
                    $filtered_variant_meta[$key]['productId'] = $single_variant_meta['productId'];
                }
            }
        }

        // get countries list from DB as we have to use in product custom information section/tab
        $countries_array = Country::get()->toArray();

        // if countries data not exists in DB then set variable to empty to avoid error
        if (!$countries_array) {

            $countries_array = array();
        }

        //DB::enableQueryLog(); // Enable query log
        //dd(DB::getQueryLog()); // Show results of log

        return view('admin.pages.product.edit', [
            'product' => $product_data,
            'product_categories' => $categories,
            'storefront_categories' => array_unique($storefront_mapped_categories, SORT_REGULAR),
            'variant_meta_data' => $filtered_variant_meta,
            'countries_list' => $countries_array,
        ]);
    }

    /**
     * Method triggered from UI ajax call edit product page
     * Method responsible for update sale price column in product variant DB table
     * @param null
     * @return boolean
     */


    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to add product as "simple product" in storefront/Magento using Create Product API
     * Endpoint: https://domain.com/rest/default/V1/products/
     * Method: POST
     * @param string,string,array,array,array
     * @return array
     */
    private function addSimpleMagentoProduct($auth_token, $base_url, $product_data, $prepared_categories_to_upload, $prepared_custom_attributes_to_upload)
    {

        // (4 is the value of magento default attribute set)
        $attribute_set_id = env('MAGENTO_STORE_ATTRIBUTE_SET_ID', 4);

        $variant_meta_sale_price = VariantMeta::select('sale_price')->where('variantId', $product_data[0]['product_variants'][0]['variantId'])->first();

        if ($variant_meta_sale_price->sale_price) {

            $sale_price = $variant_meta_sale_price->sale_price;
        } else {

            $sale_price = $product_data[0]['product_variants'][0]['price'];
        }

        $prepared_data = array(

            "product" => array(
                //'id' => 2079,
                'sku' => ($product_data[0]['product_variants'][0]['sku'] ? $product_data[0]['product_variants'][0]['sku'] : "Default_SKU_" . $product_data[0]['product_variants'][0]['id']),
                'name' => $product_data[0]['title'],
                'attribute_set_id' => $attribute_set_id,
                'price' => $sale_price,
                'status' => ($product_data[0]['status'] == 'active' ? 1 : 0),
                'visibility' => ($product_data[0]['product_extra_detail']['is_visible_on_site'] ? 4 : 1),
                'type_id' => 'simple',
                //'created_at' => '2017-11-29 20:40:07',
                //'updated_at' => '2017-11-29 20:40:07',
                'weight' => $product_data[0]['product_variants'][0]['weight'],
                'extension_attributes' => array(
                    'website_ids' => array(
                        0 => 1,
                    ),
                    'category_links' => $prepared_categories_to_upload,
                    'stock_item' => array(
                        'qty' => ($product_data[0]['product_variants'][0]['quantity'] ? $product_data[0]['product_variants'][0]['quantity'] : "0"),
                        'is_in_stock' => true,
                        'use_config_min_qty' => false,
                        'use_config_min_sale_qty' => 0,
                        'min_sale_qty' => ($product_data[0]['product_extra_detail']['order_minimum_quantity'] ? $product_data[0]['product_extra_detail']['order_minimum_quantity'] : 1),
                        'use_config_max_sale_qty' => false,
                        'max_sale_qty' => ($product_data[0]['product_extra_detail']['order_maximum_quantity'] ? $product_data[0]['product_extra_detail']['order_maximum_quantity'] : 100),
                    )
                ),
                'product_links' => array(),
                'options' => array(),
                'media_gallery_entries' => array(),
                'tier_prices' => array(),
                'custom_attributes' => $prepared_custom_attributes_to_upload,
            )
        );


        /*
          // variable product complete sample
          $prepared_data = array(
            "product" => array (
                //'id' => 2079,
                'sku' => $product_data[0]['product_variants'][0]['sku'],
                'name' => $product_data[0]['title'],
                'attribute_set_id' => 4,
                'price' => $product_data[0]['product_variants'][0]['price'],
                'status' => ($product_data[0]['status'] == 'active' ? 1 : 0),
                'visibility' => 1,
                'type_id' => 'simple',
                //'created_at' => '2017-11-29 20:40:07',
                //'updated_at' => '2017-11-29 20:40:07',
                'weight' => $product_data[0]['product_variants'][0]['weight'],
                'extension_attributes' => array (
                    'website_ids' => array (
                            0 => 1,
                    ),
                    'category_links' => array (
                        0 => array (
                                'position' => 0,
                                'category_id' => '11',
                        ),
                        1 => array (
                                'position' => 1,
                                'category_id' => '12',
                        ),
                        2 =>
                          array (
                            'position' => 2,
                            'category_id' => '16',
                        ),
                    ),
                    'stock_item' => array (
                        'item_id' => 2079,
                        'product_id' => 2079,
                        'stock_id' => 1,
                        'qty' => 10,
                        'is_in_stock' => true,
                        'is_qty_decimal' => false,
                        'show_default_notification_message' => false,
                        'use_config_min_qty' => true,
                        'min_qty' => 0,
                        'use_config_min_sale_qty' => 1,
                        'min_sale_qty' => 1,
                        'use_config_max_sale_qty' => true,
                        'max_sale_qty' => 10000,
                        'use_config_backorders' => true,
                        'backorders' => 0,
                        'use_config_notify_stock_qty' => true,
                        'notify_stock_qty' => 1,
                        'use_config_qty_increments' => true,
                        'qty_increments' => 0,
                        'use_config_enable_qty_inc' => true,
                        'enable_qty_increments' => false,
                        'use_config_manage_stock' => true,
                        'manage_stock' => true,
                        'low_stock_date' => NULL,
                        'is_decimal_divided' => false,
                        'stock_status_changed_auto' => 0,
                    ),
                ),
                'product_links' => array (
                ),
                  'options' => array (
                ),
                'media_gallery_entries' => array (
                ),
                'tier_prices' => array (
                ),
                'custom_attributes' => array (
                    0 => array (
                      'attribute_code' => 'description',
                      'value' => 'The Champ Tee keeps you cool and dry while you do your thing. Let everyone know who you are by adding your name on the back for only $10.',
                    ),
                    1 => array (
                      'attribute_code' => 'color',
                      'value' => '52',
                    ),
                    {
                      "attribute_code": "image",
                      "value": "/w/i/wijn1_back_1.jpg"
                    },
                    {
                      "attribute_code": "small_image",
                      "value": "/w/i/wijn1_back_1.jpg"
                    },
                    {
                      "attribute_code": "thumbnail",
                      "value": "/w/i/wijn1_back_1.jpg"
                    },
                    2 => array (
                        'attribute_code' => 'category_ids',
                        'value' => array (
                            0 => '11',
                            1 => '12',
                            2 => '16',
                        ),
                    ),
                    3 => array (
                        'attribute_code' => 'options_container',
                        'value' => 'container2',
                    ),
                    4 => array (
                      'attribute_code' => 'required_options',
                      'value' => '0',
                    ),
                    5 => array (
                      'attribute_code' => 'has_options',
                      'value' => '0',
                    ),
                    6 => array (
                      'attribute_code' => 'url_key',
                      'value' => 'champ-tee-small',
                    ),
                    7 => array (
                      'attribute_code' => 'tax_class_id',
                      'value' => '2',
                    ),
                    8 => array (
                      'attribute_code' => 'material',
                      'value' => '148',
                    ),
                    9 => array (
                      'attribute_code' => 'size',
                      'value' => '168',
                    ),
                    10 => array (
                      'attribute_code' => 'pattern',
                      'value' => '196',
                    ),
                ),
            )
        );

        */

        $prepared_data_json = json_encode($prepared_data);


        // Magento create product endpoint
        $magento_create_product_endpoint  = 'default/V1/products/';

        //http://192.168.10.5/magento240/rest/default/V1/products/

        $response = Http::acceptJson()->withBody($prepared_data_json, 'application/json')->withToken($auth_token)->post($base_url . $magento_create_product_endpoint);

        if (!$response->successful()) {

            $error_message = $response['message'];
            echo "upload_failed";
            die();
        }

        $response_product_info = json_decode($response->body(), true);

        if ($response_product_info) {

            return $response_product_info;
        }
    }

    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to attach image with newly added product in Magento store using API
     * Endpoint: https://domain.com/rest/default/V1/products/sku/media
     * Method: POST
     * @param string, string, string, string, int
     * @return array
     */
    private function attacheImageWithMagentoProduct($auth_token, $base_url, $response_product_sku, $image_source, $magento_product_id)
    {


        // get file extenstion
        $infoPath = pathinfo(public_path($image_source));
        $image_extension = $infoPath['extension'];

        $site_url = URL::to('/');

        // fix for open ssl issue
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $image = file_get_contents($site_url . $image_source, true, stream_context_create($arrContextOptions));

        $encoded_image = base64_encode($image);

        $prepared_image_data = array(
            'entry' =>
            array(
                'media_type' => 'image',
                'label' => 'Image',
                'position' => 1,
                'disabled' => false,
                'types' =>
                array(
                    0 => 'image',
                    1 => 'small_image',
                    2 => 'thumbnail',
                ),
                'content' =>
                array(
                    'base64_encoded_data' => $encoded_image,
                    'type' => 'image/jpeg',
                    'name' => 'product_' . date('m-d-Y') . time() . '.' . $image_extension,
                ),
            ),
        );


        $prepared_data_json = json_encode($prepared_image_data);



        // Magento attach image to product endpoint
        $magento_add_media_to_product_endpoint  = 'default/V1/products/' . $response_product_sku . '/media';

        //http://fhgstorefront.test/rest/default/V1/products/MF: TP3438: TEST1/media

        $response = Http::acceptJson()->withBody($prepared_data_json, 'application/json')->withToken($auth_token)->post($base_url . $magento_add_media_to_product_endpoint);

        if (!$response->successful()) {

            $error_message = $response['message'];
            echo "image_upload_to_product_error";
            dd($error_message);
            die();
        }

        $response_image_id = json_decode($response->body(), true);

        if ($response_image_id) {

            return $response_image_id;
        }
    }


    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to add product as "configurable product" in storefront/Magento using Create Product API
     * Endpoint: https://domain.com/rest/default/V1/products/
     * Method: POST
     * @param string,string,array,array,array
     * @return array
     */
    private function addConfigurableMagentoProduct($auth_token, $base_url, $product_data, $prepared_categories_to_upload, $product_description)
    {

        // (4 is the value of magento default attribute set)
        $attribute_set_id = env('MAGENTO_STORE_ATTRIBUTE_SET_ID', 4);

        $variant_meta_sale_price = VariantMeta::select('sale_price')->where('variantId', $product_data[0]['product_variants'][0]['variantId'])->first();

        if ($variant_meta_sale_price->sale_price) {

            $sale_price = $variant_meta_sale_price->sale_price;
        } else {

            $sale_price = $product_data[0]['product_variants'][0]['price'];
        }

        $prepared_data = array(
            "product" => array(
                'sku' => ($product_data[0]['product_variants'][0]['sku'] ? $product_data[0]['product_variants'][0]['sku'] . '_parent' : "Default_Parent_SKU_" . $product_data[0]['product_variants'][0]['id']),
                'name' => $product_data[0]['title'],
                'attribute_set_id' => $attribute_set_id,
                'price' => $sale_price,
                'status' => ($product_data[0]['status'] == 'active' ? 1 : 0),
                'visibility' => ($product_data[0]['product_extra_detail']['is_visible_on_site'] ? 4 : 1),
                'type_id' => 'configurable',
                'weight' => $product_data[0]['product_variants'][0]['weight'],
                'extension_attributes' => array(
                    'website_ids' => array(
                        0 => 1,
                    ),
                    'category_links' => $prepared_categories_to_upload,
                    'stock_item' => array(
                        'qty' => ($product_data[0]['product_variants'][0]['quantity'] ? $product_data[0]['product_variants'][0]['quantity'] : "0"),
                        'is_in_stock' => true,
                        'use_config_min_qty' => false,
                        'use_config_min_sale_qty' => 0,
                        'min_sale_qty' => ($product_data[0]['product_extra_detail']['order_minimum_quantity'] ? $product_data[0]['product_extra_detail']['order_minimum_quantity'] : 1),
                        'use_config_max_sale_qty' => false,
                        'max_sale_qty' => ($product_data[0]['product_extra_detail']['order_maximum_quantity'] ? $product_data[0]['product_extra_detail']['order_maximum_quantity'] : 100),
                    ),
                ),
                'custom_attributes' => $product_description,
            )
        );


        /*
          // variable product complete sample
          $prepared_data = array(
            "product" => array (
                //'id' => 2079,
                'sku' => $product_data[0]['product_variants'][0]['sku'],
                'name' => $product_data[0]['title'],
                'attribute_set_id' => 4,
                'price' => $product_data[0]['product_variants'][0]['price'],
                'status' => ($product_data[0]['status'] == 'active' ? 1 : 0),
                'visibility' => 1,
                'type_id' => 'simple',
                //'created_at' => '2017-11-29 20:40:07',
                //'updated_at' => '2017-11-29 20:40:07',
                'weight' => $product_data[0]['product_variants'][0]['weight'],
                'extension_attributes' => array (
                    'website_ids' => array (
                            0 => 1,
                    ),
                    'category_links' => array (
                        0 => array (
                                'position' => 0,
                                'category_id' => '11',
                        ),
                        1 => array (
                                'position' => 1,
                                'category_id' => '12',
                        ),
                        2 =>
                          array (
                            'position' => 2,
                            'category_id' => '16',
                        ),
                    ),
                    'stock_item' => array (
                        'item_id' => 2079,
                        'product_id' => 2079,
                        'stock_id' => 1,
                        'qty' => 10,
                        'is_in_stock' => true,
                        'is_qty_decimal' => false,
                        'show_default_notification_message' => false,
                        'use_config_min_qty' => true,
                        'min_qty' => 0,
                        'use_config_min_sale_qty' => 1,
                        'min_sale_qty' => 1,
                        'use_config_max_sale_qty' => true,
                        'max_sale_qty' => 10000,
                        'use_config_backorders' => true,
                        'backorders' => 0,
                        'use_config_notify_stock_qty' => true,
                        'notify_stock_qty' => 1,
                        'use_config_qty_increments' => true,
                        'qty_increments' => 0,
                        'use_config_enable_qty_inc' => true,
                        'enable_qty_increments' => false,
                        'use_config_manage_stock' => true,
                        'manage_stock' => true,
                        'low_stock_date' => NULL,
                        'is_decimal_divided' => false,
                        'stock_status_changed_auto' => 0,
                    ),
                ),
                'product_links' => array (
                ),
                  'options' => array (
                ),
                'media_gallery_entries' => array (
                ),
                'tier_prices' => array (
                ),
                'custom_attributes' => array (
                    0 => array (
                      'attribute_code' => 'description',
                      'value' => 'The Champ Tee keeps you cool and dry while you do your thing. Let everyone know who you are by adding your name on the back for only $10.',
                    ),
                    1 => array (
                      'attribute_code' => 'color',
                      'value' => '52',
                    ),
                    {
                      "attribute_code": "image",
                      "value": "/w/i/wijn1_back_1.jpg"
                    },
                    {
                      "attribute_code": "small_image",
                      "value": "/w/i/wijn1_back_1.jpg"
                    },
                    {
                      "attribute_code": "thumbnail",
                      "value": "/w/i/wijn1_back_1.jpg"
                    },
                    2 => array (
                        'attribute_code' => 'category_ids',
                        'value' => array (
                            0 => '11',
                            1 => '12',
                            2 => '16',
                        ),
                    ),
                    3 => array (
                        'attribute_code' => 'options_container',
                        'value' => 'container2',
                    ),
                    4 => array (
                      'attribute_code' => 'required_options',
                      'value' => '0',
                    ),
                    5 => array (
                      'attribute_code' => 'has_options',
                      'value' => '0',
                    ),
                    6 => array (
                      'attribute_code' => 'url_key',
                      'value' => 'champ-tee-small',
                    ),
                    7 => array (
                      'attribute_code' => 'tax_class_id',
                      'value' => '2',
                    ),
                    8 => array (
                      'attribute_code' => 'material',
                      'value' => '148',
                    ),
                    9 => array (
                      'attribute_code' => 'size',
                      'value' => '168',
                    ),
                    10 => array (
                      'attribute_code' => 'pattern',
                      'value' => '196',
                    ),
                ),
            )
        );

        */

        $prepared_data_json = json_encode($prepared_data);


        // Magento create product endpoint
        $magento_create_product_endpoint  = 'default/V1/products/';

        //http://192.168.10.5/magento240/rest/default/V1/products/

        $response = Http::acceptJson()->withBody($prepared_data_json, 'application/json')->withToken($auth_token)->post($base_url . $magento_create_product_endpoint);

        if (!$response->successful()) {

            $error_message = $response['message'];
            echo "upload_failed";
            die();
        }

        $response_product_info = json_decode($response->body(), true);

        if ($response_product_info) {

            return $response_product_info;
        }
    }

    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to add product as "child simple product for Configurable product" in storefront/Magento using Create Product API
     * Endpoint: https://domain.com/rest/default/V1/products/
     * Method: POST
     * @param string,string,array,array,array
     * @return array
     */
    private function addConfigurableSimpleMagentoProduct($auth_token, $base_url, $product_data, $prepared_categories_to_upload, $prepared_custom_attributes_to_upload, $product_variant_seller_portal_data)
    {

        // (4 is the value of magento default attribute set)
        $attribute_set_id = env('MAGENTO_STORE_ATTRIBUTE_SET_ID', 4);

        $variant_meta_sale_price = VariantMeta::select('sale_price')->where('variantId', $product_variant_seller_portal_data['variantId'])->first();

        if ($variant_meta_sale_price->sale_price) {

            $sale_price = $variant_meta_sale_price->sale_price;
        } else {

            $sale_price = $product_data[0]['product_variants'][0]['price'];
        }

        $prepared_data = array(

            "product" => array(
                'sku' => ($product_variant_seller_portal_data['sku'] ? $product_variant_seller_portal_data['sku'] : "Default_SKU_" . $product_variant_seller_portal_data['id']),
                'name' => $product_variant_seller_portal_data['title'],
                'attribute_set_id' => $attribute_set_id,
                'price' => $sale_price,
                'status' => 1,
                'visibility' => 1,
                'type_id' => 'simple',
                'weight' => $product_variant_seller_portal_data['weight'],
                'extension_attributes' => array(
                    'website_ids' => array(
                        0 => 1,
                    ),
                    'category_links' => $prepared_categories_to_upload,
                    'stock_item' => array(
                        'qty' => ($product_variant_seller_portal_data['quantity'] ? $product_variant_seller_portal_data['quantity'] : "0"),
                        'is_in_stock' => true,
                    ),
                ),
                'custom_attributes' => $prepared_custom_attributes_to_upload,
            )
        );


        /*
          // variable product complete sample
          $prepared_data = array(
            "product" => array (
                //'id' => 2079,
                'sku' => $product_data[0]['product_variants'][0]['sku'],
                'name' => $product_data[0]['title'],
                'attribute_set_id' => 4,
                'price' => $product_data[0]['product_variants'][0]['price'],
                'status' => ($product_data[0]['status'] == 'active' ? 1 : 0),
                'visibility' => 1,
                'type_id' => 'simple',
                //'created_at' => '2017-11-29 20:40:07',
                //'updated_at' => '2017-11-29 20:40:07',
                'weight' => $product_data[0]['product_variants'][0]['weight'],
                'extension_attributes' => array (
                    'website_ids' => array (
                            0 => 1,
                    ),
                    'category_links' => array (
                        0 => array (
                                'position' => 0,
                                'category_id' => '11',
                        ),
                        1 => array (
                                'position' => 1,
                                'category_id' => '12',
                        ),
                        2 =>
                          array (
                            'position' => 2,
                            'category_id' => '16',
                        ),
                    ),
                    'stock_item' => array (
                        'item_id' => 2079,
                        'product_id' => 2079,
                        'stock_id' => 1,
                        'qty' => 10,
                        'is_in_stock' => true,
                        'is_qty_decimal' => false,
                        'show_default_notification_message' => false,
                        'use_config_min_qty' => true,
                        'min_qty' => 0,
                        'use_config_min_sale_qty' => 1,
                        'min_sale_qty' => 1,
                        'use_config_max_sale_qty' => true,
                        'max_sale_qty' => 10000,
                        'use_config_backorders' => true,
                        'backorders' => 0,
                        'use_config_notify_stock_qty' => true,
                        'notify_stock_qty' => 1,
                        'use_config_qty_increments' => true,
                        'qty_increments' => 0,
                        'use_config_enable_qty_inc' => true,
                        'enable_qty_increments' => false,
                        'use_config_manage_stock' => true,
                        'manage_stock' => true,
                        'low_stock_date' => NULL,
                        'is_decimal_divided' => false,
                        'stock_status_changed_auto' => 0,
                    ),
                ),
                'product_links' => array (
                ),
                  'options' => array (
                ),
                'media_gallery_entries' => array (
                ),
                'tier_prices' => array (
                ),
                'custom_attributes' => array (
                    0 => array (
                      'attribute_code' => 'description',
                      'value' => 'The Champ Tee keeps you cool and dry while you do your thing. Let everyone know who you are by adding your name on the back for only $10.',
                    ),
                    1 => array (
                      'attribute_code' => 'color',
                      'value' => '52',
                    ),
                    {
                      "attribute_code": "image",
                      "value": "/w/i/wijn1_back_1.jpg"
                    },
                    {
                      "attribute_code": "small_image",
                      "value": "/w/i/wijn1_back_1.jpg"
                    },
                    {
                      "attribute_code": "thumbnail",
                      "value": "/w/i/wijn1_back_1.jpg"
                    },
                    2 => array (
                        'attribute_code' => 'category_ids',
                        'value' => array (
                            0 => '11',
                            1 => '12',
                            2 => '16',
                        ),
                    ),
                    3 => array (
                        'attribute_code' => 'options_container',
                        'value' => 'container2',
                    ),
                    4 => array (
                      'attribute_code' => 'required_options',
                      'value' => '0',
                    ),
                    5 => array (
                      'attribute_code' => 'has_options',
                      'value' => '0',
                    ),
                    6 => array (
                      'attribute_code' => 'url_key',
                      'value' => 'champ-tee-small',
                    ),
                    7 => array (
                      'attribute_code' => 'tax_class_id',
                      'value' => '2',
                    ),
                    8 => array (
                      'attribute_code' => 'material',
                      'value' => '148',
                    ),
                    9 => array (
                      'attribute_code' => 'size',
                      'value' => '168',
                    ),
                    10 => array (
                      'attribute_code' => 'pattern',
                      'value' => '196',
                    ),
                ),
            )
        );

        */

        $prepared_data_json = json_encode($prepared_data);


        // Magento create product endpoint
        $magento_create_product_endpoint  = 'default/V1/products/';

        //http://192.168.10.5/magento240/rest/default/V1/products/

        $response = Http::acceptJson()->withBody($prepared_data_json, 'application/json')->withToken($auth_token)->post($base_url . $magento_create_product_endpoint);

        if (!$response->successful()) {

            $error_message = $response['message'];
            echo "error_in_addConfigurableSimpleMagentoProduct";
            dd($error_message);
            echo "upload_failed";
            die();
        }

        $response_product_info = json_decode($response->body(), true);

        if ($response_product_info) {

            return $response_product_info;
        }
    }

    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to create association between configurable and its simple(child) products using Magento REST API
     * Endpoint: https://domain.com/rest/default/V1/configurable-products/Parent-SKU/child
     * Method: POST
     * @param string,string,array,string
     */
    private function createMagentoConfigurableAndSimpleProductAssociation($auth_token, $base_url, $configurable_product_child_skus, $configurable_product_parent_sku)
    {

        foreach ($configurable_product_child_skus as $configurable_product_child_sku) {

            $prepared_data_json = json_encode($configurable_product_child_sku);


            // Magento create product endpoint
            $link_simple_products_to_configurable_product_endpoint  = 'default/V1/configurable-products/' . $configurable_product_parent_sku . '/child';

            //https://domain.com/rest/default/V1/configurable-products/Parent-SKU/child

            $response = Http::acceptJson()->withBody($prepared_data_json, 'application/json')->withToken($auth_token)->post($base_url . $link_simple_products_to_configurable_product_endpoint);

            if (!$response->successful()) {

                $error_message = $response['message'];
                echo "error_in_createMagentoConfigurableAndSimpleProductAssociation";
                dd($error_message);
                echo "upload_failed";
                die();
            }

            $response_association_info = json_decode($response->body(), true);
        }
    }


    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to get mapped categories, as user has define which Magento categories are being used with requested product
     * @param int
     * @return array
     */
    private function getMappedCategoriesToUploadWithProduct($product_id)
    {

        $prepared_categories_to_upload = array();

        // get categories associated with requested product
        $product_categories = Product::where('id', $product_id)->with('productCategories')->get()->toArray();

        // check if requested product have categories
        if ($product_categories[0]['product_categories']) {

            // start finding storefront/FHG categories associated with current product categories
            // store all mapped fhg categories by pushing below code logic
            $categories_mapped_with_fhg = array();

            // product may have more than one category so loop through it
            foreach ($product_categories[0]['product_categories'] as $single_product_category) {

                // get data from mapped categories table using relation with ProductCategory and productCategoryMappedCategories, to get storefront category id
                $mapped_categories = ProductCategory::where('id', $single_product_category)->with('productCategoryMappedCategories')->get()->toArray();

                // we may have more than one category in mapping table
                foreach ($mapped_categories as $single_mapped_category) {

                    // check if mapping exists with product category, otherwise means current product have categories but not mapped with storefront/FHG categories so skip this process
                    if ($single_mapped_category['product_category_mapped_categories']) {

                        foreach ($single_mapped_category['product_category_mapped_categories'] as $single_mapped_storefront_category) {

                            // get storefront category info
                            $storefront_categories = StoreFrontCategory::find($single_mapped_storefront_category['store_front_category_id'])->toArray();

                            // prepare array to push in $categories_mapped_with_fhg
                            $category_mapped_with_fhg = array(
                                //'product_category_id' => $single_mapped_category['id'],
                                //'product_category_name' => $single_mapped_category['name'],

                                // used for seller portal, $single_mapped_storefront_category['store_front_category_id'] = primary key of table
                                //'storefront_category_id' => $single_mapped_storefront_category['store_front_category_id'],

                                // used for upload tp magento, $storefront_categories['categoryId'] = magento reference id
                                'storefront_category_id' => $storefront_categories['categoryId'],

                                //'storefront_category_name' => $storefront_categories['name'],
                                //'storefront_category_type' => $storefront_categories['type'],
                            );

                            array_push($categories_mapped_with_fhg, $category_mapped_with_fhg);
                        }
                    }
                }
            }

            // storefront/FHG categories associated with current product's categories
            $storefront_mapped_categories = array_unique($categories_mapped_with_fhg, SORT_REGULAR);
        }

        // if current product do not any category then set variables to empty as we are passing these to view
        else {

            $storefront_mapped_categories = array();
        }

        // check if we have got mapped categories, means product categories has mapping with storefront categories
        if (isset($storefront_mapped_categories) && !empty($storefront_mapped_categories)) {

            foreach ($storefront_mapped_categories as $key => $storefront_mapped_category) {

                // prepare data according to magento required json structure
                $prepared_categories_to_upload[] =  array(
                    'position' => $key,
                    'category_id' => $storefront_mapped_category['storefront_category_id'],
                );
            }
        }

        return $prepared_categories_to_upload;
    }

    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to get default attribute set from Magento
     * @param string, string
     * @return array
     */
    private function getAllAttributesFromMagento($auth_token, $base_url)
    {

        // get Magento store attribute set id from env file
        $attribute_set_id = env('MAGENTO_STORE_ATTRIBUTE_SET_ID', 4);

        // Magento get attributes endpoint (4 is the value of magento default attribute set)
        $magento_get_attributes_endpoint  = 'default/V1/products/attribute-sets/' . $attribute_set_id . '/attributes/';

        //http://192.168.10.5/magento240/rest/default/V1/products/attribute-sets/4/attributes/

        $response = Http::acceptJson()->withToken($auth_token)->get($base_url . $magento_get_attributes_endpoint);

        if (!$response->successful()) {

            echo "error_in_getAllAttributesFromMagento";
            $error_message = $response['message'];
            dd($error_message);
        }

        // array contains all options/attributes names and values from Magento store
        $all_magento_attributes = json_decode($response->body(), true);

        return $all_magento_attributes;
    }

    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to compare and get matched/existing attributes names from Magento attribute list
     * @param array, int
     * @return array
     */
    private function getAllExistingAttributeNames($all_magento_attributes, $product_id)
    {

        $existing_attributes_names = array();

        // get product attributes / options of requested product from seller portal product_options table
        $product_options_obj = ProductOption::select('name', 'value')->where('product_id', $product_id)->get();

        if ($product_options_obj) {

            // array contains all options/attributes names and values of requested product
            $product_options = $product_options_obj->toArray();

            // lets find if requested product attribute/option name and value exists in Magento attributes array/lists
            // product may have multiple options so loop it for matched attributes
            foreach ($product_options as $product_option) {

                foreach ($all_magento_attributes as $attribute) {

                    // check if product option name found in Magento attributes list
                    if (isset($attribute['attribute_code']) && $attribute['attribute_code'] == strtolower($product_option['name'])) {

                        // product option found, store it in array
                        $existing_attributes_names[] = $attribute['attribute_code'];
                    }

                    /*
                    // in our DB we have column name "Colour" and Magento spells it as "Color"


                    elseif (isset($attribute['attribute_code']) && $attribute['attribute_code'] == "color" && isset($product_option['name']) && strtolower($product_option['name']) == "colour") {

                        $existing_attributes_names[] = $attribute['attribute_code'];

                    }

                    // we have decided to add this attribute as new attribute in magento instead of name handling
                    */
                }
            }
        }

        return array_unique($existing_attributes_names);
    }

    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to compare and get unmatched attributes using matched attributes array
     * @param array, int
     * @return array
     */
    private function getAllNonExistingAttributeNames($existing_attributes_names, $product_id)
    {

        // for unmatched attributes, store unmatched attributes so we can add them to Magento
        $unmatched_option_names = array();

        // get product attributes / options of requested product from seller portal product_options table
        $product_options_obj = ProductOption::select('name', 'value')->where('product_id', $product_id)->get();

        if ($product_options_obj) {

            // array contains all options/attributes names and values of requested product
            $product_options = $product_options_obj->toArray();

            foreach ($product_options as $product_option) {

                if (!in_array(strtolower($product_option['name']), $existing_attributes_names)) {

                    $unmatched_option_names[] = strtolower($product_option['name']);
                }

                /*
                // comment this logic because previously we were handling Colour = Color, now we are adding this as new attribute with different spelling
                elseif (strtolower($product_option['name']) == "colour" && !in_array("color", $existing_attributes_names )){

                    $unmatched_option_names[] = "color";
                }

                */
            }
        }

        return array_unique($unmatched_option_names);
    }


    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to create new attributes/options using Magento API
     * In return we will get all new attributes their values/ids to use in product upload
     * Endpoint: https://domain.com/rest/default/V1/products/attributes/
     * Method: POST
     * @param string, string, array, int
     * @return array
     */
    private function createAndGetNonExistingAttributeWithValues($auth_token, $base_url, $non_existing_attributes_names, $product_id)
    {

        $prepared_attributes_with_values = array();
        $options = array();

        // we have an array of attributes which needs to add to Magento store
        foreach ($non_existing_attributes_names as $unmatched_attributes_name) {

            // get the option values from DB product_options table to add in Magento store
            $seller_portal_options_obj = ProductOption::select('value AS label')->where('product_id', $product_id)->where('name', $unmatched_attributes_name)->get();

            // check if requested attribute like (size) has options/values in DB
            if ($seller_portal_options_obj) {

                // values array i.e array:2 [ 0 => array:1 [ "label" => "1" ] 1 => array:1 [ "label" => "2" ]  ]
                // we use alias "label" as it require in Magento structure
                $prepared_options = $seller_portal_options_obj->toArray();

                $post_body = [
                    'attribute' => array(
                        'is_wysiwyg_enabled' => false,
                        'is_html_allowed_on_front' => false,
                        'used_for_sort_by' => false,
                        'is_filterable' => true,
                        'is_filterable_in_search' => true,
                        'is_used_in_grid' => true,
                        'is_visible_in_grid' => false,
                        'is_filterable_in_grid' => true,
                        'position' => 0,
                        'apply_to' => array(),
                        'is_searchable' => '1',
                        'is_visible_in_advanced_search' => '1',
                        'is_comparable' => '1',
                        'is_used_for_promo_rules' => '0',
                        'is_visible_on_front' => '0',
                        'used_in_product_listing' => '1',
                        'is_visible' => true,
                        'scope' => 'global',
                        'attribute_code' => $unmatched_attributes_name,
                        'frontend_input' => 'select',
                        'entity_type_id' => '4',
                        'is_required' => false,
                        'options' => $prepared_options,
                        'is_user_defined' => true,
                        'default_frontend_label' => $unmatched_attributes_name,
                        'frontend_labels' => NULL,
                        'backend_type' => 'int',
                        'default_value' => '',
                        'is_unique' => '0',
                    ),
                ];

                // Magento add new attributes endpoint
                $magento_add_new_attributes_endpoint  = 'default/V1/products/attributes';

                //http://192.168.10.10/magento240/rest/default/V1/products/attributes

                // call API to create attribute with its options/values
                $response = Http::acceptJson()->withBody(json_encode($post_body), 'application/json')->withToken($auth_token)->post($base_url . $magento_add_new_attributes_endpoint);


                /*
                // here we are going to handle issue if requested unmatched attribute already exists in any other attribute set then above endpoint throwing error
                // so we need to get this attribute data
                if ($response->status() == 400){

                    // Magento add new attributes endpoint
                    $magento_get_existing_attribute_data  = 'V1/products/attributes/'.$unmatched_attributes_name;

                    // http://ec2-3-8-20-90.eu-west-2.compute.amazonaws.com/rest/V1/products/attributes/material

                }*/

                if (!$response->successful()) {

                    echo "error_in_createAndGetNonExistingAttributeWithValues";
                    var_dump($unmatched_attributes_name);
                    $error_message = $response['message'];
                    dd($error_message);
                    die();
                }

                // array contains all options/attributes names and values from Magento store
                $new_attribute_data = json_decode($response->body(), true);

                // variable used for variant structure
                $values = array();

                // we may have multiple option/values against each attribute
                // for example color => Red,Blue,Green
                foreach ($new_attribute_data['options'] as $attribute_option) {

                    // value is Magento option id, actually we are storing option ids
                    //$options[] = $attribute_option['value'];

                    // in Magento response we are getting first option key value as empty string, so add check for empty
                    if ($attribute_option['value'] && $attribute_option['value'] != "") {

                        // variable used for variant structure
                        // new logic
                        $values[] = array(
                            'name' => $attribute_option['label'],
                            'magento_option_id' => $attribute_option['value'],
                        );
                    }
                }

                // preparing/formatting array to use in product upload structure
                /*$prepared_new_attribute = array(
                    'attribute_code' => $new_attribute_data['attribute_code'],
                    'value' => $options,
                );*/


                // sample structure for variation combination
                /*array(

                    array(
                        'attribute_code' => 'color',
                        'values' => array( 'name' => 'red', 'magento_option_id' => 123),
                                    array( 'name' => 'blue', 'magento_option_id' => 456),
                    ),
                    array(
                        'attribute_code' => 'size',
                        'values' => array( 'name' => 'Large', 'magento_option_id' => 123),
                                    array( 'name' => 'small', 'magento_option_id' => 456),
                    ),
                );*/

                // modify array structure for variant structure
                // new logic
                $prepared_new_attribute = array(
                    'attribute_code' => $new_attribute_data['attribute_code'],
                    'values' => $values,
                );

                // we may have multiple attributes so format each attribute packet with its options
                $prepared_attributes_with_values[] = $prepared_new_attribute;
            }
        }

        return $prepared_attributes_with_values;
    }

    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible for to add newly created attributes to attribute set
     * Endpoint: https://domain.com/rest/default/V1/products/attribute-sets/attributes
     * Method: POST
     * @param string, string, array
     * @return array
     */
    private function addNewAttributesToAttributeSet($auth_token, $base_url, $non_existing_attributes_names)
    {

        // get Magento store attribute set id from env file (4 is the value of magento default attribute set)
        $attribute_set_id = env('MAGENTO_STORE_ATTRIBUTE_SET_ID', 4);

        $attribute_set_response_ids = array();

        // we have an array of attributes which needs to add to Magento store
        foreach ($non_existing_attributes_names as $unmatched_attributes_name) {

            $post_body = [
                'attributeSetId' => $attribute_set_id,
                'attributeGroupId' => 7,
                'attributeCode' => $unmatched_attributes_name,
                'sortOrder' => 99,
            ];

            // Magento add new attributes endpoint
            $magento_assign_attribute_set_endpoint  = 'default/V1/products/attribute-sets/attributes';

            //http://192.168.10.10/magento240/rest/default/V1/products/attribute-sets/attributes

            // call API to create attribute with its options/values
            $response = Http::acceptJson()->withBody(json_encode($post_body), 'application/json')->withToken($auth_token)->post($base_url . $magento_assign_attribute_set_endpoint);

            if (!$response->successful()) {

                echo "error_in_addNewAttributesToAttributeSet";
                $error_message = $response['message'];
                dd($error_message);

                die();
            }

            // on success API return some ID like "730"
            $attribute_set_response_ids[] = json_decode($response->body());
        }


        return $attribute_set_response_ids;
    }

    /**
     * Method triggered from uploadProductToStorefront method
     * Method responsible get all matched attributes/options value using Magento API
     * Endpoint: https://domain.com/rest/default/V1/products/attributes/attribute-code(size)/options
     * Method: GET
     * @param string, string, array, int
     * @return array
     */
    private function getAllExistingAttributeValues($auth_token, $base_url, $existing_attributes_names, $product_id)
    {

        $prepared_attributes_with_values = array();

        $seller_portal_options = array();

        // we have an array of attributes which needs to add to Magento store
        foreach ($existing_attributes_names as $matched_attributes_name) {

            // get the option values from DB product_options table
            $seller_portal_options_obj = ProductOption::select('value AS label')->where('product_id', $product_id)->where('name', $matched_attributes_name)->get();

            // check if requested attribute like (size) has options/values in DB
            if ($seller_portal_options_obj) {

                // $seller_portal_options array contain array having all key values of attributes/options from DB
                // we use alias "label"
                /*{
                 ["color"] => {
                    [0]=> { ["label"]=> "Beige"  }
                    [1]=> { ["label"]=> "Blue" }
                 }
                 ["size"]=>  {
                    [0]=> { "One Size (fits UK 8-14)" }
                    [1]=> { ["label"]=> "M"}
                }
                 ["ration2"]=> {
                    [0]=> { ["label"]=> "1" }
                    [1]=>{ ["label"]=>"2" }
                }
                }*/

                $seller_portal_options[$matched_attributes_name] = $seller_portal_options_obj->toArray();
            }
        }

        // loop through all seller_portal_options to check one by one in Magento list
        // $attribute_name = attribute-code i.e (color)
        foreach ($seller_portal_options as $attribute_name => $values_arr) {

            // now call the Magento API to check if attribute options exists in Magento store
            // Magento endpoint to get the attribute values/options by attribute-code
            $magento_get_options_by_attribute_code_endpoint  = 'default/V1/products/attributes/' . $attribute_name . '/options';

            //http://192.168.10.10/magento240/rest/default/V1/products/attributes/color/options

            // call API to get the attribute values/options by attribute-code
            $response = Http::acceptJson()->withToken($auth_token)->get($base_url . $magento_get_options_by_attribute_code_endpoint);

            if (!$response->successful()) {

                echo "error_in_getAllExistingAttributeValues";
                $error_message = $response['message'];
                dd($error_message);
            }

            // array contains all options/attributes names and values from Magento store
            $magento_attribute_options = json_decode($response->body(), true);


            // above array is multidimensional, so here $values_arr contains array of values like e.g below
            /*$values_arr = [
                0 => [ "label" => "Beige" ]
                1 => [ "label" => "Blue"  ]
            ]*/

            $options = array();

            // variable used for variant structure
            $values = array();
            foreach ($values_arr as $value) {

                $flag = 0;

                // loop through Magento existing options
                foreach ($magento_attribute_options as $key => $val) {

                    // check if option exists in Magento options list by comparing with seller portal options list
                    if ($val['label'] == $value['label']) {

                        //  $val['value'] is Magento option id, actually we are storing option ids
                        // old logic
                        //$options[] = $val['value'];

                        // variable used for variant structure
                        // new logic
                        $values[] = array(
                            'name' => $value['label'],
                            'magento_option_id' => $val['value'],
                        );

                        // flag used for non matched options
                        $flag++;
                    }
                }

                // flag 0 means option not found in Magento existing list so we need to add this option in Magento using API
                if ($flag == 0) {

                    // call method to new option in Magento
                    $new_option_id = $this->addNewOptionsInExistingAttribute($auth_token, $base_url, $attribute_name, $value['label']);

                    // old logic
                    //$options[] = $new_option_id;

                    // variable used for variant structure
                    // new logic
                    $values[] = array(
                        'name' => $value['label'],
                        'magento_option_id' => $new_option_id,
                    );
                }
            }

            // preparing/formatting array to use in product upload structure
            // old logic
            /*$prepared_new_attribute = array(
                'attribute_code' => $attribute_name,
                'value' => $options,
            );*/


            // sample structure for variation combination
            /*array(

                array(
                    'attribute_code' => 'color',
                    'values' => array( 'name' => 'red', 'magento_option_id' => 123),
                                array( 'name' => 'blue', 'magento_option_id' => 456),
                ),
                array(
                    'attribute_code' => 'size',
                    'values' => array( 'name' => 'Large', 'magento_option_id' => 123),
                                array( 'name' => 'small', 'magento_option_id' => 456),
                ),
            );*/


            // modify array structure for variant structure
            // new logic
            $prepared_new_attribute = array(
                'attribute_code' => $attribute_name,
                'values' => $values,
            );




            // we may have multiple attributes so format each attribute packet with its options
            $prepared_attributes_with_values[] = $prepared_new_attribute;
        }
        return $prepared_attributes_with_values;
    }

    /**
     * Method triggered from getAllExistingAttributeValues method
     * Method responsible for to add new option/value in Magento using API
     * Endpoint: https://domain.com/rest/default/V1/products/attributes/color/options
     * Method: POST
     * @param string, string, string, string
     * @return string
     */
    private function addNewOptionsInExistingAttribute($auth_token, $base_url, $attribute_name, $attribute_value)
    {

        $post_body = [
            'option' =>
            array(
                'label' => $attribute_value,
            ),
        ];

        // Magento add new option/value in existing attribute endpoint
        $magento_add_new_option_endpoint  = 'default/V1/products/attributes/' . $attribute_name . '/options';

        //http://192.168.10.8/magento240/rest/default/V1/products/attributes/color/options

        // call API to create option in existing attribute
        $response = Http::acceptJson()->withBody(json_encode($post_body), 'application/json')->withToken($auth_token)->post($base_url . $magento_add_new_option_endpoint);

        if (!$response->successful()) {

            echo "error_in_addNewOptionsInExistingAttribute";
            $error_message = $response['message'];
            dd($error_message);
        }

        // on success API return some ID like "id_259"
        $new_option_id = json_decode($response->body());

        // get the value after underscore
        $new_option_id = substr($new_option_id, strpos("$new_option_id", "_") + 1);

        return $new_option_id;
    }

    /**
     * Method responsible for to update the "is_uploaded_to_storefront" status of requested product
     * @param int
     */
    private function updateProductUploadToMagentoStatus($product_id)
    {

        // get requested product details object
        $product_detail_obj = Product::find($product_id);

        // if flag is 0 then update is_uploaded_to_storefront field
        if ($product_detail_obj->is_uploaded_to_storefront == "0") {

            $product_detail_obj->is_uploaded_to_storefront = "1";
            $product_detail_obj->save();
        }
    }
    /**

     * Method responsible for to make attribute Configurable in Magento store using REST API
     * Endpoint: https://domain.com/rest/default/V1/configurable-products/Parent-SKU/options
     * Method: POST
     * @param string, string, array, array, int, string
     */
    private function setConfigurableVariationOrAttribute($auth_token, $base_url, $prepared_custom_attributes_to_upload_for_configurable_child_product, $all_magento_attributes, $product_id, $configurable_product_parent_sku)
    {

        $prepared_configurable_attributes = array();

        // get unique variant names(attribute_code) from seller portal DB
        $unique_variation_names_obj = ProductOption::select('name')->where('product_id', $product_id)->distinct()->get();

        if ($unique_variation_names_obj) {

            // array('color','size','ratio')
            $unique_variation_names = $unique_variation_names_obj->toArray();

            foreach ($unique_variation_names as $unique_variation_name) {

                // i.e $variant_name = 'color'
                $variant_name = strtolower($unique_variation_name['name']);

                // get the attribute_id of attribute_code from $all_magento_attributes
                foreach ($all_magento_attributes as $attribute) {

                    // check if attribute name from $all_magento_attributes array matched with our $variant_name from DB
                    if (isset($attribute['attribute_code']) && $attribute['attribute_code'] == $variant_name) {

                        // create new array with specific structure to use further
                        $attributes_data[$variant_name] = array(
                            "attribute_id" => $attribute['attribute_id'],
                        );
                    }
                }
            }
        }

        /*
         $attributes_data = array:2 [
              "size" => array:2 [
                "attribute_id" => 144
              ]
              "color" => array:2 [
                "attribute_id" => 93
              ]
            ]
        */

        // now we have array with keys as attribute_code(attribute_name) and attribute_id of this attribute_code, i.e attribute_code = size and attribute_id of size is 144 as above same array explained
        foreach ($attributes_data as $attribute_code_name => $attribute_code_id) {

            // we have to add/append array of values in existing $attributes_data array
            $values = array();

            // $prepared_custom_attributes_to_upload_for_configurable_child_product array contains all the values of product
            // for example product using (color=red, size=large etc...)
            foreach ($prepared_custom_attributes_to_upload_for_configurable_child_product as $child_product_variant_data) {

                foreach ($child_product_variant_data as $data) {

                    if ($data['attribute_code'] == $attribute_code_name)
                        $values[] = $data['value'];
                }
            }

            array_push($attributes_data[$attribute_code_name], array_values(array_unique($values)));
        }

        /* now it becomes
         $attributes_data = array:2 [
              "size" => array:2 [
                "attribute_id" => 144
                0 => array:3 [
                  0 => "299" i.e (small)
                  1 => "300" i.e (medium)
                  2 => "301" i.e (large)
                ]
              ]
              "color" => array:2 [
                "attribute_id" => 93
                0 => array:3 [
                  0 => "302" i.e (red)
                  1 => "303" i.e (blue)
                  2 => "304" i.e (green)
                ]
              ]
            ]
        */

        // now create a final array that follow the Magento required structure

        // Magento API required payload structure
        /*
            {
              "option": {
                "attribute_id": "141",
                "label": "Size",
                "position": 0,
                "is_use_default": true,
                "values": [
                  {
                    "value_index": 9
                  }
                ]
              }
            }
        */
        foreach ($attributes_data as $attribute_code_name => $attribute_data) {

            $values_arr = array();

            foreach ($attribute_data[0] as $values) {

                $values_arr[] = array(
                    "value_index" => $values
                );
            }


            $prepared_configurable_attributes[] = array(
                'option' => array(
                    "attribute_id" => $attribute_data['attribute_id'],
                    "label" => $attribute_code_name,
                    "position" => 0,
                    "is_use_default" => true,
                    "values" => $values_arr
                )
            );
        }

        /* final array
         $prepared_configurable_attribute = array:3 [
          0 => array:1 [
            "option" => array:5 [
              "attribute_id" => 185
              "label" => "colour"
              "position" => 0
              "is_use_default" => true
              "values" => array:2 [
                0 => array:1 [
                  "value_index" => "305"
                ]
                1 => array:1 [
                  "value_index" => "306"
                ]
              ]
            ]
          ]
          1 => array:1 [
            "option" => array:5 [
              "attribute_id" => 144
              "label" => "size"
              "position" => 0
              "is_use_default" => true
              "values" => array:2 [
                0 => array:1 [
                  "value_index" => "261"
                ]
                1 => array:1 [
                  "value_index" => "168"
                ]
              ]
            ]
          ]
          2 => array:1 [
            "option" => array:5 [
              "attribute_id" => 170
              "label" => "ratio"
              "position" => 0
              "is_use_default" => true
              "values" => array:2 [
                0 => array:1 [
                  "value_index" => "251"
                ]
                1 => array:1 [
                  "value_index" => "252"
                ]
              ]
            ]
          ]
        ]

          */

        // we may have multiple attributes to set as configurable in Magento so loop the $prepared_configurable_attribute array
        foreach ($prepared_configurable_attributes as $prepared_configurable_attribute) {


            // Magento add new option/value in existing attribute endpoint
            $set_attribute_configurable_endpoint  = 'default/V1/configurable-products/' . $configurable_product_parent_sku . '/options';

            //http://192.168.10.8/magento240/rest/default/V1/configurable-products/Parent-SKU/options

            // call API to set attribute configurable
            $response = Http::acceptJson()->withBody(json_encode($prepared_configurable_attribute), 'application/json')->withToken($auth_token)->post($base_url . $set_attribute_configurable_endpoint);

            if (!$response->successful()) {

                echo "error_in_setConfigurableVariationOrAttribute";
                $error_message = $response['message'];
                dd($error_message);
            }

            // on success API return some ID like "335"
            $configurable_option_id = json_decode($response->body());

            // get the value after underscore

            //return $configurable_option_id;

        }
    }
}
