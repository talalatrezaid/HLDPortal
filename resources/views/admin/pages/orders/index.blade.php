@extends('admin.layouts.app')
@section('content')
<style>
    .rate {
        float: left;
        height: 46px;
        padding: 0 10px;
    }

    #feedback_message {
        resize: none;
    }

    .rate:not(:checked)>input {
        position: absolute;
        opacity: 0;
        /* top: -9999px; */
    }

    .rate:not(:checked)>label {
        float: right;
        width: 1em;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        font-size: 30px;
        color: #ccc;
    }

    .rate:not(:checked)>label:before {
        content: '★ ';
    }

    .rate>input:checked~label {
        color: #ffc700;
    }

    .rate:not(:checked)>label:hover,
    .rate:not(:checked)>label:hover~label {
        color: #deb217;
    }

    .rate>input:checked+label:hover,
    .rate>input:checked+label:hover~label,
    .rate>input:checked~label:hover,
    .rate>input:checked~label:hover~label,
    .rate>label:hover~input:checked~label {
        color: #c59b08;
    }

    .form-control {
        width: auto;
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-9">
                <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('dashboard'); ?>">Orders</a> <span>@if(count($orders)>1) Count: {{$orders->total()}} @endif</span></h1>
            </div>

        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">

        <div class="row" id="overlay" style="display: none;">
            <img width="150" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" alt="Loading" />
        </div>
        <div class="row" id="overlay2" style="display: none;">
            <img width="150" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" alt="Loading" />
        </div>





        {{--@php dd($products); @endphp--}}

        <div class="row card px-3">
            <form method="POST" action="<?php echo Adminurl("orders") ?>" id="form_filter">
                @csrf
                <div>
                    <h2>Filters</h2s>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="input-group date" id="datetimepicker1">
                            <input type="date" name="from_date" class="form-control">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <div class="input-group date" id="datetimepicker1">
                            <input type="date" name="to_date" class="form-control">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Export
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" onclick="export_with_filters()">Export Charity Products</a>
                                <a class="dropdown-item" href="#" onclick="export_additional_products_with_filters()">Export Additional Products</a>
                                <a class="dropdown-item" href="#" onclick="exportordersForHermes()">Export File For Hermes</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row p-1">
                    <div class="col-md-2 col-lg-2">
                        <label>Search</label><br />
                        <input value="<?php echo $search_keyword; ?>" placeholder="Search" class="form-control" style="width: 100%;" id="search" name="search" />
                    </div>
                    <div class="col-md-3">
                        <label>Select Charity</label><br />
                        <select class="form-control" name="charity" style="width: 70%;">
                            <option value="-1">Select Chairty</option>
                            <option <?php if ($charity == 0) echo "selected"; ?> value="0">Holy Land Dates Shopify</option>
                            <?php foreach ($charities as $row) {
                            ?>
                                <option <?php if ($charity == $row->id) echo "selected"; ?> value="<?php echo $row->id ?>"><?php echo $row->charity_name ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="col-md-2">
                            <label>Payments</label><br />
                            <select class="form-control abc" name="payment">
                                <option value="" <?php if ($payment == "") echo "selected"; ?>>Please Select</option>
                                <option value="paid" <?php if ($payment == "paid") echo "selected"; ?>>Paid</option>
                                <option class="unpaid" value="unpaid" <?php if ($payment == "unpaid") echo "selected"; ?>>Unpaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="col-md-2"><label>Status</label><br />
                            <select class="form-control abc" name="status">
                                <option value="" <?php if ($status == "") echo "selected"; ?>>Please Select</option>
                                <option <?php if ($status == "unfulfilled") echo "selected"; ?> value="unfulfilled">Pending</option>
                                <option <?php if ($status == "fulfilled") echo "selected"; ?> value="fulfilled">Complete</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3"> <label>Action</label><br />
                        <input type="submit" name="submit" value="Apply" class="btn btn-sm btn-success mr-2" />
                        <input type="reset" value="Reset" class="btn btn-sm btn-danger mr-2" />


                    </div>

                </div>

                <div class="row">
                    <div class="col-md-2 mb-2 mt-4"><label>Per Page</label><br />
                        <select class="form-control" name="per_page" style="width: 70%;">
                            <option <?php if ($per_page == 10) echo "selected"; ?> value="10">10</option>
                            <option <?php if ($per_page == 20) echo "selected"; ?> value="20">20</option>
                            <option <?php if ($per_page == 50) echo "selected"; ?> value="50">50</option>
                            <option <?php if ($per_page == 100) echo "selected"; ?> value="100">100</option>
                            <option <?php if ($per_page == 200) echo "selected"; ?> value="200">200</option>
                            <option <?php if ($per_page == 500) echo "selected"; ?> value="500">500</option>
                            <option <?php if ($per_page == 1000) echo "selected"; ?> value="1000">1000</option>
                        </select>
                    </div>
                </div>
            </form>
            <div class="row">
                <table class="table " id="store_product_table">

                    <thead>
                        <tr>
                            <th>#</th>
                            {{-- <input  type="checkbox" value="-1" id="select_all_checkboxes" />--}}
                            {{-- </th>--}}
                            <th scope="col">Order No</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Payment</th>
                            <th scope="col">Charity</th>
                            <th scope="col">Status</th>
                            <th scope="col">Rating</th>
                            <th scope="col">Action</th>
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

                        </tr>
                    </thead>
                    <tbody>

                        @if(count($orders)<1) <tr>
                            <td>
                                <h4>No Orders found</h4>
                            </td>
                            </tr>
                            @else
                            @foreach($orders as $product)


                            <tr class="trbody">
                                <td>{{ $no++ }}</td>
                                {{-- <td scope="row">--}}
                                {{-- <input  type="checkbox" value="{{$product->id}}" data-product_name="{{$product->title}}" id="flexCheckDefault" />--}}
                                {{-- </td>--}}
                                <td class="product_name_data">{{$product->id}}
                                    <br />
                                    <?php echo date('d/m/Y h:m:a', strtotime($product->created_at)) ?>
                                </td>
                                <td>{{$product->name ?? ''}}</td>
                                <td>£{{$product->total_price ?? '0'}}
                                    <br />
                                    <?php
                                    if (strtolower($product->financial_status) == "paid") {
                                    ?>
                                        <span class="badge badge-sm badge-success">Paid</span>
                                    <?php
                                    } else if (strtolower($product->financial_status) == "unpaid") {
                                    ?>
                                        <span class="badge badge-sm badge-danger">Unpaid</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($product->charity_id > 0) {
                                    ?>
                                        <a href="https://<?php echo $product->charities->user_name; ?>.datesfrompalestine.com/" target="_blank"><?php echo $product->charities->charity_name ?></a>
                                    <?php
                                    } else {
                                        echo "Holy Land Dates Shopify";
                                    } ?>
                                </td>
                                <td>
                                    <?php
                                    if (strtolower($product->fulfillment_status) == "fulfilled" || strtolower($product->fulfillment_status) == "completed") {
                                    ?>
                                        <span class="badge badge-sm badge-success">Completed</span>
                                    <?php
                                    } else if (strtolower($product->fulfillment_status) == "unfulfilled") {
                                    ?>
                                        <span class="badge badge-sm badge-danger">Pending</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <div class="rate">
                                        <input type="radio" id="star5" name="rate" value="5" <?php if ($product->rate == 5) echo "checked"; ?> disabled />
                                        <label for="star5" title="text">5 stars</label>
                                        <input type="radio" id="star4" name="rate" <?php if ($product->rate == 4) echo "checked"; ?> value="4" disabled />
                                        <label for="star4" title="text">4 stars</label>
                                        <input type="radio" id="star3" name="rate" <?php if ($product->rate == 3) echo "checked"; ?> value="3" disabled />
                                        <label for="star3" title="text">3 stars</label>
                                        <input type="radio" id="star2" name="rate" <?php if ($product->rate == 2) echo "checked"; ?> value="2" disabled />
                                        <label for="star2" title="text">2 stars</label>
                                        <input type="radio" id="star1" name="rate" <?php if ($product->rate == 1) echo "checked"; ?> value="1" disabled />
                                        <label for="star1" title="text">1 star</label>
                                    </div>
                                </td>
                                <td><a href="<?php echo Adminurl('orderdetail/'); ?>{{$product->id}}">View Detail</a> <br />
                                    Hermes Export: <?php if ($product->is_exported_for_hermes == 0) {
                                                        echo "No";
                                                    } else {
                                                        echo "YES";
                                                    } ?>
                                    {{-- - <a href="javascript:void(0)" class="upload_product_to_storefront" id="{{$product->id}}" data-upload_product_id="{{$product->id}}">--}}
                                    {{-- @if($product->is_uploaded_to_storefront == "1")--}}
                                    {{-- Uploaded--}}
                                    {{-- @else--}}
                                    {{-- Upload--}}
                                    {{-- @endif--}}
                                    {{-- </a>--}}
                                </td>
                            </tr>

                            @endforeach


                            @endif

                    </tbody>
                </table>

                @if(count($orders)>1)
                {{--show pagination links by laravel and bootstrap--}}
                {{ $orders->links() }}
                @endif
            </div>
        </div>
</section>
<script>
    <?php if (session('not_allowed')) { ?>
        toastr.error('Sorry! You don&#39;t have permission to edit this product.', {
            timeOut: 5000
        });
    <?php } ?>
</script>

<!-- <script>
    // Start product syncing process
    $(document).on('click', '#sync_products', function() {

        // show loader on ajax start
        $('#overlay').show();

        $.ajax({
            url: '<?php echo Adminurl('sync_products'); ?>',
            type: 'get',
            data: {},
            cache: false,
            success: function(response) {

                // if records successfully inserted in DB
                if (response == "sync_process_complete") {

                    // previously we are fetching products from DB and appending in listing using ajax
                    // now we are refreshing page because of pagination
                    // code saved in backup folder with name getProductAndAppendInListingAjax.php

                }
                // validation current user has no store
                else if (response == "store_not_exists") {

                    toastr.error('Please add your store credentials to start syncing process.', {
                        timeOut: 5000
                    });

                    // validation current user has store but it is not active
                } else if (response == "invalid_store") {

                    toastr.error('Your store is not connected either credentials are not valid or not verified yet', {
                        timeOut: 5000
                    });
                }
            },
            complete: function() {

                // stop loader on ajax complete
                $('#overlay').fadeOut();
                //    location.reload();
            }

        });


    });
</script> -->

<script>
    $(document).on('click', '.upload_product_to_storefront', function() {

        // show loader on ajax start
        $('#overlay').show();

        // get product_id from data attribute
        product_id = $(this).data('upload_product_id');

        $.ajax({
            url: '<?php echo Adminurl('upload_product_to_storefront'); ?>',
            type: 'post',
            data: {
                product_id: product_id,
                "_token": $('#token').val()
            },
            cache: false,
            success: function(response) {

                // if records successfully inserted in DB
                if (response == "upload_successful") {

                    // successful
                    toastr.success('Requested product has been successfully uploaded to storefront .', {
                        timeOut: 5000
                    });
                    $('#' + product_id).text("Uploaded");
                    //$(this).text("Uploaded");

                }
                // validation store credentials not found in env file
                else if (response == "invalid_store_credentials") {

                    toastr.error('Please verify your store credentials in environment file to start process.', {
                        timeOut: 5000
                    });

                    // validation invalid store credentials
                } else if (response == "authentication_failed") {

                    toastr.error('The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later.', {
                        timeOut: 5000
                    });

                } else if (response == "upload_failed") {

                    toastr.error('Something went wrong with uploading product to storefront. Please wait and try again later or contact administrator to report this issue.', {
                        timeOut: 5000
                    });
                }
            },
            complete: function() {

                // stop loader on ajax complete
                $('#overlay').fadeOut();
                //location.reload();
            }

        });

    });
</script>

<script>
    $(document).ready(function() {

        // on click top check select all check boxes
        $("#select_all_checkboxes").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        // on click bulk upload get the product_ids of selected products and create an array of these
        var products_data_arr = [];
        $("#magento_bulk_upload").click(function(event) {
            event.preventDefault();
            $("#store_product_table input:checkbox:checked").map(function() {

                // avoid add data from first checkbox which is for selecting all checkboxes and not contain any actual product value
                if ($(this).data("product_name")) {

                    products_data_arr.push([$(this).val(), $(this).data("product_name")]);
                }

            }).get();

            if (products_data_arr == "") {

                alert("Please select product(s) to upload");
            }

            if (products_data_arr) {

                // loop through product ids to upload each product in magento
                $.each(products_data_arr, function(key, value) {

                    // show loader on ajax start
                    $('#overlay').show();

                    $.ajax({
                        url: '<?php echo Adminurl('upload_product_to_storefront'); ?>',
                        type: 'post',
                        data: {
                            product_id: value[0],
                            "_token": $('#token').val()
                        },
                        cache: false,
                        success: function(response) {

                            // if records successfully inserted in DB
                            if (response == "upload_successful") {

                                // successful
                                toastr.success('Requested product ' + value[1] + ' has been successfully uploaded to storefront .', {
                                    timeOut: 5000
                                });
                                $('#' + value[0]).text("Uploaded");

                            }
                            // validation store credentials not found in env file
                            else if (response == "invalid_store_credentials") {

                                toastr.error('Please verify your store credentials in environment file to start process.', {
                                    timeOut: 5000
                                });

                                // validation invalid store credentials
                            } else if (response == "authentication_failed") {

                                toastr.error('The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later.', {
                                    timeOut: 5000
                                });

                            } else if (response == "upload_failed") {

                                toastr.error('Something went wrong with uploading product to storefront. Please wait and try again later or contact administrator to report this issue.', {
                                    timeOut: 5000
                                });
                            }
                        },
                        complete: function() {

                            // stop loader on ajax complete, as we are calling multiple ajax call so hide loader after last call
                            if (products_data_arr.length - 1 == key) {

                                $('#overlay').fadeOut();

                                // reset all check boxes
                                $("#store_product_table input:checkbox").prop("checked", false);

                                // clear stored selected products array
                                products_data_arr = [];
                                //products_data_arr.splice(0, products_data_arr.length);
                                //location.reload();
                            }
                        }

                    });
                });
            } else {

            }
        });
    });

    function export_with_filters() {
        let form_filter = $("#form_filter").serialize();
        window.open("<?php echo Adminurl("exportcharityorders?") ?>" + form_filter);
    }

    function export_additional_products_with_filters() {
        let form_filter = $("#form_filter").serialize();
        window.open("<?php echo Adminurl("exportordersAdditionalProducts?") ?>" + form_filter);
    }

    function exportordersForHermes() {
        let form_filter = $("#form_filter").serialize();
        window.open("<?php echo Adminurl("exportordersForHermes?") ?>" + form_filter);
    }
</script>
@endsection