@extends('admin.layouts.app')
@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-9">
                <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('dashboard'); ?>">Products</a> <span>@if(count($products)>1) Count: {{$products->total()}} @endif</span></h1>
            </div>
            <div class="col-sm-3">
                {{-- //bulk upload  button comment code not use --}}
                {{-- <button class="btn btn-info" id="magento_bulk_upload">Bulk Upload</button>--}}
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo Adminurl('dashboard'); ?>">Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?php echo Adminurl('dashboard'); ?>">Dashboard</a></li>
                </ol>
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

        @if(count($products)<1) <div class="row">
            <div class="col-sm-6 text-left mb-2">
                {{--<a href="<?php echo Adminurl('sync_products'); ?>" class="btn btn-success" id="sync_products">Sync Products</a>--}}

                {{-- @if(isset($storefront_categories_count) && $storefront_categories_count <= 0)--}}
                {{-- <button type="button" class="btn btn-success" disabled>Sync Products</button>--}}
                {{-- <p>Contact administrator to active syncing process</p>--}}

                {{-- @else--}}
                <a href="javascript:void(0)" class="btn btn-success" id="sync_products">Sync Products</a>
                {{-- @endif--}}
            </div>
    </div>
    @endif

    @if (session('sync_process_complete'))
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ session('sync_process_complete') }}
    </div>
    @elseif (session('store_not_exists'))
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ session('edit_invalid_store') }}
    </div>
    @endif

    {{--@php dd($products); @endphp--}}

    <div class="row">

        <table class="table" id="store_product_table">
            <thead>
                <tr>
                    {{-- <th scope="col">--}}
                    {{-- <input  type="checkbox" value="-1" id="select_all_checkboxes" />--}}
                    {{-- </th>--}}
                    <th scope="col">Image</th>
                    <th scope="col">Name</th>
                    <th scope="col">SKU</th>
                    <th scope="col">Price</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

                </tr>
            </thead>
            <tbody>

                @if(count($products)<1) <tr class="trbody">
                    <td>
                        <h4>No products found</h4>
                    </td>
                    </tr>
                    @else
                    @foreach($products as $product)


                    <tr class="trbody">
                        {{-- <td scope="row">--}}
                        {{-- <input  type="checkbox" value="{{$product->id}}" data-product_name="{{$product->title}}" id="flexCheckDefault" />--}}
                        {{-- </td>--}}
                        <td scope="row"><img width="50" src="{{$product->productImages[0]->source ?? 'https://cdn.shopify.com/s/files/1/0533/2089/files/placeholder-images-image_large.png'}}" alt=""></td>
                        <td class="product_name_data">{{$product->title}}</td>
                        <td>{{$product->productVariants[0]->sku ?? ''}}</td>
                        <td>{{$product->productVariants[0]->price ?? ''}}</td>
                        <td>active</td>
                        <td><a href="<?php echo Adminurl('products/edit/'); ?>{{$product->id}}">View Detail</a>
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

        @if(count($products)>1)
        {{--show pagination links by laravel and bootstrap--}}
        {{ $products->links() }}
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

<script>
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
                location.reload();
            }

        });


    });
</script>

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
</script>
@endsection