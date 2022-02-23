<?php

namespace App\Console\Commands;

use App\Models\Orders\Orders;
use Illuminate\Console\Command;
use App\Mail\OrdersFeedbackReminderEmail;
use Exception;
use Illuminate\Support\Facades\Mail;

class OrderFeedbackEmailCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orderfeedbackemail:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");

        $date = date_create(now());
        date_sub($date, date_interval_create_from_date_string("4 days"));
        $date = date_format($date, "Y-m-d");
        $aorders = Orders::select("id", "email", "charity_id")->with("charities")->where("rate", 0)->where("feedback_email_status", 0)->where("created_at", "<", $date)->orderby("created_at", "desc")->get();


        foreach ($aorders as $order) {
            //get order send email and update feedback_email_status = 1
            \Log::info($order->charity_id);
            $email = $order->email;

            $data['order_no'] = $order->id;
            if ($order->charity_id > 0) {
                $charity = $order->charities;
                $orders = Orders::findorfail($order->id);
                $charity_user_name = $charity->user_name;
                $data['charity_name'] = $charity->charity_name;
                //charity url feedback/orderid encrypted/ email encrypted/
                $orders->feedback_email_status = 1;
                $hash = $order->order_verification_link;
                if ($hash == null ||  $hash == "") {
                    $hash = password_hash($order->id . "Rezaid" . $order->email, PASSWORD_DEFAULT);
                    $orders->order_verification_link = $hash;
                }
                $orders->save();

                $data['order_feedback_link'] = "https://" . $charity_user_name . ".datesfrompalestine.com/orderfeedback/" . base64_encode($hash);
                $emaildata = new  OrdersFeedbackReminderEmail($data);
                try {
                    \Log::info("do email");
                    Mail::to($email)->send($emaildata);
                } catch (Exception $x) {
                    \Log::info($x);
                }
            }
        }
    }
}
