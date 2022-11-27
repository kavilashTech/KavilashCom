<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = ['product_id', 
                            'variant', 
                            'sku', 
                            'expiry_month',
                            'expiry_year',
                            'batch_number',
                            'hsn_code', 
                            'price', 
                            'qty', 
                            'image'
                        ];
    //
    public function product(){
    	return $this->belongsTo(Product::class);
    }

    public function wholesalePrices() {
        return $this->hasMany(WholesalePrice::class);
    }
}
