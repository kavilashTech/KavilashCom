@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Move Franchisee')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                      
                        <div class="col-9">
                            <div class="tab-content" id="v-pills-tabContent">
                               
                                    <div class="tab-pane fade show  active" id="v-pills-" role="tabpanel" aria-labelledby="v-pills-tab-1">
                                        <form action="{{ route('customer.upgradefranchisee') }}" method="POST">
                                        @csrf
                                            @csrf
                                          
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <label class="col-from-label">{{translate('Customer')}}</label>
                                                            <select class="form-control aiz-selectpicker" name="customer_id" id="customer_id" data-live-search="true" required>
                                                                <option value = "">--</option>
                                                                @foreach ($users as $usersdata)
                                                                <option value="{{ $usersdata->id }}">
                                                                    {{ $usersdata->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="col-from-label">{{translate('Franchisee')}}</label>
                                                            <select class="form-control aiz-selectpicker" name="franchisee_id" id="franchisee_id" data-live-search="true" required>
                                                                <option value = "">--</option>
                                                                @foreach ($usersfranchisee as $franchiseedata)
                                                                <option value="{{ $franchiseedata->id }}">
                                                                    {{ $franchiseedata->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                    </div>
                                                   
                                                </div>
                                                <div class="form-group row">
                                                <div class="col-md-6">
                                                        <label class="col-from-label">{{translate('Description')}}</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                    <textarea name="short_description" rows="5" class="form-control" required=""></textarea>
                                                    </div>

                                                </div>
                                          

                                           
                                            
                                            <div class="form-group mb-3 text-right">
                                                <button type="submit" class="btn btn-primary">{{translate('Update Settings')}}</button>
                                            </div>
                                        </form>
                                    </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
