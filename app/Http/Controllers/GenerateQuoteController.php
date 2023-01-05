<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Tax;
use App\Mail\QuotationMail;
use App\Models\Currency;
use App\Models\Language;
use PDF;
use Config;
use Auth;
use Session;
use Cookie;
use Mail;
use Response;

class GenerateQuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::find($request->id);
        $carts = array();
        $data = array();
        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $data['user_id'] = $user_id;
            $carts = Quotation::where('user_id', $user_id)->whereNull('quotation_id')->get();
        } else {
            if ($request->session()->get('temp_user_id1')) {
                $temp_user_id = $request->session()->get('temp_user_id1');
            } else {
                $temp_user_id = bin2hex(random_bytes(10));
                $request->session()->put('temp_user_id1', $temp_user_id);
            }
            $data['temp_user_id'] = $temp_user_id;
            $carts = Quotation::where('temp_user_id', $temp_user_id)->whereNull('quotation_id')->get();
        }

        $data['product_id'] = $product->id;
        $data['owner_id'] = $product->user_id;

        $str = '';
        $tax = 0;
        if ($product->auction_product == 0) {
            // if($product->digital != 1 && $request->quantity < $product->min_qty) {
                //     return array(
                    //         'status' => 0,
                    //         'cart_count' => count($carts),
                    //         'modal_view' => view('frontend.partials.minQtyNotSatisfied', [ 'min_qty' => $product->min_qty ])->render(),
                    //         'nav_cart_view' => view('frontend.partials.cart')->render(),
                    //     );
                    // }

                    //check the color enabled or disabled for the product
            if ($request->has('color')) {
                $str = $request['color'];
            }
            if ($product->digital != 1) {
                //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
                foreach (json_decode(Product::find($request->id)->choice_options) as $key => $choice) {
                    if ($str != null) {
                        $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                    } else {
                        $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                    }
                }
            }

            $data['variation'] = $str;

            $product_stock = $product->stocks->where('variant', $str)->first();
            $price = $product_stock->price;

            if ($product->wholesale_product) {
                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
                if ($wholesalePrice) {
                    $price = $wholesalePrice->price;
                }
            }

            $quantity = $product_stock->qty;

            // if($quantity < $request['quantity']){
            //     return array(
            //         'status' => 0,
            //         'cart_count' => count($carts),
            //         'modal_view' => view('frontend.partials.outOfStockCart')->render(),
            //         'nav_cart_view' => view('frontend.partials.cart')->render(),
            //     );
            // }

            //discount calculation
            $discount_applicable = false;

            if ($product->discount_start_date == null) {
                $discount_applicable = true;
            } elseif (
                strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
                ) {
                    $discount_applicable = true;
                }

                if ($discount_applicable) {
                if ($product->discount_type == 'percent') {
                    $price -= ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $price -= $product->discount;
                }
            }

            //calculation of taxes
            $data['tax1'] =  0.00;
            $data['tax1_amount'] =  0.00;
            $data['tax2'] = 0.00;
            $data['tax2_amount'] =  0.00;
            $data['quantity'] = $request['quantity'];
            if ($request['quantity'] == null) {
                $data['quantity'] = 1;
            }
            foreach ($product->taxes as $product_tax) {
                $tax_name = Tax::where('id', $product_tax->tax_id)->first();
                if ($product_tax->tax_type == 'percent') {
                    $tax += ($price * $product_tax->tax) / 100;
                } elseif ($product_tax->tax_type == 'amount') {
                    $tax += $product_tax->tax;
                }
                if (!empty($tax_name) && $tax_name->name == 'CGST') {
                    $data['tax1'] =  $product_tax->tax;
                    $data['tax1_amount'] = (($price * $data['tax1']) / 100);
                }
                if (!empty($tax_name) && $tax_name->name == 'SGST') {
                    $data['tax2'] =  $product_tax->tax;
                    $data['tax2_amount'] = (($price * $data['tax2']) / 100);
                }
            }
            $data['price'] = $price;
            $data['tax'] = $tax;
            //$data['shipping'] = 0;
            $data['shipping_cost'] = 0;
            $data['product_referral_code'] = null;
            $data['cash_on_delivery'] = $product->cash_on_delivery;
            $data['digital'] = $product->digital;

            if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
                $data['product_referral_code'] = Cookie::get('product_referral_code');
            }

            if ($carts && count($carts) > 0) {
                $foundInCart = false;

                foreach ($carts as $key => $cartItem) {
                    $cart_product = Product::where('id', $cartItem['product_id'])->first();
                    // if($cart_product->auction_product == 1){
                        //     return array(
                            //         'status' => 0,
                            //         'cart_count' => count($carts),
                    //         'modal_view' => view('frontend.partials.auctionProductAlredayAddedCart')->render(),
                    //         'nav_cart_view' => view('frontend.partials.cart')->render(),
                    //     );
                    // }

                    if ($cartItem['product_id'] == $request->id) {
                        $product_stock = $cart_product->stocks->where('variant', $str)->first();
                        $quantity = $product_stock->qty;
                        // if($quantity < $cartItem['quantity'] + $request['quantity']){
                            //     return array(
                                //         'status' => 0,
                                //         'cart_count' => count($carts),
                                //         'modal_view' => view('frontend.partials.outOfStockCart')->render(),
                                //         'nav_cart_view' => view('frontend.partials.cart')->render(),
                                //     );
                                // }
                                if (($str != null && $cartItem['variation'] == $str) || $str == null) {
                                    $foundInCart = true;

                                    $cartItem['quantity'] += $request['quantity'];

                                    if ($cart_product->wholesale_product) {
                                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
                                if ($wholesalePrice) {
                                    $price = $wholesalePrice->price;
                                }
                            }

                            $cartItem['price'] = $price;

                            $cartItem->save();
                        }
                    }
                }
                if (!$foundInCart) {
                   Quotation::create($data);
                }
            } else {
                Quotation::create($data);
            }

            if (auth()->user() != null) {
                $user_id = Auth::user()->id;
                $carts = Quotation::where('user_id', $user_id)->whereNull('quotation_id')->get();
            } else {
                $temp_user_id = $request->session()->get('temp_user_id1');
                $carts = Quotation::where('temp_user_id', $temp_user_id)->whereNull('quotation_id')->get();
            }
            return true;
            // return array(
            //     'status' => 1,
            //     'cart_count' => count($carts),
            //     'modal_view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
            //     'nav_cart_view' => view('frontend.partials.cart')->render(),
            // );
        } else {
            $price = $product->bids->max('amount');

            foreach ($product->taxes as $product_tax) {
                if ($product_tax->tax_type == 'percent') {
                    $tax += ($price * $product_tax->tax) / 100;
                } elseif ($product_tax->tax_type == 'amount') {
                    $tax += $product_tax->tax;
                }
            }

            $data['quantity'] = 1;
            $data['price'] = $price;
            $data['tax'] = $tax;
            $data['shipping_cost'] = 0;
            $data['product_referral_code'] = null;
            $data['cash_on_delivery'] = $product->cash_on_delivery;
            $data['digital'] = $product->digital;

            if (count($carts) == 0) {
                Quotation::create($data);
            }
            if (auth()->user() != null) {
                $user_id = Auth::user()->id;
                $carts = Quotation::where('user_id', $user_id)->whereNull('quotation_id')->get();
            } else {
                $temp_user_id = $request->session()->get('temp_user_id1');
                $carts = Quotation::where('temp_user_id', $temp_user_id)->whereNull('quotation_id')->get();
            }
            return true;

            // return array(
            //     'status' => 1,
            //     'cart_count' => count($carts),
            //     'modal_view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
            //     'nav_cart_view' => view('frontend.partials.cart')->render(),
            // );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function view(Request $request)
    {
        //
        $savequotations = array();

        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            if ($request->session()->get('temp_user_id1')) {
                Quotation::where('temp_user_id', $request->session()->get('temp_user_id1'))
                    ->update(
                        [
                            'user_id' => $user_id,
                            'temp_user_id' => null
                        ]
                    );

                Session::forget('temp_user_id1');
            }
            $savequotations = Quotation::where('user_id', Auth::user()->id)->whereNotNull('quotation_id')->groupBy('quotation_id')->paginate(9);

            $quotation = Quotation::where('user_id', $user_id)->whereNull('quotation_id')->get();

        } else {
            $temp_user_id = $request->session()->get('temp_user_id1');
            // $Quotation = Quotation::where('temp_user_id1', $temp_user_id)->get();
            $quotation = ($temp_user_id != null) ? Quotation::where('temp_user_id', $temp_user_id)->whereNull('quotation_id')->get() : [];



        }

        return view('frontend.view_quotation', compact('quotation','savequotations'));
    }

    //removes from Quotation
    public function removeFromQuotation(Request $request)
    {
        Quotation::destroy($request->id);
        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Quotation::where('user_id', $user_id)->whereNull('quotation_id')->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id1');
            $carts = Quotation::where('temp_user_id', $temp_user_id)->whereNull('quotation_id')->get();
        }

        return array(
            'cart_count' => count($carts),
            'cart_view' => view('frontend.partials.quotation_details', compact('carts'))->render(),
            'nav_cart_view' => view('frontend.partials.quotation')->render(),
        );
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cartItem = Quotation::findOrFail($request->id);

        if ($cartItem['id'] == $request->id) {
            $product = Product::find($cartItem['product_id']);
            $product_stock = $product->stocks->where('variant', $cartItem['variation'])->first();
            $quantity = $product_stock->qty;
            $price = $product_stock->price;

            //discount calculation
            $discount_applicable = false;

            if ($product->discount_start_date == null) {
                $discount_applicable = true;
            } elseif (
                strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
            ) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($product->discount_type == 'percent') {
                    $price -= ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $price -= $product->discount;
                }
            }

            if ($quantity >= $request->quantity) {
                if ($request->quantity >= $product->min_qty) {
                    $cartItem['quantity'] = $request->quantity;
                }
            }

            if ($product->wholesale_product) {
                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
                if ($wholesalePrice) {
                    $price = $wholesalePrice->price;
                }
            }
            // $cartItem['tax1_amount'] =(($price * $cartItem['tax1']) / 100);
            // $cartItem['tax2_amount'] =(($price * $cartItem['tax2']) / 100);
            // $cartItem['price'] = $price +  $cartItem['tax1_amount'] +  $cartItem['tax2_amount'];
            $cartItem['price'] = $price;
            $cartItem->save();
        }

        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Quotation::where('user_id', $user_id)->whereNull('quotation_id')->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id1');
            $carts = Quotation::where('temp_user_id', $temp_user_id)->whereNull('quotation_id')->get();
        }

        return array(
            'cart_count' => count($carts),
            'cart_view' => view('frontend.partials.quotation_details', compact('carts'))->render(),
            'nav_cart_view' => view('frontend.partials.quotation')->render(),
        );
    }

    public function sendMail(Request $request){
        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $email = Auth::user()->email;
            $carts = Quotation::where('user_id', $user_id)->whereNull('quotation_id')->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id1');
            $email =  $request->email;
            $carts = Quotation::where('temp_user_id', $temp_user_id)->whereNull('quotation_id')->get();
        }
        if (empty($email)) {
            return Response::json(['error' => 1,'message' => 'Please enter email'], 404);
        }
        $array['view'] = 'emails.quotation';
        $array['subject'] = 'Quotation';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] =   $carts;
        $array['sender'] = env('MAIL_FROM_ADDRESS');

        $array['details'] = 'l';
        try {
            Mail::to($email)->queue(new QuotationMail($array));
            $max_quotation_id = \DB::table('quotations')->max('quotation_id');
            if (auth()->user() != null) {
                $user_id = Auth::user()->id;
                $carts = Quotation::where('user_id', $user_id)->whereNull('quotation_id')->whereIn('id',$request->quotation_ids)->update(['quotation_id' => $max_quotation_id+1,'quote_total' => $request->total]);
            } else {
                $temp_user_id = $request->session()->get('temp_user_id1');
                $carts = Quotation::where('temp_user_id', $temp_user_id)->whereNull('quotation_id')->whereIn('id',$request->quotation_ids)->update(['quotation_id' => $max_quotation_id+1,'quote_total' => $request->total]);
            }
            return Response::json(['success' => 1], 200);
        } catch (\Exception $e) {
            return Response::json(['error' => 1,'message' => $e->getMessage()], 404);
        }
    }

    public function list()
    {

        //
        $quotations = Quotation::where('user_id', Auth::user()->id)->whereNotNull('quotation_id')->groupBy('quotation_id')->paginate(9);
        return view('frontend.user.quotation_history', compact('quotations'));
        // $orders = Order::where('user_id', Auth::user()->id)->orderBy('code', 'desc')->paginate(9);
        // return view('frontend.user.purchase_history', compact('orders'));
    }

    public function quoteView(Request $request,$id)
    {
        $quotations_id = decrypt($id);

        //
        if (auth()->user() != null && !empty($quotations_id)) {
            $user_id = Auth::user()->id;
            $quotation = Quotation::where('user_id', $user_id)->where('quotation_id',$quotations_id)->get();
            return view('frontend.view_user_quotation', compact('quotation'));
        }
    }

    public function quotation_invoice_download($id)
    {
        $quotations_id = $id;
        if(Session::has('currency_code')){
            $currency_code = Session::get('currency_code');
        }
        else{
            $currency_code = Currency::findOrFail(get_setting('system_default_currency'))->code;
        }
        $language_code = Session::get('locale', Config::get('app.locale'));

        if(Language::where('code', $language_code)->first()->rtl == 1){
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        }else{
            $direction = 'ltr';
            $text_align = 'left';
            $not_text_align = 'right';
        }

        if($currency_code == 'BDT' || $language_code == 'bd'){
            $font_family = "'Hind Siliguri','sans-serif'";
        }elseif($currency_code == 'KHR' || $language_code == 'kh'){
            $font_family = "'Hanuman','sans-serif'";
        }elseif($currency_code == 'AMD'){
            $font_family = "'arnamu','sans-serif'";
        }elseif($currency_code == 'AED' || $currency_code == 'EGP' || $language_code == 'sa' || $currency_code == 'IQD' || $language_code == 'ir' || $language_code == 'om' || $currency_code == 'ROM' || $currency_code == 'SDG' || $currency_code == 'ILS'|| $language_code == 'jo'){
            $font_family = "'Baloo Bhaijaan 2','sans-serif'";
        }elseif($currency_code == 'THB'){
            $font_family = "'Kanit','sans-serif'";
        }else{
            $font_family = "'Roboto','sans-serif'";
        }
        $config = [];
        //
        if (auth()->user() != null && !empty($quotations_id)) {
            $user_id = Auth::user()->id;
            $quotation = Quotation::where('user_id', $user_id)->where('quotation_id',$quotations_id)->get();

            return PDF::loadView('backend.invoices.quote_invoice',[
                'quotation' => $quotation,
                'font_family' => $font_family,
                'direction' => $direction,
                'text_align' => $text_align,
                'not_text_align' => $not_text_align
            ], [], $config)->download('invoice-'.$id.'.pdf');
        }
    }
}
