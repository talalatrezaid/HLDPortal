@extends('admin.layouts.app')
@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('product_categories') ;?>">Categories Mapping</a></h1>
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
    <section class="content">
        <div class="container-fluid">

            <div class="row" id="overlay" style="display: none;">
                <img width="150" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" alt="Loading" />
            </div>

            @if(count($product_categories)<1)
                <div class="row">
                    <div class="col-sm-6 text-left mb-2">
                        {{--<a href="<?php echo Adminurl('sync_categories') ;?>" class="btn btn-success" id="sync_categories">Sync Categories</a>--}}

                        @if(isset($store_products_count) && $store_products_count <= 0)
                            <button type="button" class="btn btn-success" disabled>Sync Categories</button>
                            <p>Please complete your products syncing process before syncing categories information</p>

                        @else
                            <a href="javascript:void(0)" class="btn btn-success" id="sync_categories">Sync Categories</a>
                        @endif


                    </div>
                </div>
            @endif

            <div class="pl-3" style="width: 90%;">

                    <table class="table" id="store_product_table" >
                        <thead>
                        <tr>
                            <th class="pl-5" width="50%" scope="col">Seller Categories</th>
                            <th width="40%" scope="col">FHG Categories</th>

                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">


                        </tr>
                        </thead>
                        <tbody>

                        @if(count($product_categories)<1)
                            <tr class="trbody pl-5"><td><h4>No categories found</h4></td></tr>
                        @else
                            @foreach($product_categories as $category)
                                {{--@php dd($category->id); @endphp--}}
                                <tr class="trbody" data-row_product_category="{{$category->id}}">
                                    <td class="pl-5">{{$category->name}}</td>
                                    <td>
                                        @if(count($storefront_categories)<1)
                                            <h6>No categories to map</h6>
                                        @else
                                            <select class="js-select2-multi select_category_mapping" id="" multiple="multiple">

                                                @if(count($mapped_categories)>0)

                                                    @foreach($storefront_categories as $storefront_category_index => $storefront_category)

                                                        @foreach($mapped_categories as $mapped_category_index => $mapped_category)

                                                            @if($mapped_category->product_category_id == $category->id && $mapped_category->store_front_category_id == $storefront_category->id )

                                                                <option
                                                                        value={{$storefront_category->id}}
                                                                        data-product_category="{{$category->id}}"
                                                                        data-store_id="{{$category->store_id}}"
                                                                        selected
                                                                >
                                                                    {{$storefront_category->name}}
                                                                </option>

                                                                @break

                                                            @endif

                                                            @if($mapped_category_index == count($mapped_categories) - 1  )

                                                                    <option value={{$storefront_category->id}} data-product_category="{{$category->id}}" data-store_id="{{$category->store_id}}">{{$storefront_category->name}}</option>

                                                            @endif

                                                         @endforeach

                                                    @endforeach

                                                @else

                                                    @foreach($storefront_categories as $storefront_category)
                                                        <option value={{$storefront_category->id}} data-product_category="{{$category->id}}" data-store_id="{{$category->store_id}}">{{$storefront_category->name}}</option>
                                                    @endforeach

                                                @endif

                                            </select>
                                            <a href="javascript:void(0)" class="btn btn-success category_mapping" id="">Update</a>
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                        @endif
                        </tbody>
                    </table>

                    @if(count($product_categories)>0)
                        {{--show pagination links by laravel and bootstrap--}}
                        {{ $product_categories->links() }}
                    @endif
            </div>
        </div>
    </section>

    <script>
        // Start category syncing process
        $(document).on('click', '#sync_categories', function () {

            // show loader on ajax start
            $('#overlay').show();

            $.ajax({
                url: '<?php echo Adminurl('sync_categories');?>',
                type: 'get',
                data: {},
                cache: false,
                success: function(response){

                    // if records successfully inserted in DB
                    if (response == "sync_process_complete"){
                        // do something
                    }
                    // validation current user has no store
                    else if(response == "store_not_exists"){

                        toastr.error('Please add your store credentials to start syncing process.',{timeOut: 5000});

                        // validation current user has store but it is not active
                    }else if (response == "invalid_store") {

                        toastr.error('Your store is not connected either credentials are not valid or not verified yet',{timeOut: 5000});
                    }
                },complete: function(){

                    // stop loader on ajax complete
                    $('#overlay').fadeOut();
                    location.reload();
                }

            });
        });
    </script>

    <script>

        // Start category mapping process
        $(document).on('click', '.category_mapping', function () {


            var selected_storefront_categories = 0;
            var selected_product_category = 0;
            var selected_store_id = 0;

            selected_storefront_categories = $(this).siblings('.select_category_mapping').val();
            selected_store_id = $(this).siblings('.select_category_mapping').find(':selected').data('store_id');
            selected_product_category = $(this).closest('tr').data('row_product_category');



            // prevent user to submit call without selecting any category
            /*if (selected_storefront_categories.length === 0){
                toastr.error('Please select at least one category to map',{timeOut: 5000});
                return;
            }*/

            // show loader on ajax start
            $('#overlay').show();

            $.ajax({
                url: '<?php echo Adminurl('map_categories');?>',
                type: 'post',
                data: {storefront_categories:selected_storefront_categories, product_category:selected_product_category, store_id:selected_store_id, "_token": $('#token').val()},
                cache: false,
                success: function(response){

                    // if records successfully inserted in DB
                    if (response == "mapping_saved"){
                        toastr.success('Mapping has been saved successfully.',{timeOut: 5000});
                    }
                    // validation current user has no store
                    else if(response == "mapping_removed"){
                        toastr.success('Mapping has been removed successfully.',{timeOut: 5000});
                    }
                },complete: function(){

                    // stop loader on ajax complete
                    $('#overlay').fadeOut();
                }

            });


        });
    </script>

    <script>
        $(document).ready(function() {

            $(".js-select2").select2();

            $(".js-select2-multi").select2();

            $(".large").select2({
                dropdownCssClass: "big-drop",
            });

        });
    </script>

    @endsection
