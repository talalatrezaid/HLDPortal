@extends('admin.layouts.app')
@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('users'); ?>">Users</a></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo Adminurl('dashboard'); ?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?php echo Adminurl('users'); ?>">Users</a></li>
            </ol>
          </div>
        </div>


        {{--<div class="col-sm-6">
      <?php if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2) { ?>
        <a href="<?php echo Adminurl('users/register'); ?>" ><button class="btn btn-success">Add User</button></a>
      <?php } ?>
    </div>--}}


      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          @if (session('create_success'))
          <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('create_success') }}
          </div>
          @elseif (session('user_added'))
          <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('user_added') }}
          </div>
          @elseif(session('delete_user_success'))
          <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('delete_user_success') }}
          </div>
          @elseif(session('delete_users_faliure'))
          <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('delete_users_faliure') }}
          </div>
          @elseif(session('delete_users_faliure1'))
          <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('delete_users_faliure1') }}
          </div>
          @elseif(session('delete_users_faliure3'))
          <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('delete_users_faliure3') }}
          </div>
          @elseif(session('update_user_error'))
          <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('update_user_error') }}
          </div>
          @elseif(session('update_user_error1'))
          <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('update_user_error1') }}
          </div>
          @elseif(session('add_user_faliure'))
          <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('add_user_faliure') }}
          </div>
          @endif

          <div id="status"></div>

          <div class="card-header">
            <h3 class="card-title">Users Table</h3>
          </div>
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <div class="card-body table-responsive p-0">
            <table class="table table-hover table-sm table-bordered">
              <thead>
                <tr style="text-align:center;">
                  <th>ID</th>
                  <th>Name</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @php
                $current_page = $users->currentPage();
                $per_page = $users->PerPage();
                if ($current_page == 1) {
                $i = 1;
                } else {
                $current_page = $current_page - 1;
                $i = $current_page * $per_page + 1;
                }
                @endphp
                @foreach ($users as $userdata)
                <tr style="text-align:center;">
                  <td>{!! @$i !!}</td>
                  <td>{{ $userdata->name }}</td>
                  <td>{{ $userdata->user_name }}</td>
                  <td>{{ $userdata->email }}</td>
                  <td>
                    @foreach($user_roles as $user_roles_1)
                    <?php if ($userdata->role_id == $user_roles_1->id) {
                      echo $user_roles_1->user_role;
                    } ?>
                    @endforeach
                  </td>
                  <td>
                    <?php
                    if (Auth::user()->role_id == 1) {
                      if ($userdata->role_id != 1) {
                    ?>
                        <?php
                        if ($userdata->status == 1) {
                          echo "Active";
                        } else {
                          echo "In-Active";
                        }
                        ?>

                        {{--<input type="checkbox" class="make-switch" id="status_{{$userdata->id}}" name="status" data-on-color="success" data-off-color="danger" value="true" @if($userdata->status == '1') checked @endif>--}}
                        {{--<input type="text" name="status_value" id="status_value_{{$userdata->id}}" value="{{ $userdata->status }}" hidden="">--}}
                      <?php }
                    } elseif (Auth::user()->role_id == 2) {
                      if ($userdata->role_id != 1 && $userdata->role_id != 2) { ?>
                        <input type="checkbox" class="make-switch" id="status_{{$userdata->id}}" name="status" data-on-color="success" data-off-color="danger" value="true" @if($userdata->status == '1') checked @endif>
                        <input type="text" name="status_value" id="status_value_{{$userdata->id}}" value="{{ $userdata->status }}" hidden="">
                    <?php }
                    } ?>
                  </td>
                  <td>
                    <?php
                    if (Auth::user()->role_id == 1) { ?>
                      <a href="<?php echo Adminurl('users/edit/'); ?>{{$userdata->id}}"><button class="btn btn-primary">EDIT</button></a>
                      <?php } elseif (Auth::user()->role_id == 2) {
                      if ($userdata->role_id != 2 && $userdata->role_id != 1 || $userdata->user_name == Auth::user()->user_name) { ?>
                        <a href="<?php echo Adminurl('users/edit/'); ?>{{$userdata->id}}"><button class="btn btn-primary">EDIT</button></a>
                      <?php }
                    } elseif ($userdata->user_name == Auth::user()->user_name) { ?>
                      <a href="<?php echo Adminurl('users/edit/'); ?>{{$userdata->id}}"><button class="btn btn-primary">EDIT</button></a>
                      <?php }
                    if (Auth::user()->role_id == 1) {
                      if ($userdata->role_id != 1) { ?>
                        <button onclick="mydelete('{{$userdata->id}}')" class="btn btn-danger">DELETE</button>
                      <?php }
                    } elseif (Auth::user()->role_id == 2) {
                      if ($userdata->role_id != 1 && $userdata->role_id != 2) { ?>
                        <button onclick="mydelete('{{$userdata->id}}')" class="btn btn-danger">DELETE</button>
                    <?php }
                    } ?>
                  </td>
                </tr>
                @php
                $i++;
                @endphp
                @endforeach
              </tbody>
            </table>

          </div>

        </div>
      </div>
    </div>
    <div style="float: right;" class="row">
      <b style="margin: 8px;">Total
        Records:{{ $users->total() }}</b>&emsp;{{ $users->withQueryString()->links('pagination::bootstrap-4') }}
    </div>
  </div>
</section>
<script>
  function mydelete(id) {
    var r = confirm("Are You Sure You Want to Delete this User?");
    if (r == true) {
      window.location.href = "<?php echo Adminurl('user/delete/'); ?>" + id;
    } else {
      return false;
    }
  }
</script>
</div>
<script type="text/javascript">
  $(".make-switch").bootstrapSwitch({
    onSwitchChange: function(e, state) {
      var id = e.target.id;
      var strArray = id.split("_");
      var x = $('#status_value_' + strArray[1]).val();
      if (x == '1') {
        var r = confirm("Are you sure you want to Disable this User?");
      } else {
        var r = confirm("Are you sure you want to Enable this User?");
      }
      if (r == true) {
        var id = e.target.id;
        var strArray = id.split("_");
        if (state == true) {
          $('#status_value_' + strArray[1]).val('1');
        } else {
          $('#status_value_' + strArray[1]).val('0');
        }
        var status = $('#status_value_' + strArray[1]).val();
        var id = strArray[1];
        $.ajax({
          url: '<?php echo Adminurl('updateStatus'); ?>',
          type: 'post',
          data: {
            id: id,
            status: status,
            "_token": $('#token').val()
          },
          success: function(response) {
            if (response == "The User has been successfully Enabled.") {
              toastr.success('The User has been successfully Enabled.', {
                timeOut: 5000
              });
            } else {
              toastr.error('The User has been successfully Disabled.', {
                timeOut: 5000
              });
            }
          }
        });
      } else {
        return false;
      }
    }
  });
</script>

@endsection