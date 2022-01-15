@extends('admin.layouts.app')
@section('content')
<section class="content">
  <div class="container-fluid">
  <div class="card-footer">
    <a href="<?php echo Adminurl('pages') ;?>" ><button class="btn btn-success">Go Back</button></a>
  </div>
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Edit Page Data</h3>
      </div>
      <form action="<?php echo Adminurl('updatepage/') ;?>{{$page[0]->id}}" method="POST" role="form" class="php-email-form" enctype="multipart/form-data" id="editform" name="editform">
      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
      @csrf
      @if(session('update_success'))
        <div class="alert alert-success" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('update_success') }}
        </div>
        @elseif(session('image_type_error'))
        <div class="alert alert-danger" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('image_type_error') }}
        </div>
        @elseif(session('update_success_slug'))
        <div class="alert alert-success" role="alert">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('update_success_slug') }}
        </div>
      @elseif (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
      @endif
      <div class="form-group" style="float: right;margin: 20px;">
          <input type="submit" id="submit" name="submit" value="Update" class="btn btn-primary"/>
      </div>
  <div class="card-body custom_fix">
    <div class="form-group ">
      <label for="title">Page Status:</label>
      <select class="form-control" name="page_status" style="width:120px; cursor: pointer;">
        <option value="Publish" {{ $page[0]->page_status == 'Publish' ? 'selected' : '' }}>Publish</option>
        <option value="Draft"   {{ $page[0]->page_status == 'Draft' ? 'selected' : '' }}>Draft</option>
      </select>
    </div>
    <div class="form-group ">
      <label for="title">Title:*</label>
      <input type="text" class="form-control" id="title1" placeholder="Title" value="{{ $page[0]->title }}" name="title" onkeyup="mySlugFunction()">
    </div>
    <div class="form-group">
      <label for="title">Slug:*</label>
      <a href="<?php echo Adminurl() ;?>{{ $page[0]->slug }}" id="url_link"><span id="url"> <?php echo Adminurl() ;?>{{ $page[0]->slug }}</span></a>
      <input type="text" class="form-control" id="slug1" placeholder="Slug" value="{{ $page[0]->slug }}" name="slug" onkeyup="mySlugFunction2()">
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-outline card-info">
          <div class="card-header">
            <label for="title">Content:</label>
            <div class="card-tools">
              <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                <i class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool btn-sm" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                <i class="fas fa-times"></i></button>
            </div>
          </div>
          <div class="card-body pad">
            <div class="mb-3">
              <textarea placeholder="Content" class="custom_class" name="content">{{ $page[0]->content }}</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    @if(!empty($page[0]->featured_image))
    <div class="form-group">
      <label for="exampleInputFile">Featured Image:</label>
      <div>
        	<img name="featured_image" src="{{ Storage::url('images/pages/'.$page[0]->featured_image)  }}" alt="Featured Image" width="150" height="auto">
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputFile">Update Featured Image:</label>
      <div class="input-group">
        <div class="custom-file">
          <div class="input-group">
            <div class="custom-file">
              <div class="input-group">
                <input type="text" id="newimage" class="form-control max-width" name="newimage" value="{{ session('edit_faliure4') }}"
                       aria-label="Image" aria-describedby="button-image" readonly onchange="updateFeaturedImage(this,{{ $page[0]->id }})">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="newimage_btn">Select</button>
                </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    <span style="color: red;font-size: 12px;">(<b>Supported file types:</b>&nbsp; jpg, png, bmp, gif & svg.&nbsp; <b>Maximum Size:</b>&nbsp;2mb)</span>
    </div>
    @else
    <div class="form-group">
      <label for="exampleInputFile">Featured Image:</label>
      <div class="input-group">
        <div class="custom-file">
          <div class="input-group">
            <input type="text" id="image_label" class="form-control max-width" name="featured_image" value="{{ session('edit_faliure1') }}"
                   aria-label="Image" aria-describedby="button-image" readonly onchange="addFeaturedImage(this)">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="button-image">Select</button>
            </div>
        </div>
        </div>
      </div>
       <span style="color: red;font-size: 12px;">(<b>Supported file types:</b>&nbsp; jpg, png, bmp, gif & svg.&nbsp; <b>Maximum Size:</b>&nbsp;2mb)</span>
    </div>
    @endif
    <h5><b>Add more fields:</b></h5>
    <div id='TextBoxesGroup'>
    <?php $counterText1=1; $counterArea1=1;$counterFile1=1; $countRichText=1;?>
    @if(!empty($customdata))
      @foreach ($customdata as $data)
        @if ($data->Type == "text" && $data->is_image <> 1)
        <div id="TextBoxDiv<?php echo $counterText1;?>" class="container-fluid">
          <div class="card">
            <div class="card-header min-height65">
              <input type="button" class="customclass close btn" value="x"  aria-label="Close" id="DeleteTextButton<?php echo $counterText1;?>" onclick="deletefunc()" />
            </div>
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-md-1">
                  <i class="fas fa-expand-arrows-alt"></i>
                </div>
                <div class="col-md-10">
                  <span class="spanhover" id="textID<?php echo $counterText1;?>"><b>{{$data->Custom_Attribute}}</b></span>
                  <input class="form-control" type="{{$data->Type}}" name="{{$data->Name}}" custom_attr="{{$data->Custom_Attribute}}" value="{{$data->Value}}">
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php  $counterText1++;?>
        @continue
        @elseif($data->Type == "text" &&  $data->is_image == 1)
      <div id="FileDiv<?php echo $counterFile1;?>" class="container-fluid">
        <div class="card">
          <div class="card-header min-height65">
            <input type="button" class="customclass close btn btn-sm" value="x"  aria-label="Close" id="DeleteFileButton<?php echo $counterFile1;?>" onclick="deleteFuncFile('{{$data->Name}}','{{$data->Value}}','{{$page[0]->id}}')"/>
          </div>
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-1">
                <i class="fas fa-expand-arrows-alt"></i>
              </div>
              <div class="col-md-10">
                <span class="spanhover" id="fileID<?php echo $counterFile1;?>"><b>{{$data->Custom_Attribute}}:</b></span>
                &nbsp;&nbsp;<span style="color: red;font-size: 12px;">(<b>Supported file types:</b>&nbsp; pdf, doc, xls, jpg, png, bmp, gif & svg <b>Maximum Size:</b>&nbsp;2Mb)</span>
                <div class="input-group">
                  <input custom_attr="File"  type="text" class="form-control max-width" aria-label="Image" aria-describedby="button-image{{$counterFile1}}" name="{{$data->Name}}" id="image{{$counterFile1}}" value="{{$data->Value}}" onchange="getFileName(this,'{{$page[0]->id}}')" data-image="1" readonly>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="button-image{{$counterFile1}}" data-count="{{$counterFile1}}">Select
                      </button>
                    </div>
                  </div>
                <br>
                   <?php $extension = substr(strrchr($data->Value, "."), 1);
                        $allowedfileExtensions = array('PDF','DOC','DOCX','XLS','XLSX','pdf','doc','docx','xls','xlsx');
                          if (in_array($extension, $allowedfileExtensions))
                            { ?>
                              <label>File:&ensp;</label>
                              <a href="{{ Storage::url('images/pages/custom_files/'.$data->Value)  }}" class="btn btn-outline-primary btn-sm" role="button" target="_blank">Download File</a>
                      <?php }
                          else
                            { ?>
                              <label>Image:&ensp;&ensp;</label>
                              <img src="{{ Storage::url('images/pages/custom_files/'.$data->Value)  }}" alt="Image" width="150" height="auto">
                      <?php } ?>
                </div>
            </div>
          </div>
        </div>
      </div>
      <?php $counterFile1++;?>
      @continue
      @elseif($data->Type == "textarea")
        <div id="TextAreaDiv<?php echo $counterArea1;?>" class="container-fluid">
          <div class="card">
            <div class="card-header min-height65">
              <input type="button" class="customclass close btn" value="x"  aria-label="Close" id="DeleteAreaButton<?php echo $counterArea1;?>" onclick="deletefunc()"/>
            </div>
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-md-1">
                  <i class="fas fa-expand-arrows-alt"></i>
                </div>
                <div class="col-md-10">
                  <span class="spanhover" id="textareaID<?php echo $counterArea1;?>"><b>{{$data->Custom_Attribute}}</b></span>
                  <textarea type="textarea" rows="8" class="form-control" name="{{$data->Name}}" custom_attr="{{$data->Custom_Attribute}}"><?php echo preg_replace("<<br>>", "\n", $data->Value)?></textarea>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <?php $counterArea1++;?>
        @continue
        @elseif($data->Type == "textarea1")
        <div id="RichTextArea<?php echo $countRichText;?>" class="container-fluid">
          <div class="card">
            <div class="card-header min-height65">
              <input type="button" class="customclass close btn" value="x"  aria-label="Close" id="DeleteRichTextButton<?php echo $countRichText;?>" onclick="deletefunc()"/>
            </div>
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-md-1">
                  <i class="fas fa-expand-arrows-alt"></i>
                </div>
                <div class="col-md-10">
                  <span class="spanhover" id="richtextarea<?php echo $countRichText;?>"><b>{{$data->Custom_Attribute}}</b></span>
                  <textarea type="textarea1" rows="8" class="custom_class" id="<?php echo $countRichText;?>" name="{{$data->Name}}" custom_attr="{{$data->Custom_Attribute}}"><?php echo preg_replace("<<br>>", "\n", $data->Value)?></textarea>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <?php $countRichText++;?>
        @continue
        @endif
      @endforeach
      @endif
    </div>
      <input type='button' value='Add Text Field' id='addTextButton' class="btn btn-dark btn-sm">
      <input type='button' value='Add Textarea' id='addTextAreaButton' class="btn btn-dark btn-sm">
      <input type='button' value='Add Rich TextArea' id='addRichTextAreaButton' class="btn btn-dark btn-sm">
      <input type='button' value='Add File' id='addFileButton' class="btn btn-dark btn-sm">
    </div>
<!-- Seo Div -->
  <div class="card-footer">
    <label>SEO:</label>
    <div class="col-12">
      <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
          <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true"><i class="fas fa-circle"></i> SEO</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false"><i class="fas fa-share-alt"></i> Social</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content" id="custom-tabs-one-tabContent">
            <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
              <div class="form-group">
                <label style="cursor: pointer;"><input type="radio" name="selectView" value="desktop" checked=""> Desktop View</label>&emsp;
                <label style="cursor: pointer;"><input type="radio" name="selectView" value="mobile"> Mobile View</label>
                <div class="desktop box1 box form-group shadow p-3 mb-5 bg-white rounded col-md-6" >
                  <div class="form-group">
                    <span id="page_title" style="color: blue;"><b><?php if(!empty($seo[0]->seo_title)){echo $seo[0]->seo_title;}else{echo "Rezaid";}?></b></span><br>
                    <a href="<?php echo Adminurl() ;?>{{ $seo[0]->seo_slug }}" id="page_link"><span id="page_url" style="color: green;"><?php echo Adminurl() ;?>{{ $seo[0]->seo_slug }}</span></a>
                  </div>
                  <hr class="new4">
                  <div class="form-group">
                    <span id="page_description"><?php if(!empty($seo[0]->seo_description)){ echo substr($seo[0]->seo_description, 0, 110)."...";}else{echo "Please provide a meta description by editing the snippet below. If you don’t, Google will try to find a relevant part of your post to show in the search results.";}?></span>
                  </div>
                </div>
                <div class="mobile box form-group shadow p-3 mb-5 bg-white rounded col-md-6" >
                  <div class="form-group">
                    <a href="<?php echo Adminurl() ;?>/{{ $seo[0]->seo_slug }}" id="page_link_1"><span id="page_url_1" style="color: grey;"><?php echo Adminurl() ;?>/{{ $seo[0]->seo_slug }}</span></a><br>
                    <span id="page_title_1" style="color: #5c93df;"><?php if(!empty($seo[0]->seo_title)){echo $seo[0]->seo_title;}else{echo "Rezaid";}?></span>
                  </div>
                  <hr class="new4">
                  <div class="form-group">
                    <span id="page_description_1"><?php if(!empty($seo[0]->seo_description)){ echo substr($seo[0]->seo_description, 0, 110)."...";}else{echo "Please provide a meta description by editing the snippet below. If you don’t, Google will try to find a relevant part of your post to show in the search results.";}?></span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="title">SEO Title:</label>
                <input type="text" class="form-control" name="seo_title" id="seo_title" placeholder="SEO Title" value="{{ $seo[0]->seo_title }}" onkeyup="changeTitle()">
              </div>
              <div class="form-group">
                <label for="title">Slug:</label>
                <input type="text" class="form-control" id="seo_slug" name="seo_slug" placeholder="SEO Slug" value="{{ $seo[0]->seo_slug }}" onkeyup="mySlugFunction3()">
              </div>
              <div class="form-group">
                <label for="title">Meta Description:</label>
                <textarea onkeyup="countChar(this,'seo_count')" maxlength="200" class="form-control" name="seo_description" id="seo_description" placeholder="Meta Description" rows="5" onkeyup="changeMetaDesc()">{{ $seo[0]->seo_description }}</textarea>
                <div id="seo_count" style="float: right;color:black"></div>
              </div>
            </div>
            <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
              <!-- Facebook Div -->
              <div class="col-12">
                <div class="card bg-gradient-primary">
                  <div class="card-header">
                    <h3 class="card-title"><i class="fab fa-facebook-f"></i>&emsp;Facebook</h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                  </div>
                  <div class="card-body" style="display: block; background-color: white;">
                    <div class="form-group">
                      <label for="title" style="color: black;">Facebook Title:</label>
                      <input type="text" class="form-control" name="fb_title" id="fb_title" placeholder="Facebook Title" value="{{ $seo[0]->fb_title }}">
                    </div>
                    <div class="form-group">
                      <label for="title" style="color: black;">Facebook Description:</label>
                      <textarea onkeyup="countChar(this,'fb_count')" maxlength="200" class="form-control" name="fb_description" id="fb_description" rows="5" placeholder="Facebook Description">{{ $seo[0]->fb_description }}</textarea>
                      <div id="fb_count" style="float: right;color:black"></div>
                    </div>
                    <div class="form-group">
                      <label for="title" style="color: black;">Facebook Image:</label>
                      <div class="input-group">
                        <input type="text" id="fb_image" class="form-control max-width" name="fb_image" value="{{ session('edit_faliure2') }}"
                               aria-label="Image" aria-describedby="fb_image_btn" readonly onchange="updateFeaturedImage(this,{{ $page[0]->id }})">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="fb_image_btn">Select</button>
                        </div>
                    </div><br>

                      @if(!empty($seo[0]->fb_image))
                      <label style="color: black;">Facebook Image:</label>
                      <img src="{{ Storage::url('images/social/pages/facebook/'.$seo[0]->fb_image)  }}" alt="Facebook Image"  width="150" height="auto">
                      @endif
                      <br><span style="color: red;font-size: 12px;">(<b>Supported file types:</b>&nbsp; jpg, png, bmp, gif & svg.&nbsp; <b>Maximum Size:</b>&nbsp;2mb)</span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Twitter Div -->
              <div class="col-12">
                <div class="card bg-gradient-primary collapsed-card">
                  <div class="card-header">
                    <h3 class="card-title"><i class="fab fa-twitter"></i>&emsp;Twitter</h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                  </div>
                  <div class="card-body" style="display: none; background-color: white;">
                    <div class="form-group">
                      <label for="title" style="color: black;">Twitter Title:</label>
                      <input type="text" class="form-control" name="tw_title" id="tw_title" placeholder="Twitter Title" value="{{ $seo[0]->tw_title }}">
                    </div>
                    <div class="form-group">
                      <label for="title" style="color: black;">Twitter Description:</label>
                      <textarea onkeyup="countChar(this,'tw_count')"  maxlength="200" class="form-control" name="tw_description" id="tw_description" rows="5" placeholder="Twitter Description">{{ $seo[0]->tw_description }}</textarea>
                      <div id="tw_count" style="float: right;color:black"></div>
                    </div>
                    <div class="form-group">
                      <label for="title" style="color: black;">Twitter Image:</label>
                      <div class="input-group">
                        <input type="text" id="tw_image" class="form-control max-width" name="tw_image" value="{{ session('edit_faliure3') }}"
                               aria-label="Image" aria-describedby="tw_image_btn" readonly onchange="updateFeaturedImage(this,{{ $page[0]->id }})">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="tw_image_btn">Select</button>
                        </div>
                    </div><br>
                      @if(!empty($seo[0]->tw_image))
                      <label style="color: black;">Twitter Image:</label>
                      <img src="{{ Storage::url('images/social/pages/twitter/'.$seo[0]->tw_image)  }}" alt="Twitter Image"  width="150" height="auto">
                      @endif
                      <br><span style="color: red;font-size: 12px;">(<b>Supported file types:</b>&nbsp; jpg, png, bmp, gif & svg.&nbsp; <b>Maximum Size:</b>&nbsp;2mb)</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<div class="card-footer">
    <textarea name="custom_fields" id="custom_fields" class="form-control" hidden=""></textarea>
    <input type="text" name="deleted_files_name" id="deleted_files_name" hidden="">
    <input type="text" name="deleted_files_name_1" id="deleted_files_name_1" hidden="">
    <div style="float: right;">
      <input type="submit" id="submit1" name="submit1" value="Update" class="btn btn-primary"/>
    </div>
  </div>
</form>
</div>
</div>
</div>
</section>
</div>
<style type="text/css">
 .custom_class{}
 .custom_fix .min-height65 {
    min-height: 30px;
    padding: 4px;
}
.custom_fix input.close.btn {
    font-size: 16px;
    font-weight: 700;
    line-height: 3px;
padding: 6px 7px 10px;
}
.custom_fix .card-body {
   padding-top: 5px;
    padding-bottom: 10px;
}
.custom_fix .spanhover {
    display: inline-block;
    margin-bottom: 5px;
}
.custom_fix .ui-sortable .row.align-items-center .col-md-1 {
    max-width: 40px;
}
  .min-height65{min-height: 65px;}
    .customclass {
      display: none;
    }
    .card-header:hover .customclass {
      display: inline-block;
    }
    .customclass:hover {
      background-color: #FE0000;
      color: white;
    }
    .myclass:hover{
       border-color: #fff;
    }
    hr.new4 {
    border: 1px solid lightgrey;
  }
    .spanhover{
      border-bottom: dashed 1px #0088cc;
      cursor: pointer;
    }
    .box{
      color: #fff;
      padding: 20px;
      display: none;
      margin-top: 20px;
  }
  .box1{
      color: #fff;
      padding: 20px;
      display: block;
      margin-top: 20px;
  }
</style>
<script>
  customData = '{!! @json_encode($customdata) !!}';
  customData = JSON.parse(customData);
featured_image = '{{@$page[0]->featured_image}}';
//file manager event listeners
document.addEventListener("DOMContentLoaded", function() {
    //featured image update
    if(featured_image != ""){
        document.getElementById('newimage_btn').addEventListener('click', (event) => {
          event.preventDefault();
          inputId ='newimage';
          window.open('/file-manager/fm-button/?leftPath=pages', 'fm', 'width=1400,height=800');
        });
    }
  //featured image
  if(featured_image == ""){
      document.getElementById('button-image').addEventListener('click', (event) => {
        event.preventDefault();
        inputId ='image_label';
        window.open('/file-manager/fm-button/?leftPath=pages', 'fm', 'width=1400,height=800');
      });
  }
  //social facebook image
      document.getElementById('fb_image_btn').addEventListener('click', (event) => {
        event.preventDefault();
        inputId ='fb_image';
        window.open('/file-manager/fm-button/?leftPath=pages', 'fm', 'width=1400,height=800');
    });
      //social twitter image
      document.getElementById('tw_image_btn').addEventListener('click', (event) => {
        event.preventDefault();
        inputId ='tw_image';
        window.open('/file-manager/fm-button/?leftPath=pages', 'fm', 'width=1400,height=800');
    });
    //dynamic intilization for php generated file manager stand alone buttons
    counter = 1;
    $.each(customData, function( index, value ) {
      if(value.Type == "text" &&  value.is_image == 1){
          document.getElementById('button-image'+counter+'').addEventListener('click', (event) => {
            event.preventDefault();
            inputId ='image'+event.srcElement.attributes[3].nodeValue+'';
            window.open('/file-manager/fm-button/?leftPath=pages', 'fm', 'width=1400,height=800');
          });
        counter++;
      }
    });
  });
  //initialize tinymice wysiwyg editor
tinymce.init({
    plugins: 'help insertdatetime paste charmap emoticons visualblocks table autoresize image media code wordcount codesample autolink link lists hr searchreplace',
    menu: {
      file:   { title: 'File', items: 'restoredraft | preview | print ' },
      edit:   { title: 'Edit', items: 'undo redo | cut copy paste | selectall | searchreplace' },
      view:   { title: 'View', items: 'code | visualaid ' },
      insert: { title: 'Insert', items: 'image link media template codesample inserttable | charmap hr | pagebreak nonbreaking anchor toc | insertdatetime' },
      format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontformats fontsizes align lineheight | forecolor backcolor | removeformat' },
      tools:  { title: 'Tools', items: 'spellchecker spellcheckerlanguage | code wordcount' },
      table:  { title: 'Table', items: 'inserttable | cell row column | tableprops deletetable' },
      help:   { title: 'Help', items: 'help' }
    },
    // menubar: 'insert edit format tools table view custom',
    selector: '.custom_class',
    font_formats: "Sans Serif = arial, helvetica, sans-serif;Serif = times new roman, serif;Fixed Width = monospace;Wide = arial black, sans-serif;Narrow = arial narrow, sans-serif;Comic Sans MS = comic sans ms, sans-serif;Garamond = garamond, serif;Georgia = georgia, serif;Tahoma = tahoma, sans-serif;Trebuchet MS = trebuchet ms, sans-serif;Verdana = verdana, sans-serif",
    fontsize_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
    toolbar: 
    [
        "|styleselect | fontselect | fontsizeselect | lineheight | bold italic underline | blockquote | charmap | forecolor | backcolor | removeformat |",
        "|numlist bullist | alignleft aligncenter alignright alignjustify | outdent indent | link unlink | hr | undo redo | image media| code | searchreplace | insertdatetime |"
    ],
    toolbar_sticky: true,
    default_link_target: '_blank',
    link_default_protocol: 'https',
    table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
    image_title: true,
    lineheight_formats: '1 1.1 1.2 1.3 1.4 1.5 2',
    /* enable automatic uploads of images represented by blob or data URIs*/
    // automatic_uploads: true,
    relative_urls: false,
    file_picker_callback (callback, value, meta) {
        let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth
        let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight
        tinymce.activeEditor.windowManager.openUrl({
          url : '/file-manager/tinymce5/',
          title : 'Laravel File manager',
          width : x * 0.8,
          height : y * 0.8,
          onMessage: (api, message) => {
            callback(message.content, { text: message.text })
          }
        })
      },
});

$(document).ready(function(){
  $('input[type="radio"]').click(function(){
      var inputValue = $(this).attr("value");
      var targetBox = $("." + inputValue);
      $(".box").not(targetBox).hide();
      $(targetBox).show();
  });
});

//Function to Make Slug Automatically
function mySlugFunction(){
      var input1 = document.getElementById('title1');
      var input2 = document.getElementById('slug1');

      var updateInputs = function () {
        var str = (input1.value).trim();
        var str1 = str.replace(/[!@^_=|;&\/\\#,+()$~%.'":*?<>{}]/g,'');
        str2 = str1.replace(/\s+/g, '-').toLowerCase();
        input2.value = str2;
        $("span#url").text('<?php echo Adminurl() ;?>/'+str2);
        $('#url_link').prop('href', '<?php echo Adminurl() ;?>/'+str2);
        $('#seo_slug').val(str2);
        $("span#page_url").text('<?php echo Adminurl() ;?>/'+str2);
        $('#page_link').prop('href', '<?php echo Adminurl() ;?>/'+str2);
        $("span#page_url_1").text('<?php echo Adminurl() ;?>/'+str2);
        $('#page_link_1').prop('href', '<?php echo Adminurl() ;?>/'+str2);

      }

      if (input1.addEventListener) {
          input1.addEventListener('keyup', function () {
              updateInputs();
          });
          input1.addEventListener('change', function () {
          updateInputs();
      });
      }
      else if (input1.attachEvent) { // support IE
          input1.attachEvent('onkeyup', function () {
              updateInputs();
          });
        }
    }

function mySlugFunction2()
    {
      var input1 = document.getElementById('slug1');

      var updateInputs = function () {
        var str = (input1.value);
        var str1 = str.replace(/[!@^_=|;&\/\\#,+()$~%.'":*?<>{}]/g,'');
        str2 = str1.replace(/\s+/g, '-').toLowerCase();
        input1.value = str2;
        $("span#url").text('<?php echo Adminurl() ;?>'+str2);
        $('#url_link').prop('href', '<?php echo Adminurl() ;?>/'+str2);
        $('#seo_slug').val(str2);
        $("span#page_url").text('<?php echo Adminurl() ;?>/'+str2);
        $('#page_link').prop('href', '<?php echo Adminurl() ;?>/'+str2);
        $("span#page_url_1").text('<?php echo Adminurl() ;?>/'+str2);
        $('#page_link_1').prop('href', '<?php echo Adminurl() ;?>/'+str2);
      }

      if (input1.addEventListener) {
          input1.addEventListener('keyup', function () {
              updateInputs();
          });
          input1.addEventListener('change', function () {
          updateInputs();
      });
      }
      else if (input1.attachEvent) { // support IE
          input1.attachEvent('onkeyup', function () {
              updateInputs();
          });
        }
    }

function mySlugFunction3()
    {
      var input1 = document.getElementById('seo_slug');

      var updateInputs = function () {
        var str = input1.value;
        var str1 = str.replace(/[!@^_=|;&\/\\#,+()$~%.'":*?<>{}]/g,'');
        str2 = str1.replace(/\s+/g, '-').toLowerCase();
        input1.value = str2;
        $("span#page_url").text('<?php echo Adminurl() ;?>/'+str2);
        $('#page_link').prop('href', '<?php echo Adminurl() ;?>/'+str2);
        $('#slug1').val(str2);
        $("span#url").text('<?php echo Adminurl() ;?>/'+str2);
        $('#url_link').prop('href', '<?php echo Adminurl() ;?>/'+str2);
        $("span#page_url_1").text('<?php echo Adminurl() ;?>/'+str2);
        $('#page_link_1').prop('href', '<?php echo Adminurl() ;?>/'+str2);
      }
      if (input1.addEventListener) {
          input1.addEventListener('keyup', function () {
              updateInputs();
          });
          input1.addEventListener('change', function () {
          updateInputs();
      });
      }
      else if (input1.attachEvent) { // support IE
          input1.attachEvent('onkeyup', function () {
              updateInputs();
          });
        }

    }

function changeTitle()
    {
      var input1 = document.getElementById('seo_title');

      var updateInputs = function () {
        var str = input1.value;
        $("span#page_title").text(str).css("font-weight", "bold");
        $("span#page_title_1").text(str);
        $("#fb_title").val(str);
        $("#tw_title").val(str);

      }

      if (input1.addEventListener) {
          input1.addEventListener('keyup', function () {
              updateInputs();
          });
          input1.addEventListener('change', function () {
          updateInputs();
      });
      }
      else if (input1.attachEvent) { // support IE
          input1.attachEvent('onkeyup', function () {
              updateInputs();
          });
        }
    }

function changeMetaDesc()
    {
      var input1 = document.getElementById('seo_description');
      var length = 110;
      n =  new Date();
      y = n.getFullYear();
      m = n.getMonth() + 1;
      d = n.getDate();
      var updateInputs = function () {
        var str = input1.value;
        var trimmedString = str.length > length ?
        str.substring(0, length) + "..." :
        str
        $("span#page_description").text(trimmedString);
        $("span#page_description_1").text(trimmedString);
        $("#fb_description").val(str);
        $("#tw_description").val(str);

      }

      if (input1.addEventListener) {
          input1.addEventListener('keyup', function () {
              updateInputs();
          });
          input1.addEventListener('change', function () {
          updateInputs();
      });
      }
      else if (input1.attachEvent) { // support IE
          input1.attachEvent('onkeyup', function () {
              updateInputs();
          });
        }
    }

function getFileName(tag,id){

    var value1 = tag.value;
    var id1 = id;
    var name1  = tag.name;
    var formData = new FormData();
    formData.append('value', value1);
    formData.append('id', id1);
    formData.append('name', name1);
    $.ajax({
      url: '<?php echo Adminurl('upload_custom_image') ;?>',
      type: 'post',
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      headers: {
          'X-CSRF-TOKEN': $('#token').val()
      },
      success: function(response){
          if (response.isSuccessful == 'Yes')
          {
            $("[name ="+name1+"]").val(response.imagename);
            // $("[name ="+name1+"]").prop('disabled', true);
            toastr.success('File uploaded successfully.',{timeOut:5000});
          }
          else
          {
            $("[name ="+name1+"]").attr("value",response.imagename);
            toastr.error('File upload failed. Please Upload File of type JPG,SVG, GIF, GIF, PNG,JPEG,BMP,PDF,DOC,DOCX,XLS,XLSX',{timeOut:5000});
          }
        }
    });
}

function addNewImage(tag){
    var id            = "<?php echo $page[0]->id;?>";
    var value1        = tag.value;
    var name1         = tag.name;
    var custom_attr   = $(tag).attr("custom_attr")
    var formData      = new FormData();
    formData.append('id', id);
    formData.append('value', value1);
    formData.append('name', name1);
    formData.append('custom_attr', custom_attr);
    $.ajax({
      url: '<?php echo Adminurl('add_new_image_1') ;?>',
      type: 'post',
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      headers: {
          'X-CSRF-TOKEN': $('#token').val()
      },
      success: function(response){
          if (response.isSuccessful == 'Yes')
          {
            $("[name ="+name1+"]").val(response.imagename);
            // $(tag).closest('div').find('.btn').prop('disabled',true);
            toastr.success('File uploaded successfully.',{timeOut:5000});
          }
          else
          {
            $("[name ="+name1+"]").attr("value",response.imagename);
            toastr.error('File upload failed. Please Upload File of type JPG,SVG, GIF, GIF, PNG,JPEG,BMP,PDF,DOC,DOCX,XLS,XLSX.',{timeOut: 5000});
          }
        }
    });
}

// Text Field
  var counterText = <?php echo ++$counterText1;?>;
  var d = new Date();
  $("#addTextButton").click(function() {

      var newTextBoxDiv = $(document.createElement('div')).attr({
          "id": 'TextBoxDiv' + counterText,
          "class": 'container-fluid'
      });

      var newtext = '<div class="card">'+
                      '<div class="card-header min-height65">'+
                        '<input type="button" class="customclass close btn" value="x"  aria-label="Close" id="DeleteTextButton' + counterText + '"/>'+
                      '</div>'+
                      '<div class="card-body">'+
                            '<div class="row align-items-center">'+
                              '<div class="col-md-1">'+
                                '<i class="fas fa-expand-arrows-alt"></i>'+
                              '</div>'+
                              '<div class="col-md-10">' +
                                    '<span class="spanhover" id="text'+counterText+'"><b>Textbox:</b></span>'+
                                    '<input class="form-control" type="text" custom_attr="Textbox:" name="textbox' + d.getTime()+counterText +'" id="textbox' + counterText + '" value="" placeholder="Enter Text">' +
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>';

      newTextBoxDiv.after().html(newtext);

      newTextBoxDiv.appendTo("#TextBoxesGroup");
      $("#DeleteTextButton" + counterText).click(function(event) {
          var element = event.target.parentElement.parentElement.parentElement.id;
          var r = confirm("Are you sure you want to delete?");
          if (r == true) {
              $("#" + element).remove();
          } else {
              return false;
          }
      });
      $('span#text'+counterText).editable("click", function(e){
        e.target.nextAll('input').attr("custom_attr",e.value);
      });

      counterText++;
  });

// Add Wysiwyg Editor
  var counterRichTextArea = <?php echo $countRichText;?>;
  var d = new Date();
  $("#addRichTextAreaButton").click(function() {

      var newTextBoxDiv = $(document.createElement('div')).attr({
          "id": 'TextBoxDivQ' + counterRichTextArea,
          "class": 'container-fluid'
      });

      var newtext =  '<div class="card">'+
                      '<div class="card-header min-height65">'+
                        '<input type="button" class="close btn" value="x"  aria-label="Close" id="DeleteRichTextButtonQ' + counterRichTextArea + '"/>'+
                      '</div>'+
                      '<div class="card-body">' +
                            '<div class="row align-items-center">'+
                              '<div class="col-md-1">'+
                                '<i class="fas fa-expand-arrows-alt"></i>'+
                              '</div>'+
                              '<div class="col-md-10">' +
                                    '<span class="spanhover" id="richtextarea'+counterRichTextArea+'"><b>Rich TextArea:</b></span>'+
                                    '<textarea class="custom_class" type="textarea1" id="'+counterRichTextArea+'" custom_attr="RichTextArea" name="editordata'+counterRichTextArea+'"></textarea>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>';

      newTextBoxDiv.after().html(newtext);

      newTextBoxDiv.appendTo("#TextBoxesGroup");
      $("#DeleteRichTextButtonQ" + counterRichTextArea).click(function(event) {
          var element = event.target.parentElement.parentElement.parentElement.id;
          var r = confirm("Are you sure you want to delete?");
          if (r == true) {
              $("#" + element).remove();
          } else {
              return false;
          }
      });
      $('span#richtextarea'+counterRichTextArea).editable("click", function(e){
        e.target.nextAll('textarea').attr("custom_attr",e.value);
      });
      tinymce.init({
          plugins: 'help insertdatetime paste charmap emoticons visualblocks table autoresize image media code wordcount codesample autolink link lists hr searchreplace',
          menu: {
            file:   { title: 'File', items: 'restoredraft | preview | print ' },
            edit:   { title: 'Edit', items: 'undo redo | cut copy paste | selectall | searchreplace' },
            view:   { title: 'View', items: 'code | visualaid ' },
            insert: { title: 'Insert', items: 'image link media template codesample inserttable | charmap hr | pagebreak nonbreaking anchor toc | insertdatetime' },
            format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontformats fontsizes align lineheight | forecolor backcolor | removeformat' },
            tools:  { title: 'Tools', items: 'spellchecker spellcheckerlanguage | code wordcount' },
            table:  { title: 'Table', items: 'inserttable | cell row column | tableprops deletetable' },
            help:   { title: 'Help', items: 'help' }
          },
          // menubar: 'insert edit format tools table view custom',
          selector: '#'+counterRichTextArea,
          font_formats: "Sans Serif = arial, helvetica, sans-serif;Serif = times new roman, serif;Fixed Width = monospace;Wide = arial black, sans-serif;Narrow = arial narrow, sans-serif;Comic Sans MS = comic sans ms, sans-serif;Garamond = garamond, serif;Georgia = georgia, serif;Tahoma = tahoma, sans-serif;Trebuchet MS = trebuchet ms, sans-serif;Verdana = verdana, sans-serif",
          fontsize_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
          toolbar: 
          [
              "|styleselect | fontselect | fontsizeselect | lineheight | bold italic underline | blockquote | charmap | forecolor | backcolor | removeformat |",
              "|numlist bullist | alignleft aligncenter alignright alignjustify | outdent indent | link unlink | hr | undo redo | image media| code | searchreplace | insertdatetime |"
          ],
          toolbar_sticky: true,
          default_link_target: '_blank',
          link_default_protocol: 'https',
          table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
          image_title: true,
          lineheight_formats: '1 1.1 1.2 1.3 1.4 1.5 2',
          /* enable automatic uploads of images represented by blob or data URIs*/
          // automatic_uploads: true,
          relative_urls: false,
          file_picker_callback (callback, value, meta) {
              let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth
              let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight
              tinymce.activeEditor.windowManager.openUrl({
                url : '/file-manager/tinymce5/',
                title : 'Laravel File manager',
                width : x * 0.8,
                height : y * 0.8,
                onMessage: (api, message) => {
                  callback(message.content, { text: message.text })
                }
              })
            },
      });

      counterRichTextArea++;
  });

// Add Textarea Field
  var counterArea = <?php echo ++$counterArea1;?>;
  var d = new Date();
  $("#addTextAreaButton").click(function() {

    var newTextBoxDiv = $(document.createElement('div')).attr({
        "id": 'TextAreaDiv' + counterArea,
        "class": 'container-fluid'
    });

    var newtextarea = '<div class="card">'+
                        '<div class="card-header min-height65">'+
                          '<input type="button" class="customclass close btn" value="x"  aria-label="Close" id="DeleteAreaButton' + counterArea + '"/>'+
                        '</div>'+
                        '<div class="card-body">' +
                              '<div class="row align-items-center">'+
                                '<div class="col-md-1">'+
                                  '<i class="fas fa-expand-arrows-alt"></i>'+
                                '</div>'+
                                '<div class="col-md-10">' +
                                      '<span class="spanhover" id="area'+counterArea+'"><b>TextArea: </b></span>'+
                                      '<textarea type="textarea" rows="8" class="form-control" custom_attr="TextArea:"  name="textarea' + d.getTime()+ counterArea +'" id="textarea' + counterArea + '"></textarea>' +
                                  '</div>'+
                              '</div>'+
                          '</div>'+
                      '</div>';

    newTextBoxDiv.after().html(newtextarea);

    newTextBoxDiv.appendTo("#TextBoxesGroup");

    $("#DeleteAreaButton"+counterArea).click(function(event) {
        var element = event.target.parentElement.parentElement.parentElement.id;
        var r = confirm("Are you sure you want to delete?");
        if (r == true) {
            $("#" + element).remove();
        } else {
            return false;
        }
    });

    $("span#area"+counterArea).editable("click", function(e){
      e.target.nextAll('textarea').attr("custom_attr",e.value);
    });

    counterArea++;
  });

// Add File Div
  var counterFile = <?php echo ++$counterFile1;?>;
  $("#addFileButton").click(function() {

      var newTextBoxDiv = $(document.createElement('div')).attr({
          "id": 'FileDiv' + counterFile,
          "class": 'container-fluid'
      });

      var newfile = '<div class="card">'+
                      '<div class="card-header min-height65">'+
                        '<input type="button" class=" customclass close btn" value="x" aria-label="Close" id="DeleteFileButton' + counterFile + '"/>'+
                      '</div>'+
                      '<div class="card-body">' +
                            '<div class="row align-items-center">'+
                              '<div class="col-md-1">'+
                                '<i class="fas fa-expand-arrows-alt"></i>'+
                              '</div>'+
                              '<div class="col-md-10">' +
                                    '<span class="spanhover" id="file'+counterFile+'"><b>File: </b></span>'+
                                    '&nbsp;&nbsp;<span style="color: red;font-size: 12px;">(<b>Supported file types:</b>&nbsp; pdf, doc, xls, jpg, png, bmp, gif & svg <b>Maximum Size:</b>&nbsp;2Mb)</span>'+
                                    '<div class="input-group"><input custom_attr="File"  type="text" class="form-control max-width" aria-label="Image" aria-describedby="button-image'+counterFile+'" name="file' + d.getTime()+counterFile +'" id="image'+counterFile+'" value="" onchange="addNewImage(this)" data-image="1" readonly><div class="input-group-append"><button class="btn btn-outline-secondary" type="button" id="button-image'+counterFile+'" data-count="'+counterFile+'">Select</button></div></div>' +
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>';

      newTextBoxDiv.after().html(newfile);

      newTextBoxDiv.appendTo("#TextBoxesGroup");
      $("#DeleteFileButton" + counterFile).click(function(event) {
        var name = $(this).closest('div').next('div').find('input:file').attr('value');
        var element = event.target.parentElement.parentElement.parentElement.id;
        var formData = new FormData();
        formData.append('name', name);
        var r = confirm("Are you sure you want to delete?");
        if (r == true) {
            $("#" + element).remove();
            if(name != '' && name != undefined){
            $.ajax({
              url: '<?php echo Adminurl('delete_new_image') ;?>',
              type: 'post',
              dataType: 'json',
              cache: false,
              contentType: false,
              processData: false,
              data: formData,
              headers: {
                  'X-CSRF-TOKEN': $('#token').val()
              },
              success: function(response){
                if (response.isSuccessful == 'Yes')
                {
                  toastr.success('File deleted successfully',{timeOut:5000});
                }
                else
                {
                  toastr.error('File deletion failed.',{timeOut:5000});
                }
              }
            });
            }
        }
        else {
            return false;
        }
      });

      $("span#file"+counterFile).editable("click", function(e){
        e.target.nextAll('.input-group').find('input').attr("custom_attr",e.value);
      });
      document.getElementById('button-image'+counterFile+'').addEventListener('click', (event) => {
  event.preventDefault();
  //input id for file manager
  inputId ='image'+event.srcElement.attributes[3].nodeValue+'';
  window.open('/file-manager/fm-button/?leftPath=pages', 'fm', 'width=1400,height=800');
})
      counterFile++;
  });

function deletefunc(){
  var element = event.target.parentElement.parentElement.parentElement.id;
  var r = confirm("Are you sure you want to delete?");
  if (r == true) {
      $("#" + element).remove();
  }
  else {
      return false;
  }
}

function deleteFuncFile(name,value,id){

  var name = name;
  var imagename = value;
  var id = id;
  var element = event.target.parentElement.parentElement.parentElement.id;
  var formData = new FormData();
  formData.append('name', name);
  formData.append('imagename', imagename);
  formData.append('id', id);
  var r = confirm("Are you sure you want to delete?");
  if (r == true) {
      $("#" + element).remove();
      $.ajax({
        url: '<?php echo Adminurl('delete_old_image') ;?>',
        type: 'post',
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('#token').val()
        },
        success: function(response){
          if (response.isSuccessful == 'Yes')
            {
              toastr.success('File deleted successfully.',{timeOut:5000});
            }
            else
            {
              toastr.error('File deletion failed.',{timeOut:5000});
            }
          }
      });
  }
  else {
      return false;
  }
}


//Editable span field for DB entries
  for (var i = 0; i < <?php echo $counterFile1;?>; i++) {
      $("#fileID"+i).editable("click", function(e){
        e.target.nextAll('.input-group').find('input').attr("custom_attr",e.value);
      });
  }
  for (var x = 0; x < <?php echo $counterText1;?>; x++) {
      $("#textID"+x).editable("click", function(e){
          e.target.nextAll('input').attr("custom_attr",e.value);
      });
  }
  for (var y = 0; y < <?php echo $counterArea1;?>; y++) {
      $("#textareaID"+y).editable("click", function(e){
          e.target.nextAll('textarea').attr("custom_attr",e.value);
      });
  }
  for (var y = 0; y < <?php echo $countRichText;?>; y++) {
      $("#richtextarea"+y).editable("click", function(e){
          e.target.nextAll('textarea').attr("custom_attr",e.value);
      });
  }

//Save Data to textarea to Save in DB
document.getElementById('submit').addEventListener('click', function(e) {
  // e.preventDefault();
  var initial_data = '';
  $("#TextBoxesGroup :input[type=text],#TextBoxesGroup :input[type=file],#TextBoxesGroup textarea,#TextBoxesGroup .custom_class").each(function(index){
      var d = new Date();
      var r = Math.random().toString(36).substring(7);
      var input = $(this);
      if (input.attr('type') == 'file') {
        var custom  = input.attr('custom_attr');
        var value = input.attr("value");
        if(value == ''){
          var custom  = input.attr('custom_attr');
          var value = input.val();
        }

      }
      else if(input.attr('type') == 'textarea')
      {
        var custom  = input.attr('custom_attr');
        var value1 = input.val().trim();
        var value = value1.replace(/\r?\n|\r/g,"<br>");
        // Replace new line and line break with br tag
      }

      else if( input.attr('type') == 'textarea1')
      {
        var x       = input.attr('id');
        var custom  = $('span#richtextarea'+x).text();
        var value1  = tinyMCE.editors[x].getContent();
        var value2  = value1.replace(/\r?\n|\r/g,"<br>");
        var value   = value2.replace(/"/g, "&quot;");
      }

      else {
        var custom  = input.attr('custom_attr');
        var value   = input.val();
        var is_image = input.attr("data-image");
        if(is_image == undefined)
        is_image = 0;
      }
      var data = '{"UID": "' + d.getTime()+r +'","Type": "' + input.attr('type') + '","is_image": "' + is_image + '","Name": "' + input.attr('name') + '","Value": "' + value + '","Custom_Attribute": "' + custom+"\"}";
      initial_data = initial_data + ',' + data;

      });
  var newString = initial_data.replace(',','');
  var final_data = "["+ newString + "]";
  $("#custom_fields").val(final_data);
  });


document.getElementById('submit1').addEventListener('click', function(e) {
 // e.preventDefault();
  var initial_data = '';
  $("#TextBoxesGroup :input[type=text],#TextBoxesGroup :input[type=file],#TextBoxesGroup textarea,#TextBoxesGroup .custom_class").each(function(index){
      var d = new Date();
      var r = Math.random().toString(36).substring(7);
      var input = $(this);
      if (input.attr('type') == 'file') {
        var custom  = input.attr('custom_attr');
        var value = input.attr("value");

        if(value == ''){
          var custom  = input.attr('custom_attr');
          var value = input.val();
        }

      }
      else if(input.attr('type') == 'textarea')
      {
        var custom  = input.attr('custom_attr');
        var value1 = input.val().trim();
        var value = value1.replace(/\r?\n|\r/g,"<br>");
        // Replace new line and line break with br tag
      }

      else if( input.attr('type') == 'textarea1')
      {
        var x       = input.attr('id');
        var custom  = $('span#richtextarea'+x).text();
        var value1  = tinyMCE.editors[x].getContent();
        var value2  = value1.replace(/\r?\n|\r/g,"<br>");
        var value   = value2.replace(/"/g, "&quot;");
      }

      else {
        var custom  = input.attr('custom_attr');
        var value   = input.val();
        var is_image = input.attr("data-image");
        if(is_image == undefined)
        is_image = 0;
      }
      var data = '{"UID": "' + d.getTime()+r +'","Type": "' + input.attr('type') + '","is_image": "' + is_image + '","Name": "' + input.attr('name') + '","Value": "' + value + '","Custom_Attribute": "' + custom+"\"}";
      initial_data = initial_data + ',' + data;

      });
  var newString = initial_data.replace(',','');
  var final_data = "["+ newString + "]";
  $("#custom_fields").val(final_data);
  });

//Sortable Library
$(function() {
    $("#TextBoxesGroup").sortable({
      start: function (e, ui) {
        $(ui.item).find('.custom_class').each(function () {
           tinymce.execCommand('mceRemoveEditor', false, $(this).attr('id'));
        });
      },
      stop: function (e, ui) {
        $(ui.item).find('.custom_class').each(function () {
           tinymce.execCommand('mceAddEditor', true, $(this).attr('id'));
        });
      }
    });
    $("#TextBoxesGroup").disableSelection();
});
// Add Featured Image
function addFeaturedImage(tag)
{
    var image = tag.value;
    var name1  = tag.name;
    var formData = new FormData();
    formData.append("image", image);
    formData.append('name', name1);
    $.ajax({
      url: '<?php echo Adminurl('add_featured_image') ;?>',
      type: 'post',
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      headers: {
          'X-CSRF-TOKEN': $('#token').val()
      },
      success: function(response){
        if (response.isSuccessful == 'Yes')
        {
          $("[name ="+name1+"]").val(response.imagename);
          // $(tag).closest('div').find('.btn').prop('disabled',true);
          toastr.success('File uploaded successfully.',{timeOut:5000});
        }
        else if(response.isSuccessful == 'No_1' )
        {
          $("[name ="+name1+"]").val("");
          toastr.error('Image upload failed. Please Upload Image of type JPG,SVG, GIF, GIF, PNG,JPEG,BMP',{timeOut:5000});
        }
        else
        {
          $("[name ="+name1+"]").val("");
          toastr.error('Image upload failed. Please Upload Image of Size less than 2MB.',{timeOut:5000});
        }
      }
    });
}

function updateFeaturedImage(tag,id)
{
    var value1    = tag.value;
    var id1       = id;
    var name1     = tag.name;
    var formData  = new FormData();
    formData.append('value', value1);
    formData.append('id', id1);
    formData.append('name', name1);
    $.ajax({
      url: '<?php echo Adminurl('update_featured_image') ;?>',
      type: 'post',
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      headers: {
          'X-CSRF-TOKEN': $('#token').val()
      },
      success: function(response){
          if (response.isSuccessful == 'Yes')
          {
            $("[name ="+name1+"]").val(response.imagename);
            // $(tag).closest('div').find('.btn').prop('disabled',true);
            toastr.success('File uploaded successfully.',{timeOut:5000});
          }
          else if(response.isSuccessful == 'No_1' )
          {
            $("[name ="+name1+"]").val("");
            toastr.error('Image upload failed. Please Upload Image of type JPG,SVG, GIF, GIF, PNG,JPEG,BMP',{timeOut:5000});
          }
          else
          {
            $("[name ="+name1+"]").val("");
            toastr.error('Image upload failed. Please Upload Image of Size less than 2MB.',{timeOut:5000});
          }
        }
    });
}

function countChar(val,el) {
        var len = val.value.length;
          $('#'+el+'').text(''+len+'/200 Charcters');
      };
//callback function for file manager
// set file link
function fmSetLink($url) {
  $('#'+inputId+'').val($url);
  $('#'+inputId+'').change();
}
</script>
@endsection
