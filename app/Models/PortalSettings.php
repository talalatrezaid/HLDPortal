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
        'facebook_pixel_script',
        'google_analytics_id',
        'google_tag_manger_id',
        "shipping_charges",
        "welcome_email_message_user",
        "welcome_email_message_charity",
        "hermes_access_token_sandbox",
        "hermes_api_url_sandbox",
        "hermes_access_token_live",
        "hermes_api_url_live",
        "is_hermes_live",
    ];
}
