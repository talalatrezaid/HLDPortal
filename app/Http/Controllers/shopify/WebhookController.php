<?php

namespace App\Http\Controllers\shopify;

use App\Http\Controllers\Controller;
use App\Mail\AssignProductToCharity;
use App\Mail\ProductQuantityUpdatedEmail;
use App\Models\AssignedCharitiesProducts;
use App\Models\MappedCategory;
use App\Models\Orders\Customers;
use App\Models\Orders\OrderBillingAddress;
use App\Models\Orders\OrderFullfilments;
use App\Models\Orders\OrderListOfProducts;
use App\Models\Orders\OrderPaymentDetails;
use App\Models\Orders\Orders;
use App\Models\Orders\OrderShippingAddress;
use App\Models\PortalSettings;
use App\Models\Product;
use App\Models\Product_ProductCategory;
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
use App\Models\VariantMeta;
use Barryvdh\Debugbar\Facade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Environment\Console;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use OrderListProducts;

define('SHOPIFY_APP_SECRET', env('SHOPIFY_APP_SECRET'));

class WebhookController extends Controller
{

    /**
     * Method called by Magento store using webhook
     * Method is responsible for getting payload from Magento on trigger create,update or delete event and handle DB record according to request
     *
     * @param array
     * @return null
     */
    public function magento_index()
    {


        // get payload from webhook request
        $data = file_get_contents('php://input');

        // convert json payload to array
        $data_array = json_decode($data, true);

        /*$fp = fopen('magento.txt', 'w');
        fwrite($fp, "webhook test");
        fclose($fp);
        file_put_contents('magento.txt', print_r($data_array, true));*/

        if ($data_array['actionname'] == "addcategory" || $data_array['actionname'] == "updatecategory") {

            // call method to add or update information in DB
            $this->addMagentoCategoriesWebhook($data_array);
        } elseif ($data_array['actionname'] == "deletecategory") {

            // call method to delete information in DB
            $res = $this->deleteMagentoCategoriesWebhook($data_array);
            error_log("Magento Product Delete response: " . $res);
        }
    }

    /**
     * Method to insert categories data in DB store_front_categories table.
     * Getting category info from Magento webhook and store it in seller portal storefront categories DB table
     * @param array
     * @return boolean
     */
    private function addMagentoCategoriesWebhook($category_data)
    {

        // get category data from DB
        $existing_category = StoreFrontCategory::where('categoryId', $category_data['entityid'])->first();

        // if category not exists in DB then add new entry otherwise update record
        if (!$existing_category) {

            $category = StoreFrontCategory::create([
                'categoryId' => $category_data['entityid'],
                'name' => $category_data['name'],
                'level' => $category_data['level'],
                'categoryParentId' => $category_data['parentid'],
            ]);
        }
        // update record in DB
        else {
            $existing_category->name = $category_data['name'];
            $existing_category->level = $category_data['level'];
            $existing_category->categoryParentId = $category_data['parentid'];
            $existing_category->save();
        }

        $fp = fopen('magento2.txt', 'w');
        fwrite($fp, "Category added on Magento add_category webhook");
        fclose($fp);
        //file_put_contents('magento.txt', print_r($data_array, true));

        error_log("Category added on Magento add_category webhook ");
    }


    /**
     * Delete Category data from DB and category association with product
     *
     * @param string,array
     * @return null
     */
    private function deleteMagentoCategoriesWebhook($category_data)
    {

        // get storefront category data from DB
        $saved_category_data = StoreFrontCategory::where('categoryId', $category_data['entityid'])->first();

        if ($saved_category_data) {

            // get storefront primary key
            $storefront_category_id = $saved_category_data->id;

            // delete associations with mapped_categories table, where user has mapped Magento/storefront and seller portal categories
            $deleted_mapped_category_relation   = MappedCategory::where('store_front_category_id', $storefront_category_id)->delete();
            error_log("Delete storefront and seller portal categories relation on delete category from Magento using webhook: " . $deleted_mapped_category_relation);

            // delete actual category from storefront categories DB table
            $deleted_product   = StoreFrontCategory::where('id', $storefront_category_id)->delete();
            error_log("Delete storefront Category on delete category from Magento using webhook: " . $deleted_product);
        } else {

            error_log("Category not found on delete category webhook");
        }
    }

    /**
     * Method called by Shopify store using webhook
     * Method is responsible for getting payload from shopify on trigger create,update or delete event and handle DB record according to request
     *
     * @param array
     * @return null
     */
    public function index()
    {


        $data = "";
        $data_array = array();

        // get from  shopify header
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];

        // get payload from webhook request
        $data = file_get_contents('php://input');

        // convert json payload to array
        $data_array = json_decode($data, true);

        // $fp = fopen('magento.txt', 'w');
        // fwrite($fp, "aaa");
        // fclose($fp);
        // file_put_contents('magento.txt', print_r($data_array, true));


        // call method to verify webhook
        $verified = $this->verify_webhook($data, $hmac_header);

        // get domain name from shopify header to compare with client's store domain
        $hmac_header_domain = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];

        // get event name from shopify request header // product/update, product/create, product/delete
        $hmac_header_topic = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];

        /*$fp = fopen('topic.txt', 'w');
        fwrite($fp, $hmac_header_topic);
        fclose($fp);*/
        Log::info($hmac_header_topic);

        // if event is products/create
        if ($hmac_header_topic == 'products/create') {

            // convert json payload to array
            $data_array = json_decode($data, true);


            /*$file_name = rand(10,100);
            $fp = fopen($file_name.'.txt', 'w');
            fwrite($fp, $data);
            fclose($fp);
            file_put_contents($file_name.'.txt', print_r($data_array, true));*/


            // get product info from DB to check if product already exists in DB
            $product_data = Product::where('productId', $data_array['id'])->first();

            // only add new product if we do not have already
            if (!$product_data) {

                // call method to add information in DB
                $res = $this->addProductWebhook($hmac_header_domain, $data_array);
                error_log("Product Add response: " . $res);
            }
        }

        // if event is products/update
        elseif ($hmac_header_topic == 'products/update') {

            // convert json payload to array
            $data_array = json_decode($data, true);

            // call method to update information in DB
            $res = $this->updateProductWebhook($hmac_header_domain, $data_array);
            error_log("Product Update response: " . $res);
        }
        // if event is products/delete
        elseif ($hmac_header_topic == 'products/delete') {

            // convert json payload to array
            $data_array = json_decode($data, true);

            // call method to delete information in DB
            $res = $this->deleteProductWebhook($hmac_header_domain, $data_array);
            error_log("Product Delete response: " . $res);
        }
        // if event is collections/create
        elseif ($hmac_header_topic == 'collections/create') {

            // convert json payload to array
            $data_array = json_decode($data, true);


            /*$file_name = rand(10,100);
            $fp = fopen($file_name.'.txt', 'w');
            fwrite($fp, $data);
            fclose($fp);
            file_put_contents($file_name.'.txt', print_r($data_array, true));*/

            // get category info from DB to check if category already exists in DB
            $category_data = ProductCategory::where('categoryId', $data_array['id'])->first();

            // only add new product if we do not have already
            if (!$category_data) {

                // call method to add information in DB
                $res = $this->addCategoryWebhook($hmac_header_domain, $data_array);
                error_log("Category Add response: " . $res);
            }
        }
        // if event is collections/update
        elseif ($hmac_header_topic == 'collections/update') {

            // convert json payload to array
            $data_array = json_decode($data, true);


            /*$fp = fopen('update_collection.txt', 'w');
            fwrite($fp, "a");
            fclose($fp);
            file_put_contents('update_collection.txt', print_r($data_array, true));*/

            // call method to update information in DB
            $res = $this->updateCategoryWebhook($hmac_header_domain, $data_array);
            error_log("Category Update response: " . $res);
            // if event is collections/delete
        } elseif ($hmac_header_topic == 'collections/delete') {

            // convert json payload to array
            $data_array = json_decode($data, true);

            /*$fp = fopen('delete_collection.txt', 'w');
            fwrite($fp, "a");
            fclose($fp);
            file_put_contents('delete_collection.txt', print_r($data_array, true));*/

            // call method to delete category information from DB
            $res = $this->deleteCategoryWebhook($hmac_header_domain, $data_array);
            error_log("Category Update response: " . $res);
            // if event is orders/delete
        } else if ($hmac_header_topic == 'orders/delete') {
            // there are many scenarios for order delete also 
            //if only shopify order
            // if holy land date and shopify order
            // if product assignment order
        } else if ($hmac_header_topic == 'orders/create') {

            // convert json payload to array
            $data_array = json_decode($data, true);

            // R & D which things we can save in our localdatabase that are must for showing an order
            // customer information
            // shipping information
            // billing information
            // products bought by customer 
            // payment details
            // discount details
            // refund details
            // order cancellation 
            Log::info(array("Hook Excecuted" => 'orders/create new once'));
            //    Log::info($data);
            // parameters domain,  order array , charity_id
            // call method to add information in DB
            if ($data_array->email == "jane@example.com") {
            } else {
                $res = $this->createOrder($hmac_header_domain, $data_array, 0);
            }
            error_log("Product Add response: " . $res);
        } else if (trim($hmac_header_topic) == 'orders/updated' || $hmac_header_topic == 'orders/fulfilled') {
            // if event is orders/update
            // convert json payload to array
            $data_array = json_decode($data, true);

            // R & D which things we can save in our localdatabase that are must for showing an order
            // customer information
            // shipping information
            // billing information
            // products bought by customer 
            // payment details
            // discount details
            // refund details
            // order cancellation 
            Log::info(array("Hook Excecuted" => 'orders/update'));
            //       Log::info($data);
            // parameters domain,  order array , charity_id
            // call method to add information in DB
            $res = $this->updateOrder($hmac_header_domain, $data_array, 0);
            error_log("Product Add response: " . $res);
            // if event is products/create
        } else if ($hmac_header_topic == 'inventory_levels/update') {

            Log::info(array("Hook Excecuted" => 'inventory_levels/update'));
            // convert json payload to array
            $data_array = json_decode($data, true);
            Log::info($data_array);
            // we will get inventory_item_id as id which reflect in local database 
            // as inventory_item_id in product variant table 
            // just don't panic and update the inventory item quantity

            //first of all get inventory_item_id from data
            $inventory_item_id = $data_array['inventory_item_id'];
            $available_quantity = 0;
            //flag for quantity update or not
            $is_update_quanity = FALSE;
            if (isset($data_array['available'])) {
                //it's mean inventory updated and we got available quantity against this item
                $available_quantity = $data_array['available'];
                $is_update_quanity = TRUE; // update flag
            } else {
                //it's mean quanitity level updated but we did not got any available quantity
                //so we should send a counter request to shopify and get the updated quantity of this product
                $available_quantity = $this->getQuantityDetailsFromShopifyStore($inventory_item_id);
                if ($available_quantity > 0) {
                    $is_update_quanity = TRUE;
                }
            }

            //now update quanitity in database
            if ($is_update_quanity == TRUE) {
                $product_variant = ProductVariant::where("inventory_item_id", $inventory_item_id)->firstOrFail();
                $product_variant->quantity = $available_quantity;
                $product_variant->save();
                $product = Product::where("id", $product_variant->product_id)->first();
                $product_name =  $product->title;
                $charity_product['user_name'] = "Holy Land Dates";
                $charity_product["product_name"] = $product_name;
                $charity_product['product_quantity'] = $available_quantity;
                //email here
                $email = new  ProductQuantityUpdatedEmail($charity_product);
                //get superadmin email from settings
                $settingemail = PortalSettings::select("website_notify_email")->where("id", 1)->first();
                try {
                    Mail::to($settingemail->website_notify_email)->send($email);
                    Log::info("product quanity updated successfully");
                } catch (Exception $x) {
                    Log::info("product quanity update email from webshook error on next link Line:390");
                    Log::info(array("error" => $x));
                }
            }
        } else {
            // if there is any issue
            Log::info(array("comes here", "why"));
            //$data
            // $fp = fopen('shopify_webhook_erro.txt', 'w');
            // fwrite($fp, 'Something went word with web-hook');
            // fclose($fp);
        }

        //error_log('Webhook verified: '.var_export($verified, true)); //check error.log to see the result

    }

    function verify_webhook($data, $hmac_header)
    {

        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
        return hash_equals($hmac_header, $calculated_hmac);
    }

    /**
     * Save product basic data in DB to get MySQL product_id
     * Within method called three another methods to save data based on product_id
     *
     * @param array,int
     * @return null
     */
    private function addProductWebhook($store_domain_name, $product_data_array)
    {

        // get store_id from DB where store domain name equals to webhook's domain name
        $store_data = Store::where('api_domain', $store_domain_name)->first();

        if ($store_data) {

            $store_id = $store_data->id;
            $user_id = $store_data->user_id;

            $product = Product::create([
                'productId'     => $product_data_array['id'],
                'user_id'       => $user_id,
                'store_id'      => $store_id,
                'title'         => $product_data_array['title'],
                'description'   => $product_data_array['body_html'],
                'brand'         => $product_data_array['vendor'],
                'type'          => $product_data_array['product_type'],
                'handle'        => $product_data_array['handle'],
                'status'        => $product_data_array['status'],
                'tags'          => $product_data_array['tags'],
            ]);

            $this->addProductVariantWebhook($product_data_array['variants'], $product->id);
            $this->addProductImagesWebhook($product_data_array['images'], $product->id);
            $this->addProductOptionsWebhook($product_data_array['variants'], $product_data_array['options'], $product->id);
            $this->addProductExtraDetailsWebhook($product->id);
            $this->addProductCustomInformationWebhook($product->id);
            $this->addCategoryAssociationWebhook($store_data, $product->id, $product_data_array['id']);
        } else {
            error_log("Store not found on update product");
        }
    }

    /**
     * update product basic data in DB and get MySQL product_id
     * within method delete variants, options and images then re-create
     *
     * @param string,array
     * @return null
     */
    private function updateProductWebhook($store_domain_name, $product_data_array)
    {



        // get store_id from DB where store domain name equals to webhook's domain name
        $store_data = Store::where('api_domain', $store_domain_name)->first();

        if ($store_data) {

            $store_id = $store_data->id;

            // get product data from DB by store_id and productId in data array
            $saved_product_data = Product::where('store_id', $store_id)->where('productId', $product_data_array['id'])->first();

            if ($saved_product_data) {

                $saved_product_id = $saved_product_data->id;
                $product_data = Product::find($saved_product_id);
                $product_data->title        = $product_data_array['title'];
                $product_data->description  = $product_data_array['body_html'];
                $product_data->brand        = $product_data_array['vendor'];
                $product_data->type         = $product_data_array['product_type'];
                $product_data->handle       = $product_data_array['handle'];
                $product_data->status       = $product_data_array['status'];
                $product_data->tags         = $product_data_array['tags'];
                $product_data->save();

                // start removing images from storage
                $images_data = ProductImage::where('product_id', $saved_product_id)->get()->toArray();

                if ($images_data) {

                    foreach ($images_data as $image_data) {

                        // get file name
                        $name = substr($image_data['source'], strrpos($image_data['source'], '/') + 1);

                        // remove file
                        Storage::delete('public/product_images/' . $name);
                    }
                }
                // end removing images from storage

                $deleted_options    = ProductOption::where('product_id', $saved_product_id)->delete();
                error_log("Delete Option on update product: " . $deleted_options);

                $deleted_images     = ProductImage::where('product_id', $saved_product_id)->delete();
                error_log("Delete Images on update product: " . $deleted_images);

                $deleted_variants   = ProductVariant::where('product_id', $saved_product_id)->delete();
                error_log("Delete Variants on update product: " . $deleted_variants);

                $deleted_product_category_relation   = Product_ProductCategory::where('product_id', $saved_product_id)->delete();
                error_log("Delete Product and product category relation on update product: " . $deleted_product_category_relation);

                $this->addProductVariantWebhook($product_data_array['variants'], $saved_product_id);
                //                $this->updateProductVariantWebhook($product_data_array['variants'], $saved_product_id);

                $this->addProductImagesWebhook($product_data_array['images'], $saved_product_id);
                $this->addProductOptionsWebhook($product_data_array['variants'], $product_data_array['options'], $saved_product_id);

                $this->addCategoryAssociationWebhook($store_data, $saved_product_id, $product_data_array['id']);
            } else {

                error_log("Product not found on update product");
            }
        } else {
            error_log("Store not found on update product");
        }
    }


    /** Variant logic start   */

    /** note: initially our logic was to update variations we are using method updateProductVariantWebhook  */
    /** note: now our logic is to remove variations and re-create and we are using method addProductVariantWebhook  */
    /** note: also comment or un-comment methods accordingly in above updateProductWebhook method  */


    /**
     * Update product variants data in DB
     *
     * @param array,int
     * @return null
     */
    private function updateProductVariantWebhook($variants, $product_id)
    {

        foreach ($variants as $key => $variant) {

            // get variant data from DB by reference variantId and product_id
            $saved_variant_data = ProductVariant::where('variantId', $variant['id'])->where('product_id', $product_id)->first();

            if ($saved_variant_data) {

                $variant_data = ProductVariant::find($saved_variant_data->id);
                $variant_data->title       = $variant['title'];
                $variant_data->price       = $variant['price'];
                $variant_data->sku         = $variant['sku'];
                $variant_data->quantity    = $variant['inventory_quantity'];
                $variant_data->weight      = $variant['weight'];
                $variant_data->weight_unit = $variant['weight_unit'];
                $variant_data->shipping    = $variant['requires_shipping'];
                $variant_data->taxable     = $variant['taxable'];
                $variant_data->save();
            } else {

                error_log("Variant not found on update variant");
            }
        }
    }

    /**
     * Save product variants data in DB
     *
     * @param array,int
     * @return null
     */
    private function addProductVariantWebhook($variants, $product_id)
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
                'inventory_item_id' => $variant['inventory_item_id'],
            ]);
        }
    }


    /** Variant logic end   */

    /**
     * Save product images data in DB
     *
     * @param array,int
     * @return null
     */
    private function addProductImagesWebhook($images, $product_id)
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
            Storage::put('public/product_images/' . $trimmed_name, $contents);

            // get url of stored image
            $image_url = Storage::url('public/product_images/' . $trimmed_name);

            $url = $image['src'];

            ProductImage::create([
                'product_id' => $product_id,
                'product_variant_id' => $variantId,
                'imageId' => $image['id'],
                'source' => $url,
            ]);
        }
    }

    /**
     * Save product options data in DB
     *
     * @param array,array,int
     * @return null
     */
    private function addProductOptionsWebhook($variants, $options, $product_id)
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
     * save product extra details data in DB
     * At this step we are going to save product_id only for product reference, as we're not getting information from API but user can add info from portal
     * @param int
     * @return null
     */
    private function addProductExtraDetailsWebhook($product_id)
    {

        ProductExtraDetail::create([
            'product_id' => $product_id,
        ]);
    }

    /**
     * save product custom information data in DB
     * At this step we are going to save product_id only for product reference, as we're not getting information from API but user can add info from portal
     * @param int
     * @return null
     */
    private function addProductCustomInformationWebhook($product_id)
    {

        ProductCustomInformation::create([
            'product_id' => $product_id,
        ]);
    }


    /**
     * Delete product data from DB
     *
     * @param string,array
     * @return null
     */
    private function deleteProductWebhook($store_domain_name, $product_data_array)
    {

        // get store_id from DB where store domain name equals to webhook's domain name
        $store_data = Store::where('api_domain', $store_domain_name)->first();

        if ($store_data) {

            $store_id = $store_data->id;

            // get product data from DB by store_id and productId in data array
            $saved_product_data = Product::where('store_id', $store_id)->where('productId', $product_data_array['id'])->first();

            if ($saved_product_data) {

                $saved_product_id = $saved_product_data->id;

                //check if this product assigned to any charity 
                $count = AssignedCharitiesProducts::where("product_id", $saved_product_id)->count();
                $saved_product_data->status = "draft";
                $saved_product_data->save();
                error_log("This Product assigned to charity so we have to deactivate it");


                if ($count > 0) {
                } else {
                    // start removing images from storage
                    $images_data = ProductImage::where('product_id', $saved_product_id)->get()->toArray();

                    if ($images_data) {

                        foreach ($images_data as $image_data) {

                            // get file name
                            $name = substr($image_data['source'], strrpos($image_data['source'], '/') + 1);

                            // remove file
                            Storage::delete('public/product_images/' . $name);
                        }
                    }
                    // end removing images from storage


                    $deleted_options    = ProductOption::where('product_id', $saved_product_id)->delete();
                    error_log("Delete Option on delete product: " . $deleted_options);

                    $deleted_images     = ProductImage::where('product_id', $saved_product_id)->delete();
                    error_log("Delete Images on delete product: " . $deleted_images);

                    $deleted_variants   = ProductVariant::where('product_id', $saved_product_id)->delete();
                    error_log("Delete Variants on delete product: " . $deleted_variants);

                    $deleted_variant_meta   = VariantMeta::where('product_id', $saved_product_id)->delete();
                    error_log("Delete Variants meta Product on delete product: " . $deleted_variant_meta);

                    $deleted_extra_details   = ProductExtraDetail::where('product_id', $saved_product_id)->delete();
                    error_log("Delete Product Extra Details on delete product: " . $deleted_extra_details);

                    // start deleting product custom information record

                    // get the product custom information id to delete record from product_info_hex_codes table as this table has association
                    $product_custom_info = ProductCustomInformation::where('product_id', $saved_product_id)->first();
                    $product_custom_info_id = $product_custom_info->id;

                    if ($product_custom_info_id) {
                        // delete product_info_hex_codes record from DB
                        ProductInfoHexCode::where('product_custom_information_id', $product_custom_info_id)->delete();
                    }

                    $deleted_custom_info = ProductCustomInformation::where('product_id', $saved_product_id)->delete();
                    error_log("Delete Product Custom Information on delete product: " . $deleted_custom_info);

                    // end deleting product custom information record

                    $deleted_product_category_relation   = Product_ProductCategory::where('product_id', $saved_product_id)->delete();
                    error_log("Delete Product and product category relation on delete product: " . $deleted_product_category_relation);

                    $deleted_product   = Product::where('id', $saved_product_id)->delete();
                    error_log("Delete Actual Product on delete product: " . $deleted_product);
                }
            } else {

                error_log("Product not found on update product");
            }
        } else {
            error_log("Store not found on update product");
        }
    }

    /**
     * Save categories association in DB
     *
     * @param array
     * @return null
     */
    private function addCategoryAssociationWebhook($store_data, $product_id, $shopify_product_id)
    {


        // Shopify store credentials
        $api_key            = $store_data->api_key;
        $api_password       = $store_data->api_password;
        $api_domain_name    = $store_data->api_domain;
        $base_url           = $store_data->base_url;   // "admin/api/2021-04"

        // we will store all processed data in these arrays and then insert in one go
        $smart_collection_container = array();
        $custom_collection_container = array();
        $smart_counter = 0;
        $custom_counter = 0;


        // get all shopify reference productId and product ids using relationship store and Product
        //$store_product_ids = Store::find($store_data[0]->id)->storeProducts()->pluck('id', 'productId')->toArray(); // [6786450489532]=>int(1) [6687345967292]=> int(2) [6606052917436]=> int(3)

        $store_product_ids = [

            (int)$shopify_product_id => (int)$product_id
        ];


        if ($store_product_ids) {

            foreach ($store_product_ids as $shopify_product_id =>  $store_product_id) {

                /* ********** Start process for Shopify custom collections ********** */

                // pass product id in shopify endpoint to get product  custom collections, Endpoint: GET /admin/api/2021-07/custom_collections.json?product_id=632910392
                $custom_collections_by_product_id_api_endpoint  = '/custom_collections.json?product_id=' . $shopify_product_id;
                $custom_collections_by_product_id_response      = Http::timeout(240)->retry(3, 1000)->get('https://' . $api_key . ':' . $api_password . '@' . $api_domain_name . '/' . $base_url . $custom_collections_by_product_id_api_endpoint);
                $custom_collections_by_product_id               = json_decode($custom_collections_by_product_id_response->body(), true);

                $count_1 = count($custom_collections_by_product_id['custom_collections']);
                $custom_counter = $custom_counter + $count_1;
                if ($custom_collections_by_product_id) {

                    foreach ($custom_collections_by_product_id['custom_collections'] as $collection) {

                        // store specific data in this array then merge to main container array for insertion
                        $categories = array();

                        // get existing category ids from DB to store in product category relation table
                        $existing_category_ids_obj = ProductCategory::select('id', 'categoryId')->where('categoryId', $collection['id'])->first();

                        if ($existing_category_ids_obj) {

                            $existing_category_ids = $existing_category_ids_obj->toArray();

                            $categories['product_id']   = $store_product_id;
                            $categories['productId']    = $shopify_product_id;
                            $categories['product_category_id']  = $existing_category_ids['id'];
                            $categories['categoryId']   = $existing_category_ids['categoryId'];

                            array_push($custom_collection_container, $categories);
                            //Product_ProductCategory::insert($categories);
                        }
                    }
                }

                /* ********** End process for Shopify custom collections ********** */

                /* ********** Start process for Shopify smart collections ********** */

                // pass product id in shopify endpoint to get product smart collections, Endpoint: GET /admin/api/2021-07/smart_collections.json?product_id=632910392
                $smart_collections_by_product_id_api_endpoint  = '/smart_collections.json?product_id=' . $shopify_product_id;
                $smart_collections_by_product_id_response = Http::timeout(240)->retry(3, 1000)->get('https://' . $api_key . ':' . $api_password . '@' . $api_domain_name . '/' . $base_url . $smart_collections_by_product_id_api_endpoint);
                $smart_collections_by_product_id    = json_decode($smart_collections_by_product_id_response->body(), true);

                $count_2 = count($smart_collections_by_product_id['smart_collections']);
                $smart_counter = $smart_counter + $count_2;

                if ($smart_collections_by_product_id) {

                    foreach ($smart_collections_by_product_id['smart_collections'] as $collection) {

                        // store specific data in this array then merge to main container array for insertion
                        $categories = array();

                        // get existing category ids from DB to store in product category relation table
                        $existing_category_ids = ProductCategory::select('id', 'categoryId')->where('categoryId', $collection['id'])->first()->toArray();

                        if ($existing_category_ids) {

                            $categories['product_id']   = $store_product_id;
                            $categories['productId']    = $shopify_product_id;
                            $categories['product_category_id']  = $existing_category_ids['id'];
                            $categories['categoryId']   = $existing_category_ids['categoryId'];

                            array_push($smart_collection_container, $categories);
                            //Product_ProductCategory::insert($categories);
                        }
                    }
                }

                /* ********** Start process for Shopify smart collections ********** */
            }
        }

        // save product collections in product_category relational table

        if ($smart_collection_container) {
            // insertion moved above code
            Product_ProductCategory::insert($smart_collection_container);
        }

        if ($custom_collection_container) {
            // insertion moved above code
            Product_ProductCategory::insert($custom_collection_container);
        }

        error_log("Smart-Count: .$smart_counter.");
        error_log("custom-Count: .$custom_counter.");
    }

    /**
     * add new category in DB
     *
     * @param string, array
     * @return null
     */
    private function addCategoryWebhook($store_domain_name, $category_data_array)
    {

        // get store_id from DB where store domain name equals to webhook's domain name
        $store_data = Store::where('api_domain', $store_domain_name)->first();

        if ($store_data) {

            $store_id = $store_data->id;
            // get category data from DB
            $existing_category = ProductCategory::where('store_id', $store_id)->where('categoryId', $category_data_array['id'])->first();

            // if category not exists in DB then add new entry otherwise skip insertion to avoid duplication
            if (!$existing_category) {

                $category = ProductCategory::create([
                    'store_id' => $store_id,
                    'categoryId' => $category_data_array['id'],
                    'name' => $category_data_array['handle'],
                ]);
            }
            // update record in DB
            else {
                $existing_category->name = $category_data_array['handle'];
                $existing_category->save();
            }
        } else {
            error_log("Store not found on add category");
        }
    }

    /**
     * update existing category in DB
     *
     * @param string, array
     * @return null
     */
    private function updateCategoryWebhook($store_domain_name, $category_data_array)
    {

        // get store_id from DB where store domain name equals to webhook's domain name
        $store_data = Store::where('api_domain', $store_domain_name)->first();

        if ($store_data) {

            $store_id = $store_data->id;
            $api_key            = $store_data->api_key;
            $api_password       = $store_data->api_password;
            $api_domain_name    = $store_data->api_domain;
            $base_url           = $store_data->base_url;   // "admin/api/2021-04"

            // call end point to get all products of requested collection/category
            $products_by_api_endpoint  = '/collections/' . $category_data_array["id"] . '/products.json';

            // url sample for FHG staging store
            //https://6bf28460cb7112330b8224f9bae3576e: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@fashionhubglobal.myshopify.com/admin/api/2021-04/collections/274673434778/products.json

            $products_by_api_endpoint_response      = Http::timeout(240)->retry(3, 1000)->get('https://' . $api_key . ':' . $api_password . '@' . $api_domain_name . '/' . $base_url . $products_by_api_endpoint);
            $products_by_api  = json_decode($products_by_api_endpoint_response->body(), true);

            if ($products_by_api) {
                $count = 0;
                // delete associations with requested collection/category id
                $deleted_product_category_relation   = Product_ProductCategory::where('categoryId', $category_data_array["id"])->delete();

                // loop through products we have get from above shopify API
                foreach ($products_by_api['products'] as $key => $single_collection_product) {
                    $count++;
                    // get product data from DB based on shopify product id
                    $saved_product_data = Product::where('productId', $single_collection_product['id'])->first();

                    // product_id from DB
                    $saved_product_id = $saved_product_data->id;

                    // as we have remove old association now create new
                    $this->addCategoryAssociationOnClollectionWebhook($saved_product_id, $single_collection_product['id'], $category_data_array['id']);
                }
            }

            // get category data from DB
            $existing_category = ProductCategory::where('store_id', $store_id)->where('categoryId', $category_data_array['id'])->first();

            // if category not exists in DB then add new entry otherwise skip insertion to avoid duplication
            if (!$existing_category) {

                $category = ProductCategory::create([
                    'store_id' => $store_id,
                    'categoryId' => $category_data_array['id'],
                    'name' => $category_data_array['handle'],
                ]);
            }
            // update record in DB
            // we are saving handle as category name but decided in case of update we are replacing handle with title (from shopify payload)
            else {
                $existing_category->name = $category_data_array['title'];
                $existing_category->save();
            }
        } else {
            error_log("Store not found on update category");
        }
    }

    /**
     * Save categories association in DB for collection update webhook
     *
     * @param array
     * @return null
     */
    private function addCategoryAssociationOnClollectionWebhook($product_id, $shopify_product_id, $shopify_category_id)
    {

        // we will store all processed data in these arrays and then insert in one go
        $collection_container = array();

        if ($product_id && $shopify_product_id && $shopify_category_id) {

            // store specific data in this array then merge to main container array for insertion
            $categories = array();

            // get existing category ids from DB to store in product category relation table
            $existing_category_ids_obj = ProductCategory::select('id', 'categoryId')->where('categoryId', (int)$shopify_category_id)->first();

            if ($existing_category_ids_obj) {

                $existing_category_ids = $existing_category_ids_obj->toArray();

                $categories['product_id']   = (int)$product_id;
                $categories['productId']    = (int)$shopify_product_id;
                $categories['product_category_id']  = (int)$existing_category_ids['id'];
                $categories['categoryId']   = (int)$existing_category_ids['categoryId'];

                array_push($collection_container, $categories);
                //Product_ProductCategory::insert($categories);
            }
        }

        // save product collections in product_category relational table

        if ($collection_container) {
            // insertion moved above code
            Product_ProductCategory::insert($collection_container);
        }
    }

    /**
     * Delete Category data from DB and category association with product
     *
     * @param string,array
     * @return null
     */
    private function deleteCategoryWebhook($store_domain_name, $category_data_array)
    {

        // get store_id from DB where store domain name equals to webhook's domain name
        $store_data = Store::where('api_domain', $store_domain_name)->first();

        if ($store_data) {

            $store_id = $store_data->id;

            // get category data from DB by store_id and categoryId in data array
            $saved_category_data = ProductCategory::where('store_id', $store_id)->where('categoryId', $category_data_array['id'])->first();

            if ($saved_category_data) {

                $saved_category_id = $saved_category_data->id;

                // delete associations with requested collection/category id
                $deleted_product_category_relation   = Product_ProductCategory::where('product_category_id', $saved_category_id)->delete();
                error_log("Delete Product and product category relation on delete category: " . $deleted_product_category_relation);

                // delete actual category from DB
                $deleted_product   = ProductCategory::where('id', $saved_category_id)->delete();
                error_log("Delete Actual Category on delete category: " . $deleted_product);
            } else {

                error_log("Category not found on delete category webhook");
            }
        } else {
            error_log("Store not found on delete category webhook");
        }
    }

    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function createOrder($store_domain_name, $order_data_array, $charity_id)
    {

        // get store_id from DB where store domain name equals to webhook's domain name
        $store_data = Store::where('api_domain', $store_domain_name)->first();
        Log::info($order_data_array);
        if ($store_data) {

            $store_id = $store_data->id;
            $user_id = $charity_id; //mean charity id

            // make order array because it will be reused in main site order 
            // because this is webhook 
            // and we have main website also where we will make charity orders
            $create_order = [
                'order_id'     => array_key_exists('id', $order_data_array) ? $order_data_array['id'] : 0,
                'store_id'      => $store_id,
                "cancel_reason" => array_key_exists('cancel_reason', $order_data_array) ? $order_data_array['cancel_reason'] : "",
                "cancelled_at" => array_key_exists('cancelled_at', $order_data_array) ? $order_data_array['cancelled_at'] : "",
                "cart_token" => array_key_exists('cart_token', $order_data_array) ? $order_data_array['cart_token'] : "",
                "checkout_id" => array_key_exists('checkout_id', $order_data_array) ? $order_data_array['checkout_id'] : "",
                "checkout_token" => array_key_exists('checkout_token', $order_data_array) ? $order_data_array['checkout_token'] : "",
                // "client_details" => array_key_exists('client_details',$order_data_array) ? $order_data_array['client_details'] : "",
                "closed_at" => array_key_exists('closed_at', $order_data_array) ? $order_data_array['closed_at'] : "",
                "confirmed" => array_key_exists('confirmed', $order_data_array) ? $order_data_array['confirmed'] : "",
                "contact_email" => array_key_exists('contact_email', $order_data_array) ? $order_data_array['contact_email'] : "",
                "currency" => array_key_exists('currency', $order_data_array) ? $order_data_array['currency'] : "",
                "current_subtotal_price" => array_key_exists('current_subtotal_price', $order_data_array) ? $order_data_array['current_subtotal_price'] : 0,
                "current_total_discounts" => array_key_exists('current_total_discounts', $order_data_array) ? $order_data_array['current_total_discounts'] : "",
                "current_total_duties_set" => array_key_exists('current_total_duties_set', $order_data_array) ? $order_data_array['current_total_duties_set'] : "",
                "current_total_price" => array_key_exists('current_total_price', $order_data_array) ? $order_data_array['current_total_price'] : "",
                "current_total_tax" => array_key_exists('current_total_tax', $order_data_array) ? $order_data_array['current_total_tax'] : "",
                "email" => array_key_exists('email', $order_data_array) ? $order_data_array['email'] : "",
                "estimated_taxes" => array_key_exists('estimated_taxes', $order_data_array) ? $order_data_array['estimated_taxes'] : "",
                "financial_status" => array_key_exists('financial_status', $order_data_array) ? $order_data_array['financial_status'] : "",
                "fulfillment_status" => array_key_exists('fulfillment_status', $order_data_array) ? $order_data_array['fulfillment_status'] : "",
                "gateway" => array_key_exists('gateway', $order_data_array) ? $order_data_array['gateway'] : "",
                "landing_site" => array_key_exists('landing_site', $order_data_array) ? $order_data_array['landing_site'] : "",
                "landing_site_ref" => array_key_exists('landing_site_ref', $order_data_array) ? $order_data_array['landing_site_ref'] : "",
                "location_id" => array_key_exists('location_id', $order_data_array) ? $order_data_array['location_id'] : "",
                "name" => array_key_exists('name', $order_data_array) ? $order_data_array['name'] : "",
                "note" => array_key_exists('note', $order_data_array) ? $order_data_array['note'] : "",
                // "note_attributes" => array_key_exists('note_attributes',$order_data_array) ? $order_data_array['note_attributes'] : "",
                "number" => array_key_exists('number', $order_data_array) ? $order_data_array['number'] : "",
                "order_number" => array_key_exists('order_number', $order_data_array) ? $order_data_array['order_number'] : "",
                "order_status_url" => array_key_exists('order_status_url', $order_data_array) ? $order_data_array['order_status_url'] : "",
                "original_total_duties_set" => array_key_exists('original_total_duties_set', $order_data_array) ? $order_data_array['original_total_duties_set'] : "",
                "payment_gateway_names" => implode(",", $order_data_array['payment_gateway_names']),
                "phone" => array_key_exists('phone', $order_data_array) ? $order_data_array['phone'] : "",
                "presentment_currency" => array_key_exists('presentment_currency', $order_data_array) ? $order_data_array['presentment_currency'] : "",
                "processed_at" => array_key_exists('processed_at', $order_data_array) ? $order_data_array['processed_at'] : "",
                "processing_method" => array_key_exists('processing_method', $order_data_array) ? $order_data_array['processing_method'] : "",
                "reference" => array_key_exists('reference', $order_data_array) ? $order_data_array['reference'] : "",
                "referring_site" => array_key_exists('referring_site', $order_data_array) ? $order_data_array['referring_site'] : "",
                "source_identifier" => array_key_exists('source_identifier', $order_data_array) ? $order_data_array['source_identifier'] : "",
                "source_name" => array_key_exists('source_name', $order_data_array) ? $order_data_array['source_name'] : "",
                "source_url" => array_key_exists('source_url', $order_data_array) ? $order_data_array['source_url'] : "",
                "subtotal_price" => array_key_exists('subtotal_price', $order_data_array) ? $order_data_array['subtotal_price'] : "",
                "tags" => array_key_exists('tags', $order_data_array) ? $order_data_array['tags'] : "",
                "taxes_included" => array_key_exists('taxes_included', $order_data_array) ? $order_data_array['taxes_included'] : "",
                "test" => array_key_exists('test', $order_data_array) ? $order_data_array['test'] : "",
                "token" => array_key_exists('token', $order_data_array) ? $order_data_array['token'] : "",
                "total_discounts" => array_key_exists('total_discounts', $order_data_array) ? $order_data_array['total_discounts'] : "",
                "total_line_items_price" => array_key_exists('total_line_items_price', $order_data_array) ? $order_data_array['total_line_items_price'] : "",
                "total_outstanding" => array_key_exists('total_outstanding', $order_data_array) ? $order_data_array['total_outstanding'] : "",
                "total_price" => array_key_exists('total_price', $order_data_array) ? $order_data_array['total_price'] : "",
                "total_price_usd" => array_key_exists('total_price_usd', $order_data_array) ? $order_data_array['total_price_usd'] : "",
                "total_tax" => array_key_exists('total_tax', $order_data_array) ? $order_data_array['total_tax'] : "",
                "total_tip_received" => array_key_exists('total_tip_received', $order_data_array) ? $order_data_array['total_tip_received'] : "",
                "total_weight" => array_key_exists('total_weight', $order_data_array) ? $order_data_array['total_weight'] : "",
                "user_id" => array_key_exists('user_id', $order_data_array) ? $order_data_array['user_id'] : "",
                "is_shopify_order" => 1,
                "is_charity_order" => 0,
            ];


            $order = new Orders();
            //check because if this order generated by us and returning from shopify as a hook
            // case possible
            // assigning the products create an order on shopify to reduce quantity for log
            // create order in case a customer bought 1 or more holylanddates products along with or without charity product
            // for example islamicrelief: have Majdool date product only
            // and customer bought olive oil and majdool date 
            // so we have to make an order for olive oil to update quantity there
            // and reduce majdool date quantity from assigned products
            // in that cases we can get orders/create webhook hit so we can check if order exists

            $check_if_order_exists = $order->check_order_exists($order_data_array['id']);
            if ($check_if_order_exists == FALSE) {
                //it's mean order does not exists create order and insert all the details
                //      Log::info($create_order);
                $neworder = Orders::create($create_order);
                Log::info(array("order created" => "----------" . $neworder->id . "-------------"));

                if ($neworder) {
                    // add billing detail
                    //params local id and billing detail
                    if (array_key_exists('billing_address', $order_data_array)) {
                        if ($order_data_array['billing_address'] <> null)
                            $this->addShopifyOrderBillingDetail($neworder->id, $order_data_array['billing_address']);
                    }
                    Log::info(array("order billing" => "---------- created -------------"));

                    // add fullfilment
                    // params local id and fullfilment detail
                    if (array_key_exists('fulfillments', $order_data_array)) {
                        if ($order_data_array['fulfillments'] <> null)
                            $this->addShopifyOrderFullfilmentDetail($neworder->id, $order_data_array['fulfillments']);
                    }
                    Log::info(array("order fulfillments" => "---------- created -------------"));

                    // add products list
                    // params local id and products list

                    if (array_key_exists('line_items', $order_data_array)) {
                        if ($order_data_array['line_items'] <> null)
                            $this->addShopifyOrderProductListDetail($neworder->id, $order_data_array['line_items']);
                    }
                    Log::info(array("order line_items" => "---------- created -------------"));

                    // add payment detail
                    // params local id and payment
                    if (array_key_exists('payment_details', $order_data_array))
                        $this->addShopifyOrderPaymentDetail($neworder->id, $order_data_array['payment_details']);

                    Log::info(array("order payment_details" => "---------- created -------------"));

                    // add refunds
                    // params local id and refund details
                    //   $this->addShopifyOrderRefundDetail($neworder->id);

                    // add shipping detail
                    // params local id and shipping details
                    if ($order_data_array['shipping_address'] <> null)
                        $this->addShopifyOrderShippingDetail($neworder->id, $order_data_array['shipping_address']);

                    Log::info(array("order shipping_address" => "---------- created -------------"));

                    // add customer detail
                    // params local id and customer
                    if ($order_data_array['customer'] <> null) {
                        Log::info(array("found" => "bhaifound "));

                        $this->addShopifyOrderCustomerDetail($neworder->id, $order_data_array['customer']);
                    } else {
                        Log::info(array("found" => "----------no customer-------------"));
                    }
                }
                //                Log::info($neworder);
            } else {
                Log::info(array("order exists main ja raha hai" => "----------" . $check_if_order_exists->id . "-------------"));

                //it's mean order exists
                // so in that case check if this order is from charity or not
                //if it's charity order we don't have to update it
                if ($order->is_charity_order == 1) {
                } else {
                    //else it's mean it's an order from holylanddates product so we can update it

                }
            }
            // $product = Order::create();

            // $this->addProductVariantWebhook($product_data_array['variants'], $product->id);
            // $this->addProductImagesWebhook($product_data_array['images'], $product->id);
            // $this->addProductOptionsWebhook($product_data_array['variants'], $product_data_array['options'], $product->id);
            // $this->addProductExtraDetailsWebhook($product->id);
            // $this->addProductCustomInformationWebhook($product->id);
            // $this->addCategoryAssociationWebhook($store_data, $product->id, $product_data_array['id']);
        } else {
            error_log("Store not found on update product");
        }
    }

    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function addShopifyOrderBillingDetail($order_id_local_db, $data)
    {
        //check order bill detail 
        $OrderBillingAddress = new OrderBillingAddress();
        $check_result = $OrderBillingAddress->check_bill_data($order_id_local_db);
        if ($check_result == FALSE) {
            $OrderBillingAddress->insertBillingDetail($order_id_local_db, $data);
        }
    }
    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function addShopifyOrderFullfilmentDetail($order_id_local_db, $data)
    {
        //check order bill detail 
        $OrderFullfilments = new OrderFullfilments();
        $check_result = $OrderFullfilments->check_fullfilment_data($order_id_local_db);

        if ($check_result == FALSE) {
            $OrderFullfilments->insertFullfilmentgDetail($order_id_local_db, $data);
        }
    }
    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function addShopifyOrderProductListDetail($order_id_local_db, $data)
    {
        //loop all products 
        foreach ($data as $product) {
            $OrderListOfProducts = new OrderListOfProducts();
            $check_result = $OrderListOfProducts->check_product_exists($order_id_local_db, $product['id']);
            if ($check_result == FALSE) {
                $OrderListOfProducts->insertProductDetail($order_id_local_db, $product);
            }
        }
    }
    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function addShopifyOrderPaymentDetail($order_id_local_db, $data)
    {
        //check order bill detail 
        $OrderPaymentDetails = new OrderPaymentDetails();
        $check_result = $OrderPaymentDetails->check_payment_data($order_id_local_db);

        if ($check_result == FALSE) {
            $OrderPaymentDetails->insertPaymentgDetail($order_id_local_db, $data);
        }
    }
    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function addShopifyOrderRefundDetail($order_id_local_db, $data)
    {
    }


    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function addShopifyOrderShippingDetail($order_id_local_db, $data)
    {
        //check order bill detail 
        $OrderShippingAddress = new OrderShippingAddress();
        $check_result = $OrderShippingAddress->check_shipping_data($order_id_local_db);

        if ($check_result == FALSE) {
            $OrderShippingAddress->insertShippingDetail($order_id_local_db, $data);
        }
    }



    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function addShopifyOrderCustomerDetail($order_id_local_db, $data)
    {
        Log::info(array("found" => "------------------------customer----------------------------"));
        Log::info($data);

        //check order bill detail 
        $Customers = new Customers();
        $check_result = $Customers->check_Customer_data($data['email']);

        if ($check_result == FALSE) {
            $make_customer_data_here = [
                "email" => $data['email'],
                "accepts_marketing" => $data['accepts_marketing'],
                "first_name" => $data['first_name'],
                "last_name" => $data['last_name'],
                "orders_count" => $data['orders_count'],
                "state" => $data['state'],
                "total_spent" => $data['total_spent'],
                "last_order_id" => $data['last_order_id'],
                "note" => $data['note'],
                "verified_email" => $data['verified_email'],
                "multipass_identifier" => $data['multipass_identifier'],
                "tax_exempt" => $data['tax_exempt'],
                "phone" => $data['phone'],
                "tags" => $data['tags'],
                "last_order_name" => $data['last_order_name'],
                "currency" => $data['currency'],
                "accepts_marketing_updated_at" => $data['accepts_marketing_updated_at'],
                "marketing_opt_in_level" => $data['marketing_opt_in_level'],
                "sms_marketing_consent" => $data['sms_marketing_consent'],
                "admin_graphql_api_id" => $data['admin_graphql_api_id'],
                "shopify_customer_id" => $data['default_address']['customer_id'],
                "default_address_address1" => $data['default_address']['address1'],
                "default_address_address2" => $data['default_address']['address2'],
                "default_address_city" => $data['default_address']['city'],
                "default_address_province" => $data['default_address']['province'],
                "default_address_country" => $data['default_address']['country'],
                "default_address_zip" => $data['default_address']['zip'],
                "default_address_phone" => $data['default_address']['phone'],
                "default_address_province_code" => $data['default_address']['province_code'],
                "default_address_country_code" => $data['default_address']['country_code'],
                "default_address_country_name" => $data['default_address']['country_name']
            ];

            $Customers->insertCustomerDetail($order_id_local_db, $make_customer_data_here);
        } else {
            $order = Orders::findorfail($order_id_local_db);
            $order->customer_id = $check_result->id;
            $order->update();
        }
    }


    /****** update orders functions start from here  */

    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function updateOrder($store_domain_name, $order_data_array, $charity_id)
    {
        Log::info(array("comies in " => "update"));
        // get store_id from DB where store domain name equals to webhook's domain name
        $store_data = Store::where('api_domain', $store_domain_name)->first();
        Log::info($order_data_array);
        if ($store_data) {
            Log::info(array("comies in " => "update 2"));

            $store_id = $store_data->id;
            $user_id = $charity_id; //mean charity id

            // make order array because it will be reused in main site order 
            // because this is webhook 
            // and we have main website also where we will make charity orders
            $create_order = [
                'order_id'     => array_key_exists('id', $order_data_array) ? $order_data_array['id'] : 0,
                'store_id'      => $store_id,
                "cancel_reason" => array_key_exists('cancel_reason', $order_data_array) ? $order_data_array['cancel_reason'] : "",
                "cancelled_at" => array_key_exists('cancelled_at', $order_data_array) ? $order_data_array['cancelled_at'] : "",
                "cart_token" => array_key_exists('cart_token', $order_data_array) ? $order_data_array['cart_token'] : "",
                "checkout_id" => array_key_exists('checkout_id', $order_data_array) ? $order_data_array['checkout_id'] : "",
                "checkout_token" => array_key_exists('checkout_token', $order_data_array) ? $order_data_array['checkout_token'] : "",
                // "client_details" => array_key_exists('client_details',$order_data_array) ? $order_data_array['client_details'] : "",
                "closed_at" => array_key_exists('closed_at', $order_data_array) ? $order_data_array['closed_at'] : "",
                "confirmed" => array_key_exists('confirmed', $order_data_array) ? $order_data_array['confirmed'] : "",
                "contact_email" => array_key_exists('contact_email', $order_data_array) ? $order_data_array['contact_email'] : "",
                "currency" => array_key_exists('currency', $order_data_array) ? $order_data_array['currency'] : "",
                "current_subtotal_price" => array_key_exists('current_subtotal_price', $order_data_array) ? $order_data_array['current_subtotal_price'] : 0,
                "current_total_discounts" => array_key_exists('current_total_discounts', $order_data_array) ? $order_data_array['current_total_discounts'] : "",
                "current_total_duties_set" => array_key_exists('current_total_duties_set', $order_data_array) ? $order_data_array['current_total_duties_set'] : "",
                "current_total_price" => array_key_exists('current_total_price', $order_data_array) ? $order_data_array['current_total_price'] : "",
                "current_total_tax" => array_key_exists('current_total_tax', $order_data_array) ? $order_data_array['current_total_tax'] : "",
                "email" => array_key_exists('email', $order_data_array) ? $order_data_array['email'] : "",
                "estimated_taxes" => array_key_exists('estimated_taxes', $order_data_array) ? $order_data_array['estimated_taxes'] : "",
                "financial_status" => array_key_exists('financial_status', $order_data_array) ? $order_data_array['financial_status'] : "",
                "fulfillment_status" => array_key_exists('fulfillment_status', $order_data_array) ? $order_data_array['fulfillment_status'] : "",
                "gateway" => array_key_exists('gateway', $order_data_array) ? $order_data_array['gateway'] : "",
                "landing_site" => array_key_exists('landing_site', $order_data_array) ? $order_data_array['landing_site'] : "",
                "landing_site_ref" => array_key_exists('landing_site_ref', $order_data_array) ? $order_data_array['landing_site_ref'] : "",
                "location_id" => array_key_exists('location_id', $order_data_array) ? $order_data_array['location_id'] : "",
                "name" => array_key_exists('name', $order_data_array) ? $order_data_array['name'] : "",
                "note" => array_key_exists('note', $order_data_array) ? $order_data_array['note'] : "",
                // "note_attributes" => array_key_exists('note_attributes',$order_data_array) ? $order_data_array['note_attributes'] : "",
                "number" => array_key_exists('number', $order_data_array) ? $order_data_array['number'] : "",
                "order_number" => array_key_exists('order_number', $order_data_array) ? $order_data_array['order_number'] : "",
                "order_status_url" => array_key_exists('order_status_url', $order_data_array) ? $order_data_array['order_status_url'] : "",
                "original_total_duties_set" => array_key_exists('original_total_duties_set', $order_data_array) ? $order_data_array['original_total_duties_set'] : "",
                "payment_gateway_names" => implode(",", $order_data_array['payment_gateway_names']),
                "phone" => array_key_exists('phone', $order_data_array) ? $order_data_array['phone'] : "",
                "presentment_currency" => array_key_exists('presentment_currency', $order_data_array) ? $order_data_array['presentment_currency'] : "",
                "processed_at" => array_key_exists('processed_at', $order_data_array) ? $order_data_array['processed_at'] : "",
                "processing_method" => array_key_exists('processing_method', $order_data_array) ? $order_data_array['processing_method'] : "",
                "reference" => array_key_exists('reference', $order_data_array) ? $order_data_array['reference'] : "",
                "referring_site" => array_key_exists('referring_site', $order_data_array) ? $order_data_array['referring_site'] : "",
                "source_identifier" => array_key_exists('source_identifier', $order_data_array) ? $order_data_array['source_identifier'] : "",
                "source_name" => array_key_exists('source_name', $order_data_array) ? $order_data_array['source_name'] : "",
                "source_url" => array_key_exists('source_url', $order_data_array) ? $order_data_array['source_url'] : "",
                "subtotal_price" => array_key_exists('subtotal_price', $order_data_array) ? $order_data_array['subtotal_price'] : "",
                "tags" => array_key_exists('tags', $order_data_array) ? $order_data_array['tags'] : "",
                "taxes_included" => array_key_exists('taxes_included', $order_data_array) ? $order_data_array['taxes_included'] : "",
                "test" => array_key_exists('test', $order_data_array) ? $order_data_array['test'] : "",
                "token" => array_key_exists('token', $order_data_array) ? $order_data_array['token'] : "",
                "total_discounts" => array_key_exists('total_discounts', $order_data_array) ? $order_data_array['total_discounts'] : "",
                "total_line_items_price" => array_key_exists('total_line_items_price', $order_data_array) ? $order_data_array['total_line_items_price'] : "",
                "total_outstanding" => array_key_exists('total_outstanding', $order_data_array) ? $order_data_array['total_outstanding'] : "",
                "total_price" => array_key_exists('total_price', $order_data_array) ? $order_data_array['total_price'] : "",
                "total_price_usd" => array_key_exists('total_price_usd', $order_data_array) ? $order_data_array['total_price_usd'] : "",
                "total_tax" => array_key_exists('total_tax', $order_data_array) ? $order_data_array['total_tax'] : "",
                "total_tip_received" => array_key_exists('total_tip_received', $order_data_array) ? $order_data_array['total_tip_received'] : "",
                "total_weight" => array_key_exists('total_weight', $order_data_array) ? $order_data_array['total_weight'] : "",
                "user_id" => array_key_exists('user_id', $order_data_array) ? $order_data_array['user_id'] : "",
                "is_shopify_order" => 1,
                "is_charity_order" => 0,
            ];

            $order = new Orders();
            //check because if this order generated by us and returning from shopify as a hook
            // case possible
            // assigning the products create an order on shopify to reduce quantity for log
            // create order in case a customer bought 1 or more holylanddates products along with or without charity product
            // for example islamicrelief: have Majdool date product only
            // and customer bought olive oil and majdool date 
            // so we have to make an order for olive oil to update quantity there
            // and reduce majdool date quantity from assigned products
            // in that cases we can get orders/create webhook hit so we can check if order exists

            $check_if_order_exists = $order->check_order_exists($order_data_array['id']);
            Log::info($check_if_order_exists);
            if ($check_if_order_exists == FALSE) {
            } else {
                //it's mean order exists
                // so in that case check if this order is from charity or not
                //if it's charity order we don't have to update it
                if ($order->is_charity_order == 1) {
                } else {
                    //else it's mean it's an order from holylanddates product so we can update it
                    //it's mean order does not exists create order and insert all the details
                    $neworder = Orders::where("order_id", $order_data_array['id'])->first();
                    $neworder->update($create_order);
                    Log::info(array("what is here", "check"));
                    if (!$neworder == false) {
                        Log::info(array("message" => "updating order test"));

                        // add billing detail
                        //params local id and billing detail
                        if (array_key_exists('billing_address', $order_data_array)) {
                            if ($order_data_array['billing_address'] <> null)
                                $this->updateShopifyOrderBillingDetail($neworder->id, $order_data_array['billing_address']);
                        }
                        // add fullfilment
                        // params local id and fullfilment detail
                        if (array_key_exists('fulfillments', $order_data_array)) {
                            if ($order_data_array['fulfillments'] <> null)
                                $this->updateShopifyOrderFullfilmentDetail($neworder->id, $order_data_array['fulfillments']);
                        }
                        // add products list
                        // params local id and products list
                        if (array_key_exists('line_items', $order_data_array)) {
                            if ($order_data_array['line_items'] <> null)
                                $this->updateShopifyOrderProductListDetail($neworder->id, $order_data_array['line_items']);
                        }
                        // add payment detail
                        // params local id and payment
                        if (array_key_exists('payment_details', $order_data_array)) {
                            if ($order_data_array['payment_details'] <> null)
                                $this->updateShopifyOrderPaymentDetail($neworder->id, $order_data_array['payment_details']);
                        }
                        // add refunds
                        // params local id and refund details
                        //   $this->updateShopifyOrderRefundDetail($neworder->id);

                        // add shipping detail
                        // params local id and shipping details
                        if (array_key_exists('shipping_address', $order_data_array)) {
                            if ($order_data_array['shipping_address'] <> null)
                                $this->updateShopifyOrderShippingDetail($neworder->id, $order_data_array['shipping_address']);
                        }
                    }

                    //  Log::info($neworder);
                }
            }
            // $product = Order::create();

            // $this->addProductVariantWebhook($product_data_array['variants'], $product->id);
            // $this->addProductImagesWebhook($product_data_array['images'], $product->id);
            // $this->addProductOptionsWebhook($product_data_array['variants'], $product_data_array['options'], $product->id);
            // $this->addProductExtraDetailsWebhook($product->id);
            // $this->addProductCustomInformationWebhook($product->id);
            // $this->addCategoryAssociationWebhook($store_data, $product->id, $product_data_array['id']);
        } else {
            error_log("Store not found on update product");
        }
    }

    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function updateShopifyOrderBillingDetail($order_id_local_db, $data)
    {
        Log::info(array("update" => "Bill detail"));
        //check order bill detail 
        $OrderBillingAddress = new OrderBillingAddress();
        $check_result = $OrderBillingAddress->check_bill_data($order_id_local_db);
        $check_result->updateBillingDetail($order_id_local_db, $data);
    }
    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function updateShopifyOrderFullfilmentDetail($order_id_local_db, $data)
    {
        //check order bill detail 
        $OrderFullfilments = new OrderFullfilments();
        $check_result = $OrderFullfilments->check_fullfilment_data($order_id_local_db);
        Log::info(array("test" => "check_result fullfillment"));
        Log::info($check_result);
        if ($check_result == FALSE) {
            $OrderFullfilments->insertFullfilmentgDetail($order_id_local_db, $data);
        } else {
            $OrderFullfilments->updateFullfilmentgDetail($order_id_local_db, $data);
        }
    }
    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function updateShopifyOrderProductListDetail($order_id_local_db, $data)
    {

        //loop all products 
        foreach ($data as $product) {
            $OrderListOfProducts = new OrderListOfProducts();
            $check_result = $OrderListOfProducts->check_product_exists($order_id_local_db, $product['id']);
            $OrderListOfProducts->updateProductDetail($order_id_local_db, $product);
        }
    }
    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function updateShopifyOrderPaymentDetail($order_id_local_db, $data)
    {
        //check order bill detail 
        $OrderPaymentDetails = new OrderPaymentDetails();
        $check_result = $OrderPaymentDetails->check_payment_data($order_id_local_db);

        $OrderPaymentDetails->updatePaymentgDetail($order_id_local_db, $data);
    }
    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function updateShopifyOrderRefundDetail($order_id_local_db, $data)
    {
    }
    /**
     * Save a new order basic data in DB to get MySQL base on order id
     * Within method called various methods based on order_id
     *
     * @param string,array,int
     * @return null
     */
    private function updateShopifyOrderShippingDetail($order_id_local_db, $data)
    {
        //check order bill detail 
        $OrderShippingAddress = new OrderShippingAddress();
        $check_result = $OrderShippingAddress->check_shipping_data($order_id_local_db);
        $OrderShippingAddress->updateShippingDetail($order_id_local_db, $data);
    }
}
