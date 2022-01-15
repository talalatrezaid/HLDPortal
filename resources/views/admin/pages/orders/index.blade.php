@extends('admin.layouts.app')
@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-9 col-lg-9">
                <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('orders'); ?>">Orders</a></h1>
            </div>
            <div class="col-lg-3">
                <!-- <a href="{{route('charities.create')}}" class="btn btn-primary float-right">Add New Charity</a>
             -->
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
                                <th>Charity</th>
                                <th>Date</th>
                                <th>Total Price</th>
                                <th>Payment Status</th>
                                <th>Fullfillment Status</th>
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
            "order": [
                [0, 'desc']
            ],
            "scrollX": true,
            "ajax": '/orders/show',
            'columns': [{
                "data": "id",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span>' + row.id + '</span>';
                }
            }, {
                "data": "is_charity_order",
                "render": function(data, type, row) {
                    console.log(row);
                    var flag_charity_order = "HOLY LAND DATES";
                    if (data == 1) {
                        //find charity name here
                        flag_charity_order = row.charities.charity_name;
                    }
                    return '<span id="' + row.id + '">' + flag_charity_order + '</span>';
                }
            }, {
                "data": "created_at",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span id="email' + row.id + '">' + row.created_at + '</span>';
                }
            }, {
                "data": "total_price",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span id="email' + row.id + '">£' + row.total_price + '</span>';
                }
            }, {
                "data": "financial_status",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span class="badge badge-secondary" id="email' + row.id + '">' + row.financial_status + '</span>';
                }
            }, {
                "data": "fulfillment_status",
                "render": function(data, type, row) {
                    console.log(row);
                    var status_full = "No";
                    if (row.fulfillment_status) {
                        status_full = row.fulfillment_status;
                    }
                    return '<span id="' + row.id + '">' + status_full + '</span>';
                    return data;
                }
            }, {
                "data": "fulfillment_status",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<a href="<?php echo Adminurl('orderdetail/'); ?>' + row.id + '"><i class="fa fa-eye" id="view' + row.id + '"></i></a>';
                }
            }]
        });
    });
</script>


@endsection