<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharityDonation extends Model
{
    use HasFactory;
    protected $table = "charity_donation";
    protected $fillable = [
        "order_id",
        "project_id",
        "project_name",
        "amount",
        "charity_id",
        "refund",
        "refund_amount",
        "refund_reason",
        "status"
    ];

    public $timestamps = true;

    //insert any kind of order's Donation detail whether it coming from shopify or local db every order should have this detail
    public function insertDonationDetail($order_id_local_db, $data)
    {
        $DonationDetails = [
            "order_id" => $order_id_local_db,
            "project_id" => $data['project_id'],
            "project_name" => $data['project_name'],
            "amount" => $data['amount'],
            "charity_id" => $data['charity_id'],
            "refund" => $data['refund'],
            "refund_amount" => $data['refund_amount'],
            "refund_reason" => $data['refund_reason'],
            "status"
        ];

        $insert = CharityDonation::create($DonationDetails);
        if ($insert) {
            return $insert;
        } else {
            return 0;
        }
    }
}
