@extends('admin.layouts.app')
@section('content')
<style>
    #panel,
    #flip {
        padding: 5px;
        text-align: center;
        background-color: #e5eecc;
        border: solid 1px #c3c3c3;
    }

    #panel {
        padding: 50px;
        display: none;
    }

    body {
        margin-top: 20px;
        color: #484b51;
    }

    .text-secondary-d1 {
        color: #728299 !important;
    }

    .page-header {
        margin: 0 0 1rem;
        padding-bottom: 1rem;
        padding-top: 0.5rem;
        border-bottom: 1px dotted #e2e2e2;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-pack: justify;
        justify-content: space-between;
        -ms-flex-align: center;
        align-items: center;
    }

    .page-title {
        padding: 0;
        margin: 0;
        font-size: 1.75rem;
        font-weight: 300;
    }

    .brc-default-l1 {
        border-color: #dce9f0 !important;
    }

    .ml-n1,
    .mx-n1 {
        margin-left: -0.25rem !important;
    }

    .mr-n1,
    .mx-n1 {
        margin-right: -0.25rem !important;
    }

    .mb-4,
    .my-4 {
        margin-bottom: 1.5rem !important;
    }

    hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .text-grey-m2 {
        color: #888a8d !important;
    }

    .text-success-m2 {
        color: #86bd68 !important;
    }

    .font-bolder,
    .text-600 {
        font-weight: 600 !important;
    }

    .text-110 {
        font-size: 110% !important;
    }

    .text-blue {
        color: #478fcc !important;
    }

    .pb-25,
    .py-25 {
        padding-bottom: 0.75rem !important;
    }

    .pt-25,
    .py-25 {
        padding-top: 0.75rem !important;
    }

    .bgc-default-tp1 {
        background-color: rgba(121, 169, 197, 0.92) !important;
    }

    .bgc-default-l4,
    .bgc-h-default-l4:hover {
        background-color: #f3f8fa !important;
    }

    .page-header .page-tools {
        -ms-flex-item-align: end;
        align-self: flex-end;
    }

    .btn-light {
        color: #757984;
        background-color: #f5f6f9;
        border-color: #dddfe4;
    }

    .w-2 {
        width: 1rem;
    }

    .text-120 {
        font-size: 120% !important;
    }

    .text-primary-m1 {
        color: #4087d4 !important;
    }

    .text-danger-m1 {
        color: #dd4949 !important;
    }

    .text-blue-m2 {
        color: #68a3d5 !important;
    }

    .text-150 {
        font-size: 150% !important;
    }

    .text-60 {
        font-size: 60% !important;
    }

    .text-grey-m1 {
        color: #7b7d81 !important;
    }

    .align-bottom {
        vertical-align: bottom !important;
    }

    /* admin */
    .date_veiw {
        font-size: 14px;
    }

    .first_left_view,
    .first_right_view,
    .scnd_left_paid_view {
        border-radius: 10px;
        box-shadow: 0px 1px 10px -5px rgb(0 0 0 / 75%);
    }

    .first_right_contact_customer_view,
    .first_right_info_customer_view,
    .first_right_billing_address_view {
        border-top: 1px solid lightgray;
    }

    .first_right_customer_view {
        border-radius: 10px;
        box-shadow: 0px 1px 10px -5px rgb(0 0 0 / 75%);
    }

    .button_fulfill {
        justify-content: right;
        padding: 14px 0 0 0;
        border-top: 1px solid lightgray;
    }

    .button_fulfill button {
        margin: 0 10px;
        border: none;
        border-radius: 5px;
        padding: 10px 25px;
        background-color: rgb(3, 66, 3);
    }

    .button_fulfill button:hover {
        background-color: rgb(3, 66, 3);
    }

    .last_det {
        justify-content: right;
    }

    .b-top {
        padding: 12px 0 0 0;
        border-top: 1px solid lightgray;
    }

    p {}

    span {
        font-weight: 600;
    }

    .notify {
        background: rgb(199, 199, 199);
        padding: 0 5px;
        border-radius: 50%;
        color: #000;
        position: absolute;
        left: 41px;
        top: -6px;
        font-weight: bold;
        font-size: 10px;
    }

    .top_identity {
        font-weight: 700;
        font-size: 20px;
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-9 col-lg-9">
                <h1 class="m-0 text-dark"><a href="<?php

                                                    use Illuminate\Support\Facades\Log;

                                                    echo Adminurl('orders'); ?>">Orders</a></h1>
            </div>
            <div class="col-lg-3">
                <!-- <a href="{{route('charities.create')}}" class="btn btn-primary float-right">Add New Charity</a>
             -->
            </div>

        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
            @endif
            <div class="col-lg-12 p-3" style="background-color: #F6f6f6;">
                <div class="col-lg-11  mx-auto" id="overlay">
                    <div class="row">
                        <div class="col-md-8 col-10 mx-auto">
                            <div class="card first_left_view">
                                <div class="row mt-3">
                                    <div class="col-12 col-sm-12 text-grey-d2 text-95 mt-2 mt-lg-0 p-3">
                                        <span><img class="img-fluid" src="./mosq_logo.png" alt="" style="width: 40px" /></span><span class="top_identity">
                                            Products</span>
                                        <?php
                                        try {
                                            foreach ($order->list_items as $row) {
                                                $title = $row->title;
                                                $amount = $row->price;
                                                $quantity = $row->quantity;
                                                $product_id = $row->product_id;
                                                $variantId = $row->variant_id;

                                        ?>

                                                <div class="row  my-4">
                                                    <div class="col-12 text-left">
                                                        <span><img class="img-fluid" src="<?php echo write_product_image($product_id, $variantId); ?>" alt="" style="width: 40px; border-radius: 10px" /><span class="notify"><?php echo $quantity; ?></span></span>
                                                        <span class="ml-4"><?php echo $title; ?> (£<?php echo $amount * $quantity ?>)</span>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        } catch (Exception $x) {
                                            Log::info(array("error in order detail", "detail.blade.php line number 288"));
                                        } ?>
                                    </div>


                                </div>

                                <div class="row no-gutters button_fulfill pb-3">
                                    <button type="submit" class="btn-primary text-right">
                                        Fulfill item
                                    </button>
                                </div>
                            </div>
                            <div class="card scnd_left_paid_view p-3 my-4">

                                <div class="row mt-3">
                                    <div class="col-12 col-sm-12 text-grey-d2 text-95 mt-2 mt-lg-0 p-3">
                                        <span><img class="img-fluid" src="./mosq_logo.png" alt="" style="width: 40px" /></span><span class="top_identity">
                                            Donations</span>
                                        <?php
                                        //calling helper function here
                                        write_order_donations($order->id);
                                        ?>
                                    </div>


                                </div>
                                <div class="row mt-3 b-top">

                                </div>
                            </div>
                            <div class="card scnd_left_paid_view my-4">
                                <div class="row mt-3">
                                    <div class="col-12 col-sm-3 text-grey-d2 text-95 mt-2 mt-lg-0 p-3">
                                        <span></span><span class="top_identity">Paid</span>

                                        <div class="row my-4">
                                            <div class="col-7 text-left">
                                                <p>Subtotal</p>
                                                <p>Shipping</p>
                                                <p>tax</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-5 text-grey-d2 text-95 mt-2 mt-lg-0">
                                        <div class="row mt-5 pt-2">

                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-4 text-grey-d2 text-95 mt-2 mt-lg-0">
                                        <div class="row mt-5 pt-2 last_det">
                                            <div class="col-7 text-left">
                                                <p>£<?php echo $order->current_subtotal_price; ?></p>
                                                <p><?php echo $order->total_weight; ?></p>
                                                <p><?php echo $order->total_tax; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3 b-top no-gutters p-2">
                                    <div class="col-12 col-sm-6 text-grey-d2 text-95 mt-2 mt-lg-0">
                                        <div class="row">
                                            <div class="col-7 text-left">
                                                <p>Total</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-5 text-grey-d2 text-95 mt-2 mt-lg-0">
                                        <div class="row last_det">
                                            <div class="col-7 text-right ">
                                                <span class="text-120 text-success-d3 opacity-2">£<?php echo $order->total_price; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="col-md-4 col-10 mx-auto">
                            <div class="card first_right_customer_view p-3">

                                Order Status: <span><?php echo $order->fulfillment_status;
                                                    ?></b> </span> </div>
                            <div class="card first_right_customer_view p-3">
                                <div class="row mt-3">
                                    <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                        <span>Customer</span>
                                    </div>
                                </div>

                                <div class="col-12text-grey-d2 text-95 mt-2 mt-lg-0">
                                    <div class="row my-4">
                                        <?php
                                        //calling helper function here
                                        write_customer_information($order->id);
                                        ?>
                                    </div>
                                </div>

                                <div class="first_right_contact_customer_view no-gutters">
                                    <div class="row mt-3">
                                        <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                            <span>Charity Information</span>
                                        </div>
                                    </div>

                                    <div class="col-12text-grey-d2 text-95 mt-2 mt-lg-0">
                                        <div class="row my-4">
                                            <?php
                                            //calling helper function here
                                            write_charity_detail($order->charities);
                                            ?>
                                        </div>
                                    </div>
                                </div>


                                <div class="first_right_contact_customer_view no-gutters">
                                    <div class="row mt-3">
                                        <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                            <span>Shipping Information</span>
                                        </div>
                                    </div>

                                    <div class="col-12text-grey-d2 text-95 py-3">
                                        <?php
                                        //calling helper function here
                                        write_customer_shipping_address($order->id);
                                        ?>
                                    </div>
                                </div>

                                <div class="first_right_contact_customer_view no-gutters">
                                    <div class="row mt-3">
                                        <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                            <span>Billing Information</span>
                                        </div>
                                    </div>

                                    <div class="col-12text-grey-d2 text-95 py-3">
                                        <?php
                                        //calling helper function here
                                        write_customer_billing_address($order->id);
                                        ?>
                                    </div>
                                </div>


                                <div class="first_right_billing_address_view p-3">
                                    <div class="row mt-3">
                                        <div class="col-12text-grey-d2 text-95 mt-2 mt-lg-0">
                                            <p class="mb-2">Ahmed rehan</p>
                                            <p class="mb-2">Karachi</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection