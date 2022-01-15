@extends('admin.layouts.app')
@section('content')
<section class="content">
<div class="container-fluid">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('user_roles') ;?>">User Roles</a></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo Adminurl('dashboard') ;?>">Home</a></li>
            <li class="breadcrumb-item active"><a href="<?php echo Adminurl('user_roles') ;?>">User Roles</a></li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><b>Modules & Roles</b></h3>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-borderless table-responsive-sm table-hover table-sm">
          <thead>
            <tr style="text-align:center;" >
              <th style="text-align:left;">Module</th>
              @foreach($userroles as $users)
              <th>{{$users->user_role}}</th>
              @endforeach
            </tr>

          </thead>
          <tbody>
            @foreach ($modules as $module_names)
            <tr style="text-align:center;">
              <td style="text-align:left;">{{$module_names->name}}</td>
              @foreach($userroles as $user_data)
              <td>
                <div class="icheck-success d-inline">
                  <input type="checkbox" <?php if($user_data->user_role == 'Super Admin'){echo "disabled";}?> <?php if($module_names->id == '1' || $module_names->id == '13'){echo "disabled";}?>  id="checkboxSuccess_{{$module_names->id}}_{{$user_data->id}}" <?php $admin_modules = explode(',', $user_data->module); if(in_array($module_names->id, $admin_modules)){echo "checked";}?> onchange="myFunc('{{$module_names->name}}','{{$module_names->id}}','{{$user_data->id}}','{{$user_data->user_role}}')">
                  <label for="checkboxSuccess_{{$module_names->id}}_{{$user_data->id}}">
                  </label>
                </div>
              </td>
              @endforeach
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</section>
</div>
<style type="text/css">

</style>
<script>
  function myFunc(module_name,module_id,user_id,user_role)
  {
    var module_name   = module_name;
    var module_id     = module_id;
    var user_id       = user_id;
    var user_role     = user_role;
    $.ajax({
        url: '<?php echo Adminurl('change_user_access');?>',
        type: 'post',
        data: { module_name:module_name, module_id:module_id, user_id:user_id, "_token": $('#token').val()},
        success: function(response){
            if (response.isSuccessful == 'Enabled')
            {
              toastr.success('"'+response.module+'" module is successfully accessible for "'+user_role+'"',{timeOut: 5000});
            }
            else if(response.isSuccessful == 'Disabled')
            {
              toastr.success('"'+response.module+'" module is successfully inaccessible for "'+user_role+'"',{timeOut: 5000});
            }
            else
            {
              toastr.error('Role couldnot be changed',{timeOut:5000});
            }
          }
      });
  }
</script>
@endsection
