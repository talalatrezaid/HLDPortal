@extends('admin.layouts.app')
@section('content')

@if(!empty($store_data[0]))
    @php  $is_active = $store_data[0]->is_active; @endphp
    @if($is_active == "1"){
        @php $api_status = '<span style="color: green">Connected</span>'@endphp
    @else
        @php $api_status = '<span style="color: red">Not Connected</span>'@endphp

    @endif
@else
    @php $api_status = '<span style="color: red">Not Connected</span>'@endphp
@endif

<section class="content">
    <div class="container-fluid">
        <div class="card-footer">
            <a href="{{Adminurl('dashboard')}}" ><button class="btn btn-success">Go Back</button></a>
        </div>
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Store Credentials</h3>
            </div>
            <form action="<?php echo Adminurl('update_store_connection');?>" method="POST">
                <input type="hidden" name="store_id" value="{{ $store_data[0]->id ?? '' }}">
                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                @csrf

                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @elseif (session('store_update'))
                    <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        {{ session('store_update') }}
                    </div>
                @elseif (session('invalid_connection'))
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        {{ session('invalid_connection') }}
                    </div>
                @endif

                <div class="card-body">
                    <div class="form-group">

                        <div class="form-group" >
                            <label for="title">Store Name:*</label>
                            <select name="name" class="form-control" required>
                                <option value="{{ $store_data[0]->name ?? 'Shopify' }}" selected >Shopify</option>
                            </select>

                        </div>

                        <div class="form-group" >
                            <label for="title">Store API Key:*</label>
                            <input type="text" name="api_key" class="form-control" value="{{ $store_data[0]->api_key ?? '' }}" required >
                        </div>

                        <div class="form-group" >
                            <label for="title">Store API Password:*</label>
                            <input type="text" name="api_password" class="form-control" value="{{ $store_data[0]->api_password ?? '' }}" required >
                        </div>

                        <div class="form-group" >
                            <label for="title">Store Domain Address:*</label>
                            <input type="text" name="api_domain" class="form-control" value="{{ $store_data[0]->api_domain ?? '' }}" required>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Verify Credentials</button>
                        <span class="ml-5">API Status: <?php echo  $api_status ?> </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection