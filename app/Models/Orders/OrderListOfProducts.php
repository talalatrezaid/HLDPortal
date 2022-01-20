<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class OrderListOfProducts extends Model
{
    use HasFactory;

    protected $fillable = [
        "order_id",
        "shopify_id",
        "shopify_id",
        "fulfillable_quantity",
        "fulfillment_service",
        "fulfillment_status",
        "gift_card",
        "grams",
        "name",
        "price",
        "product_exists",
        "product_id",
        "quantity",
        "requires_shipping",
        "sku",
        "taxable",
        "title",
        "total_discount",
        "variant_id",
        "variant_inventory_management",
        "variant_title",
        "vendor"
    ];

    public function check_product_exists($order_id_local_db, $product_id)
    {
        # code...
        // product id from shopify 
        $qry =  OrderListOfProducts::where("order_id", $order_id_local_db)->where("product_id", $product_id)->first();

        if ($qry)
            return $qry;
        else
            return FALSE;
    }


    //insert any kind of order's product detail whether it coming from shopify or local db every order should have this detail
    public function insertProductDetail($order_id_local_db, $data)
    {
        $productDetail = [
            "order_id" => $order_id_local_db,
            "shopify_id" => $data["id"],
            "fulfillable_quantity" => $data["fulfillable_quantity"],
            "fulfillment_service" => $data["fulfillment_service"],
            "fulfillment_status" => $data["fulfillment_status"],
            "gift_card" => $data["gift_card"],
            "grams" => $data["grams"],
            "name" => $data["name"],
            "price" => $data["price"],
            "product_exists" => $data["product_exists"],
            "product_id" => $data["product_id"],
            "quantity" => $data["quantity"],
            "requires_shipping" => $data["requires_shipping"],
            "sku" => $data["sku"],
            "taxable" => $data["taxable"],
            "title" => $data["title"],
            "total_discount" => $data["total_discount"],
            "variant_id" => $data["variant_id"],
            "variant_inventory_management" => $data["variant_inventory_management"],
            "variant_title" => $data["variant_title"],
            "vendor" => $data["vendor"],
        ];

        $insert = OrderListOfProducts::create($productDetail);
        if ($insert) {
            return $insert;
        } else {
            return 0;
        }
    }


    //insert any kind of order's product detail whether it coming from shopify or local db every order should have this detail
    public function updateProductDetail($order_id_local_db, $data)
    {
        Log::info(array("product -> update" => $data['product_id']));
        $productDetail = [
            "order_id" => $order_id_local_db,
            "shopify_id" => $data["id"],
            "fulfillable_quantity" => $data["fulfillable_quantity"],
            "fulfillment_service" => $data["fulfillment_service"],
            "fulfillment_status" => $data["fulfillment_status"],
            "gift_card" => $data["gift_card"],
            "grams" => $data["grams"],
            "name" => $data["name"],
            "price" => $data["price"],
            "product_exists" => $data["product_exists"],
            "product_id" => $data["product_id"],
            "quantity" => $data["quantity"],
            "requires_shipping" => $data["requires_shipping"],
            "sku" => $data["sku"],
            "taxable" => $data["taxable"],
            "title" => $data["title"],
            "total_discount" => $data["total_discount"],
            "variant_id" => $data["variant_id"],
            "variant_inventory_management" => $data["variant_inventory_management"],
            "variant_title" => $data["variant_title"],
            "vendor" => $data["vendor"],
        ];
        //  Log::info(array("product -> update order_id" => $data['order_id']));
        Log::info($productDetail);
        $find = OrderListOfProducts::where("order_id", $order_id_local_db)->where("product_id", $data['product_id'])->first();
        if ($find <> null) {
            //     Log::info(array("product -> updating product " => $data['order_id']));

            $find->update($productDetail);
        }
        if ($find) {
            return $find;
        } else {
            return 0;
        }
    }
}
