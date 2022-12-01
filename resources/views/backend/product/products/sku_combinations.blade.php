@if(count($combinations[0]) > 0)
<table class="table table-bordered aiz-table">
	<thead>
		<tr>
			<td class="text-center">
				{{translate('Variant')}}
			</td>
			<td class="text-center">
				{{translate('Variant Price')}}
			</td>
			<td class="text-center" data-breakpoints="all">
				{{translate('SKU')}}
			</td>
			<td class="text-center" data-breakpoints="all">
				{{translate('Dimensions(cm)')}}
			</td>
            <td class="text-center" data-breakpoints="all">
				{{translate('USD Price')}}
			</td>
            <td class="text-center" data-breakpoints="all">
				{{translate('USD Points')}}
			</td>
            <td class="text-center" data-breakpoints="all">
				{{translate('Dispatch Days')}}
			</td>
			<td class="text-center" data-breakpoints="all">
				{{translate('Expiry Month')}}
			</td>
			<td class="text-center" data-breakpoints="all">
				{{translate('Expiry Year')}}
			</td>
			<td class="text-center" data-breakpoints="all">
				{{translate('Batch Number')}}
			</td>
			<td class="text-center" data-breakpoints="all">
				{{translate('Quantity')}}
			</td>
			<td class="text-center" data-breakpoints="all">
				{{translate('Photo')}}
			</td>
		</tr>
	</thead>
	<tbody>
	@foreach ($combinations as $key => $combination)
		@php
			$sku = '';
			foreach (explode(' ', $product_name) as $key => $value) {
				$sku .= substr($value, 0, 1);
			}

			$str = '';
			foreach ($combination as $key => $item){
				if($key > 0 ){
					$str .= '-'.str_replace(' ', '', $item);
					$sku .='-'.str_replace(' ', '', $item);
				}
				else{
					if($colors_active == 1){
						$color_name = \App\Models\Color::where('code', $item)->first()->name;
						$str .= $color_name;
						$sku .='-'.$color_name;
					}
					else{
						$str .= str_replace(' ', '', $item);
						$sku .='-'.str_replace(' ', '', $item);
					}
				}
			}
		@endphp
		@if(strlen($str) > 0)
			<tr class="variant">
				<td>
					<label for="" class="control-label">{{ $str }}</label>
				</td>
				<td>
					<input type="number" lang="en" name="price_{{ $str }}" value="{{ $unit_price }}" min="0" step="0.01" class="form-control" required>
				</td>
				<td>
					<input type="text" name="sku_{{ $str }}" value="" class="form-control">
				</td>
				<td>
					<input class="txtdimension dimension" type="number" name="width_{{ $str }}"  placeholder="width" min="0" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" >
					<input class="txtdimension dimension" type="number" name="breadth_{{ $str }}" placeholder="breadth" min="0" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" >
					<input class="txtdimension dimension" type="number" name="height_{{ $str }}" placeholder="height" min="0" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" >
				</td>
				<td>
					<input type="number" name="usd_price_{{ $str }}" class="form-control" required>
				</td>
				<td>
					<input type="number" name="usd_points_{{ $str }}" class="form-control"  min="0" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required>
				</td>
				<td>
					<input type="number" name="dispatch_days_{{ $str }}" class="form-control" min="0" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required>
				</td>
				<td>
					<div class="col-md-8">
                        <select class="form-control aiz-selectpicker" name="expiry_month_{{ $str }}" id="expiry_month" data-live-search="true">
                            <option value="">Select Expiry Month</option>
                            @foreach (json_decode( get_setting('expiry_month_array'), true) as $key => $value)
                            	<option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
				</td>
				<td>
					<div class="col-md-8">
                        <select class="form-control aiz-selectpicker" name="expiry_year_{{ $str }}" id="expiry_year" data-live-search="true">
                            <option value="">Select Expiry Year</option>
                            @foreach (json_decode( get_setting('expiry_year_array'), true) as $key => $value)
                            	<option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
				</td>
				<td>
					<input type="text" name="batch_number_{{ $str }}" value="" class="form-control">
				</td>
				<td>
					<input type="number" lang="en" name="qty_{{ $str }}" value="10" min="0" step="1" class="form-control" required>
				</td>
				<td>
					<div class=" input-group " data-toggle="aizuploader" data-type="image">
						<div class="input-group-prepend">
							<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
						</div>
						<div class="form-control file-amount text-truncate">{{ translate('Choose File') }}</div>
						<input type="hidden" name="img_{{ $str }}" class="selected-files">
					</div>
					<div class="file-preview box sm"></div>
				</td>
			</tr>
		@endif
	@endforeach
	</tbody>
</table>
@endif
<style>
    .dimension{
        width:32% !important;
        line-height:34px;
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
        height: calc(1.3125rem + 1.2rem + 2px);
        border: 1px solid #e2e5ec;
        color: #898b92;
    }
</style>