<?php

namespace App\Http\Controllers\shopify;

use App\Http\Controllers\Controller;
use App\Models\MappedCategory;
use App\Models\Product;
use App\Models\Product_ProductCategory;
use App\Models\ProductCategory;
use App\Models\Settings;
use App\Models\Store;
use App\Models\StoreFrontCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProductCategoryController extends Controller
{

    // initializing array in class as we have to use in recursive method "getParentCategoryNameById"
    public $parent_category_name = array();

    /**
     * Display category page with listing
     *
     * @param null
     * @return null
     */
    public function index(){
        // Check if user is authenticate and login
        if (Auth::user() == null) {

            return view('admin.pages.account.login');
        }

        $store_product_categories = array();
        $store_products_count = 0;

        // Get store connection data using relationship between User and Store
        $store_data = User::find(Auth::user()->id)->userStores()->get();

        // If user do not have store credentials yet then send an empty product categories array
        if ($store_data->isEmpty()) {
            return view('admin.pages.product.categories', [
                'product_categories' => $store_product_categories
            ]);
        }

        // get store product data from DB, as we have to add check in view file based on it
        $store_products_count = Product::get()->count();

        // If store products are not being added in DB yet then send an empty categories array and products count as we have added conditions based on these
        if ($store_products_count <=0){

            return view('admin.pages.product.categories', [
                'product_categories' => $store_product_categories,
                'store_products_count' => $store_products_count
            ]);
        }


        // Get category data using relationship store and Product category without pagination
        //$store_product_categories = Store::find($store_data[0]->id)->storeProductCategories()->get()->toArray();

        // get data from setting table to check if pagination setting exists
        $settings = Settings::where('settings_name', 'Pagination')->get();

        // if setting table do not have pagination setting value then set it to 10 by default
        if($settings->isEmpty()){

            $per_page = 10;

        }else{

            $per_page = $settings[0]->settings_value;
        }


        // Get category data using relationship store and Product category with pagination
        $store_product_categories = Store::find($store_data[0]->id)->storeProductCategories()->paginate($per_page);


        // If we do not have categories in DB
        /*if (!$store_product_categories){

            return view('admin.pages.product.categories', [
                'products' => $store_product_categories
            ]);
        }*/

        // Get store front categories
        $storefront_categories = StoreFrontCategory::get();

        // make hierarchical array to show categories with their respective parent categories i.e Root Catalog >Default >Men >Tops
        // main array to store created hierarchical data
        $hierarchical_storefront_categories = array();

        if ($storefront_categories){

            $all_storefront_categories_array = $storefront_categories->toArray();

            // loop through all storefront categories
            foreach ($all_storefront_categories_array as $storefront_category_array){

                // check if category do not have any parent (level = 0 means this category do not have any parent)
                if ($storefront_category_array['level'] == "0"){

                    $hierarchical_storefront_categoy = array(
                        'id' => $storefront_category_array['id'],
                        'categoryId' => $storefront_category_array['categoryId'],
                        'name' => $storefront_category_array['name'],
                        'level' => $storefront_category_array['level'],
                        'categoryParentId' => $storefront_category_array['categoryParentId'],
                    );

                    array_push($hierarchical_storefront_categories,$hierarchical_storefront_categoy);
                }

                // category has parent(s) category/categories
                else{

                    // we are using this array to store name of multiple parent_categories, so empty it in each iteration to avoid duplicate
                    $this->parent_category_name = array();

                    // store current category name, further method "getParentCategoryNameById" will store name of the parent categories not current
                    array_push($this->parent_category_name,$storefront_category_array['name']);

                    // call method and pass all storefront categories array, category categoryParentId and number of expected parent categories to get  hierarchical info
                    $hierarchical_name_array = $this->getParentCategoryNameById($all_storefront_categories_array, $storefront_category_array['categoryParentId'], $storefront_category_array['level']);

                    // convert array to string and final output will be like "Root Catalog > Default > Men >Tops "
                    $hierarchical_name = implode(" > ", array_reverse($hierarchical_name_array));

                    $hierarchical_storefront_categoy = array(
                        'id' => $storefront_category_array['id'],
                        'categoryId' => $storefront_category_array['categoryId'],
                        'name' => $hierarchical_name,
                        'level' => $storefront_category_array['level'],
                        'categoryParentId' => $storefront_category_array['categoryParentId'],
                    );

                    array_push($hierarchical_storefront_categories,$hierarchical_storefront_categoy);
                }
            }

            // convert array into eloquent collection
            $hierarchical_storefront_categories = collect($hierarchical_storefront_categories)->map(function ($hierarchical_storefront_categories) {
                return (object) $hierarchical_storefront_categories;
            });
        }

        //dd($hierarchical_storefront_categories[1]->name);

        // Get mapped categories to display, pre filled data in case user already save previously
        $mapped_categories =  MappedCategory::select('id','store_front_category_id','product_category_id')->where('store_id', $store_data[0]->id)->get();


        return view('admin.pages.product.categories', [
            'product_categories' => $store_product_categories,
            'storefront_categories' => $hierarchical_storefront_categories,
            'mapped_categories' => $mapped_categories
        ]);

    }

    /**
     * Method responsible for to find category info by ID
     * Method called from index method
     * @param array,string,int,int
     * @return array
     */
    private function getParentCategoryNameById($all_storefront_categories_array, $parent_category_id, $levels_to_search){

        // loop through all storefront categories
        foreach ($all_storefront_categories_array as $single_storefront_category_array){

            // check if looped level category is equals to requested parent id
            if ($single_storefront_category_array['categoryId'] == $parent_category_id){

                // store category parent category name
                array_push($this->parent_category_name,$single_storefront_category_array['name']);


                // check if we have more expected parents, -1 means category has no more parents
                if ($levels_to_search-1 != -1){

                    // prepare data to get parent name of current parent
                    $parent_category_id = $single_storefront_category_array['categoryParentId'];

                    // decrement in level to get next parent
                    $levels_to_search = $levels_to_search-1;

                    $this->getParentCategoryNameById($all_storefront_categories_array, $parent_category_id, $levels_to_search);
                }

            }
        }
        return $this->parent_category_name;

    }

    /**
     * Initiate sync process and trigger Shopify collections API
     * Method called by ajax
     * @param null
     * @return null
     */
    public function syncCategories(){

        // get store connection data using relationship between User and Store
        $store_data = User::find(Auth::user()->id)->userStores()->get();

        if ($store_data->isEmpty()) {
            echo "store_not_exists";
            die();
        }

        if ($store_data[0]->is_active == '0'){

            //return redirect('' . env('ADMIN_PREFIX') . '/products')->with('invalid_store', 'Your store is not connected either credentials are not valid or not verified yet');
            echo "invalid_store";
            die();
        }

        /*$counter = 0;
        try{

            for ($i=0; $i<1000; $i++){
                $counter++;
                $smart_collections_response = Http::timeout(30)->get('https://46a81163aba443603fc21314c7654c73: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@pixusnodoubtshoes.myshopify.com/admin/api/2021-04/smart_collections.json?product_id=6786450489532');
                echo $counter;
            }
            echo "try .$counter.";
        }
        catch(\Throwable $e){
            echo "catch .$counter.";
        }


       die();*/

        // call method to get categories data from Shopify smart collection APIs and save in DB
        $this->callShopifySmartCollectionAPI($store_data, $pagination_link='');

        // call method to get categories data from Shopify custom collection APIs and save in DB
        $this->callShopifyCustomCollectionAPI($store_data, $pagination_link='');

        // call method to perform insertion in product category relation table
        $this->addCategoryAssociation($store_data);


        //return redirect('' . env('ADMIN_PREFIX') . '/products')->with('sync_process_complete', 'Syncing process has been successfully completed');
        echo "sync_process_complete";
        die();

    }


    /**
     * Get categories data from shopify smart and custom collections API
     * @param array, string
     * @return null
     */
    private function callShopifySmartCollectionAPI($store_data, $pagination_link){

        // Shopify store credentials
        $api_key            = $store_data[0]->api_key;
        $api_password       = $store_data[0]->api_password;
        $api_domain_name    = $store_data[0]->api_domain;
        $base_url           = $store_data[0]->base_url;   // "admin/api/2021-04"
        $pagination = '&page_info='.$pagination_link;
        $smart_collections_api_endpoint  = '/smart_collections.json?limit=250';


        $smart_collections_response = Http::get('https://' . $api_key . ':' . $api_password . '@' . $api_domain_name.'/'.$base_url . $smart_collections_api_endpoint . $pagination);

        $smart_collections  = json_decode($smart_collections_response->body(), true);

        // check if $merge_smart_and_custom_collection array has some data
        if ($smart_collections){

            // call method to perform insertion in category DB
            $this->addCategories($smart_collections['smart_collections'],$store_data[0]->id);

        }

        // check if response header contains a pagination link
        if (isset($smart_collections_response->headers()['Link'])){

            // call method from helper to get next and previous page links from response header link array
            $pagination_links =  parsePaginationLinkHeader($smart_collections_response->headers()['Link'][0]);

            // check if $pagination_links variable has some data, means we have pagination request from shopify product API response
            if (isset($pagination_links['next'])){

                $this->callShopifySmartCollectionAPI($store_data, $pagination_link=$pagination_links['next']);
            }

        }

    }


    /**
     * Get categories data from shopify custom collection API
     * @param array, string
     * @return null
     */
    private function callShopifyCustomCollectionAPI($store_data, $pagination_link){

        // Shopify store credentials
        $api_key            = $store_data[0]->api_key;
        $api_password       = $store_data[0]->api_password;
        $api_domain_name    = $store_data[0]->api_domain;
        $base_url           = $store_data[0]->base_url;   // "admin/api/2021-04"
        $pagination = '&page_info='.$pagination_link;
        $custom_collections_api_endpoint = '/custom_collections.json?limit=250';

        $custom_collections_response = Http::get('https://' . $api_key . ':' . $api_password . '@' . $api_domain_name.'/'.$base_url . $custom_collections_api_endpoint . $pagination);

        $custom_collections = json_decode($custom_collections_response->body(), true);

        // check if $merge_smart_and_custom_collection array has some data
        if ($custom_collections){

            // call method to perform insertion in category DB
            $this->addCategories($custom_collections['custom_collections'],$store_data[0]->id);

        }

        // check if response header contains a pagination link
        if (isset($custom_collections_response->headers()['Link'])){

            // call method from helper to get next and previous page links from response header link array
            $pagination_links =  parsePaginationLinkHeader($custom_collections_response->headers()['Link'][0]);

            // check if $pagination_links variable has some data, means we have pagination request from shopify product API response
            if (isset($pagination_links['next'])){

                $this->callShopifyCustomCollectionAPI($store_data, $pagination_link=$pagination_links['next']);
            }

        }

    }

    /**
     * Save categories in DB
     *
     * @param array, int
     * @return null
     */
    private function addCategories($categories, $store_id){


        foreach ($categories as $key => $single_category){

            // get category data from DB
            $existing_category = ProductCategory::where('categoryId', $single_category['id'])->first();

            // if category not exists in DB then add new entry otherwise skip insertion to avoid duplication
            if (!$existing_category){

                $category = ProductCategory::create([
                'store_id' => $store_id,
                'categoryId' => $single_category['id'],
                'name' => $single_category['handle'],
                ]);

            }
            // update record in DB
            else{
                $existing_category->name = $single_category['handle'];
                $existing_category->save();
            }
        }
    }

    /**
     * Save categories association in DB
     *
     * @param array
     * @return null
     */
    private function addCategoryAssociation($store_data){

        // Shopify store credentials
        $api_key            = $store_data[0]->api_key;
        $api_password       = $store_data[0]->api_password;
        $api_domain_name    = $store_data[0]->api_domain;
        $base_url           = $store_data[0]->base_url;   // "admin/api/2021-04"

        // we will store all processed data in these arrays and then insert in one go
        $smart_collection_container = array();
        $custom_collection_container = array();
        $smart_counter = 0;
        $custom_counter = 0;

        // get all shopify reference productId and product ids using relationship store and Product
        $store_product_ids = Store::find($store_data[0]->id)->storeProducts()->pluck('id', 'productId')->toArray(); // [6786450489532]=>int(1) [6687345967292]=> int(2) [6606052917436]=> int(3)

        if ($store_product_ids){

            foreach ($store_product_ids as $shopify_product_id =>  $store_product_id){

                /* ********** Start process for Shopify custom collections ********** */

                // pass product id in shopify endpoint to get product  custom collections, Endpoint: GET /admin/api/2021-07/custom_collections.json?product_id=632910392
                $custom_collections_by_product_id_api_endpoint  = '/custom_collections.json?product_id='.$shopify_product_id;
                $custom_collections_by_product_id_response      = Http::timeout(240)->retry(3, 1000)->get('https://' . $api_key . ':' . $api_password . '@' . $api_domain_name.'/'.$base_url . $custom_collections_by_product_id_api_endpoint);
                $custom_collections_by_product_id               = json_decode($custom_collections_by_product_id_response->body(), true);

                $count_1 = count($custom_collections_by_product_id['custom_collections']);
                $custom_counter = $custom_counter + $count_1;
                if ($custom_collections_by_product_id){

                    foreach ($custom_collections_by_product_id['custom_collections'] as $collection){

                        // store specific data in this array then merge to main container array for insertion
                        $categories = array();

                        // get existing category ids from DB to store in product category relation table
                        $existing_category_ids_obj = ProductCategory::select('id','categoryId')->where('categoryId', $collection['id'])->first();

                        if($existing_category_ids_obj){

                            $existing_category_ids = $existing_category_ids_obj->toArray();

                            $categories['product_id']   = $store_product_id;
                            $categories['productId']    = $shopify_product_id;
                            $categories['product_category_id']  = $existing_category_ids['id'];
                            $categories['categoryId']   = $existing_category_ids['categoryId'];

                            array_push($custom_collection_container,$categories);
                            //Product_ProductCategory::insert($categories);
                        }
                    }
                }

                /* ********** End process for Shopify custom collections ********** */

                /* ********** Start process for Shopify smart collections ********** */

                // pass product id in shopify endpoint to get product smart collections, Endpoint: GET /admin/api/2021-07/smart_collections.json?product_id=632910392
                $smart_collections_by_product_id_api_endpoint  = '/smart_collections.json?product_id='.$shopify_product_id;
                $smart_collections_by_product_id_response = Http::timeout(240)->retry(3, 1000)->get('https://' . $api_key . ':' . $api_password . '@' . $api_domain_name.'/'.$base_url . $smart_collections_by_product_id_api_endpoint);
                $smart_collections_by_product_id    = json_decode($smart_collections_by_product_id_response->body(), true);

                $count_2 = count($smart_collections_by_product_id['smart_collections']);
                $smart_counter = $smart_counter + $count_2;

                if ($smart_collections_by_product_id){

                    foreach ($smart_collections_by_product_id['smart_collections'] as $collection){

                        // store specific data in this array then merge to main container array for insertion
                        $categories = array();

                        // get existing category ids from DB to store in product category relation table
                        $existing_category_ids_obj = ProductCategory::select('id','categoryId')->where('categoryId', $collection['id'])->first();

                        if($existing_category_ids_obj){

                            $existing_category_ids = $existing_category_ids_obj->toArray();

                            $categories['product_id']   = $store_product_id;
                            $categories['productId']    = $shopify_product_id;
                            $categories['product_category_id']  = $existing_category_ids['id'];
                            $categories['categoryId']   = $existing_category_ids['categoryId'];

                            array_push($smart_collection_container,$categories);
                            //Product_ProductCategory::insert($categories);
                        }
                    }
                }

                /* ********** Start process for Shopify smart collections ********** */
            }
        }

        // save product collections in product_category relational table

        if ($smart_collection_container){
            // insertion moved above code
            Product_ProductCategory::insert($smart_collection_container);
        }

        if ($custom_collection_container){
            // insertion moved above code
            Product_ProductCategory::insert($custom_collection_container);
        }

        echo "Smart-Count: .$smart_counter.";
        echo "custom-Count: .$custom_counter.";


    }
}
