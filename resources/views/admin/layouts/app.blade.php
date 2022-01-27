<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>HLD Charity</title>
  <link rel="icon" href="{{ asset('dist/img/charity.png')}}" />
  {{-- <link rel="icon" href="https://cdn11.bigcommerce.com/s-u3oz6f4oa0/product_images/FHG_Favicon_Circle_Black.png?t=1604508921"/>--}}
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- app level csrf token to be used in ajax calls --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css')}}">
  <!-- Toastr -->
  <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/rezaid.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.css')}}">
  <!-- data table -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.0/css/fixedHeader.dataTables.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet" />
  {{-- custom css --}}
  <link rel="stylesheet" href="{{ asset('dist/css/custom.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  {{-- file manager css --}}
  <link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
  <!-----------------------------------------SCRIPTS--------------------------------------------------->
  <!-- jQuery -->
  <script src="{{ asset('plugins/jquery/jquery.min.js')}}"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.js"></script>


  <!-- jQuery UI 1.11.4 -->
  <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Jquery Form Builder -->
  <script src="{{ asset('dist/js/jquery-editable.min.js')}}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- ChartJS -->
  <script src="{{ asset('plugins/chart.js/Chart.min.js')}}"></script>
  <!-- Sparkline -->
  <script src="{{ asset('plugins/sparklines/sparkline.js')}}"></script>
  <!-- jQuery Knob Chart -->
  <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js')}}"></script>
  <!-- daterangepicker -->
  <script src="{{ asset('plugins/moment/moment.min.js')}}"></script>
  <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
  <!-- TinyMCE Editor -->
  <script src="https://cdn.tiny.cloud/1/78kqo2ouwtf558sfaspken979so15ncapycp2vp2muzpv9cr/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <!-- Summernote -->
  <script src="{{ asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
  <!-- CK Editor -->
  <script src="{{ asset('plugins/ckeditor/ckeditor.js')}}"></script>
  <!-- overlayScrollbars -->
  <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
  <!-- Rezaid App -->
  <script src="{{ asset('dist/js/rezaid.js')}}"></script>
  <!-- Rezaid dashboard demo (This is only for demo purposes) -->
  <script src="{{ asset('dist/js/pages/dashboard.js')}}"></script>
  <!-- Rezaid for demo purposes -->
  <script src="{{ asset('dist/js/demo.js')}}"></script>
  <!-- Toastr -->
  <script src="{{ asset('plugins/toastr/toastr.min.js')}}"></script>
  <!-- Rezaid App -->
  <!-- <script src="{{ asset('dist/js/rezaid.min.js')}}"></script> -->
  <!-- Bootstrap Switch -->
  <script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
  <!-- datatables -->
  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!--Begin Sidebar-->
    @include('admin.includes.sidebar')
    <!--End Sidebar-->
    @include('admin.includes.header')
    <!--Begin Main Panel-->
    <div class="content-wrapper">
      <!-- Navbar -->

      <!-- End Navbar -->
      @yield('content')
      <!--Begin Footer-->
      @include('admin.includes.footer')
      <!--End Footer-->
    </div>
  </div>
  <!--EndMain Panel-->
</body>
{{-- filemanager js --}}
<script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
<script>
  $(document).ready(function() {
    var down = false;
    $('#bell').click(function(e) {
      var color = $(this).text();
      if (down) {

        $('#box').css('height', '0px');
        $('#box').css('opacity', '0');
        down = false;
      } else {
        get_notifications();
        $('#box').css('height', 'auto');
        $('#box').css('opacity', '1');
        down = true;
      }
    });

  });

  setInterval(get_notifications_count, 10000);

  function get_notifications_count() {

    $.get("/notificationscount", function(data, status) {
      $("#count_message").html(data.count);
    });
  }


  function get_notifications() {
    html = "";
    $.get("/notifications", function(data, status) {
      console.log(data.notifications);
      data.notifications.map((item, index) => {
        html += '<div class="notifications-item" onclick="readnotification(' + item.id + ',' + item.link + ',\'' + data.base_url + '\')"><div class="text"><p>' + item.title + '</p></div> </div>';
      });

      $("#box").html(html);
    });
  }

  function readnotification(id, order_id, base_url) {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.post("/readnotifications/" + id, function(data, status) {

      window.location.href = base_url + order_id;
    });
  }
</script>

</html>