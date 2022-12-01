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
                $variation_available = false;
                $sku = '';
                foreach (explode(' ', $product_name) as $key => $value) {
                    $sku .= substr($value, 0, 1);
                }

                $str = '';
                foreach ($combination as $key => $item){
                    if($key > 0 ) {
                        $str .= '-'.str_replace(' ', '', $item);
                        $sku .='-'.str_replace(' ', '', $item);
                    }
                    else {
                        if($colors_active == 1) {
                            $color_name = \App\Models\Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                            $sku .='-'.$color_name;
                        }
                        else {
                            $str .= str_replace(' ', '', $item);
                            $sku .='-'.str_replace(' ', '', $item);
                        }
                    }
                    $stock = $product->stocks->where('variant', $str)->first();
                    // if($stock != null) {
                    //     $variation_available = true;
                    // }
                }
            @endphp
            @if(strlen($str) > 0)
            <tr class="variant">
                <td>
                    <label for="" class="control-label">{{ $str }}</label>
                </td>
                <td>
                    <input type="number" lang="en" name="price_{{ $str }}" value="@php
                            if ($product->unit_price == $unit_price) {
                                if($stock != null){
                                    echo $stock->price;
                                }
                                else {
                                    echo $unit_price;
                                }
                            }
                            else{
                                echo $unit_price;
                            }
                           @endphp" min="0" step="0.01" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="sku_{{ $str }}" value="@php
                            if($stock != null) {
                                echo $stock->sku;
                            }
                            else {
                                echo $str;
                            }
                           @endphp" class="form-control">
                </td>
                <td>
				    <input class="txtdimension dimension" name="width_{{ $str }}" type="number" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  placeholder="width"  value="@php
                            if($stock != null){
                                echo $stock->width;
                            }
                            else{
                                echo '';
                            }
                           @endphp">
                    <input class="txtdimension dimension" name="breadth_{{ $str }}" type="number" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  placeholder="breadth" value="@php
                            if($stock != null){
                                echo $stock->breadth;
                            }
                            else{
                                echo '';
                            }
                           @endphp">
                    <input class="txtdimension dimension" name="height_{{ $str }}" type="number" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"   placeholder="height" value="@php
                            if($stock != null){
                                echo $stock->height;
                            }
                            else{
                                echo '';
                            }
                           @endphp">
                </td>
                <td>
                    <input type="number" lang="en" name="usd_price_{{ $str }}" value="@php
                            if($stock != null){
                                echo $stock->usd_price;
                            }
                            else{
                                echo '';
                            }
                           @endphp" min="0" step="1" class="form-control" required>
                </td>
                <td>
                    <input type="number" lang="en" name="usd_points_{{ $str }}" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value="@php
                            if($stock != null){
                                echo $stock->usd_points;
                            }
                            else{
                                echo '';
                            }
                           @endphp" min="0" step="1" class="form-control" required>
                </td>
                <td>
                    <input type="number" lang="en" name="dispatch_days_{{ $str }}" maxlength="3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value="@php
                            if($stock != null){
                                echo $stock->dispatch_days;
                            }
                            else{
                                echo '';
                            }
                           @endphp" min="0" step="1" class="form-control" required>
                </td>
                <td>
                    @php
                        if($stock != null) {
                            $expiryMonth = $stock->expiry_month;
                        }
                        else {
                            $expiryMonth = $str;
                        }
                    @endphp
                    <div class="col-md-8">
                        <select class="form-control aiz-selectpicker" name="expiry_month_{{ $str }}" id="expiry_month" data-live-search="true">
                            <option value="">Select Expiry Month</option>
                            @foreach (json_decode( get_setting('expiry_month_array'), true) as $key => $value)
                                <option value="{{ $value }}" <?php if ($expiryMonth == $value) echo "selected"; ?> >{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    @php
                        if($stock != null) {
                            $expiryYear = $stock->expiry_year;
                        }
                        else {
                            $expiryYear = $str;
                        }
                    @endphp
                    <div class="col-md-8">
                        <select class="form-control aiz-selectpicker" name="expiry_year_{{ $str }}" id="expiry_year" data-live-search="true">
                            <option value="">Select Expiry Year</option>
                            @foreach (json_decode( get_setting('expiry_year_array'), true) as $key => $value)
                                <option value="{{ $value }}" <?php if ($expiryYear == $value) echo "selected"; ?> >{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <input type="text" name="batch_number_{{ $str }}" value="@php
                            if($stock != null) {
                                echo $stock->batch_number;
                            }
                            else {
                                echo '';
                            }
                           @endphp" class="form-control">
                </td>
                <td>
                    <input type="number" lang="en" name="qty_{{ $str }}" value="@php
                            if($stock != null){
                                echo $stock->qty;
                            }
                            else{
                                echo '10';
                            }
                           @endphp" min="0" step="1" class="form-control" required>
                </td>
                <td>
                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                        <div class="input-group-prepend">
                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                        </div>
                        <div class="form-control file-amount text-truncate">{{ translate('Choose File') }}</div>
                        <input type="hidden" name="img_{{ $str }}" class="selected-files" value="@php
                                if($stock != null){
                                    echo $stock->image;
                                }
                                else{
                                    echo null;
                                }
                               @endphp">
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
