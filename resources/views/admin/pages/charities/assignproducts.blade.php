@extends('admin.layouts.app')
@section('content')

<input type="hidden" value="<?php echo $charity->id ?>" name="hidden_charity_id" id="hidden_charity_id" />

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-9 col-lg-9">
                <span class="m-0 text-dark"><a href="<?php echo Adminurl('charities'); ?>">Charities</a>/<a href="<?php echo Adminurl('productToCharity/' . $charity_id); ?>">Add Products To Charities</a></span>
            </div>


        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
        @endif
        <div class="card col-lg-12 p-3">
            <div class="row">

                <div class="col-md-6">
                    <b>Charity Name:</b> <?php echo $charity->charity_name; ?>
                </div>
                <div class="col-md-6">
                    <b style="float: right;">Total Products: <b id="total_products"><?php echo count($charity->assignedPRoducts); ?></b></b>
                </div>
            </div>

        </div>
        <div class="form_top card">
            <h4 class="text-center pt-4 add_to_product" style="font-weight: bold;"> Add Products to <?php echo $charity->charity_name; ?></h4>

            <form class="container-fluid py-3">
                <div class="row">
                    <div class="col-md-6 col-10 mx-auto">
                        <div class="alert alert-warning">
                            Product stock quantity is updated from shopify store,
                            <br />
                            #1) 1st time synced <br />
                            #2) Order on shopify store it's updated here <br />
                            #3) Manully updated on shopify store it's updated here <br />

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-10 mx-auto">

                        <div class="form-group">
                            <label for="exampleInputEmail1">Select Product</label>
                            <select class="js-data-example-ajax form-control" style="width:74% !important"></select>
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputPassword1">Selected Product</label>
                        </div>
                        <div class="row" id="selected_product" data-id='0'>
                            <div class="col-md-3 col-10 mx-auto">
                                <img class="img-fluid" id="selected_product_image" src="https://cdn.shopify.com/s/files/1/0555/4038/3941/products/alaqsa01.png?v=1617817494" style="width: 10vh;" />
                            </div>
                            <div class="col-md-7 col-10 mx-auto">
                                <p class="premium-text" id="selected_product_name" style="font-weight: bold; margin: 0; position: absolute;  top: 38%; -ms-transform: translateY(-50%); transform: translateY(-50%);">-</span></p>
                            </div>
                            <div class="col-md-2 col-10 mx-auto">
                                <div class="price_estimate" id="selected_product_quantity" style="font-weight: bold; margin: 0; position: absolute;  top: 38%; -ms-transform: translateY(-50%); transform: translateY(-50%);">
                                    -
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="exampleInputPassword1">Add Quantity</label>
                            <input type="number" min="1" class="form-control" id="qty" name="qty" value="1" placeholder="123">
                        </div>
                        <div class="alert alert-danger d-none" id="error">
                        </div>
                        <button type="button" onclick="verify_product_quantity()" class="btn btn-primary">Assign To Charity</button>
                        <div class="loading d-none"><i class="fa fa-spinner fa-spin"></i> please wait...</div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card col-lg-12 p-3">
            <div class="col-lg-12" id="overlay">
                <table id="example" class="display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Quanitity</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>

    </div>
</section>
<script>
    <?php if (session('not_allowed')) { ?>
        toastr.error('Sorry! You don&#39;t have permission to edit this product.', {
            timeOut: 5000
        });
    <?php } ?>
    var table;
    $(document).ready(function() {
        table = $('#example').DataTable({
            responsive: true, // Move this outside of the ajax option

            "ajax": '/charities/<?php echo $charity_id; ?>/assigned_products',
            'columns': [{
                "data": "id",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span>' + row.id + '</span>';
                }
            }, {
                "data": "products",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span id="' + row.products.title + '">' +
                        row.products.title + '</span>';
                }
            }, {
                "data": "qty",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span id="slug_' + row.id + '">' + row.qty + '</span>';
                }
            }, {
                "data": "updated_at",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span id="email' + row.id + '">' + row.updated_at + '</span>';
                }
            }, {
                "data": "action",
                "render": function(data, type, row) {
                    console.log(row);
                    let last_login = "-";
                    if (row.last_login == null || row.last_login === "null") {} else {
                        last_login = row.last_login;
                    }
                    return data;
                }
            }]
        });
    });

    function verify_product_quantity() {
        //checking selected product quantity must be lower then shopify store quantity
        var qty = $("#qty").val();
        if (qty > 0) {} else {
            alert("Please provide a quantity number greater than 0");
            return false;

        }

        $(".loading").removeClass("d-none");

        //get selected product values 
        var selected_product = ($('.js-data-example-ajax').select2('data')[0]);
        const product_id = selected_product.product_id;
        let product_shopify_quantity = 0;
        if (selected_product?.variant.length > 0)
            product_shopify_quantity = parseInt(selected_product?.variant[0]?.quantity);

        //checking selected quantity must be below then particular quantity
        if (qty > product_shopify_quantity) {
            $("#error").html("please select value below then '" + product_shopify_quantity + "'");
            $("#error").removeClass("d-none");
            return false;
        } else {
            $("#error").addClass("d-none");
        }

        //there i have to send these things
        // #1 product_id local db
        // #2 product_variant_id local_db
        // #3 product_shopify_id from shopify
        // #4 product_variant_shopify_id from shopify
        // #5 qty
        // #6 charity_id
        // #7 product_name
        // #8 total_quantity
        const charity_id = $("#hidden_charity_id").val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.post("<?php echo Adminurl("thisproductassigntocharity") ?>", {
                "charity_id": charity_id,
                "local_product_id": selected_product.id,
                "local_product_variant_id": selected_product?.variant[0].id,
                "shopify_product_id": selected_product.shopify_product_id,
                "shopify_product_variant_id": selected_product?.variant[0].variantId,
                "quantity": qty,
                "total_quantity": product_shopify_quantity,
                "product_name": selected_product?.text
            },
            function(data, status) {
                $(".loading").addClass("d-none");
                $("#qty").val("");
                if (data.success == 1) {
                    alert(data.message);
                    table.ajax.reload();
                    $(".js-data-example-ajax").val(null).trigger("change");
                    $(".js-data-example-ajax").select2("destroy");

                    initSelect2();

                } else {
                    alert(data.message);
                    return false;
                    window.location.reload();
                }
            });
    }

    function setCurrency(item) {

        if (!item.id) {

            return item.text;
        }
        var $currency = $('<div class="" data-text="' + item.id + "-" +
            item.product_variants[0].quantity + '">' + item.text + '</div>');
        return $currency;
    };
    $(document).ready(function() {
        //using as it is with this name in function but it's work is to return product data


        $('.js-data-example-ajax').on("change", function(e) {
            var selected_product = ($('.js-data-example-ajax').select2('data')[0]);
            console.log("selected_product", selected_product);

            console.log("selected_product", selected_product?.image[0].source);

            if (selected_product?.image.length > 0)
                $("#selected_product_image").attr("src", selected_product?.image[0]?.source);
            $("#selected_product_name").html(selected_product?.text);
            if (selected_product?.variant.length > 0)
                $("#selected_product_quantity").html(selected_product?.variant[0]?.quantity);


        });

        initSelect2();


    });

    function initSelect2() {
        $(".js-data-example-ajax").select2({
            width: 'element',
            ajax: {
                url: '<?php echo Adminurl('getProuctsForCharity'); ?>',
                dataType: 'json',
                data: (params) => {
                    return {
                        q: params.term,
                        charity_id: <?php echo $charity_id; ?>
                    }
                },
                templateResult: setCurrency,
                templateSelection: setCurrency,
                processResults: (data, params) => {

                    const results = data.products.map(item => {

                        return {
                            id: item.id,
                            shopify_product_id: item.productId,
                            text: item.title || item.name,
                            image: item.product_images,
                            variant: item.product_variants
                        };
                    });
                    return {
                        results: results,
                    }
                },
            },
        });
    }
</script>



@endsection