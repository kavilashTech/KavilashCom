@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{translate('Customer List')}}</h1>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Customers')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Email Address')}}</th>
                    <th>{{translate('Phone')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $key => $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{$customer->email}}</td>
                    <td>{{$customer->phone}}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection