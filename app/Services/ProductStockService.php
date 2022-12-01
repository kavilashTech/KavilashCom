<?php

namespace App\Services;

use App\Models\ProductStock;
use App\Utility\ProductUtility;
use Combinations;

class ProductStockService
{
    public function store(array $data, $product)
    {
        $collection = collect($data);
        
        $options = ProductUtility::get_attribute_options($collection);
        
        //Generates the combinations of customer choice options
        $combinations = Combinations::makeCombinations($options);
        
        $variant = '';
        if (count($combinations[0]) > 0) {
            $product->variant_product = 1;
            $product->save();
            foreach ($combinations as $key => $combination) {
                $str = ProductUtility::get_combination_string($combination, $collection);
                
                $product_stock = new ProductStock();
                $product_stock->product_id = $product->id;
                $product_stock->variant = $str;
                $product_stock->price = request()['price_' . str_replace('.', '_', $str)];
                $product_stock->sku = request()['sku_' . str_replace('.', '_', $str)];
                $product_stock->hsn_code = $data['hsn_code'] ?? "";
                $product_stock->width  =  request()['width_' . str_replace('.','_', $str)];
                $product_stock->breadth  =  request()['breadth_' . str_replace('.','_', $str)];
                $product_stock->height  =  request()['height_' . str_replace('.','_', $str)];
                $product_stock->usd_price  =  request()['usd_price_' . str_replace('.','_', $str)];
                $product_stock->usd_points  =  request()['usd_points_' . str_replace('.','_', $str)];
                $product_stock->dispatch_days  =  request()['dispatch_days_' . str_replace('.','_', $str)];
                $product_stock->expiry_month = request()['expiry_month_' . str_replace('.', '_', $str)];
                $product_stock->expiry_year = request()['expiry_year_' . str_replace('.', '_', $str)];
                $product_stock->batch_number = request()['batch_number_' . str_replace('.', '_', $str)];
                $product_stock->qty = request()['qty_' . str_replace('.', '_', $str)];
                $product_stock->image = request()['img_' . str_replace('.', '_', $str)];
                $product_stock->save();
            }
        } else {
            unset($collection['colors_active'], $collection['colors'], $collection['choice_no']);
            $qty = $collection['current_stock'];
            $price = $collection['unit_price'];
            unset($collection['current_stock']);

            $data = $collection->merge(compact('variant', 'qty', 'price'))->toArray();
            
            ProductStock::create($data);
        }
    }

    public function product_duplicate_store($product_stocks , $product_new)
    {
        foreach ($product_stocks as $key => $stock) {
            $product_stock              = new ProductStock;
            $product_stock->product_id  = $product_new->id;
            $product_stock->variant     = $stock->variant;
            $product_stock->price       = $stock->price;
            $product_stock->sku         = $stock->sku;
            $product_stock->hsn_code    = $stock->hsn_code;
            $product_stock->qty         = $stock->qty;
            $product_stock->save();
        }
    }
}
