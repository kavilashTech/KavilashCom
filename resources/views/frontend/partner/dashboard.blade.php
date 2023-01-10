@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Franchisee Dashboard') }}</h1>
        </div>
    </div>
</div>
<div class="row gutters-10">
    <div class="col-md-4">
        <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3">
                @php
                    $user_id = Auth::user()->id;
                    $customer = \App\Models\user::where('franchisee_id', $user_id)->get();
                @endphp
                @if(count($customer) > 0)
                <div class="h3 fw-700">
                    {{ count($customer) }} {{ translate('Customer(s)') }}
                </div>
                @else
                <div class="h3 fw-700">
                    0 {{ translate('Customer') }}
                </div>
                @endif
                <div class="opacity-50">{{ translate('Number of Customers') }}</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,192L30,208C60,224,120,256,180,245.3C240,235,300,181,360,144C420,107,480,85,540,96C600,107,660,149,720,154.7C780,160,840,128,900,117.3C960,107,1020,117,1080,112C1140,107,1200,85,1260,74.7C1320,64,1380,64,1410,64L1440,64L1440,320L1410,320C1380,320,1320,320,1260,320C1200,320,1140,320,1080,320C1020,320,960,320,900,320C840,320,780,320,720,320C660,320,600,320,540,320C480,320,420,320,360,320C300,320,240,320,180,320C120,320,60,320,30,320L0,320Z"></path>
            </svg>
        </div>
    </div>
</div>
<div class="row gutters-10">
    <!-- <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ translate('Default Shipping Address') }}</h6>
            </div>
            <div class="card-body">
                @if(Auth::user()->addresses != null)
                    @php
                        $address = Auth::user()->addresses->where('set_default', 1)->first();
                    @endphp
                    @if($address != null)
                        <ul class="list-unstyled mb-0">
                            <li class=" py-2"><span>{{ translate('Address') }} : {{ $address->address }}</span></li>
                            <li class=" py-2"><span>{{ translate('Country') }} : {{ $address->country->name }}</span></li>
                            <li class=" py-2"><span>{{ translate('State') }} : {{ $address->state->name }}</span></li>
                            <li class=" py-2"><span>{{ translate('City') }} : {{ $address->city->name }}</span></li>
                            <li class=" py-2"><span>{{ translate('Postal Code') }} : {{ $address->postal_code }}</span></li>
                            <li class=" py-2"><span>{{ translate('Phone') }} : {{ $address->phone }}</span></li>
                        </ul>
                    @endif
                @endif
            </div>
        </div>
    </div> -->
    @if (get_setting('classified_product'))
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ translate('Purchased Package') }}</h6>
            </div>
            <div class="card-body text-center">
                @php
                    $customer_package = \App\Models\CustomerPackage::find(Auth::user()->customer_package_id);
                @endphp
                @if($customer_package != null)
                    <img src="{{ uploaded_asset($customer_package->logo) }}" class="img-fluid mb-4 h-110px">
                    <p class="mb-1 text-muted">{{ translate('Product Upload') }}: {{ $customer_package->product_upload }} {{ translate('Times')}}</p>
                    <p class="text-muted mb-4">{{ translate('Product Upload Remaining') }}: {{ Auth::user()->remaining_uploads }} {{ translate('Times')}}</p>
                    <h5 class="fw-600 mb-3 text-primary">{{ translate('Current Package') }}: {{ $customer_package->getTranslation('name') }}</h5>
                @else
                    <h5 class="fw-600 mb-3 text-primary">{{translate('Package Not Found')}}</h5>
                @endif
                    <a href="{{ route('customer_packages_list_show') }}" class="btn btn-success d-inline-block">{{ translate('Upgrade Package') }}</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
