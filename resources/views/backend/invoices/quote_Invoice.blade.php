<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{  translate('Quotation') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
	<style media="all">
        @page {
			margin: 0;
			padding:0;
		}
		body{
			font-size: 0.875rem;
            font-family: '<?php echo  $font_family ?>';
            font-weight: normal;
            direction: <?php echo  $direction ?>;
            text-align: <?php echo  $text_align ?>;
			padding:0;
			margin:0; 
		}
		.gry-color *,
		.gry-color{
			color:#000;
		}
		table{
			width: 100%;
		}
		table th{
			font-weight: normal;
		}
		table.padding th{
			padding: .25rem .7rem;
		}
		table.padding td{
			padding: .25rem .7rem;
		}
		table.sm-padding td{
			padding: .1rem .7rem;
		}
		.border-bottom td,
		.border-bottom th{
			border-bottom:1px solid #eceff4;
		}
		.text-left{
			text-align:<?php echo  $text_align ?>;
		}
		.text-right{
			text-align:<?php echo  $not_text_align ?>;
		}
	</style>
</head>
<body>
	<div>

		@php
			$logo = get_setting('header_logo');
		@endphp

		<div style="background: #eceff4;padding: 1rem;">
			<table>
				<tr>
					<td>
						@if($logo != null)
							<img src="{{ uploaded_asset($logo) }}" height="30" style="display:inline-block;">
						@else
							<img src="{{ static_asset('assets/img/logo.png') }}" height="30" style="display:inline-block;">
						@endif
					</td>
					<td style="font-size: 1.5rem;" class="text-right strong">{{  translate('Quotation') }}</td>
				</tr>
			</table>
			<table>
				<tr>
					<td style="font-size: 1rem;" class="strong">{{ get_setting('site_name') }}</td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small">{{ get_setting('contact_address') }}</td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small">{{  translate('Email') }}: {{ get_setting('contact_email') }}</td>
					<td class="text-right small"><span class="gry-color small">{{  translate('Quotation ID') }}:</span> <span class="strong">{{ $quotation[0]->quotation_id }}</span></td>
				</tr>
				<tr>
					<td class="gry-color small">{{  translate('Phone') }}: {{ get_setting('contact_phone') }}</td>
					<td class="text-right small"><span class="gry-color small">{{  translate('Quotation Date') }}:</span> <span class=" strong">{{ date('d-m-Y',strtotime($quotation[0]->updated_at)) }}</span></td>
				</tr>
			</table>

		</div>
		@php
			$CGST_total = '0.00';
			$SGST_total = '0.00';
		@endphp
	    <div style="padding: 1rem;">
			<table class="padding text-left small border-bottom">
				<thead>
	                <tr class="gry-color" style="background: #eceff4;">
						<th width="16%" class="text-left">{{ translate('Product') }}</th>
						<th width="12%" class="text-left">{{ translate('Price') }}</th>
						<th width="12%" class="text-left">{{ translate('Quantity') }}</th>
						<th width="12%" class="text-left">{{ translate('CGST %') }}</th>
						<th width="12%" class="text-left">{{ translate('CGST Amount') }}</th>
						<th width="12%" class="text-left">{{ translate('SGST %') }}</th>
						<th width="12%" class="text-left">{{ translate('SGST Amount') }}</th>
						<th width="12%" class="text-left">{{ translate('Total') }}</th>
	                </tr>
				</thead>
				<tbody class="strong">

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
						<tr class="">
							<td>	
								<!-- <span class="mr-2 ml-0">
									<img src="{{ uploaded_asset($product->thumbnail_img) }}"
										class="img-fit size-60px rounded"
										alt="{{ $product->getTranslation('name') }}">
								</span><br> -->
								<span class="d-block fs-14 opacity-60">{{ $product_name_with_choice }}</span>
							</td>
							<td>
								<span class="fw-600 fs-16">{{ cart_product_price($cartItem, $product, true, false) }}</span>
							</td>
							<td>
								@if ($cartItem['digital'] != 1 && $product->auction_product == 0)
										
									<span class="fw-600 fs-16"> {{ $cartItem['quantity'] }}</span>
										
								@elseif($product->auction_product == 1)
									<span class="fw-600 fs-16">1</span>
								@endif
							</td>
							<td>
								<span class="fw-600 fs-16">{{ $cartItem->tax1 }}</span>
							</td>
							<td>
								<span class="fw-600 fs-16">{{ $cartItem->tax1_amount * $cartItem['quantity']}}</span>
								@php
									$CGST_total += $cartItem->tax1_amount * $cartItem['quantity']; 
								@endphp
							</td>
							<td>
								<span class="fw-600 fs-16">{{ $cartItem->tax2 }}</span>
							</td>
							<td>
								<span class="fw-600 fs-16">{{ $cartItem->tax2_amount * $cartItem['quantity']}}</span>
								@php
									$SGST_total += $cartItem->tax2_amount * $cartItem['quantity'];
								@endphp
							</td>
							<td>
								<span class="fw-600 fs-16 text-primary">{{ single_price(($cartItem['price'] ) * $cartItem['quantity']) }}</span>
							</td>
						</tr>
					@endforeach
	            </tbody>
			</table>
		</div>

	    <div style="padding:0 1.5rem;">
	        <table class="text-right sm-padding small strong">
	        	<thead>
	        		<tr>
	        			<th width="60%"></th>
	        			<th width="40%"></th>
	        		</tr>
	        	</thead>
		        <tbody>
			        <tr>
			            <td class="text-left">
                            @php
                                $removedXML = '<?xml version="1.0" encoding="UTF-8"?>';
                            @endphp
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
