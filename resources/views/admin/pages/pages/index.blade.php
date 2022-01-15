@extends('admin.layouts.app')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark"><a
                                    href="<?php echo Adminurl('pages'); ?>">Pages</a></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a
                                        href="<?php echo Adminurl('dashboard'); ?>">Home</a>
                                </li>
                                <li class="breadcrumb-item active"><a
                                        href="<?php echo Adminurl('pages'); ?>">Pages</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <a href="<?php echo Adminurl('pages/add'); ?>"><button
                                class="btn btn-success">Add Page</button></a>
                        @if ($page_status == 'Trash')
                            <a href="<?php echo Adminurl('pages'); ?>"><button
                                    class="btn btn-primary">Go Back</button></a>
                        @endif
                        <form name="status_form" id="status_form"
                            action="<?php echo Adminurl('pages'); ?>" style="float: right;">
                            <select name="page_status" id="page_status" class="browser-default custom-select"
                                onchange="statusFunc()">
                                <option value="All" @if (isset($status) && $status == 'All') selected="" @endif>All</option>
                                <option value="Publish" @if (isset($status) && $status == 'Publish') selected="" @endif>Publish</option>
                                <option value="Draft" @if (isset($status) && $status == 'Draft') selected="" @endif>Draft</option>
                            </select>
                        </form>
                        @if ($page_status == 'Trash')
                            <button onclick="emptyTrash()" class="btn btn-outline-secondary"
                                style="float: right;margin-right: 5px;">Empty Trash</button>
                            <button class="btn btn-outline-secondary" style="float: right;margin-right: 5px;"
                                onclick="mydelFunction()">Restore</button>
                        @endif
                        <a href="<?php echo Adminurl('pages/trash/'); ?>"
                            class="btn btn-outline-secondary" role="button"
                            style="float: right;margin-right: 5px;">Trash({{ $trash ?? '' }})</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div id="status"></div>
                    @if (session('create_success'))
                        <div class="alert alert-success" role="alert">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('create_success') }}
                        </div>
                    @elseif(session('delete_success'))
                        <div class="alert alert-success" role="alert">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('delete_success') }}
                        </div>
                    @elseif(session('delete_faliure'))
                        <div class="alert alert-danger" role="alert">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('delete_faliure') }}
                        </div>
                    @elseif(session('trash_success'))
                        <div class="alert alert-success" role="alert">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('trash_success') }}
                        </div>
                    @elseif(session('trash_complete_clear'))
                        <div class="alert alert-success" role="alert">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('trash_complete_clear') }}
                        </div>
                    @elseif(session('page_restore'))
                        <div class="alert alert-success" role="alert">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('page_restore') }}
                        </div>
                    @elseif(session('not_found'))
                        <div class="alert alert-danger" role="alert">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('not_found') }}
                        </div>
                    @endif
                    <div class="card-header">
                        <h4>Pages Table</h4>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <?php if ($pages->isEmpty()) { ?>
                        <div class="container">
                            <h4 style="text-align: center;color: red;">No Page Found</h4>
                        </div>
                        <?php } else { ?>
                        <table class="table table-hover text-nowrap table-bordered" id="pages">
                            <thead>
                                <tr style="text-align:center;">
                                    @if ($page_status == 'Trash')
                                        <th><input type="checkbox" name="select-all" id="select-all"></th>
                                    @endif
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Featured Image</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $current_page = $pages->currentPage();
                                    $per_page = $pages->PerPage();
                                    if ($current_page == 1) {
                                        $i = 1;
                                    } else {
                                        $current_page = $current_page - 1;
                                        $i = $current_page * $per_page + 1;
                                    }
                                @endphp
                                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                @csrf
                                @foreach ($pages as $pagedata)
                                    <tr style="text-align:center;" class="tr_hover"
                                        id="{{ $pagedata->id }}_{{ $pagedata->id }}">
                                        @if ($page_status == 'Trash')
                                            <td><input type="checkbox" class="page" name="page"
                                                    value="{{ $pagedata->id }}"></td>
                                        @endif
                                        <td>{!! @$i !!}</td>
                                        <td><span id="title_{{ $pagedata->id }}">{{ $pagedata->title }}</span><br>
                                            @if ($page_status != 'Trash')
                                                <button class="btn btn-outline-secondary btn-xs hide_class"
                                                    onclick="duplicateFunc('{{ $pagedata->id }}')">Duplicate</button>
                                                <button class="btn btn-outline-dark btn-xs hide_class"
                                                    onclick="quickedit('{{ $pagedata->id }}',{!! @$i !!})">Quick
                                                    Edit</button>
                                            @endif
                                        </td>
                                        <td id="slug_{{ $pagedata->id }}"><a
                                                href="<?php echo Adminurl(); ?>{{ $pagedata->slug }}"><?php echo Adminurl(); ?>{{ $pagedata->slug }}</a></td>
                                        @if (!empty($pagedata->featured_image))
                                            <td id="image_{{ $pagedata->id }}"><img
                                                    src="{{ Storage::url('images/pages/' . $pagedata->featured_image) }}"
                                                    width="150" height="auto" /></td>
                                        @else
                                            <td id="image_{{ $pagedata->id }}"></td>
                                        @endif
                                        <td id="status_{{ $pagedata->id }}">{{ $pagedata->page_status }}</td>
                                        @if ($page_status == 'Trash')
                                            <td>
                                                <button onclick="restorePage('{{ $pagedata->id }}')"
                                                    class="btn btn-primary">Restore</button>
                                                <button onclick="mydelete('{{ $pagedata->id }}')"
                                                    class="btn btn-danger">Delete</button>
                                            </td>
                                        @else
                                            <td>
                                                <a
                                                    href="<?php echo Adminurl('pages/edit/'); ?>{{ $pagedata->id }}"><button
                                                        class="btn btn-primary">Edit</button></a>
                                                <button onclick="trashFunction('{{ $pagedata->id }}')"
                                                    class="btn btn-danger">Trash</button>
                                            </td>
                                        @endif
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div style="float: right;" class="row">
            <b style="margin: 8px;">Total
                Records:{{ $pages->total() }}</b>&emsp;{{ $pages->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
        {{-- POPUP MODAL FOR QUICK EDIT --}}
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Quick Edit (<span id="current_id"></span>)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- To Show Status Regarding Ajax Call --}}
                        <input type="hidden" name="hidden_id" id="hidden_id">
                        {{-- Page Status --}}
                        <div class="form-group">
                            <label for="title">Page Status:</label>
                            <select class="form-control" name="page_status_1" id="page_status_1"
                                style="width:120px;cursor: pointer;">
                                <option value="Publish">Publish</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                        {{-- Page Title --}}
                        <div class="form-group">
                            <label for="title">Title:*</label>
                            <input onkeyup="mySlugFunction()" required type="text" class="form-control" id="title"
                                placeholder="Title" name="title" />
                        </div>
                        {{-- Page Slug --}}
                        <div class="form-group">
                            <label for="title">Slug:*</label>
                            <input required type="text" class="form-control" id="slug" placeholder="Slug" name="slug" />
                        </div>
                        <!-- Page Featured Image -->
                        <div class="form-group">
                            <label for="exampleInputFile">Featured Image:</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <div class="input-group">
                                        <input type="text" id="image_label" class="form-control max-width"
                                            name="featured_image" aria-label="Image" aria-describedby="button-image"
                                            readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="button-image">Select</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span style="color: red;font-size: 12px;">(<b>Supported file types:</b>&nbsp; jpg, png, bmp, gif
                                & svg.&nbsp; <b>Maximum Size:</b>&nbsp;2mb)</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="save_quick_edit()">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- POPUP MODAL ENDS --}}
        </div>
        <!-- Form To Restore Multiple Pages -->
        <form method="get" action="<?php echo Adminurl('pages/restore_many'); ?>"
            id="restoreMany">
            <input type="text" name="txtValue" id="txtValue" name="users" hidden="" /><br>
        </form>
        <!-- End -->
    </section>
    </div>
    <style type="text/css">
        .hide_class {
            display: none;
        }

        .tr_hover:hover .hide_class {
            display: inline-block;
        }

    </style>
    <script>
        //Select All Checkboxes if Main selected
        $('#select-all').click(function(event) {
            if (this.checked) {

                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
        });
        // Duplicate Function
        function duplicateFunc(id) {
            var id = id;
            window.location.href = "<?php echo Adminurl('pages/duplicate/'); ?>" + id;
        }

        // save ids to restore
        function updateTextArea() {
            var allVals = $('input.page:checked').map(
                function() {
                    return this.value;
                }).get().join();
            $('#txtValue').val(allVals)
        }

        // update textarea
        $(function() {
            var x = $('#pages input').click(updateTextArea);
            updateTextArea();
        });

        // pop up to confirm
        function mydelFunction() {
            var r = confirm("Are you sure you want ot restore multiple pages?");
            if (r == true) {
                document.getElementById("restoreMany").submit();
            } else {
                return false;
            }
        }
        // Move Single Page to Trash
        function trashFunction(id) {
            var r = confirm("Are you sure you want to move this page to Trash?");
            if (r == true) {
                window.location.href = "<?php echo Adminurl('pages/trash/'); ?>" + id;
            } else {
                return false;
            }
        }
        // Restore Single Page
        function restorePage(id) {
            var r = confirm("Are you sure you want to restore this page?");
            if (r == true) {
                window.location.href = "<?php echo Adminurl('pages/restore/'); ?>" + id;
            } else {
                return false;
            }
        }
        // Delete Single Page
        function mydelete(id) {
            var r = confirm("Are you sure you want to delete this page?");
            if (r == true) {
                window.location.href = "<?php echo Adminurl('pages/delete/'); ?>" + id;
            } else {
                return false;
            }
        }
        // Clear Trash
        function emptyTrash() {
            var r = confirm("Are you sure you want to empty trash?");
            if (r == true) {
                window.location.href = "<?php echo Adminurl('pages/deleteAll'); ?>";
            } else {
                return false;
            }
        }
        // Submit Value of dropdown regarding page status
        function statusFunc() {
            document.getElementById('status_form').submit();
        }
        // Quick Edit Page
        function quickedit(db_id, current_id) {
            // To Clear the Previous Fields in POPUP
            $('#hidden_id').val('');
            $('#title').val('');
            $('#slug').val('');
            $('#image_label').val('');
            $('#page_status_1 option[value="Publish"]').removeAttr('selected');
            $('#page_status_1 option[value="Draft"]').removeAttr('selected');
            $('#current_id').html('');

            var id = db_id;
            var url = '<?php echo Adminurl('quick_edit_page'); ?>';
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('#token').val()
                },
                success: function(response) {
                    if (response.isSuccessful == 'YES') {
                        $('.modal').modal('show');
                        $('#hidden_id').val(id);
                        $('#title').val(response.title);
                        $('#slug').val(response.slug);
                        $('#page_status_1 option[value="' + response.status + '"]').attr('selected',
                            'selected');
                        $('#current_id').html(current_id);
                    }
                }
            });
        }

        function save_quick_edit() {
            var id = $('#hidden_id').val();
            var title = $('#title').val();
            var slug = $('#slug').val();
            var status = $('#page_status_1 option:selected').text();
            var image = $('#image_label').val();
            if (title == '') {
                toastr.error('Please fill Title field.', {
                    timeOut: 5000
                });
                $('#title').css("border", "2px solid red");
                $('#title').focus();
                setTimeout(() => {
                    $('#title').css('border', '1px solid #ced4da');
                }, 5000);
                return false;
            }
            if (slug == '') {
                toastr.error('Please fill Slug field.', {
                    timeOut: 5000
                });
                $('#slug').css("border", "2px solid red");
                $('#slug').focus();
                setTimeout(() => {
                    $('#slug').css('border', '1px solid #ced4da');
                }, 5000);
                return false;
            }

            var url = '<?php echo Adminurl('save_quick_edit'); ?>';
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: {
                    id: id,
                    title: title,
                    slug: slug,
                    status: status,
                    image: image
                },
                headers: {
                    'X-CSRF-TOKEN': $('#token').val()
                },
                success: function(response) {
                    if (response.isSuccessful == 'YES') {
                        toastr.success('Data Successfully Updated.', {
                            timeOut: 5000
                        });
                        $('#hidden_id').val(id);
                        $('#title').val(response.title);
                        $('#slug').val(response.slug);
                        $('#page_status_1 option[value="' + response.status + '"]').attr('selected',
                            'selected');
                        $('span#title_' + id).html(response.title);
                        $('td#slug_' + id).html(
                            '<a href="<?php echo Adminurl(); ?>' + response
                            .slug + '"><?php echo Adminurl(); ?>' + response
                            .slug + '</a>');
                        $('td#image_' + id).html('<img src="<?php echo Storage::url('images / pages / '); ?>' + response.image +'" width="150" height="auto" />')
                        $('td#status_' + id).html(response.status);
                        $('.modal').modal('hide');
                        $('#' + id + '_' + id).css('background', '#def1e3');
                        setTimeout(() => {
                            $('#' + id + '_' + id).css('background', '#FFFFFF');
                        }, 5000);
                    } else if (response.isSuccessful == 'NO_1') {
                        toastr.error('Only images are allowed in featured image!', {
                            timeOut: 5000
                        });
                    } else if (response.isSuccessful == 'NO_2') {
                        toastr.error('Featured image should be max 2MB!', {
                            timeOut: 5000
                        });
                    }
                }
            });
        }

        function mySlugFunction() {
            var input1 = document.getElementById('title');
            var input2 = document.getElementById('slug');

            var updateInputs = function() {
                var str = (input1.value).trim();
                var str1 = str.replace(/[!@^_=|;&\/\\#,+()$~%.'":*?<>{}]/g, '');
                str2 = str1.replace(/\s+/g, '-').toLowerCase();
                input2.value = str2;
            }

            if (input1.addEventListener) {
                input1.addEventListener('keyup', function() {
                    updateInputs();
                });
                input1.addEventListener('change', function() {
                    updateInputs();
                });
            } else if (input1.attachEvent) { // support IE
                input1.attachEvent('onkeyup', function() {
                    updateInputs();
                });
            }
        }
        //file manager event listeners
        document.addEventListener("DOMContentLoaded", function() {
            //featured image
            document.getElementById('button-image').addEventListener('click', (event) => {
                event.preventDefault();
                inputId = 'image_label';
                window.open('/file-manager/fm-button/?leftPath=pages', 'fm', 'width=1400,height=800');
            });
        });

        function fmSetLink($url) {
            $('#' + inputId + '').val($url);
            if (inputId != 'image_label') {
                $('#' + inputId + '').change();
            }
        }

    </script>
@endsection
