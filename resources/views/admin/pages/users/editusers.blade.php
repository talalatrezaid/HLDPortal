@extends('admin.layouts.app')
@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="card-footer">
      <a href="{{Adminurl('users')}}" ><button class="btn btn-success">Go Back</button></a>
    </div>
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Edit User Data</h3>
      </div>
      <form action="<?php echo Adminurl('update-user-data/');?>{{$users[0]->id}}" method="POST">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
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
        @elseif (session('user_update'))
        <div class="alert alert-success" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('user_update') }}
        </div>
        @elseif (session('user_password_reset'))
        <div class="alert alert-success" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('user_password_reset') }}
        </div>
        @elseif (session('user_password_error'))
        <div class="alert alert-danger" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('user_password_error') }}
        </div>
        @elseif (session('user_password_error0'))
        <div class="alert alert-danger" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('user_password_error0') }}
        </div>
        @endif
          <div class="card-body">
            <div class="form-group">
            		@if(Auth::user()->id == '1')
            				@if($users[0]->role_id == '1')
                      <input type="text" name="status_value" id="status_value_{{$users[0]->id}}" value="{{ $users[0]->status }}" hidden="">
                      @elseif($users[0]->role_id != '1')
                      <i class="fas fa-toggle-on"></i>&nbsp;
                      <label for="title">Status:*</label>&emsp;
                      <input type="checkbox" class="make-switch" id="status_{{$users[0]->id}}" name="status" data-on-color="success" data-off-color="danger"  value="true" @if($users[0]->status == '1') checked @endif>
                      <input type="text" name="status_value" id="status_value_{{$users[0]->id}}" value="{{ $users[0]->status }}" hidden="">
            				@endif
            		@else
            			@if(Auth::user()->id == '2' && ($users[0]->role_id != '1' && $users[0]->role_id != '1'))
            				<i class="fas fa-toggle-on"></i>&nbsp;
                    <label for="title">Status:*</label>&emsp;
                    <input type="checkbox" class="make-switch" id="status_{{$users[0]->id}}" name="status" data-on-color="success" data-off-color="danger"  value="true" @if($users[0]->status == '1') checked @endif>
                    <input type="text" name="status_value" id="status_value_{{$users[0]->id}}" value="{{ $users[0]->status }}" hidden="">
            			@else
                  <input type="text" name="status_value" id="status_value_{{$users[0]->id}}" value="{{ $users[0]->status }}" hidden="">
                  @endif
            		@endif
              </div>
            <div class="form-group" >
              <i class="fas fa-user"></i>&nbsp;
              <label for="title">Full Name:*</label>
              <input type="text" class="form-control" value="{{ $users[0]->name }}" name="name">
            </div>

          @if(Auth::user()->id == '1' && $users[0]->role_id == '1')
          	<div class="form-group" >
              <i class="fas fa-user-tag"></i>&nbsp;
              <label for="title">Role:*</label>
              <select class="form-control" name="role" disabled="">
                  @foreach($user_roles as $users1)
                    <option disabled="" value="{{$users1->id}}" {{ $users[0]->role_id == $users1->id ? 'selected' : '' }}>{{$users1->user_role}}</option>
                  @endforeach
                </select>
            </div>
          @elseif(Auth::user()->id == '1')
            <div class="form-group" >
              <i class="fas fa-user-tag"></i>&nbsp;
              <label for="title">Role:*</label>
              <select class="form-control" name="role">
                @foreach($user_roles as $users1)
                	@if($users1->id != '1')
                		<option value="{{$users1->id}}" {{ $users[0]->role_id == $users1->id ? 'selected' : '' }}>{{$users1->user_role}}</option>
                	@endif
                @endforeach
              </select>
            </div>
          @else
            <div class="form-group" >
                <i class="fas fa-user-tag"></i>&nbsp;
                <label for="title">Role:*</label>
                  <select class="form-control" name="role" disabled="">
                  @foreach($user_roles as $users1)
                    <option disabled="" value="{{$users1->id}}" {{ $users[0]->role_id == $users1->id ? 'selected' : '' }}>{{$users1->user_role}}</option>
                  @endforeach
                </select>
            </div>
          @endif
            <div class="form-group">
              <i class="fas fa-user"></i>&nbsp;
              <label for="title">Username:*</label>
              <input type="text" class="form-control" value="{{ $users[0]->user_name }}" name="user_name" disabled="">
            </div>
            <div class="form-group">
              <i class="fas fa-envelope"></i>&nbsp;
              <label for="title">Email:*</label>
              <input type="text" class="form-control" value="{{ $users[0]->email }}" name="email" disabled="">
            </div>

              <div class="form-group" >
                  <i class="fas fa-user-tag"></i>&nbsp;
                  <label for="title">Status:*</label>
                  <select name="status_value" class="user_account_status form-control" data-user_id="{{$users[0]->id}}" id="status_{{$users[0]->id}}" <?php if ($users[0]->status == 1) echo "disabled"; ?>>
                      <option value="1" <?php if ($users[0]->status == 1) echo "selected"; ?>>Active</option>
                      <option value="0" <?php if ($users[0]->status == 0) echo "selected"; ?>>In-Active</option>
                  </select>
              </div>


            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Update Data</button>
            </div>
          </div>
      </form>
      <!-- Second Form -->
      <form action="<?php echo Adminurl('update-user-password/');?>{{$users[0]->id}}" method="POST">
        @csrf
        <div class="card-body">
          <?php if(Session::get('user_name') == $users[0]->user_name){ ?>
          <div class="form-group">
            <i class="fas fa-user-lock"></i>&nbsp;
            <label for="title">Current Password:*</label>
            <input type="password" class="form-control" placeholder="Password" name="old_password" value="{{ session('user_password_error1') }}">
          </div>
          <?php }?>


          {{--<div class="form-group">
            <i class="fas fa-lock"></i>&nbsp;
            <label for="title">New Password:*</label>
             <input type="password" class="form-control" placeholder="Password" name="password" value="{{ session('user_password_error2') }}">
          </div>--}}


              <div class="input-group mb-3">
                  <input type="password" class="form-control" id="user_password" placeholder="Password" name="password" value="{{ session('user_password_error2') }}">
                  <div class="input-group-append">
                      <div class="input-group-text">
                          <span class='fa fa-eye' id='show-hide-pass'></span>
                      </div>
                  </div>
              </div>


          {{--<div class="form-group">
            <i class="fas fa-lock"></i>&nbsp;
            <label for="title">Confirm New Password:*</label>
            <input type="password" class="form-control" placeholder="Re-type Password" name="retype_password" value="{{ session('user_password_error3') }}">
          </div>--}}

              <div class="input-group mb-3">
                  <input type="password" class="form-control" id="re_user_password" placeholder="Re-type Password" name="retype_password" value="{{ session('user_password_error3') }}">

                  <div class="input-group-append">
                      <div class="input-group-text">
                          <span class='fa fa-eye' id='show-hide-pass-2'></span>
                      </div>
                  </div>
              </div>



          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Password</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
</div>
<script type="text/javascript">
 $(".make-switch").bootstrapSwitch({
  onSwitchChange: function(e, state) {
  var id = e.target.id;
  var strArray = id.split("_");
  var x = $('#status_value_'+strArray[1]).val();
  if(x == '1')
  {
  var r = confirm("Are you sure you want to Disable this User?");
  }
  else
    {
      var r = confirm("Are you sure you want to Enable this User?");
    }
    if(r==true){
      var id = e.target.id;
      var strArray = id.split("_");
        if(state==true)
        {
          $('#status_value_'+strArray[1]).val('1');
        }
        else
        {
          $('#status_value_'+strArray[1]).val('0');
        }
      var status = $('#status_value_'+strArray[1]).val();
      var id = strArray[1];
      $.ajax({
        url: '<?php echo Adminurl('updateStatus');?>',
        type: 'post',
        data: { id:id, status:status, "_token": $('#token').val()},
        success: function(response){
            if (response == "The User has been successfully Enabled.")
            {
              toastr.success('The User has been successfully Enabled.',{timeOut: 5000});
            }
            else
            {
              toastr.error('The User has been successfully Disabled.',{timeOut: 5000});
            }
          }
      });
    }
    else{
      return false;
    }
  }
});

</script>


<script>
    $(document).on('change','.user_account_status', function () {

        var r = confirm("Are you sure you want to Enable this User?");
        if(r==true){
            var status = this.value;
            var id = $(this).data("user_id");

            $.ajax({
                url: '<?php echo Adminurl('updateStatus');?>',
                type: 'post',
                data: { id:id, status:status, "_token": $('#token').val()},
                success: function(response){
                    if (response == "The User has been successfully Enabled.")
                    {
                        toastr.success('The User has been successfully Enabled.',{timeOut: 5000});
                        $(".user_account_status").prop('disabled', true);

                    }
                    else
                    {
                        toastr.error('The User has been successfully Disabled.',{timeOut: 5000});
                    }
                }
            });
        }
    });
</script>

<script>
    // password revealed functionality
    $(document).ready(function(){

        $(document).on('click', '#show-hide-pass', function(){
            var x = document.querySelector("#user_password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        });

        $(document).on('click', '#show-hide-pass-2', function(){
            var x = document.querySelector("#re_user_password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        });

    });
</script>
@endsection
