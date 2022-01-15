<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>FHG | Recover Password</title>
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
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
        <img src="{{ asset('dist/img/RezaidLogo.png')}}">
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">You are only one step a way from your new password, recover your password now.</p>

      <form action="<?php echo Adminurl('submit-password');?>" method="POST">
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
        @elseif (session('recover_error2'))
        <div class="alert alert-danger" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('recover_error2') }}
        </div>
        @elseif (session('recover_success'))
        <div class="alert alert-danger" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('recover_success') }}
        </div>
        @endif
        <div class="input-group mb-3">
          <input type="text" name="tokenval" value="{{ $tokenval }}" hidden="">
          <input type="password" class="form-control" id="recover_password" name="password" placeholder="Password" value="{{session('recover_error')}}">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class='fa fa-eye' id='show-hide-pass'></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" id="re_recover_password" name="retype_password" placeholder="Confirm Password" value="{{session('recover_error1')}}">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class='fa fa-eye' id='show-hide-pass-2'></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Change password</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mt-3 mb-1">
        <a href="<?php echo Adminurl('login') ;?>">Login</a>
      </p>
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
  $(document).ready(function(){

    $(document).on('click', '#show-hide-pass', function(){
      var x = document.querySelector("#recover_password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    });

    $(document).on('click', '#show-hide-pass-2', function(){
      var x = document.querySelector("#re_recover_password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    });

  });
</script>