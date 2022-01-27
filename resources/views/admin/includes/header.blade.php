  <!-- Navbar -->
  <style>
    .icon {
      cursor: pointer;
    }

    .icon span {
      background: #f00;
      padding: 7px;
      border-radius: 50%;
      color: #fff;
      vertical-align: top;
      margin-left: -25px
    }

    .icon img {
      display: inline-block;
      width: 26px;
      margin-top: 4px
    }

    .icon:hover {
      opacity: .7
    }

    .logo {
      flex: 1;
      margin-left: 50px;
      color: #eee;
      font-size: 20px;
      font-family: monospace
    }

    .notifications {
      width: 300px;
      height: 0px;
      opacity: 0;
      position: absolute;
      top: 63px;
      right: 62px;
      border-radius: 5px 0px 5px 5px;
      background-color: #fff;
      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)
    }

    .notifications h2 {
      font-size: 14px;
      padding: 10px;
      border-bottom: 1px solid #eee;
      color: #999
    }

    .notifications h2 span {
      color: #f00
    }

    .notifications-item {
      display: flex;
      border-bottom: 1px solid #eee;
      padding: 6px 9px;
      margin-bottom: 0px;
      cursor: pointer
    }

    .notifications-item:hover {
      background-color: #eee
    }

    .notifications-item img {
      display: block;
      width: 50px;
      height: 50px;
      margin-right: 9px;
      border-radius: 50%;
      margin-top: 2px
    }

    .notifications-item .text h4 {
      color: #777;
      font-size: 16px;
      margin-top: 3px
    }

    .notifications-item .text p {
      color: #aaa;
      font-size: 12px
    }
  </style>
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <!--  <li class="nav-item d-none d-sm-inline-block" style="cursor: pointer;">
        <a href="<?php echo Adminurl('dashboard'); ?>" class="nav-link">Home</a>
      </li> -->
      <!--  <li class="nav-item d-none d-sm-inline-block" style="cursor: not-allowed;">
        <a class="nav-link"><b>{{Auth::user()->name,'saanat'}}</b>&ensp;({{getRolename(Auth::user()->role_id)}})</a>
      </li> -->
    </ul>
    <ul class="navbar-nav ml-auto" id="">
      <!--  <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo Adminurl('logout'); ?>" class="nav-link">Logout&nbsp;<i class="fas fa-power-off"></i></a>
      </li> -->
      <div class="icon" id="bell"> <img src="https://i.imgur.com/AC7dgLA.png" alt=""></div> <span class="badge badge-sm badge-danger" id="count_message">0</span>
      <div class="notifications" id="box">
        <!-- <h2>Notifications - <span>2</span></h2> -->
        <div>please wait...</div>
      </div>
      <li class="nav-item d-none d-sm-inline-block sanata" style="cursor: not-allowed;">
        <a class="nav-link"><b>HLD Charity</b>&ensp;</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->