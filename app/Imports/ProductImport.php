<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Facades\DB;
HeadingRowFormatter::default('none');

class ProductImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
         // $current_sku= $row["Variant SKU"];
        // $product_exists=DB::table('products')->where('product_sku', $current_sku)->exists();
        // echo $product_exists;

        return new Product([
            //
            // "vendor_id"=> $row["vendor"],
            "connector_name"=> $row["Variant Inventory Tracker"],
            "product_name"=> $row["Title"],
            "product_sku"=>  $row["Variant SKU"],
            // "product_id"=> $row["123"],
            "product_price"=> $row["Variant Price"],
            "product_weight"=> $row["Variant Grams"],
            "product_category"=> $row["Type"],
            "product_inventory"=> $row["Variant Inventory Qty"],
            "handle"=> $row["Handle"],
            "weight_unit"=> $row["Variant Weight Unit"],
          
        ]);
    }
}
