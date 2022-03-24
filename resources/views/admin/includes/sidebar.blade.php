<?php
$role_id = Session::get('id');
$user_modules = DB::table('user_roles')->where('id', Auth::user()->role_id)->get();
$allowed_modules = explode(',', $user_modules[0]->module);
$modules_id = DB::table('modules')->pluck('id')->toArray();
$modules = DB::table('modules')->orderBy('menu_position', 'ASC')->get();
if (request()->segment(1) == env('ADMIN_PREFIX')) {
  $main_menu = request()->segment(2);
  $child_menu = request()->segment(3);
} else {
  $main_menu = request()->segment(1);
  $child_menu = request()->segment(2);
}
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  {{--<a href="<?php echo Adminurl('dashboard'); ?>" class="brand-link">
    <img src="{{ asset('dist/img/RezaidLogo.png') }}" alt="Rezaid Logo" class="brand-image img-circle elevation-3"
  style="opacity: .8">
  <span class="brand-text font-weight-light">Rezaid</span>
  </a>--}}
  <a href="<?php echo Adminurl('dashboard'); ?>" class="brand-link">
    <span class="brand-text font-weight-light">HLD Charity</span>
  </a>
  <div class="sidebar">

    <nav class="mt-2">
      <div class="user_info">
        <div class="user_img">
          <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSE26NjQaonqTRt7BXD_87Iuukitk_kcGBv3w&usqp=CAU" alt="user image" />
        </div>
        <div class="user_detail">

          <p><b>@php echo Auth::user()->user_name; @endphp</b></p>
          <p>@php echo Auth::user()->email; @endphp</p>
        </div>
      </div>
      <div class="clearfix"></div>
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">


        <li class="nav-item">
          <a href="<?php echo Adminurl('users'); ?>" class="nav-link <?php if ($content == "users") {
                                                                        echo "active";
                                                                      } ?>">
            <i class="fas fa-arrow-alt-circle-right nav-icon"></i>
            <p>All Users</p>
          </a>
        </li>


        <li class="nav-item">
          <a href="<?php echo Adminurl('charities'); ?>" class="nav-link <?php if ($content == "charities") {
                                                                            echo "active";
                                                                          } ?>">
            <i class="fas fa-hand-holding-heart nav-icon"></i>
            <p>All Charities</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo Adminurl('products'); ?>" class="nav-link <?php if ($content == "products") {
                                                                          echo "active";
                                                                        } ?>">
            <i class="nav-icon fa fa-product-hunt"></i>
            <p>Products</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo Adminurl('store_connection'); ?>" class="nav-link <?php if ($content == "store") {
                                                                                  echo "active";
                                                                                } ?>">
            <i class="nav-icon fa fa-link"></i>
            <p>Store Connection</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo Adminurl('product_categories'); ?>" class="nav-link <?php if ($content == "categories") {
                                                                                    echo "active";
                                                                                  } ?>">
            <i class="nav-icon fa fa-list-alt"></i>
            <p>Product Categories</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?php echo Adminurl('orders'); ?>" class="nav-link <?php if ($content == "orders") {
                                                                        echo "active";
                                                                      } ?>">
            <i class="nav-icon fa fa-shopping-cart"></i>
            <p>Orders Management</p>
          </a>
        </li>


        <li class="nav-item">
          <a href="<?php echo Adminurl('settings'); ?>" class="nav-link <?php if ($content == "settings") {
                                                                          echo "active";
                                                                        } ?>">
            <i class="nav-icon fa fa-gear"></i>
            <p>Settings</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?php echo Adminurl('shippingslots'); ?>" class="nav-link <?php if ($content == "shippingslots") {
                                                                                echo "active";
                                                                              } ?>">
            <i class="nav-icon fa fa-truck"></i>
            <p>Shipping Slots</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?php echo Adminurl('logout'); ?>" class="nav-link"> <i class="nav-icon fas fa-power-off"></i> &nbsp;<P>Logout</P> </a>
        </li>
      </ul>

    </nav>
  </div>
</aside>