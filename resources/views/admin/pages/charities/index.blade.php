@extends('admin.layouts.app')
@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-9 col-lg-9">
                <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('charities'); ?>">Charities</a></h1>
            </div>
            <div class="col-lg-3">
                <a href="{{route('charities.create')}}" class="btn btn-primary float-right">Add New Charity</a>
            </div>


        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
            @endif
            <div class="card col-lg-12 p-3">
                <div class="col-lg-12" id="overlay">
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Charity Name</th>
                                <th>Slug</th>
                                <th>Email.</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    <?php if (session('not_allowed')) { ?>
        toastr.error('Sorry! You don&#39;t have permission to edit this product.', {
            timeOut: 5000
        });
    <?php } ?>

    $(document).ready(function() {
        $('#example').DataTable({
            responsive: false, // Move this outside of the ajax option

            "ajax": '/charities/show',
            'columns': [{
                "data": "id",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span>' + row.id + '</span>';
                }
            }, {
                "data": "charity_name",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span id="' + row.id + '">' +
                        row.charity_name + '</span>';
                }
            }, {
                "data": "user_name",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span id="slug_' + row.id + '">' + row.user_name + '</span>';
                }
            }, {
                "data": "email",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span id="email' + row.id + '">' + row.email + '</span>';
                }
            }, {
                "data": "action",
                "render": function(data, type, row) {
                    console.log(row);
                    let last_login = "-";
                    if (row.last_login == null || row.last_login === "null") {} else {
                        last_login = row.last_login;
                    }
                    return data;
                }
            }]
        });
    });
</script>



@endsection