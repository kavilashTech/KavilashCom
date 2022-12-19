@extends('frontend.layouts.app')

@section('content')

    <section class="pt-5 mb-4">
    </section>

    <section class="mb-4" id="cart-summary-quo">
        <div class="container">
            @if ($quotation && count($quotation) > 0)
                <div class="row">
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
                                    <!-- <div class="col-auto fw-600">{{ translate('Remove') }}</div> -->
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
                                                            <!-- <button
                                                                class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                                type="button" data-type="minus"
                                                                data-field="quantity[{{ $cartItem['id'] }}]">
                                                                <i class="las la-minus"></i>
                                                            </button> -->
                                                            <input type="number" name="quantity[{{ $cartItem['id'] }}]"
                                                                class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                                placeholder="1" value="{{ $cartItem['quantity'] }}"
                                                                min="{{ $product->min_qty }}"
                                                                max="{{ $product_stock->qty }}"
                                                                onchange="updateQuantity({{ $cartItem['id'] }}, this)">
                                                            <!-- <button
                                                                class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                                type="button" data-type="plus"
                                                                data-field="quantity[{{ $cartItem['id'] }}]">
                                                                <i class="las la-plus"></i>
                                                            </button> -->
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
                                                <!-- <div class="col-lg-auto col-6 order-5 order-lg-0 text-right">
                                                    <a href="javascript:void(0)"
                                                        onclick="removeFromCartView(event, {{ $cartItem['id'] }})"
                                                        class="btn btn-icon btn-sm btn-soft-primary btn-circle">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                </div> -->
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
                                <span class="fw-600 fs-17 pl-3" id="total_quote_price">{{ single_price($total) }}</span>
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
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-xl-8 mx-auto">
                        <div class="shadow-sm bg-white p-4 rounded">
                            <div class="text-center p-3">
                                <i class="las la-frown la-3x opacity-60 mb-3"></i>
                                <h3 class="h4 fw-700">{{ translate('Your Quotation is empty') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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
            $('#getQuotationButton').hide();
            $('.sendingMail').show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('quotation.sendMail')}}",
                type: 'POST',
                data: {quotation_ids: quotation_ids, email: email},
                cache: false,
                success: function (response) {
                    $("#quote_email").val("");
                    $('#quotation-modal').modal('hide');
                    $('.sendingMail').hide();
                    $('#getQuotationButton').show();
                    AIZ.plugins.notify('success', "Your quotation sent to email");
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
@endsection
