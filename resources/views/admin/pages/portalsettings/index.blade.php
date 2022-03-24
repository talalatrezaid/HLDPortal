@extends('admin.layouts.app')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('dashboard'); ?>">Dashboard</a></h1> -->
                <h1 class="m-0 text-dark">Settings</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo Adminurl('settings'); ?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active"><a href="<?php echo Adminurl('settings'); ?>">Settings</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">

        @if(session('user_permission_error'))
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('user_permission_error') }}
        </div>
        @endif


        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif

        <div class="card">
            <div class="col-md-12 pt-2 pl-2">
                <h4>Stripe Credentials</h4>
            </div>
            <hr color="#ccc" style="border-color:#d5d0d0" />
            <div class="col-md-12">
                <form method="post" action="<?php echo route('settings.update', $settings->id); ?>">
                    @csrf
                    @method("PUT")
                    <div class="form-group">
                        <label>Stripe Public KEY(Test)</label>
                        <div class="input-group" id="show_hide_password">
                            <input class="form-control" type="password" name="testing_worldpay_client_id" value="<?php echo $settings->testing_worldpay_client_id; ?>">
                            <div class="bg-gray px-2 pt-1 input-group-addon">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Stripe Secret KEY(Test)</label>
                        <div class="input-group" id="show_hide_password1">
                            <input class="form-control" type="password" name="testing_worldpay_secret_key" value="<?php echo $settings->testing_worldpay_secret_key; ?>">
                            <div class="bg-gray px-2 pt-1 input-group-addon">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Stripe Public KEY(Live)</label>
                        <div class="input-group" id="show_hide_password2">
                            <input class="form-control" type="password" name="live_worldpay_client_id" value="<?php echo $settings->live_worldpay_client_id; ?>">
                            <div class="bg-gray px-2 pt-1 input-group-addon">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Stripe Secret KEY(Live)</label>
                        <div class="input-group" id="show_hide_password3">
                            <input class="form-control" type="password" name="live_worldpay_secret_key" value="<?php echo $settings->live_worldpay_secret_key; ?>">
                            <div class="bg-gray px-2 pt-1 input-group-addon">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="is_live_worldpay" <?php if ($settings->is_live_worldpay == 1) {
                                                                                                    echo "checked='checked'";
                                                                                                } ?> id="worldpay_check_box">
                        <label class="form-check-label" for="worldpay_check_box">Use Stripe Live (if checked mean yes)</label>
                    </div>



                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Welcome Email Message</label>
                        <textarea class="form-control" name="welcome_charity_email_messsage" id="welcome_charity_email_messsage" rows="3"><?php echo $settings->welcome_charity_email_messsage; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Assigning Product To Charity Email Message</label>
                        <textarea class="form-control" name="assigning_product_email_message" id="exampleFormControlTextarea2" rows="3"><?php echo $settings->assigning_product_email_message; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Customer Order Email Message</label>
                        <textarea class="form-control" name="customer_order_email_message" id="exampleFormControlTextarea1" rows="3"><?php echo $settings->customer_order_email_message; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Charity Order Email Message</label>
                        <textarea class="form-control" name="charity_order_email_message" id="exampleFormControlTextarea1" rows="3"><?php echo $settings->charity_order_email_message; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Superadmin Order Email Message</label>
                        <textarea class="form-control" name="superadmin_email_message" id="superadmin_email_message" rows="3"><?php echo $settings->superadmin_email_message; ?></textarea>
                    </div>


                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Email Address</label>
                        <input class="form-control" type="text" name="website_notify_email" value="<?php echo $settings->website_notify_email; ?>">
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Shipping Charges</label>
                        <input class="form-control" type="number" pattern="^\d*(\.\d{0,2})?$" name="shipping_charges" Step=".01" value="<?php echo $settings->shipping_charges; ?>">
                    </div>


                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Google Analytics Id(UA-XXXXX-Y)</label>
                        <input class="form-control" type="text" name="google_analytics_id" value="<?php echo $settings->google_analytics_id; ?>">
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Google Tag Manager Id (GTM-XXXXXX)</label>
                        <input class="form-control" type="text" name="google_tag_manger_id" value="<?php echo $settings->google_tag_manger_id; ?>">
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Facebook Pixel Code</label>
                        <textarea class="form-control" type="text" name="facebook_pixel_script"><?php echo $settings->facebook_pixel_script; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Welcome Email Message New User Register (For customer email)</label>
                        <textarea class="form-control" type="text" name="welcome_email_message_user"><?php echo $settings->welcome_email_message_user; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Welcome Email Message New User Register (For charity email)</label>
                        <textarea class="form-control" type="text" name="welcome_email_message_charity"><?php echo $settings->welcome_email_message_charity; ?></textarea>
                    </div>


                    <div class="form-group">
                        <label>Hermes Sandbox Api Url (Sandbox)</label>
                        <div class="input-group" id="">
                            <input class="form-control" type="text" name="hermes_api_url_sandbox" value="<?php echo $settings->hermes_api_url_sandbox; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hermes Sandbox Access Token (Sandbox)</label>
                        <div class="input-group" id="show_hide_hermes_access_token_sandbox">
                            <input class="form-control" type="password" name="hermes_access_token_sandbox" value="<?php echo $settings->hermes_access_token_sandbox; ?>">
                            <div class="bg-gray px-2 pt-1 input-group-addon">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hermes Live Api Url (Live)</label>
                        <div class="input-group" id="">
                            <input class="form-control" type="text" name="hermes_api_url_live" value="<?php echo $settings->hermes_api_url_live; ?>" />
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Hermes Live Access Token (Live)</label>
                        <div class="input-group" id="show_hide_hermes_access_token_live">
                            <input class="form-control" type="password" name="hermes_access_token_live" value="<?php echo $settings->hermes_access_token_live; ?>">
                            <div class="bg-gray px-2 pt-1 input-group-addon">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="is_hermes_live" <?php if ($settings->is_hermes_live == 1) {
                                                                                                    echo "checked='checked'";
                                                                                                } ?> id="is_hermes_live">
                        <label class="form-check-label" for="is_hermes_live">Use Hermes Api Live (if checked mean yes)</label>
                    </div>

                    <div class="form-group">
                        <label>Islamic Relief CRM CSV Profit</label>
                        <div class="input-group">
                            <select class="form-control" name="is_include_addional_products">
                                <option value="1" <?php if ($settings->is_include_addional_products == 1) {
                                                        echo "selected";
                                                    } ?>>YES - Include Additional Products Price</option>
                                <option value="0" <?php if ($settings->is_include_addional_products == 0) {
                                                        echo "selected";
                                                    } ?>>NO - Not Include Additional Products Price</option>
                            </select>

                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>

</section>
</div>
<script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("fa-eye-slash");
                $('#show_hide_password i').removeClass("fa-eye");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("fa-eye-slash");
                $('#show_hide_password i').addClass("fa-eye");
            }
        });
        $("#show_hide_hermes_access_token_sandbox a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_hermes_access_token_sandbox input').attr("type") == "text") {
                $('#show_hide_hermes_access_token_sandbox input').attr('type', 'password');
                $('#show_hide_hermes_access_token_sandbox i').addClass("fa-eye-slash");
                $('#show_hide_hermes_access_token_sandbox i').removeClass("fa-eye");
            } else if ($('#show_hide_hermes_access_token_sandbox input').attr("type") == "password") {
                $('#show_hide_hermes_access_token_sandbox input').attr('type', 'text');
                $('#show_hide_hermes_access_token_sandbox i').removeClass("fa-eye-slash");
                $('#show_hide_hermes_access_token_sandbox i').addClass("fa-eye");
            }
        });
        $("#show_hide_hermes_access_token_live a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_hermes_access_token_live input').attr("type") == "text") {
                $('#show_hide_hermes_access_token_live input').attr('type', 'password');
                $('#show_hide_hermes_access_token_live i').addClass("fa-eye-slash");
                $('#show_hide_hermes_access_token_live i').removeClass("fa-eye");
            } else if ($('#show_hide_hermes_access_token_live input').attr("type") == "password") {
                $('#show_hide_hermes_access_token_live input').attr('type', 'text');
                $('#show_hide_hermes_access_token_live i').removeClass("fa-eye-slash");
                $('#show_hide_hermes_access_token_live i').addClass("fa-eye");
            }
        });
        $("#show_hide_password1 a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password1 input').attr("type") == "text") {
                $('#show_hide_password1 input').attr('type', 'password');
                $('#show_hide_password1 i').addClass("fa-eye-slash");
                $('#show_hide_password1 i').removeClass("fa-eye");
            } else if ($('#show_hide_password1 input').attr("type") == "password") {
                $('#show_hide_password1 input').attr('type', 'text');
                $('#show_hide_password1 i').removeClass("fa-eye-slash");
                $('#show_hide_password1 i').addClass("fa-eye");
            }
        });


        $("#show_hide_password2 a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password2 input').attr("type") == "text") {
                $('#show_hide_password2 input').attr('type', 'password');
                $('#show_hide_password2 i').addClass("fa-eye-slash");
                $('#show_hide_password2 i').removeClass("fa-eye");
            } else if ($('#show_hide_password2 input').attr("type") == "password") {
                $('#show_hide_password2 input').attr('type', 'text');
                $('#show_hide_password2 i').removeClass("fa-eye-slash");
                $('#show_hide_password2 i').addClass("fa-eye");
            }
        });


        $("#show_hide_password3 a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password3 input').attr("type") == "text") {
                $('#show_hide_password3 input').attr('type', 'password');
                $('#show_hide_password3 i').addClass("fa-eye-slash");
                $('#show_hide_password3 i').removeClass("fa-eye");
            } else if ($('#show_hide_password3 input').attr("type") == "password") {
                $('#show_hide_password3 input').attr('type', 'text');
                $('#show_hide_password3 i').removeClass("fa-eye-slash");
                $('#show_hide_password3 i').addClass("fa-eye");
            }
        });

    });
    <?php if (session('not_allowed')) { ?>
        toastr.error('Sorry! You don&#39;t have permission to access this module.', {
            timeOut: 5000
        });
    <?php } ?>
</script>
@endsection