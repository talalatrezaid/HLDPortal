<?php

namespace App\Models\Orders;

use App\Models\charity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable =  [
        "order_id",
        "charity_id",
        "store_id",
        "is_shopify_order",
        "cancel_reason",
        "cancelled_at",
        "cart_token",
        "checkout_id",
        "checkout_token",
        "client_details",
        "closed_at",
        "confirmed",
        "contact_email",
        "created_at",
        "currency",
        "current_subtotal_price",
        "current_total_discounts",
        "current_total_price",
        "current_total_tax",
        "email",
        "estimated_taxes",
        "financial_status",
        "fulfillment_status",
        "gateway",
        "landing_site",
        "landing_site_ref",
        "location_id",
        "name",
        "note",
        "number",
        "order_number",
        "order_status_url",
        "payment_gateway_names",
        "phone",
        "presentment_currency",
        "processed_at",
        "processing_method",
        "reference",
        "referring_site",
        "source_identifier",
        "source_name",
        "source_url",
        "subtotal_price",
        "tags",
        "taxes_included",
        "test",
        "token",
        "total_discounts",
        "total_line_items_price",
        "total_outstanding",
        "total_price",
        "total_price_usd",
        "total_tax",
        "total_tip_received",
        "total_weight",
        "updated_at",
        "user_id",
        "is_charity_order"
    ];
    public function get_all_orders($order_by, $sort, $search_keyword, $charity, $payment, $status, $date_from, $date_to, $per_page)
    {
        $order_query =  Orders::with(
            'billing',
            'shipping',
            "payment",
            "list_items",
            "fullfilments",
            "charities",
            "customer",
            "donations"
        );
        if (strlen($search_keyword) > 0) {
            $order_query->where("name", "like", $search_keyword . "%");
        }
        if (strlen($payment) > 0) {
            $order_query->where("financial_status", "=", ucfirst($payment));
        }
        if (strlen($status) > 0) {

            $order_query->where("fulfillment_status",  ucfirst($status));
        }
        if ($charity > -1) {
            $order_query->where("charity_id", $charity);
        }

        if (strlen($date_from)) {
            $order_query->where('created_at', '>=', $date_from);
        }


        if (strlen($date_to)) {
            $order_query->where('created_at', '<=', $date_to);
        }
        $orders =  $order_query->orderby("created_at", "desc")->paginate($per_page);
        return $orders;
    }

    public function check_order_exists($shopify_order_id)
    {
        //in order table order_id mean shopify order id and id mean local order id
        $qry = Orders::where(
            "order_id",
            $shopify_order_id
        )->first();
        if ($qry)
            return $qry;
        else
            return FALSE;
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }


    public function billing()
    {
        return $this->hasMany(OrderBillingAddress::class, 'order_id', 'id');
    }

    public function shipping()
    {
        return $this->hasMany(OrderShippingAddress::class, 'order_id', 'id');
    }


    public function payment()
    {
        return $this->hasMany(OrderPaymentDetails::class, 'order_id', 'id');
    }

    public function list_items()
    {
        return $this->hasMany(OrderListOfProducts::class, 'order_id', 'id');
    }

    public function fullfilments()
    {
        return $this->hasMany(OrderFullfilments::class, 'order_id', 'id');
    }

    public function refunds()
    {
        return $this->hasMany(OrderRefunds::class, 'order_id', 'id');
    }

    public function donations()
    {
        return $this->hasMany(CharityDonation::class, 'order_id', 'id');
    }

    public function charities()
    {
        return $this->belongsTo(charity::class, 'charity_id', 'id');
    }
}
