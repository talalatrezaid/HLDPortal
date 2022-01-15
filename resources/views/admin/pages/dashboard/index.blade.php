@extends('admin.layouts.app')
@section('content')
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <!-- <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('dashboard') ;?>">Dashboard</a></h1> -->
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo Adminurl('dashboard') ;?>"><i class="fa fa-home"></i></a></li>
              <li class="breadcrumb-item active"><a href="<?php echo Adminurl('dashboard') ;?>">Dashboard</a></li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <section class="content">
      <div class="container-fluid">

          @if(session('user_permission_error'))
              <div class="alert alert-danger" role="alert">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  {{ session('user_permission_error') }}
              </div>
          @endif

        <div class="top_section">
{{--          <div class="row">--}}
{{--              <div class="col-md-3 col-sm-4">--}}
{{--                  <div class="statics_card">--}}
{{--                    <div class="statics_card_icon" style="background-color: #2758ea;">--}}
{{--                      <i class="fa fa-shopping-cart" aria-hidden="true"></i>--}}
{{--                    </div>--}}
{{--                    <div class="statics_card_info">--}}
{{--                      <h3>Orders</h3>--}}
{{--                      <p><span class="currency-symbol">£</span> 20</p>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--              </div>--}}
{{--              <div class="col-md-3 col-sm-4">--}}
{{--                  <div class="statics_card">--}}
{{--                    <div class="statics_card_icon" style="background-color: #ffc107;">--}}
{{--                      <i class="fa fa-usd" aria-hidden="true"></i>--}}
{{--                    </div>--}}
{{--                    <div class="statics_card_info">--}}
{{--                      <h3>Sales</h3>--}}
{{--                      <p><span class="currency-symbol">£</span> 10</p>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--              </div>--}}
{{--              <div class="col-md-3 col-sm-4">--}}
{{--                  <div class="statics_card">--}}
{{--                    <div class="statics_card_icon" style="background-color: #dc3545;">--}}
{{--                      <i class="fa fa-money" aria-hidden="true"></i>--}}
{{--                    </div>--}}
{{--                    <div class="statics_card_info">--}}
{{--                      <h3>Commission</h3>--}}
{{--                      <p><span class="currency-symbol">£</span> 2330</p>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--              </div>--}}
{{--              <div class="col-md-3 col-sm-4">--}}
{{--                  <div class="statics_card">--}}
{{--                    <div class="statics_card_icon" style="background-color: #17a2b8;">--}}
{{--                    <i class="fa fa-line-chart" aria-hidden="true"></i>--}}
{{--                    </div>--}}
{{--                    <div class="statics_card_info">--}}
{{--                      <h3>Avg. daily sale</h3>--}}
{{--                      <p><span class="currency-symbol">£</span> 23</p>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--              </div>--}}
{{--              <div class="col-md-3 col-sm-4">--}}
{{--                  <div class="statics_card">--}}
{{--                    <div class="statics_card_icon" style="background-color: #17a2b8;">--}}
{{--                      <i class="fa fa-line-chart" aria-hidden="true"></i>--}}
{{--                    </div>--}}
{{--                    <div class="statics_card_info">--}}
{{--                      <h3>Avg. monthly sale</h3>--}}
{{--                      <p><span class="currency-symbol">£</span> 145</p>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--              </div>--}}
{{--              <div class="col-md-3 col-sm-4">--}}
{{--                  <div class="statics_card">--}}
{{--                    <div class="statics_card_icon" style="background-color: #2758ea;">--}}
{{--                      <i class="fa fa-truck" aria-hidden="true"></i>--}}
{{--                    </div>--}}
{{--                    <div class="statics_card_info">--}}
{{--                      <h3>Total shipping</h3>--}}
{{--                      <p><span class="currency-symbol">£</span> 2330</p>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--              </div>--}}
{{--              <div class="col-md-3 col-sm-4">--}}
{{--                  <div class="statics_card">--}}
{{--                    <div class="statics_card_icon" style="background-color: #7211a9;">--}}
{{--                      <i class="fa fa-shopping-cart" aria-hidden="true"></i>--}}
{{--                    </div>--}}
{{--                    <div class="statics_card_info">--}}
{{--                      <h3>Total tax</h3>--}}
{{--                      <p><span class="currency-symbol">£</span> 567</p>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--              </div>--}}
{{--              <div class="col-md-3 col-sm-4">--}}
{{--                  <div class="statics_card">--}}
{{--                    <div class="statics_card_icon" style="background-color: #f904cc;">--}}
{{--                      <i class="fa fa-user" aria-hidden="true"></i>--}}
{{--                    </div>--}}
{{--                    <div class="statics_card_info">--}}
{{--                      <h3>Total Customer</h3>--}}
{{--                      <p> 567</p>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--              </div>--}}
{{--          </div>--}}

</div>
{{--      <div class="card mt-3">--}}
{{--            <div class="card-heading">Latest Orders</div>--}}
{{--            <div class="card-body pt-0">--}}
{{--            <div class="table_outer">--}}
{{--              <table class="table">--}}
{{--                <thead class="table-dark">--}}
{{--                  <tr>--}}
{{--                    <th>Oreder ID</th>--}}
{{--                    <th>Customer Name</th>--}}
{{--                    <th>Total</th>--}}
{{--                    <th>Date</th>--}}
{{--                    <th>Status</th>--}}
{{--                  </tr>--}}
{{--                </thead>--}}
{{--                <tbody>--}}
{{--                  <tr>--}}
{{--                    <td>0043555234 </td>--}}
{{--                    <td>Faisal Hameed </td>--}}
{{--                    <td>£450</td>--}}
{{--                    <td> 12-05-2021</td>--}}
{{--                    <td> Confirm</td>--}}
{{--                  </tr>--}}
{{--                  <tr>--}}
{{--                    <td>0043555234 </td>--}}
{{--                    <td>Waqas Akram </td>--}}
{{--                    <td>£250</td>--}}
{{--                    <td> 09-05-2021</td>--}}
{{--                    <td> Deliver </td>--}}
{{--                  </tr>--}}
{{--                  <tr>--}}
{{--                    <td>0043555211 </td>--}}
{{--                    <td>Faran </td>--}}
{{--                    <td>£950</td>--}}
{{--                    <td> 31-05-2021</td>--}}
{{--                    <td> Pending</td>--}}
{{--                  </tr>--}}
{{--                </tbody>--}}
{{--              </table>--}}
{{--            </div>--}}
{{--            </div>--}}
{{--      </div>--}}
      <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="card mt-2">
                <div class="card-heading">Latest Customers</div>
                <div class="card-body pt-0">
                  <div class="table_outer">
                  <table class="table">
                    <thead class="table-dark">
                      <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                      </tr>

                    </thead>
                    <tbody>
                      <tr>
                        <td>Rezaid ltd seller </td>
                        <td>moneeb5966 </td>
                        <td>Moneeb@rezaid.co.uk</td>
                        <td> Active</td>
                      </tr>
                      <tr>
                        <td>Rezaid ltd seller </td>
                        <td>moneeb5966 </td>
                        <td>Moneeb@rezaid.co.uk</td>
                        <td> Active</td>
                      </tr>
                      <tr>
                        <td>Rezaid ltd seller </td>
                        <td>moneeb5966 </td>
                        <td>Moneeb@rezaid.co.uk</td>
                        <td> Active</td>
                      </tr>
                      <tr>
                        <td>Rezaid ltd seller </td>
                        <td>moneeb5966 </td>
                        <td>Moneeb@rezaid.co.uk</td>
                        <td> Active</td>
                      </tr>
                      <tr>
                        <td>Rezaid ltd seller </td>
                        <td>moneeb5966 </td>
                        <td>Moneeb@rezaid.co.uk</td>
                        <td> Active</td>
                      </tr>
                    </tbody>
                  </table>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card mt-2">
                <div class="card-heading">Latest Products</div>
                <div class="card-body pt-0">
                <div class="table_outer">
                  <table class="table">
                    <thead class="table-dark">
                      <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Price</th>
                      </tr>

                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          <img src="https://t4.ftcdn.net/jpg/02/07/87/79/360_F_207877921_BtG6ZKAVvtLyc5GWpBNEIlIxsffTtWkv.jpg" alt="Product Image"/>
                        </td>
                        <td>White Double Strap Chunky Sole Flat Sliders </td>
                        <td>Rezaid ltd : TH181 WHITE</td>
                        <td> £96.00</td>
                      </tr>
                      <tr>
                        <td>
                          <img src="https://t4.ftcdn.net/jpg/02/07/87/79/360_F_207877921_BtG6ZKAVvtLyc5GWpBNEIlIxsffTtWkv.jpg" alt="Product Image"/>
                        </td>
                        <td>White Double Strap Chunky Sole Flat Sliders </td>
                        <td>Rezaid ltd : TH181 WHITE</td>
                        <td> £96.00</td>
                      </tr>
                      <tr>
                        <td>
                          <img src="https://t4.ftcdn.net/jpg/02/07/87/79/360_F_207877921_BtG6ZKAVvtLyc5GWpBNEIlIxsffTtWkv.jpg" alt="Product Image"/>
                        </td>
                        <td>White Double Strap Chunky Sole Flat Sliders </td>
                        <td>Rezaid ltd : TH181 WHITE</td>
                        <td> £96.00</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                </div>
            </div>
        </div>
      </div>
        <div class="row">
          <!-- <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>150</h3>

                <p>New Orders</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div> -->

         <!--  <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3></h3>

                <p>Users</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-secret"></i>
              </div>
            </div>
          </div> -->

      </div>
    </section>
  </div>
<script>
    <?php if(session('not_allowed')){?>
      toastr.error('Sorry! You don&#39;t have permission to access this module.',{timeOut:5000});
    <?php }?>
</script>
@endsection
