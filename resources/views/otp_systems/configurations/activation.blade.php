@extends('backend.layouts.app')

@section('content')
    <h4 class="text-center text-muted">{{translate('Activate OTP')}}</h4>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('Nexmo OTP')}}</h3>
                </div>
                <div class="card-body text-center">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="checkbox" onchange="updateSettings(this, 'nexmo')" @if(\App\Models\OtpConfiguration::where('type', 'nexmo')->first()->value == 1) checked @endif>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('Twilio OTP')}}</h3>
                </div>
                <div class="card-body text-center">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="checkbox" onchange="updateSettings(this, 'twillo')" @if(\App\Models\OtpConfiguration::where('type', 'twillo')->first()->value == 1) checked @endif>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('SSL Wireless OTP')}}</h3>
                </div>
                <div class="card-body text-center">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="checkbox" onchange="updateSettings(this, 'ssl_wireless')" @if(\App\Models\OtpConfiguration::where('type', 'ssl_wireless')->first()->value == 1) checked @endif>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('Fast2SMS OTP')}}</h3>
                </div>
                <div class="card-body text-center">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="checkbox" onchange="updateSettings(this, 'fast2sms')" @if(App\Models\OtpConfiguration::where('type', 'fast2sms')->first() != null && \App\Models\OtpConfiguration::where('type', 'fast2sms')->first()->value == 1) checked @endif>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('MIMO OTP')}}</h3>
                </div>
                <div class="card-body text-center">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="checkbox" onchange="updateSettings(this, 'mimo')" @if(App\Models\OtpConfiguration::where('type', 'mimo')->first() != null && \App\Models\OtpConfiguration::where('type', 'mimo')->first()->value == 1) checked @endif>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('MIMSMS')}}</h3>
                </div>
                <div class="card-body text-center">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="checkbox" onchange="updateSettings(this, 'mimsms')" @if(\App\Models\OtpConfiguration::where('type', 'mimsms')->first() != null && \App\Models\OtpConfiguration::where('type', 'mimsms')->first()->value == 1) checked @endif>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('MSEGAT SMS')}}</h3>
                </div>
                <div class="card-body text-center">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="checkbox" onchange="updateSettings(this, 'msegat')" @if(\App\Models\OtpConfiguration::where('type', 'msegat')->first() != null && \App\Models\OtpConfiguration::where('type', 'msegat')->first()->value == 1) checked @endif>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        function updateSettings(el, type){
            if($(el).is(':checked')){
                var value = 1;
            }
            else{
                var value = 0;
            }
            $.post('{{ route('otp_configurations.update.activation') }}', {_token:'{{ csrf_token() }}', type:type, value:value}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
