<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;
    protected $table = "customer";
    protected $fillable = [
        "email",
        "accepts_marketing",
        "first_name",
        "last_name",
        "orders_count",
        "state",
        "total_spent",
        "last_order_id",
        "note",
        "verified_email",
        "multipass_identifier",
        "tax_exempt",
        "phone",
        "tags",
        "last_order_name",
        "currency",
        "accepts_marketing_updated_at",
        "marketing_opt_in_level",
        "sms_marketing_consent",
        "admin_graphql_api_id",
        "shopify_customer_id",
        "default_address_address1",
        "default_address_address2",
        "default_address_city",
        "default_address_province",
        "default_address_country",
        "default_address_zip",
        "default_address_phone",
        "default_address_province_code",
        "default_address_country_code",
        "default_address_country_name"
    ];

    public function check_Customer_data($email)
    {
        $qry =  Customers::where("email", $email)->first();
        if ($qry)
            return $qry;
        else
            return FALSE;
    }

    //insert any kind of order's billing detail whether it coming from shopify or local db every order should have this detail
    public function insertCustomerDetail($order_id_local_db, $data)
    {
        $customerDetails = [
            "email" => $data["email"],
            "accepts_marketing" => $data["accepts_marketing"],
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "orders_count" => $data["orders_count"],
            "state" => $data["state"],
            "total_spent" => $data["total_spent"],
            "last_order_id" => $data["last_order_id"],
            "note" => $data["note"],
            "verified_email" => $data["verified_email"],
            "multipass_identifier" => $data["multipass_identifier"],
            "tax_exempt" => $data["tax_exempt"],
            "phone" => $data["phone"],
            "tags" => $data["tags"],
            "last_order_name" => $data["last_order_name"],
            "currency" => $data["currency"],
            "accepts_marketing_updated_at" => $data["accepts_marketing_updated_at"],
            "marketing_opt_in_level" => $data["marketing_opt_in_level"],
            "sms_marketing_consent" => $data["sms_marketing_consent"],
            "admin_graphql_api_id" => $data["admin_graphql_api_id"],
            "shopify_customer_id" => $data["shopify_customer_id"],
            "default_address_address1" => $data["default_address_address1"],
            "default_address_address2" => $data["default_address_address2"],
            "default_address_city" => $data["default_address_city"],
            "default_address_province" => $data["default_address_province"],
            "default_address_country" => $data["default_address_country"],
            "default_address_zip" => $data["default_address_zip"],
            "default_address_phone" => $data["default_address_phone"],
            "default_address_province_code" => $data["default_address_province_code"],
            "default_address_country_code" => $data["default_address_country_code"],
            "default_address_country_name" => $data["default_address_country_name"]
        ];

        $insert = Customers::create($customerDetails);
        $order = Orders::where("id", $order_id_local_db)->first();
        $order->customer_id = $insert->id;
        $order->update();
        if ($insert) {
            return $insert;
        } else {
            return 0;
        }
    }
}
