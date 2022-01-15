<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Product extends Model
{
    use HasFactory;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    //    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'productId',
        'store_id',
        'title',
        'description',
        'brand',
        'type',
        'tags',
        'handle',
        'price',
        'status',
    ];

    /**
     * Product record associated with user.
     * Relation: each product just has one user
     */
    public function productUser()
    {

        return $this->belongsTo(User::class);
    }

    /**
     * Product record associated with store.
     * Relation: each product just has one store
     */
    public function productStore()
    {

        return $this->belongsTo(Store::class);
    }

    /**
     * Product record associated with product_variants.
     * Relation: each product may have more than one product_variant
     */
    public function productVariants()
    {

        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Product record associated with product_images.
     * Relation: each product may have more than one product_image
     */
    public function productImages()
    {

        return $this->hasMany(ProductImage::class, 'product_id');
    }

    /**
     * Product record associated with product_options.
     * Relation: each product may have more than one product_option
     */
    public function productOptions()
    {

        return $this->hasMany(ProductOption::class);
    }

    /**
     * Products record associated with product_categories.
     * Relation: each product may have more than one product_categories
     */
    public function productCategories()
    {

        return $this->belongsToMany(ProductCategory::class);
    }

    /**
     * Products record associated with product_extra_detail.
     * Relation: each product may have one product_extra_detail
     */
    public function productExtraDetail()
    {

        return $this->hasOne(ProductExtraDetail::class);
    }

    /**
     * Products record associated with product_custom_information.
     * Relation: each product may have one custom_information.
     */
    public function productCustomInformation()
    {

        return $this->hasOne(ProductCustomInformation::class, 'product_id');
    }



    /**
     * Product record associated with AssignedCharitiesProducts.
     * Relation: each product may have assigned to many charities
     */
    public function assignedcharitiesproducts()
    {

        return $this->hasMany(AssignedCharitiesProducts::class);
    }


    /**
     * Shopify quantity check against product.
     * Parameters shopify_product_id.
     */
    public function checkProductQuantityFromShopifyStore($shopify_product_id, $shopify_product_variant_id)
    {

        //get from database store connection 
        $store = Store::where("user_id", Auth::user()->id)->first();
        $api_key            = $store->api_key;
        $api_password       = $store->api_password;
        $api_domain_name    = $store->api_domain;
        $base_url           = $store->base_url;   // "admin/api/2021-04"
        $api_endpoint = '/products/' . $shopify_product_id . '.json?limit=1';



        //$url = 'https://' . $api_key . ':' . $api_password . '@' . $api_domain_name.'/'.$base_url . $api_endpoint . $pagination;

        $response = Http::get('https://' . $api_key . ':' . $api_password . '@' . $api_domain_name . '/' . $base_url . $api_endpoint);
        //now we have product from shopify
        $products = json_decode($response->body(), true);


        // we are assuming hld charities have only 1 variant so for 
        // saving our time i am sticking variant 0 index for checking quantity
        // otherwise in future we can use foreach loop on variants to check particular variant
        // just return this quantity to controller
        return $products['product']['variants'][0]['inventory_quantity'];
    }

    function adjustOrderOnShopifyStore($variant_id, $quantity)
    {
        //find inventory item id 
        $inventory = ProductVariant::where("variantId", $variant_id)->first();
        //get from database store connection 
        $store = Store::where("user_id", Auth::user()->id)->first();
        $api_key            = $store->api_key;
        $api_password       = $store->api_password;
        $api_domain_name    = $store->api_domain;
        $base_url           = $store->base_url;   // "admin/api/2021-04"
        //  $api_endpoint = '/products/' . $shopify_product_id . '.json?limit=1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . $api_domain_name . '/admin/api/2021-10/inventory_levels/adjust.json',);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $order['location_id'] = "66110521585";
        $order['inventory_item_id'] = $inventory->inventory_item_id;
        $order['available_adjustment'] = $quantity;
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order));
        $headers = array();
        $headers[] = 'X-Shopify-Access-Token: ' . $api_password;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            die();
        }
        curl_close($ch);
    }

    /**
     * Method: Responsible to create order on shopify store
     * Parameters products and users details
     */
    function craeteOrderOnShopifyStore($products, $customer)
    {
        //get from database store connection 
        $store = Store::where("user_id", Auth::user()->id)->first();
        $api_key            = $store->api_key;
        $api_password       = $store->api_password;
        $api_domain_name    = $store->api_domain;
        $base_url           = $store->base_url;   // "admin/api/2021-04"
        //  $api_endpoint = '/products/' . $shopify_product_id . '.json?limit=1';

        $ch = curl_init();
        $orders = array(
            "order" => [
                "inventory_behaviour" => "decrement_obeying_policy",
                "customer" => [
                    "first_name" => "Charity name go here",
                    "last_name" => $customer,
                    "email" => "paul.norman@example.com"
                ],
                "billing_address" => [
                    "first_name" => "Charity name go here",
                    "last_name" => $customer,
                    "address1" => "Holy Land Dates Own",
                    "phone" => "",
                    "city" => "Holy Land Dates",
                    "province" => "",
                    "country" => "",
                    "zip" => ""
                ],
                "shipping_address" => [
                    "first_name" => "Charity name go here",
                    "last_name" => $customer,
                    "address1" => "",
                    "phone" => "",
                    "city" => "",
                    "province" => "",
                    "country" => "",
                    "zip" => ""
                ],
                "email" => "jane@example.com",
                "transactions" => [array("kind" => "authorization", "status" => "success", "amount" => $products[0]['price'])],
                "financial_status" => "paid",
                "line_items" => $products, // [["title" => "Red Leather Coat", "price" => 129.99, "grams" => "1700", "quantity" => 1], ["title" => "Blue Suede Shoes", "price" => 85.95, "grams" => "750", "quantity" => 1, "taxable" => false], ["title" => "Raspberry Beret", "price" => 19.99, "grams" => "320", "quantity" => 2]],
            ]

        );

        curl_setopt($ch, CURLOPT_URL, 'https://' . $api_domain_name . '/admin/api/2021-10/orders.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $order['line_items'] = $products;

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orders));
        $headers = array();
        $headers[] = 'X-Shopify-Access-Token: ' . $api_password;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        } else {
            return 1;
        }
        curl_close($ch);
    }
}
