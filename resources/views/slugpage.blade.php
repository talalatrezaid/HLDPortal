@extends('admin.layouts.app')
@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"><a href="{{Adminurl(''.$page[0]->slug.'')}}">{{$page[0]->title}}</a></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{Adminurl('dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active"><a href="{{Adminurl(''.$page[0]->slug.'')}}">{{$page[0]->title}}</a></li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">{{$page[0]->title}}</h3>
      </div>
    </div>
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-10 card">
        <br>
        <?php
        $page[0]->content = str_replace('<p><code>','',$page[0]->content);
        $page[0]->content = str_replace('</code></p>','',$page[0]->content);
        echo shortCode($page[0]->content);
        ?>
      </div>
      <div class="col-md-1"></div>
    </div>
  </div>
</section>
</div>
@endsection

