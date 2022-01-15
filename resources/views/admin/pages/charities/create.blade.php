@extends('admin.layouts.app')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('users'); ?>">Charities</a></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo Adminurl('dashboard'); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{route('charities.index')}}">Charities</a></li>
                        </ol>
                    </div>
                </div>





            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-hand-holding-heart nav-icon"></i> Add Charity</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('charities.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="container">

                                <div class="row">
                                    @if(session()->has('success'))
                                    <div class="alert alert-success">
                                        {{ session()->get('success') }}
                                    </div>
                                    @endif
                                    @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @elseif (session('user_added'))
                                    <div class="alert alert-success" role="alert">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        {{ session('user_added') }}
                                    </div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <i class="fas fa-user"></i>&nbsp;
                                    <label for="title">Charity Name:*</label>
                                    <input type="text" class="form-control form-control @error('charity_name') is-invalid @enderror" id="fname" placeholder="Full Charity Name (e.g; Islamic Relief...)" value="{{ old('charity_name') }}" name="charity_name">
                                    @if($errors->has('charity_name'))
                                    <div class="invalid-feedback">{{ $errors->first('charity_name') }}</div>
                                    @endif
                                </div>
                                <input type="hidden" name="role" value="1" />
                                <!-- <div class="form-group">
                        <i class="fas fa-user-tag"></i>&nbsp;
                        <label for="title">Role:*</label>
                        <select class="form-control" name="role">
                           
                        </select>
                    </div> -->
                                <div class="form-group">
                                    <i class="fas fa-user"></i>&nbsp;
                                    <label for="title">Slug:*</label>
                                    <input type="text" class="form-control form-control @error('user_name') is-invalid @enderror" id="user_name" placeholder="slug (e.g; /islamicrelief)" value="{{old('user_name')}}" name="user_name">
                                    @if($errors->has('user_name'))
                                    <div class="invalid-feedback">{{ $errors->first('user_name') }}</div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <i class="fas fa-envelope"></i>&nbsp;
                                    <label for="title">Email:*</label>
                                    <input type="text" class="form-control form-control @error('email') is-invalid @enderror" id="email" placeholder="Email" value="{{old('email')}}" name="email">
                                    @if($errors->has('email'))
                                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                                <!-- <div class="form-group">
                        <i class="fas fa-lock"></i>&nbsp;
                        <label for="title">Password:*</label>
                        <input type="password" class="form-control" id="password" placeholder="Password" value="{{ session('insert_error4') }}" name="password">
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock"></i>&nbsp;
                        <label for="title">Re-type Password:*</label>
                        <input type="password" class="form-control" id="retype_password" placeholder="Re-type Password" value="{{ session('insert_error5') }}" name="retype_password">
                    </div> -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Add Charity</button>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
            <div class="row">
            </div>
</section>
</div>
@endsection