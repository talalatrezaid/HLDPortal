<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        "order_id",
        "credit_card_bin",
        "avs_result_code",
        "cvv_result_code",
        "credit_card_number",
        "credit_card_company",
        "credit_card_name",
        "credit_card_wallet",
        "credit_card_expiration_month",
        "credit_card_expiration_year"
    ];

    public function check_payment_data($order_id_local_db)
    {
        $qry =  OrderPaymentDetails::where("order_id", $order_id_local_db)->first();

        if ($qry)
            return $qry;
        else
            return FALSE;
    }

    //insert any kind of order's payment detail whether it coming from shopify or local db every order should have this detail
    public function insertPaymentgDetail($order_id_local_db, $data)
    {
        $billingDetails = [
            "order_id" => $order_id_local_db,
            "credit_card_bin" => $data["credit_card_bin"],
            "avs_result_code" => $data["avs_result_code"],
            "cvv_result_code" => $data["cvv_result_code"],
            "credit_card_number" => $data["credit_card_number"],
            "credit_card_company" => $data["credit_card_company"],
            "credit_card_name" => $data["credit_card_name"],
            "credit_card_wallet" => $data["credit_card_wallet"],
            "credit_card_expiration_month" => $data["credit_card_expiration_month"],
            "credit_card_expiration_year" => $data["credit_card_expiration_year"],
        ];

        $insert = OrderPaymentDetails::create($billingDetails);
        if ($insert) {
            return $insert;
        } else {
            return 0;
        }
    }

    //insert any kind of order's payment detail whether it coming from shopify or local db every order should have this detail
    public function updatePaymentgDetail($order_id_local_db, $data)
    {
        $billingDetails = [
            "order_id" => $order_id_local_db,
            "credit_card_bin" => $data["credit_card_bin"],
            "avs_result_code" => $data["avs_result_code"],
            "cvv_result_code" => $data["cvv_result_code"],
            "credit_card_number" => $data["credit_card_number"],
            "credit_card_company" => $data["credit_card_company"],
            "credit_card_name" => $data["credit_card_name"],
            "credit_card_wallet" => $data["credit_card_wallet"],
            "credit_card_expiration_month" => $data["credit_card_expiration_month"],
            "credit_card_expiration_year" => $data["credit_card_expiration_year"],
        ];

        $find = OrderPaymentDetails::where("order_id", $order_id_local_db)->first();
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
