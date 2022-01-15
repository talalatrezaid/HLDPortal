<?php

namespace App\Http\Controllers\shopify;

use App\Http\Controllers\Controller;


use App\Models\MappedCategory;
use Illuminate\Http\Request;

/**
 * Class MappedCategoryController
 * @package App\Http\Controllers\shopify
 */
class MappedCategoryController extends Controller
{

    /**
     * Method triggered from UI ajax call category page
     * Method responsible for record storefront and sellers product categories mapping in DB
     * @param null
     * @return boolean
     */
    public function mapCategories(){

        // Store_front_category_ids, we may expect multiples ids, we're getting it in ajax requested from UI
        if (isset($_POST['storefront_categories'])){

            $store_front_category_ids = $_POST['storefront_categories'];
        }

        // Seller product_category_id, we're getting it in ajax requested from UI
        if (isset($_POST['product_category'])){

            $product_category_id = $_POST['product_category'];
        }

        // Seller store id, we're getting it in ajax requested from UI
        if (isset($_POST['store_id'])){

            $store_id = $_POST['store_id'];
        }

        // Get data by seller product_category_id
        $mapped_categories_data_by_id =  MappedCategory::where('product_category_id', $product_category_id)->first();

        // If $mapped_categories_data variable having data, means user already has existing association so delete existing entries and re-create with updated list
        if ($mapped_categories_data_by_id){

            // Delete all records against requested product_category_id
            $deletedRows = MappedCategory::where('product_category_id', $product_category_id)->delete();

        }


        // Check if we have storefront ids exists in ajax request
        if (isset($store_front_category_ids)){

            // Inset new records
            foreach ($store_front_category_ids as $store_front_category_id){

                $mapped_categories_data = new MappedCategory;
                $mapped_categories_data->store_id                   = $store_id;
                $mapped_categories_data->store_front_category_id    = $store_front_category_id;
                $mapped_categories_data->product_category_id        = $product_category_id;
                $mapped_categories_data->save();
            }

            echo "mapping_saved";
            die();
        }
        // User wants to un-map all categories against specific seller product_category_id
        else{

            // Delete all records against requested product_category_id
            $deletedRows = MappedCategory::where('product_category_id', $product_category_id)->delete();

            echo "mapping_removed";
            die();
        }
    }
}
