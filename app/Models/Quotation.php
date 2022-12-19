<?php

namespace App\Models;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $guarded = [];
    protected $fillable = ['address_id','price','tax','tax1','tax1_amount','tax2','tax2_amount','shipping_cost','discount','product_referral_code','coupon_code','coupon_applied','quantity','user_id','temp_user_id','quotation_id','owner_id','product_id','variation'];
}
