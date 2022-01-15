<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBillingAddress extends Model
{
    use HasFactory;
    protected $table = "order_billing_address";
    protected $fillable = ["order_id", "first_name", "address1", "phone", "city", "zip", "province", "country", "last_name", "address2", "company", "latitude", "longitude", "name", "country_code", "province_code"];

    public function check_bill_data($order_id_local_db)
    {
        $qry =  OrderBillingAddress::where("order_id", $order_id_local_db)->first();

        if ($qry)
            return $qry;
        else
            return FALSE;
    }
    //insert any kind of order's billing detail whether it coming from shopify or local db every order should have this detail
    public function insertBillingDetail($order_id_local_db, $data)
    {
        $billingDetails = [
            "order_id" => $order_id_local_db,
            "first_name" => $data['first_name'],
            "address1" => $data['address1'],
            "phone" => $data['phone'],
            "city" => $data['city'],
            "zip" => $data['zip'],
            "province" => $data['province'],
            "country" => $data['country'],
            "last_name" => $data['last_name'],
            "address2" => $data['address2'],
            "company" => $data['company'],
            "latitude" => $data['latitude'],
            "longitude" => $data['longitude'],
            "name" => $data['name'],
            "country_code" => $data['country_code'],
            "province_code" => $data['province_code']
        ];

        $insert = OrderBillingAddress::create($billingDetails);
        if ($insert) {
            return $insert;
        } else {
            return 0;
        }
    }

    //insert any kind of order's billing detail whether it coming from shopify or local db every order should have this detail
    public function updateBillingDetail($order_id_local_db, $data)
    {
        $billingDetails = [
            "order_id" => $order_id_local_db,
            "first_name" => $data['first_name'],
            "address1" => $data['address1'],
            "phone" => $data['phone'],
            "city" => $data['city'],
            "zip" => $data['zip'],
            "province" => $data['province'],
            "country" => $data['country'],
            "last_name" => $data['last_name'],
            "address2" => $data['address2'],
            "company" => $data['company'],
            "latitude" => $data['latitude'],
            "longitude" => $data['longitude'],
            "name" => $data['name'],
            "country_code" => $data['country_code'],
            "province_code" => $data['province_code']
        ];

        $find = OrderBillingAddress::where("order_id", $order_id_local_db)->first();
        if ($find <> null) {
            $find->update($billingDetails);
        }
        if ($find) {
            return $find;
        } else {
            return 0;
        }
    }
}
