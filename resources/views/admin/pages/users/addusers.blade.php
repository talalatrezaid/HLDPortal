@extends('admin.layouts.app')
@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="card-footer">
      <a href="<?php echo Adminurl('users');?>" ><button class="btn btn-success">Go Back</button></a>
    </div>
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Add User</h3>
      </div>
      <form action="<?php echo Adminurl('insert_user');?>" method="POST">
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
        @elseif (session('user_added'))
        <div class="alert alert-success" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('user_added') }}
        </div>
        @endif
          <div class="card-body">
            <div class="form-group" >
            	<i class="fas fa-user"></i>&nbsp;
              <label for="title">Full Name:*</label>
              <input type="text" class="form-control" id="fname" placeholder="Full Name" value="{{ session('insert_error1') }}" name="fname">
            </div>
            <div class="form-group" >
              <i class="fas fa-user-tag"></i>&nbsp;
              <label for="title">Role:*</label>
              <select class="form-control" name="role">
              @if(Auth::user()->role_id == 1)
                @foreach($user_roles as $users)
                	@if($users->id != '1')
                		<option value="{{$users->id}}">{{$users->user_role}}</option>
                	@endif
              	@endforeach
              @endif
              @if(Auth::user()->role_id == 2)
                @foreach($user_roles as $users)
                	@if($users->id != 1 && $users->id != 2)
                		<option value="{{$users->id}}">{{$users->user_role}}</option>
                	@endif
              	@endforeach
              @endif
              </select>
            </div>
            <div class="form-group">
            	<i class="fas fa-user"></i>&nbsp;
              <label for="title">Username:*</label>
              <input type="text" class="form-control" id="user_name" placeholder="Username" value="{{ session('insert_error2') }}" name="user_name">
            </div>
            <div class="form-group">
            	<i class="fas fa-envelope"></i>&nbsp;
              <label for="title">Email:*</label>
              <input type="text" class="form-control" id="email" placeholder="Email" value="{{ session('insert_error3') }}" name="email">
            </div>
            <div class="form-group">
            	<i class="fas fa-lock"></i>&nbsp;
              <label for="title">Password:*</label>
              <input type="password" class="form-control" id="password" placeholder="Password" value="{{ session('insert_error4') }}" name="password">
            </div>
            <div class="form-group">
            	<i class="fas fa-lock"></i>&nbsp;
              <label for="title">Re-type Password:*</label>
              <input type="password" class="form-control" id="retype_password" placeholder="Re-type Password" value="{{ session('insert_error5') }}" name="retype_password">
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Register</button>
            </div>
          </div>
      </form>
    </div>
  </div>
</section>
</div>
@endsection
