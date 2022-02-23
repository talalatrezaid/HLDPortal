<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo env("APP_NAME") ?> | Log in</title>
  <link rel="icon" href="{{ asset('dist/img/RezaidLogo.png')}}" />
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
      <!-- <a href="{{('/admin/index')}}"><b>Rezaid</b></a> -->
      <!-- <img src="{{ asset('dist/img/RezaidLogo.png')}}"/> -->
      <h1>Holy Land Dates</h1>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <h2 class="logo_heading"><i class="fa fa-user"></i> Sign In</h2>
        <p class="login-box-msg">Sign in to start your session</p>
        <hr />
        <form action="<?php echo Adminurl('authenticate'); ?>" method="post">
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
          @endif
          @if (session()->get('Error Message') <> NULL)
            <div class="alert alert-danger">
              <strong>Whoops!</strong> There were some problems with your input.<br><br>
              <ul>
                <li>{{ session()->get('Error Message') }}</li>
              </ul>
            </div>
            @endif
            @if (session('user_added'))
            <div class="alert alert-success" role="alert">
              <button type="button" class="close" data-dismiss="alert">×</button>
              {{ session('user_added') }}
            </div>
            @elseif (session('login_error'))
            <div class="alert alert-danger" role="alert">
              <button type="button" class="close" data-dismiss="alert">×</button>
              {{ session('login_error') }}
            </div>
            @elseif (session('password_auth_error'))
            <div class="alert alert-danger" role="alert">
              <button type="button" class="close" data-dismiss="alert">×</button>
              {{ session('password_auth_error') }}
            </div>
            @elseif (session('recover_success'))
            <div class="alert alert-success" role="alert">
              <button type="button" class="close" data-dismiss="alert">×</button>
              {{ session('recover_success') }}
            </div>
            @endif
            <div class="input-group mb-3">
              <input type="text" name="email" class="form-control" value="{{ session('login_error1') }}" placeholder="Email / User Name">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" name="password" id="login_password" class="form-control" value="{{ session('login_error2') }}" placeholder="Password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class='fa fa-eye' id='show-hide-pass'></span>
                </div>
              </div>
            </div>
            <p class="mb-1">
              <a href="<?php echo Adminurl('forget-password'); ?>">I forgot my password</a>
            </p>
            <div class="row">
              <div class="col-8">
                <!-- <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">
                Remember Me
              </label>
            </div> -->
              </div>
              <!-- /.col -->
              <div class="col-12 mb-4 mt-3">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
              </div>
              <!-- /.col -->
            </div>
        </form>


        <!-- <p class="mb-0">
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

<script>
  // password revealed functionality
  $(document).ready(function() {

    $(document).on('click', '#show-hide-pass', function() {
      var x = document.querySelector("#login_password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    });

  });
</script>