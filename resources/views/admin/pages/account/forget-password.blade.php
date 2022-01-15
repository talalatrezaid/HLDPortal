<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>FHG | Forgot Password</title>
  <link rel="icon" href="{{ asset('dist/img/RezaidLogo.png')}}"/>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/rezaid.min.css')}}">
  <link rel="stylesheet" href="{{ asset('dist/css/custom.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
      <!--   <img src="{{ asset('dist/img/RezaidLogo.png')}}"/> -->
      <h1>FASHION HUB GLOBAL</h1>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
    <h2 class="logo_heading"><i class="fa fa-user"></i> Sign In</h2>
      <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
      <hr/>
      <form action="<?php echo Adminurl('password-reset') ;?>" method="POST">
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
        @elseif (session('forgot_error'))
        <div class="alert alert-danger" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('forgot_error') }}
        </div>
        @elseif (session('forgot_error12'))
        <div class="alert alert-danger" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('forgot_error12') }}
        </div>
        @elseif (session('forgot_success1'))
        <div class="alert alert-success" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('forgot_success1') }}
        </div>
        @endif
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="forgotemail" placeholder="Email/Username" value="{{ session('password_error1') }}">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-8 mb-2">
            <button type="submit" class="btn btn-primary btn-block">Reset password</button>
          </div>
          <div class="col-sm-4">
          <a class="btn btn-default btn-block" href="<?php echo Adminurl('login') ;?>">Login</a>
          </div>
          <!-- /.col -->
        </div>
      </form>

     
<!--       <p class="mb-0">
        <a href="{{('register')}}" class="text-center">Register a new membership</a>
      </p> -->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Rezaid App -->
<script src="{{ asset('dist/js/rezaid.min.js')}}"></script>

</body>
</html>
