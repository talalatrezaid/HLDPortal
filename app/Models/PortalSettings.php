<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'testing_worldpay_client_id',
        'testing_worldpay_secret_key',
        'live_worldpay_client_id',
        'live_worldpay_secret_key',
        'is_live_worldpay',
        'welcome_charity_email_messsage',
        'assigning_product_email_message',
        'customer_order_email_message',
        'charity_order_email_message',
        'superadmin_email_message',
        'website_notify_email',
    ];
}
