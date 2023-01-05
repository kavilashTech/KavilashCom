@extends('frontend.layouts.app')


@section('content')
<style>

    <style>
        body {
            background: rgb(140, 214, 30);
        }

        [data-tab-info] {
            display: none;
        }

        .active[data-tab-info] {
            display: block;
        }


        .tabs {

            color: rgb(255, 255, 255);
            display: flex;
            margin: 0;
        }

        .tabs span {
            background: rgb(28, 145, 38);
            padding: 10px;
            border: 1px solid rgb(255, 255, 255);
        }

        .tabs span:hover {
            background: rgb(29, 185, 112);
            cursor: pointer;
            color: black;
        }

        .quotation-login-container {
    padding: 10px 20px 20px;
}
.quotation-login-container .pay-mode-notavailable {
    margin-bottom: 40px;
    background-color: #fff;
    border: 1px solid #e4e4e4;
    color: #444;
    font-size: 12px;
    float: left;
    width: 100%;
}
.quotation-login-container .pay-mode-notavailable .not-avail-left {
    background-color: #f0615d;
    float: left;
    width: 48px;
    height: 46px;
    text-align: center;
}
.quotation-login-container .pay-mode-notavailable .not-avail-left .fa-exclamation-triangle {
    padding: 12px 0;
    color: #fff;
}
.quotation-login-container .pay-mode-notavailable .not-avail-right {
    background: #fff;
    float: left;
    padding: 6px 10px;
}
.quotation-login-container .signin-button button {
    background: #fa8320;
    padding: 12px 76px;
    font-family: Open Sans Regular;
    font-size: 13px;
    color: #fff;
    text-transform: none;
    border: none;
    width: auto;
}
.quotation-login-container .pay-mode-notavailable .logged-mssg span {
    font-family: 'Open Sans Bold';
    font-family: 'Open Sans Bold';
    font-weight: 600;
    font-size: x-large;
}
    </style>
</style>

    <section class="pt-5 mb-4">
    </section>

    <section class="mb-4" id="cart-summary-quo">
        <div class="container">
            <div class="row">
                <div class="col-xxl-8 col-xl-10 mx-auto">
                    <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left">
                        <div class="tabs">
                            <span data-tab-value="#tab_1">Quotation </span>
                            <span data-tab-value="#tab_2">Saved Quotation </span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div class="tabs__tab active" id="tab_1" data-tab-info>

                <div class="row">
            @if ($quotation && count($quotation) > 0)

                    <div class="col-xxl-8 col-xl-10 mx-auto">
                        <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left">
                            @php
                             $CGST_total = '0.00';
                             $SGST_total = '0.00';
                            @endphp
                            <div class="mb-4">
                                <div class="row gutters-5 d-none d-lg-flex border-bottom mb-3 pb-3">
                                    <div class="col-md-1 fw-600">{{ translate('Product') }}</div>
                                    <div class="col fw-600">{{ translate('Price') }}</div>
                                    <!-- <div class="col fw-600">{{ translate('Tax') }}</div> -->
                                    <div class="col fw-600">{{ translate('Quantity') }}</div>
                                    <div class="col fw-600">{{ translate('CGST %') }}</div>
                                    <div class="col fw-600">{{ translate('CGST Amount') }}</div>
                                    <div class="col fw-600">{{ translate('SGST %') }}</div>
                                    <div class="col fw-600">{{ translate('SGST Amount') }}</div>
                                    <div class="col fw-600">{{ translate('Total') }}</div>
                                    <div class="col-auto fw-600">{{ translate('Remove') }}</div>
                                </div>
                                <ul class="list-group list-group-flush">
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
                                        <input name="quotation_ids[]" id="quotation_ids" value="{{$cartItem->id}}" type="hidden">
                                        <li class="list-group-item px-0 px-lg-3">
                                            <div class="row gutters-5">
                                                <div class="col-lg-1">
                                                    <span class="mr-2 ml-0">
                                                        <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                            class="img-fit size-60px rounded"
                                                            alt="{{ $product->getTranslation('name') }}">
                                                    </span>
                                                    <span class="d-block fs-14 opacity-60">{{ $product_name_with_choice }}</span>
                                                </div>

                                                <div class="col-lg col-4 order-1 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none">{{ translate('Price') }}</span>
                                                    <span
                                                        class="fw-600 fs-16">{{ cart_product_price($cartItem, $product, true, false) }}</span>
                                                </div>
                                                <!-- <div class="col-lg col-4 order-2 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none">{{ translate('Tax') }}</span>
                                                    <span
                                                        class="fw-600 fs-16">{{ cart_product_tax($cartItem, $product) }}</span>
                                                </div> -->

                                                <div class="col-lg col-6 order-4 order-lg-0">
                                                    @if ($cartItem['digital'] != 1 && $product->auction_product == 0)
                                                        <div
                                                            class="row no-gutters align-items-center aiz-plus-minus mr-2 ml-0">
                                                            <button
                                                                class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                                type="button" data-type="minus"
                                                                data-field="quantity[{{ $cartItem['id'] }}]">
                                                                <i class="las la-minus"></i>
                                                            </button>
                                                            <input type="number" name="quantity[{{ $cartItem['id'] }}]"
                                                                class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                                placeholder="1" value="{{ $cartItem['quantity'] }}"
                                                                min="{{ $product->min_qty }}"
                                                                max="{{ $product_stock->qty }}"
                                                                onchange="updateQuantity({{ $cartItem['id'] }}, this)">
                                                            <button
                                                                class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                                type="button" data-type="plus"
                                                                data-field="quantity[{{ $cartItem['id'] }}]">
                                                                <i class="las la-plus"></i>
                                                            </button>
                                                        </div>
                                                    @elseif($product->auction_product == 1)
                                                        <span class="fw-600 fs-16">1</span>
                                                    @endif
                                                </div>

                                                <div class="col-lg col-4 order-2 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none">{{ translate('CGST %') }}</span>
                                                    <span
                                                        class="fw-600 fs-16">{{ $cartItem->tax1 }}</span>
                                                </div>

                                                <div class="col-lg col-4 order-2 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none">{{ translate('CGST Amount') }}</span>
                                                    <span
                                                        class="fw-600 fs-16">{{ $cartItem->tax1_amount * $cartItem['quantity']}}</span>
                                                    @php
                                                       $CGST_total += $cartItem->tax1_amount * $cartItem['quantity'];
                                                    @endphp
                                                </div>

                                                <div class="col-lg col-4 order-2 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                    class="opacity-60 fs-12 d-block d-lg-none">{{ translate('SGST %') }}</span>
                                                    <span
                                                        class="fw-600 fs-16">{{ $cartItem->tax2 }}</span>
                                                    </div>

                                                <div class="col-lg col-4 order-2 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                    class="opacity-60 fs-12 d-block d-lg-none">{{ translate('SGST Amount') }}</span>
                                                    <span
                                                    class="fw-600 fs-16">{{ $cartItem->tax2_amount * $cartItem['quantity']}}</span>
                                                    @php
                                                       $SGST_total += $cartItem->tax2_amount * $cartItem['quantity'];
                                                    @endphp
                                                </div>

                                                <div class="col-lg col-4 order-3 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none">{{ translate('Total') }}</span>
                                                    <span
                                                        class="fw-600 fs-16 text-primary">{{ single_price(($cartItem['price'] ) * $cartItem['quantity']) }}</span>
                                                </div>
                                                <div class="col-lg-auto col-6 order-5 order-lg-0 text-right">
                                                    <a href="javascript:void(0)"
                                                        onclick="removeFromCartView(event, {{ $cartItem['id'] }})"
                                                        class="btn btn-icon btn-sm btn-soft-primary btn-circle">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="px-3 py-2 border-top d-flex justify-content-end">
                                <span class="opacity-60 fs-15">{{ translate('Subtotal') }}</span>
                                <span class="fw-600 fs-17 pl-3">{{ single_price($subTotal) }}</span>
                            </div>

                            <div class="px-3 py-2 d-flex justify-content-end mt-n3">
                                <span class="opacity-60 fs-15">{{ translate('CGST') }}</span>
                                <span class="fw-600 fs-17 pl-3">{{ single_price($CGST_total) }}</span>
                            </div>

                            <div class="px-3 py-2 d-flex justify-content-end mt-n3">
                                <span class="opacity-60 fs-15">{{ translate('SGST') }}</span>
                                <span class="fw-600 fs-17 pl-3">{{ single_price($SGST_total) }}</span>
                            </div>

                            <div class="px-3 py-2 d-flex justify-content-end mt-n3">
                                <span class="opacity-60 fs-15">{{ translate('Total') }}</span>
                                <span class="fw-600 fs-17 pl-3">{{ single_price($total) }}</span>
                                <input type="hidden" value="{{$total}}" id="total_quote_price">
                            </div>

                            <!-- <div class="px-3 py-2 d-flex justify-content-end mt-n3">
                                <span class="opacity-60 fs-15">{{ translate('Balance Due') }}</span>
                                <span class="fw-600 fs-17 pl-3">{{ single_price($total) }}</span>
                            </div> -->

                            <div class="row align-items-center">
                                <div class="col-md-6 text-center text-md-left order-1 order-md-0">
                                    <!-- <a href="{{ route('home') }}" class="btn btn-link">
                                        <i class="las la-arrow-left"></i>
                                        {{ translate('Return to shop') }}
                                    </a> -->
                                </div>
                                <div class="col-md-6 text-center text-md-right">
                                    @if (Auth::check())
                                        <button class="btn btn-primary fw-600"
                                        onclick="sendMail()" id="getQuotationButton">{{ translate('Get Quotation') }}</button>
                                        <button class="btn btn-primary fw-600 sendingMail"  disabled  style="display: none;">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ translate('Get Quotation') }}
                                        </button>
                                        <!-- <a href="{{ route('quotation.sendMail') }}" class="btn btn-primary fw-600">
                                            {{ translate('Get Quotation') }}
                                        </a> -->
                                    @else
                                        <button class="btn btn-primary fw-600"
                                            onclick="showQuotationModal()">{{ translate('Get Quotation') }}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
            </div>

            <div class="col-xxl-8 col-xl-10 mx-auto mt-2">
                        <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left">
                            <div class="text-center p-3">
                                <i class="las la-frown la-3x opacity-60 mb-3"></i>
                                <h3 class="h4 fw-700">{{ translate('Your Quotation is empty') }}</h3>
                            </div>
                        </div>
                    </div>


            @endif
            </div>
            <div class="tabs__tab" id="tab_2" data-tab-info>
                <div class="col-xxl-8 col-xl-10 mx-auto mt-2">
                    <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left">
                        @if(count($savequotations) == 0)
                        <div class="row quotation-login-container">
                            <div class="pay-mode-notavailable">
                                <div class="not-avail-left">
                                    <i class="fa fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div class="not-avail-right">
                                    <div class="logged-mssg"><span>Sorry! you are not logged In</span>
                                        <p></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7 nopad">
                                <div class="signin-button">
                                    <button type="button" data-action="QUOTE" name="submit" id="Quote_login_popup_link"><a href="{{ route('user.login') }}" class="d-inline-block py-2" style="color:#fff;">{{ translate('SIGN IN & CONTINUE')}}</a>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('My Quotations') }}</h5>
        </div>
        @if (count($savequotations) > 0)
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th data-breakpoints="md">{{ translate('id')}}</th>
                            <th data-breakpoints="md">{{ translate('Date')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($savequotations as $key => $quotation)
                            <tr>
                                <td>{{ $quotation->quotation_id }}</td>
                                <td>{{ date('d-m-Y',strtotime($quotation->created_at)) }}</td>
                                <td>
                                    {{ single_price($quotation->quote_total) }}
                                </td>
                                <td class="text-right">
                                    <a href="{{route('quote-view-id', encrypt($quotation->quotation_id))}}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Quotation Details') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('quotationInvoice.download', $quotation->quotation_id) }}" title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $savequotations->links() }}
              	</div>
            </div>
                                        </div>
                                        @endif

                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

@endsection

@section('modal')
    <div class="modal fade" id="quotation-modal">
        <div class="modal-dialog modal-dialog-zoom">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">{{ translate('Get Quotation') }}</h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <div class="form-group email-form-group">
                            <input type="email"
                                class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                value="{{ old('email') }}" placeholder="{{ translate('Email') }}" name="email"
                                id="quote_email" autocomplete="off" required>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div>
                            <button class="btn btn-primary fw-600"
                                onclick="sendMail()" id="getQuotationButton">{{ translate('Submit') }}</button>
                            <button class="btn btn-primary fw-600 sendingMail"  disabled style="display: none;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                {{ translate('Submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('modal')
    @include('modals.delete_modal')

    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        function removeFromCartView(e, key) {
            e.preventDefault();
            removeFromQuotation(key);
        }

        function updateQuantity(key, element) {
            $.post('{{ route('quotation.updateQuantity') }}', {
                _token: AIZ.data.csrf,
                id: key,
                quantity: element.value
            }, function(data) {
                updateNavQuotate(data.nav_cart_view, data.cart_count);
                $('#cart-summary-quo').html(data.cart_view);
            });
        }

        function showQuotationModal() {
            $('#quotation-modal').modal();
        }

        function sendMail() {
            var user = {!! json_encode(optional(auth()->user())->only('id', 'email')) !!}
            if(user){
                var email = user.email;
            }else{
                var email = $('#quote_email').val()
            }
            var filter = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if(!email){
                AIZ.plugins.notify('danger', 'Please enter email');
                return false;
            }
            if (!filter.test(email)) {
                AIZ.plugins.notify('danger', 'Please provide a valid email address');
                return false;
            }
            var quotation_ids = $("input[name='quotation_ids[]']").map(function(){return $(this).val();}).get();
            var total = $("#total_quote_price").val();
            $('#getQuotationButton').hide();
            $('.sendingMail').show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('quotation.sendMail')}}",
                type: 'POST',
                data: {quotation_ids: quotation_ids, email: email, total:total},
                cache: false,
                success: function (response) {
                    $("#quote_email").val("");
                    $('#quotation-modal').modal('hide');
                    $('.sendingMail').hide();
                    $('#getQuotationButton').show();
                    AIZ.plugins.notify('success', "Your quotation sent to email");
                    window.location.href = "{{ route('home')}}";
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $('.sendingMail').hide();
                    $('#getQuotationButton').show();
                    if(XMLHttpRequest?.responseJSON?.message){
                        AIZ.plugins.notify('danger', XMLHttpRequest.responseJSON.message);
                    } else{
                        AIZ.plugins.notify('danger', "Something went wrong");
                    }
                }
            });
        }
        // Country Code
        var isPhoneShown = true,
            countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if (country.iso2 == 'bd') {
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
            onlyCountries: @php echo json_encode(\App\Models\Country::where('status', 1)->pluck('code')->toArray()) @endphp,
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                if (selectedCountryData.iso2 == 'bd') {
                    return "01xxxxxxxxx";
                }
                return selectedCountryPlaceholder;
            }
        });

        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function(e) {
            // var currentMask = e.currentTarget.placeholder;

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

        });

        function toggleEmailPhone(el) {
            if (isPhoneShown) {
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                $('input[name=phone]').val(null);
                isPhoneShown = false;
                $(el).html('{{ translate('Use Phone Instead') }}');
            } else {
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                $('input[name=email]').val(null);
                isPhoneShown = true;
                $(el).html('{{ translate('Use Email Instead') }}');
            }
        }
    </script>

<script type="text/javascript">
        const tabs = document.querySelectorAll('[data-tab-value]')
        const tabInfos = document.querySelectorAll('[data-tab-info]')

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = document
                    .querySelector(tab.dataset.tabValue);

                tabInfos.forEach(tabInfo => {
                    tabInfo.classList.remove('active')
                })
                target.classList.add('active');
            })
        })
    </script>
       <script type="text/javascript">
        $('#order_details').on('hidden.bs.modal', function () {
            location.reload();
        })
    </script>
@endsection
