@extends('admin.layouts.app')
@section('content')
<style>

    .tbl_img {
        border: 1px solid #ccc;
        padding: 2px;
        border-radius: 2px;
    }
    th.vrt_wdth {
        min-width: 170px !important;
    }
    .custom_tbl_box table th {
        min-width: 100px;
        vertical-align: middle;
    }
    .custom_tbl_box{
        overflow-x: auto;
    }
    .custom_tbl_box tr:last-child td {
        border-width: .5px;
    }
    .btn-primary.btn_price_update{
        border-bottom-left-radius: 0;
        border-top-left-radius: 0;
    }
</style>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('dashboard') ;?>">Edit Product</a></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo Adminurl('dashboard') ;?>">Home</a></li>
                        <li class="breadcrumb-item active"><a href="<?php echo Adminurl('dashboard') ;?>">Dashboard</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

{{--    @php dd($product_categories); @endphp--}}

    <section class="content">
        <div class="container">
            <div class="card">
                <div class="card-body">

                    <div class="row" id="overlay" style="display: none;">
                        <img width="150" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" alt="Loading" />
                    </div>
                    <div class="row" id="overlay2" style="display: none;">
                        <img width="150" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" alt="Loading" />
                    </div>

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

                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-basicinformation-tab" data-toggle="pill" href="#pills-basicinformation" role="tab" aria-controls="pills-basicinformation" aria-selected="true">Basic Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-images-tab" data-toggle="pill" href="#pills-images" role="tab" aria-controls="pills-images" aria-selected="false">Images</a>
                        </li>

                        {{--<li class="nav-item">
                            <a class="nav-link" id="pills-videos-tab" data-toggle="pill" href="#pills-videos" role="tab" aria-controls="pills-videos" aria-selected="false">Videos</a>
                        </li>--}}

                        <li class="nav-item">
                            <a class="nav-link" id="pills-inventory-tab" data-toggle="pill" href="#pills-inventory" role="tab" aria-controls="pills-inventory" aria-selected="false">Inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-productoptions-tab" data-toggle="pill" href="#pills-productoptions" role="tab" aria-controls="pills-productoptions" aria-selected="false">Product Options</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-customfields-tab" data-toggle="pill" href="#pills-customfields" role="tab" aria-controls="pills-customfields" aria-selected="false">Custom Fields</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-extradetails-tab" data-toggle="pill" href="#pills-extradetails" role="tab" aria-controls="pills-extradetails" aria-selected="false">Extra Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-custominformation-tab" data-toggle="pill" href="#pills-custominformation" role="tab" aria-controls="pills-custominformation" aria-selected="false">Custom Information</a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- Product basic information tab content start --}}
                        <div class="tab-pane fade show active" id="pills-basicinformation" role="tabpanel" aria-labelledby="pills-basicinformation-tab">

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="product_name">Name</label>
                                    <input type="text" name="product_name" class="form-control" id="product_name" value="{{ $product[0]['title'] ?? '' }}" readonly>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="product_sku">Product Code/SKU</label>
                                    <input type="text" name="product_sku" class="form-control" id="product_sku" value="{{$product[0]['product_variants'][0]['sku'] ?? '' }}" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="product_price">Price(£)</label>
                                    <input type="text" name="product_price" class="form-control" id="product_price" value="{{$product[0]['product_variants'][0]['price'] ?? '' }}" readonly>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="product_type">Product Type</label>
                                    <input type="text" name="product_type" class="form-control" id="product_type" value="{{$product[0]['type'] ?? '' }}" readonly>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-3 form-group">
                                    <label for="product_price">Weight(KGS)</label>
                                    <input type="text" name="product_weight" class="form-control" id="product_weight" value="{{$product[0]['product_variants'][0]['weight'] ?? '' }}" readonly>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label for="product_width">Width(cm)</label>
                                    <input type="text" name="product_width" class="form-control" id="product_width" value="23.5" readonly>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label for="product_height">Height(cm)</label>
                                    <input type="text" name="product_height" class="form-control" id="product_height" value="14" readonly>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label for="product_depth">Depth(cm)</label>
                                    <input type="text" name="product_depth" class="form-control" id="product_depth" value="5" readonly>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <h6 class="mb-2"><strong>FHG Categories</strong></h6>
                                    <div class="overflow-auto" style="height: 150px;">
                                        <table class="table table-sm">
                                            @if(empty($storefront_categories))

                                                <tr class="table-active"><td>Storefront category mapping not found</td></tr>

                                            @else
                                                @foreach($storefront_categories as $storefront_category)

                                                    <tr class="table-active"><td>{{$storefront_category['storefront_category_name']}}</td></tr>

                                                @endforeach
                                            @endif
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-6 form-group">
                                    <h6 class="mb-2"><strong>Product Categories</strong></h6>
                                    <div class="overflow-auto" style="height: 150px;">
                                        <table class="table table-sm">
                                            @if(empty($product_categories))

                                                <tr class="table-active"><td>Product category not found</td></tr>

                                            @else
                                                @foreach($product_categories as $product_category)

                                                    <tr class="table-active"><td>{{$product_category['name']}}</td></tr>

                                                @endforeach
                                            @endif
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <h6 class="mb-2"><strong>Shipping</strong></h6>
                                    <div class="custom-control custom-checkbox checkbox-lg">
                                        <input type="checkbox" class="custom-control-input" name="product_shipping" id="product_shipping" value="" {{$product[0]['product_variants'][0]['shipping'] ? 'checked' : '' }} disabled>
                                        <label for="product_shipping" class="custom-control-label">Is shipping</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="mb-2"><strong>Tax</strong></h6>
                                    <div class="custom-control custom-checkbox checkbox-lg">
                                        <input type="checkbox" class="custom-control-input" name="product_taxable" id="product_taxable" value="" {{$product[0]['product_variants'][0]['taxable'] ? 'checked' : '' }} disabled>
                                        <label for="product_taxable" class="custom-control-label">Is taxable</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="product_description">Product Description</label>
                                    <textarea class="form-control"  rows="15" id="product_description" >{{$product[0]['description'] ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <h6 class="mb-2"><strong>Product Status</strong></h6>
                                    <div class="custom-control custom-checkbox checkbox-lg">
                                        <input type="checkbox" class="custom-control-input" name="product_status" id="product_status" value="" {{$product[0]['status'] == 'active' ? 'checked' : '' }} disabled>
                                        <label for="product_shipping" class="custom-control-label">Is active</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">&nbsp;</div>

                        </div>
                        {{-- Product basic information tab content end --}}

                        {{-- Product images tab content start --}}
                        <div class="tab-pane fade" id="pills-images" role="tabpanel" aria-labelledby="pills-images-tab">

                            <div class="container">

                                @if(empty($product[0]['product_images']))

                                    <div class="row"><div class="col-md-6"><strong><h4>No images found</h4></strong></div></div>

                                @else
                                    @php $item_counter = 1; @endphp
                                    <div class="row pb-4">
                                    @foreach($product[0]['product_images'] as $product_image)

                                        <div class="col-md-4">
                                            <div class="thumbnail">
                                                <a href="{{$product_image['source']}}" target="_blank">
                                                    <img src="{{$product_image['source']}}" alt="Lights" class="img-rounded" style="width:100%">
                                                </a>
                                            </div>
                                        </div>

                                        @if ($item_counter % 3 == 0)
                                        </div>
                                        <div class="row pb-4">
                                        @endif

                                        @php $item_counter++; @endphp
                                    @endforeach
                                        </div>
                                @endif

                            </div>

                        </div>
                        {{-- Product images tab content end --}}


                        {{--<div class="tab-pane fade" id="pills-videos" role="tabpanel" aria-labelledby="pills-videos-tab">videos 3</div>--}}

                        {{-- Product inventory tab content start --}}
                        <div class="tab-pane fade" id="pills-inventory" role="tabpanel" aria-labelledby="pills-inventory-tab">

                            @if(empty($product[0]['product_variants']))

                                <div class="row"><div class="col-md-6"><strong><h4>No inventory information found</h4></strong></div></div>

                            @else
                                @php $variant_quantity = 0; @endphp
                                @foreach($product[0]['product_variants'] as $product_variants)

                                    @php $variant_quantity = $variant_quantity+ (int)$product_variants['quantity']; @endphp

                                @endforeach

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="product_inventory">Product Inventory Level</label>
                                        <input type="text" name="product_inventory" class="form-control" id="product_inventory" value="{{$variant_quantity ?? 0 }}" readonly>
                                    </div>
                                </div>

                            @endif

                        </div>
                        {{-- Product inventory tab content end --}}

                        {{-- Product options tab content start --}}
                        <div class="tab-pane fade" id="pills-productoptions" role="tabpanel" aria-labelledby="pills-productoptions-tab">



                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="pills-options-tab" data-toggle="pill" href="#pills-options" role="tab" aria-controls="pills-options" aria-selected="true">Options</a>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="pills-skus-tab" data-toggle="pill" href="#pills-skus" role="tab" aria-controls="pills-skus" aria-selected="false">SKUs</a>
                                    </li>

                                </ul>

                                <div class="tab-content">

                                    <div class="tab-pane fade show active" id="pills-options" role="tabpanel" aria-labelledby="pills-options-tab">

                                        @if(empty($product[0]['product_options']))

                                            <div class="row"><div class="col-md-6"><strong><h6>No product options information found</h6></strong></div></div>

                                        @else
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th scope="col">Option Name</th>
                                                    <th scope="col">Option Type</th>
                                                    <th scope="col">Values</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach($product[0]['product_options'] as $product_option)

                                                    <tr>
                                                        <td>{{$product_option['name'] ?? ''}}</td>
                                                        <td>Radio</td>
                                                        <td>{{$product_option['value'] ?? ''}}</td>
                                                    </tr>

                                                @endforeach

                                                </tbody>
                                            </table>
                                        @endif
                                    </div>

                                    <div class="tab-pane fade custom_tbl_box" id="pills-skus" role="tabpanel" aria-labelledby="pills-skus-tab">

                                        @if(empty($product[0]['product_variants']))

                                            <div class="row"><div class="col-md-6"><strong><h6>No product variation information found</h6></strong></div></div>
                                        @else

                                        <table class="table table-responsive text-center">
                                            <thead>
                                            <tr>
                                                <th>Images</th>
                                                <th  class="vrt_wdth">Variant</th>
                                                <th  class="vrt_wdth">SKU </th>
                                                <th> Default Price</th>
                                                <th>Sale Price </th>
                                                <th>Stock </th>
                                                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($product[0]['product_variants'] as $product_variants_key => $product_variant)

                                                <tr data-row_variant_id="{{$product_variant['variantId']}}" data-row_product_id="{{$product[0]['id']}}" data-row_reference_product_id="{{$product[0]['productId']}}" >
                                                    <td>
                                                        <img class="tbl_img" src=".."/>
                                                    </td>
                                                    <td>{{$product_variant['title'] ?? ''}}</td>
                                                    <td>
                                                        <input type="text" name="variant_sku" class="form-control" value="{{$product_variant['sku'] ?? ''}}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="variant_default_price" class="form-control" value="{{$product_variant['price'] ?? ''}}" />
                                                    </td>
                                                    <td>
                                                        <div class="input-group mb-3">
                                                            @if(!empty($variant_meta_data))
                                                                @php $flag = 0; @endphp
                                                                @foreach($variant_meta_data as $variant_meta_key => $variant_meta)
                                                                    @if($product_variant['variantId'] == $variant_meta['variantId'])

                                                                        <input type="text" name="variant_sale_price" class="form-control sale_price_field" value="{{$variant_meta['sale_price'] ?? ''}}" />
                                                                        @php $flag = 1 @endphp
                                                                        @break

                                                                    @endif
                                                                @endforeach

                                                                @if($flag == 0)
                                                                    <input type="text" name="variant_sale_price" class="form-control sale_price_field" value="{{$product_variant['price'] ?? ''}}" />
                                                                @endif

                                                            @else

                                                                <input type="text" name="variant_sale_price" class="form-control sale_price_field" value="{{$product_variant['price'] ?? ''}}" />

                                                            @endif
                                                            <button class="btn btn-primary btn_price_update sale_price_btn" type="button" >Update</button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="variant_quantity" class="form-control" value="{{$product_variant['quantity'] ?? ''}}" />
                                                    </td>
                                                </tr>

                                            @endforeach

                                            </tbody>
                                        </table>
                                    @endif
                                    </div>

                                </div>



                        </div>
                        {{-- Product options tab content end --}}

                        {{-- Product custom fields tab content start --}}
                        <div class="tab-pane fade" id="pills-customfields" role="tabpanel" aria-labelledby="pills-customfields-tab">

                            @if(empty($product[0]['product_options']))

                                <div class="row"><div class="col-md-6"><strong><h6>No product options information found</h6></strong></div></div>

                            @else
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Option Name</th>
                                        <th scope="col">Option Type</th>
                                        <th scope="col">Values</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($product[0]['product_options'] as $product_option)

                                        <tr>
                                            <td>{{$product_option['name'] ?? ''}}</td>
                                            <td>Radio</td>
                                            <td>{{$product_option['value'] ?? ''}}</td>
                                        </tr>

                                    @endforeach

                                    </tbody>
                                </table>
                            @endif

                        </div>
                        {{-- Product custom fields tab content end --}}

                        {{-- Product extra details fields tab content start --}}
                        <div class="tab-pane fade" id="pills-extradetails" role="tabpanel" aria-labelledby="pills-extradetails-tab">

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="product_name">Brand</label>
                                    <input type="text" name="user_brand_name"  class="form-control" id="product_name" value="{{ $product[0]['user_brand_name'] ?? '' }}" readonly>
                                </div>
                            </div>

                            @if($product[0]['product_extra_detail'] == null)

                                <div class="row"><div class="col-md-6"><strong><h6>No product extra information found</h6></strong></div></div>

                            @else

                                <form method="post" id="product_extra_details_form" >

                                    <input type="hidden" name="product_id" value="{{$product[0]['id'] ?? '' }}">
                                    <div class="row">
                                        <div class="col-md-2 form-group">
                                            <label for="upc">Product UPC</label>
                                            <input type="text" readonly name="upc" class="form-control" id="upc" value="{{$product[0]['product_extra_detail']['upc'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2 form-group">
                                            <label for="bin_picking_number">Bin Picking Number</label>
                                            <input type="text" readonly name="bin_picking_number" class="form-control" id="bin_picking_number" value="{{$product[0]['product_extra_detail']['bin_picking_number'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="warranty">Product Warranty</label>
                                            <textarea name="warranty" readonly class="form-control" id="warranty" rows="2">{{$product[0]['product_extra_detail']['warranty'] ?? '' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="search_keyword">Product Search Keyword</label>
                                            <input type="text"  readonly name="search_keyword" class="form-control" id="search_keyword" value="{{$product[0]['product_extra_detail']['search_keyword'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="availability">Availability</label>
                                            <textarea name="availability" readonly class="form-control" id="availability" rows="2">{{$product[0]['product_extra_detail']['availability'] ?? '' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <div class="custom-control custom-checkbox checkbox-lg">
                                                <input type="checkbox" readonly class="custom-control-input" name="is_visible_on_site" id="is_visible_on_site" value="{{$product[0]['product_extra_detail']['is_visible_on_site'] ? '1' : '0' }}" {{$product[0]['product_extra_detail']['is_visible_on_site'] ? 'checked' : '' }}>
                                                <label for="is_visible_on_site" class="custom-control-label">Product Is Visible On Site</label>
                                            </div>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <div class="custom-control custom-checkbox checkbox-lg">
                                                <input type="checkbox" readonly class="custom-control-input" name="available_on" id="available_on" value="{{$product[0]['product_extra_detail']['available_on'] ? '1' : '0' }}" onchange="valueChanged()" {{$product[0]['product_extra_detail']['available_on'] ? 'checked' : '' }}>
                                                <label for="available_on" class="custom-control-label">Available On</label>
                                            </div>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <div class="custom-control custom-checkbox checkbox-lg">
                                                <input type="checkbox" readonly class="custom-control-input" name="featured" id="featured" value="{{$product[0]['product_extra_detail']['featured'] ? '1' : '0' }}" {{$product[0]['product_extra_detail']['featured'] ? 'checked' : '' }}>
                                                <label for="featured" class="custom-control-label">Is This Featured Product</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="availability_date_div">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="availability_date">Available on date</label>
                                                <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                                    <input type="text" readonly name="availability_date" id="availability_date" class="form-control datetimepicker-input" value="{{$product[0]['product_extra_detail']['availability_date'] ?? '' }}" data-target="#datetimepicker1"/>
                                                    <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="condition">Product Condition</label>
                                            <select name="condition" id="condition" class="form-control" readonly="">
                                                <option value="New" {{ $product[0]['product_extra_detail']['condition'] == 'New' ? 'selected' : '' }}>New</option>
                                                <option value="Used" {{ $product[0]['product_extra_detail']['condition'] == 'Used' ? 'selected' : '' }}>Used</option>
                                                <option value="Refurbished" {{ $product[0]['product_extra_detail']['condition'] == 'Refurbished' ? 'selected' : '' }}>Refurbished</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <div class="custom-control custom-checkbox checkbox-lg">
                                                <input type="checkbox" readonly class="custom-control-input" name="show_condition_on_product" id="show_condition_on_product" value="{{$product[0]['product_extra_detail']['show_condition_on_product'] ? '1' : '0' }}" {{$product[0]['product_extra_detail']['show_condition_on_product'] ? 'checked' : '' }}>
                                                <label for="show_condition_on_product" class="custom-control-label">Show condition on product</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label for="sort_order">Product Sort Order</label>
                                            <input type="text" readonly name="sort_order" class="form-control" id="sort_order" value="{{$product[0]['product_extra_detail']['sort_order'] != "" && $product[0]['product_extra_detail']['sort_order'] > 0 ? $product[0]['product_extra_detail']['sort_order'] : '' }}">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="order_minimum_quantity">Product Order Minimum Quantity</label>
                                            <input type="text" name="order_minimum_quantity" readonly class="form-control" id="order_minimum_quantity" value="{{$product[0]['product_extra_detail']['order_minimum_quantity'] != "" && $product[0]['product_extra_detail']['order_minimum_quantity'] > 0 ? $product[0]['product_extra_detail']['order_minimum_quantity'] : '' }}">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="order_maximum_quantity">Product Order Maximum Quantity</label>
                                            <input type="text" readonly name="order_maximum_quantity" class="form-control" id="order_maximum_quantity" value="{{$product[0]['product_extra_detail']['order_maximum_quantity'] != "" && $product[0]['product_extra_detail']['order_maximum_quantity'] > 0 ? $product[0]['product_extra_detail']['order_maximum_quantity'] : '' }}">
                                        </div>
                                    </div>


{{--                                    <div class="form-group row">--}}
{{--                                        <div class="col-sm-10">--}}
{{--                                            <button type="submit" class="btn btn-primary">Save</button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

                                </form>

                            @endif

                        </div>
                        {{-- Product extra details fields tab content end --}}

                        {{-- Product custom information fields tab content start --}}
                        <div class="tab-pane fade" id="pills-custominformation" role="tabpanel" aria-labelledby="pills-custominformation-tab">

                            {{-- variables used in scripts --}}
                            @php $var_dynamic_field=0; @endphp

                            @if (isset($product[0]['product_custom_information']['product_custom_information_hex_codes']) && !empty($product[0]['product_custom_information']['product_custom_information_hex_codes']))
                                @php $var_dynamic_field=count($product[0]['product_custom_information']['product_custom_information_hex_codes']); @endphp
                            @endif
                            @if(isset($countries_list) && !empty($countries_list))
                                @php $countries_array = $countries_list @endphp
                            @else
                                @php $countries_array = array() @endphp
                            @endif
                            {{-- variables used in scripts --}}

                            @if($product[0]['product_custom_information'] == null)

                                <div class="row"><div class="col-md-6"><strong><h6>No product extra information found</h6></strong></div></div>

                            @else
                                <form method="post" id="product_custom_info_form" >
                                    <input type="hidden" name="product_id" value="{{$product[0]['id'] ?? '' }}">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="country_of_origin">Country of origin</label>
                                            <select name="country_of_origin"  readonly id="country_of_origin" class="form-control">
                                                @if(isset($countries_list) && !empty($countries_list))

                                                    @foreach($countries_list as $country_key => $country)

                                                        @if($product[0]['product_custom_information']['country_of_origin'] == $country['name'])
                                                            <option value="{{$country['name']}}" selected>{{$country['name']}}</option>

                                                        @else
                                                            <option value="{{$country['name']}}">{{$country['name']}}</option>
                                                        @endif

                                                    @endforeach

                                                @else
                                                    <option value="na">NA</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="commodity_description">Commodity Description</label>
                                            <textarea  readonly name="commodity_description" class="form-control" id="commodity_description" rows="2">{{$product[0]['product_custom_information']['commodity_description'] ?? '' }}</textarea>
                                        </div>
                                    </div>

                                    <input type='button' value='Add HS Code' id='add' class="btn btn-dark btn-sm">

                                    <div class="form-group">
                                        <table class="table table-bordered" id="dynamic_field">

                                            @if (isset($product[0]['product_custom_information']['product_custom_information_hex_codes']) && !empty($product[0]['product_custom_information']['product_custom_information_hex_codes']))

                                                @foreach ($product[0]['product_custom_information']['product_custom_information_hex_codes'] as $key => $custom_information_hex_codes)

                                                    <tr id="row{{$key}}">
                                                        <td>
                                                            <label for="destination_country">Destination Country</label>
                                                            <select readonly name="hs_code_info[{{$key }}][destination_country]" id="destination_country" class="form-control" required>
                                                                @if(isset($countries_list) && !empty($countries_list))

                                                                    @foreach($countries_list as $country_key => $country)

                                                                        @if($custom_information_hex_codes['destination_country'] == $country['name'])
                                                                            <option value="{{$country['name']}}" selected>{{$country['name']}}</option>

                                                                        @else
                                                                            <option value="{{$country['name']}}">{{$country['name']}}</option>
                                                                        @endif

                                                                    @endforeach

                                                                @else
                                                                    <option value="na">NA</option>
                                                                @endif
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <label for="hs_code">HS Code</label>
                                                            <input type="text" readonly name="hs_code_info[{{$key }}][hs_codes]" value="{{$custom_information_hex_codes['hs_codes']}}" id="hs_codes" class="form-control " placeholder="Enter HS Code" required="required"/>
                                                        </td>

                                                        <td>
                                                          <button type="button" name="remove" id="{{$key}}" class="btn btn-danger btn_remove">X</button></td>
                                                    </tr>

                                                @endforeach
                                            @endif
                                        </table>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-10">
{{--                                            <button type="submit" class="btn btn-primary">Save</button>--}}
                                        </div>
                                    </div>

                                </form>
                             @endif

                        </div>
                        {{-- Product custom information fields tab content end --}}

                    </div>
                </div>
            </div>
        </div>
    </section>

<script>
    tinymce.init({
        selector : 'textarea#product_description',
      branding: false,
      setup: function (editor) {
        editor.setMode("readonly")
      },

    });
</script>

<script>

    // Start sale price update process
    $(document).on('click', '.sale_price_btn', function () {


        var variant_id = 0;
        var sale_price = 0;
        var reference_product_id = 0;
        var product_id = 0;

        sale_price = $(this).siblings('.sale_price_field').val();
        variant_id = $(this).closest('tr').data('row_variant_id');
        reference_product_id = $(this).closest('tr').data('row_reference_product_id');
        product_id = $(this).closest('tr').data('row_product_id');


        // show loader on ajax start
        $('#overlay').show();

        $.ajax({
            url: '<?php echo Adminurl('update_variant_sale_price');?>',
            type: 'post',
            data: {sale_price:sale_price, variant_id:variant_id, reference_product_id:reference_product_id, product_id:product_id,  "_token": $('#token').val()},
            cache: false,
            success: function(response){

                // if records successfully inserted in DB
                if (response == "price_updated"){
                    toastr.success('Sale price has been updated successfully.',{timeOut: 5000});
                }else{
                    toastr.error('Request not successful please try again.',{timeOut: 5000});
                }
            },complete: function(){

                // stop loader on ajax complete
                $('#overlay').fadeOut();
            }

        });


    });
</script>

<script>

    $(document).ready(function(){

        if($('input[name="available_on"]:checked').length > 0){
            $('#availability_date_div').show();
        }else {
            $('#availability_date_div').hide();
        }

    });

    function valueChanged()
    {
        if($('input[name="available_on"]:checked').length > 0){
            $('#availability_date_div').show();
        }else {
            $('#availability_date_div').hide();
        }
    }
</script>

<script>

    // Submit product extra detail data
    $(document).on('submit', '#product_extra_details_form', function (e) {
        e.preventDefault();

        var formdata = $(this).serialize(); // here $(this) refere to the form its submitting

        // show loader on ajax start
        $('#overlay').show();

        $.ajax({
            url: '<?php echo Adminurl('update_extra_details');?>',
            type: 'post',
            data: {data:formdata,  "_token": $('#token').val()},
            cache: false,
            success: function(response){



                // if records successfully inserted in DB
                if (response == "data_updated"){
                    toastr.success('Product extra details has been updated successfully.',{timeOut: 5000});
                }else{
                    $.each(response.errorMessage, function( index, value ) {
                        toastr.error(value,{timeOut: 5000});
                    });

                }
            },complete: function(){

                // stop loader on ajax complete
                $('#overlay').fadeOut();
            }

        });
    });

</script>

<script>
    $(document).ready(function(){

        var count='<?php echo $var_dynamic_field ?>';
        var countries_option ='';
        var countries= <?php echo json_encode($countries_array); ?>;

        if (!countries){
            countries_option = '<option value="na">NA</option>';
        }else {
            for (var i = 0; i < countries.length; ++i) {
                countries_option += '<option value="'+countries[i].name+'">'+countries[i].name+'</option>';
            }
        }

        $('#add').click(function(){

            // $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="stipulation_name['+i+'][name]" placeholder="Enter stipulation Name" class="form-control name_list" required /></td><td><input type="text" name="stipulation_name['+i+'][code]" placeholder="Enter stipulation Shortcode" class="form-control name_list" required /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
            $('#dynamic_field').append('<tr id="row'+count+'" class="dynamic-added">' +
                '<td>' +
                '<label for="destination_country">Destination Country</label>' +
                '<select readonly name="hs_code_info['+count+'][destination_country]" id="destination_country" class="form-control" required>' +
                countries_option +
                '</select>' +
                '</td>' +
                '<td>' +
                '<label for="hs_codes">HS Code</label>' +
                '<input type="text"  name="hs_code_info['+count+'][hs_codes]" placeholder="Enter HS Code" class="form-control" required />' +
                '</td>' +
                '<td>' +
                '<button type="button" name="remove" id="'+count+'" class="btn btn-danger btn_remove">X</button>' +
                '</td>' +
                '</tr>');
            count++;
        });

        $(document).on('click', '.btn_remove', function(){
            var button_id = $(this).attr("id");
            $('#row'+button_id+'').remove();
        });
    });

    // Submit product custom information data
    $(document).on('submit', '#product_custom_info_form', function (e) {
        e.preventDefault();

        var formdata = $(this).serialize(); // here $(this) refere to the form its submitting

        // show loader on ajax start
        $('#overlay').show();

        $.ajax({
            url: '<?php echo Adminurl('update_custom_info');?>',
            type: 'post',
            data: {data:formdata,  "_token": $('#token').val()},
            cache: false,
            success: function(response){

                // if records successfully inserted in DB
                if (response == "data_updated"){
                    toastr.success('Product custom information has been updated successfully.',{timeOut: 5000});
                }else{
                    $.each(response.errorMessage, function( index, value ) {
                        toastr.error(value,{timeOut: 5000});
                    });

                }
            },complete: function(){

                // stop loader on ajax complete
                $('#overlay').fadeOut();
            }

        });
    });
</script>
@endsection
