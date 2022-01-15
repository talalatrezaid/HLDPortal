<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFullfilments extends Model
{
    use HasFactory;
    protected $fillable = [
        "order_id",
        "shopify_id",
        "admin_graphql_api_id",
        "created_at",
        "location_id",
        "name",
        "shopify_order_id",
        "service",
        "shipment_status",
        "status",
        "tracking_company",
        "tracking_number",
        "tracking_url"
    ];

    //checking fullfilment data exists or not
    public function check_fullfilment_data($order_id_local_db)
    {
        $qry =  OrderFullfilments::where("order_id", $order_id_local_db)->first();
        if ($qry)
            return $qry;
        else
            return FALSE;
    }

    public function insertFullfilmentgDetail($order_id_local_db, $data)
    {

        $fullfilmentgDetail = [
            "order_id" => $data["order_id"],
            "shopify_id" => $data["shopify_id"],
            "shopify_id" => $data["shopify_id"],
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
            "vendor" => $data["vendor"]
        ];

        $insert = OrderFullfilments::create($fullfilmentgDetail);
        if ($insert) {
            return $insert;
        } else {
            return 0;
        }
    }

    public function updateFullfilmentgDetail($order_id_local_db, $data)
    {

        $fullfilmentgDetail = [
            "order_id" => $data["order_id"],
            "shopify_id" => $data["shopify_id"],
            "shopify_id" => $data["shopify_id"],
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
            "vendor" => $data["vendor"]
        ];

        $find = OrderFullfilments::where("order_id", $order_id_local_db)->first();

        if ($find <> null) {
            $find->update($fullfilmentgDetail);
        } else {
            $find::create($fullfilmentgDetail);
        }
        if ($find) {
            return $find;
        } else {
            return 0;
        }
    }
}
