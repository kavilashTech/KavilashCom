<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title></title>
    <style></style>
</head>

<body>
    <div style="width: 100%;">
        @php
            $CGST_total = '0.00';
            $SGST_total = '0.00';
        @endphp
        <div>
            <table class="gmail-table" style="border: solid 1px #000000; border-collapse: collapse; border-spacing: 0; font: normal 14px Roboto, sans-serif;">
                <thead>
                    <tr>
                        <th style="background-color: #eceff4; border: solid 1px #000000; color: #000000; padding: 10px; text-align: center; text-shadow: 1px 1px 1px #fff;" bgcolor="#eceff4" align="center">{{ translate('Product') }}</th>
                        <th style="background-color: #eceff4; border: solid 1px #000000; color: #000000; padding: 10px; text-align: center; text-shadow: 1px 1px 1px #fff;" bgcolor="#eceff4" align="center">{{ translate('Price') }}</th>
                        <th style="background-color: #eceff4; border: solid 1px #000000; color: #000000; padding: 10px; text-align: center; text-shadow: 1px 1px 1px #fff;" bgcolor="#eceff4" align="center">{{ translate('Quantity') }}</th>
                        <th style="background-color: #eceff4; border: solid 1px #000000; color: #000000; padding: 10px; text-align: center; text-shadow: 1px 1px 1px #fff;" bgcolor="#eceff4" align="center">{{ translate('CGST %') }}</th>
                        <th style="background-color: #eceff4; border: solid 1px #000000; color: #000000; padding: 10px; text-align: center; text-shadow: 1px 1px 1px #fff;" bgcolor="#eceff4" align="center">{{ translate('CGST Amount') }}</th>
                        <th style="background-color: #eceff4; border: solid 1px #000000; color: #000000; padding: 10px; text-align: center; text-shadow: 1px 1px 1px #fff;" bgcolor="#eceff4" align="center">{{ translate('SGST %') }}</th>
                        <th style="background-color: #eceff4; border: solid 1px #000000; color: #000000; padding: 10px; text-align: center; text-shadow: 1px 1px 1px #fff;" bgcolor="#eceff4" align="center">{{ translate('SGST Amount') }}</th>
                        <th style="background-color: #eceff4; border: solid 1px #000000; color: #000000; padding: 10px; text-align: center; text-shadow: 1px 1px 1px #fff;" bgcolor="#eceff4" align="center">{{ translate('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                        $subTotal = 0;
                    @endphp
                    @foreach ($quotation as $key => $cartItem)
                        @php
                            $product = \App\Models\Product::find($cartItem['product_id']);
                            $product_stock = $product->stocks->where('variant', $cartItem['variation'])->first();
                            // $total = $total + ($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity'];
                            $total = $total + cart_product_price($cartItem, $product, false) * $cartItem['quantity'];
                            $subTotal = $subTotal + ($cartItem['price']) * $cartItem['quantity'];
                            $product_name_with_choice = $product->getTranslation('name');
                            
                            if ($cartItem['variation'] != null) {
                                $product_name_with_choice = $product->getTranslation('name') . ' - ' . $cartItem['variation'];
                            }
                        @endphp
                        <tr>
                            <td style="border: solid 1px #000000; color: #000000; padding: 10px; text-shadow: 1px 1px 1px #fff;">
                                <!-- <img src="https://picsum.photos/200" style="height: 100px; width: 100px;" width="100" height="100"> -->
                                <img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{ $product->getTranslation('name') }}" style="height: 100px; width: 100px;" width="100" height="100">
                                <br>
                                {{ $product_name_with_choice }}
                            </td>
                            <td style="border: solid 1px #000000; color: #000000; padding: 10px; text-shadow: 1px 1px 1px #fff;"> 
                                {{ cart_product_price($cartItem, $product, true, false) }}</td>
                            <td style="border: solid 1px #000000; color: #000000; padding: 10px; text-shadow: 1px 1px 1px #fff;"> 
                                @if ($cartItem['digital'] != 1 && $product->auction_product == 0)
                                    {{ $cartItem['quantity'] }}
                                @elseif($product->auction_product == 1)
                                1
                                @endif
                            </td>
                            <td style="border: solid 1px #000000; color: #000000; padding: 10px; text-shadow: 1px 1px 1px #fff;"> 
                                {{ $cartItem->tax1 }}
                            </td>
                            <td style="border: solid 1px #000000; color: #000000; padding: 10px; text-shadow: 1px 1px 1px #fff;"> 
                                {{ $cartItem->tax1_amount * $cartItem['quantity']}}
                                @php
                                    $CGST_total += $cartItem->tax1_amount * $cartItem['quantity'];
                                @endphp
                            </td>
                            <td style="border: solid 1px #000000; color: #000000; padding: 10px; text-shadow: 1px 1px 1px #fff;"> 
                                {{ $cartItem->tax2 }}
                            </td>
                            <td style="border: solid 1px #000000; color: #000000; padding: 10px; text-shadow: 1px 1px 1px #fff;"> 
                                {{ $cartItem->tax2_amount * $cartItem['quantity']}}
                                @php
                                $SGST_total += $cartItem->tax2_amount * $cartItem['quantity'];
                                @endphp
                            </td>
                            <td style="border: solid 1px #000000; color: #000000; padding: 10px; text-shadow: 1px 1px 1px #fff;"> 
                                {{ single_price(($cartItem['price'] ) * $cartItem['quantity']) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <table class="gmail-table" style="float:right;margin-right:40px;border-collapse: collapse; border-spacing: 0; font: normal 14px Roboto, sans-serif;">
                <thead>
                    <tr>
                        <th style="width:60%"></th>
                        <th style="width:40%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left">
                        </td>
                        <td>
                            <table class="text-right sm-padding small strong">
                                <tbody>
                                    <tr>
                                        <th class="gry-color text-left">{{ translate('Sub Total') }}</th>
                                        <td class="currency">{{ single_price($subTotal) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="gry-color text-left">{{ translate('CGST') }}</th>
                                        <td class="currency">{{ single_price($CGST_total) }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="gry-color text-left">{{ translate('SGST') }}</th>
                                        <td class="currency">{{ single_price($SGST_total) }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="gry-color text-left">{{ translate('Total') }}</th>
                                        <td class="currency">{{ single_price($total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>