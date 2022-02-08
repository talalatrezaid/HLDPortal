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
<style>
    .datepicker {
        border-radius: 4px;
        direction: ltr;
        -webkit-user-select: none;
        -webkit-touch-callout: none;
    }


    /* basicos */
    .datepicker .day {
        border-radius: 4px;
    }

    .datepicker-dropdown {
        top: 0;
        left: 0;
        padding: 5px;
    }

    .datepicker-dropdown:before {
        content: '';
        display: inline-block;
        border-left: 7px solid transparent;
        border-right: 7px solid transparent;
        border-bottom: 7px solid red;
        border-top: 0;
        border-bottom-color: red;
        position: absolute;
    }

    .datepicker-dropdown:after {
        content: '';
        display: inline-block;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid #fff;
        border-top: 0;
        position: absolute;
    }

    .datepicker-dropdown.datepicker-orient-left:before {
        left: 6px;
    }

    .datepicker-dropdown.datepicker-orient-left:after {
        left: 7px;
    }

    .datepicker-dropdown.datepicker-orient-right:before {
        right: 6px;
    }

    .datepicker-dropdown.datepicker-orient-right:after {
        right: 7px;
    }

    .datepicker-dropdown.datepicker-orient-bottom:before {
        top: -7px;
    }

    .datepicker-dropdown.datepicker-orient-bottom:after {
        top: -6px;
    }

    .datepicker-dropdown.datepicker-orient-top:before {
        bottom: -7px;
        border-bottom: 0;
        border-top: 7px solid red;
    }

    .datepicker-dropdown.datepicker-orient-top:after {
        bottom: -6px;
        border-bottom: 0;
        border-top: 6px solid red;
    }




    .datepicker table {
        margin: 0;
        user-select: none;
    }






    .datepicker td,
    .datepicker th {
        text-align: center;
        width: 30px;
        height: 30px;
        border: none;
    }






    .datepicker .datepicker-switch,
    .datepicker .prev,
    .datepicker .next,
    .datepicker tfoot tr th {
        cursor: pointer;
    }

    /*.datepicker .datepicker-switch:hover,*/
    /*.datepicker .prev:hover,*/
    /*.datepicker .next:hover,*/
    /*.datepicker tfoot tr th:hover {*/
    /*background: red;*/
    /*border-radius: 4px;*/
    /*}*/
    .datepicker .prev .disabled,
    .datepicker .next .disabled {
        visibility: hidden;
    }




    .datepicker .range-start {
        background: #337ab7 url("../images/range-bg-1.png") top right no-repeat;
        color: #fff;
    }

    .datepicker .range-end {
        background: #337ab7 url("../images/range-bg-2.png") top left no-repeat;
        color: #fff;
    }

    .datepicker .range-start.range-end {
        background-image: none;
    }


    .datepicker .range {
        background: #d5e9f7;
    }

    /*.datepicker .disabled.day{*/
    /*color:#999;*/

    /*}*/

    /* Hover para dia mes y año*/

    .datepicker .day:hover,
    .datepicker .month:hover,
    .datepicker .year:hover,
    .datepicker .datepicker-switch:hover,
    .datepicker .next:hover,
    .datepicker .prev:hover {
        background-color: #ff8000;
        color: white;
        border-radius: 4px;
    }


    .hover {
        background-color: #ff8000;
        color: white;

    }


    .datepicker .today {
        font-weight: bold;
        color: #1ed443;

    }







    /* Estilos para meses y años */


    .datepicker-months,
    .datepicker-years {
        width: 213px;

    }

    .datepicker-months td,
    .datepicker-years td {
        width: auto;
        height: auto;

    }

    .datepicker-months .month,
    .datepicker-years .year {
        color: #fff;
        background-color: #337ab7;
        border-color: #2e6da4;
        float: left;
        display: block;
        width: 23%;
        height: 46px;
        line-height: 46px;
        margin: 1%;
        cursor: pointer;
        border-radius: 4px;
    }




    .day.active,
    .start-date-active {
        color: #fff;
        background-color: #337ab7;
        border-color: #2e6da4;
    }



    /* Desactivados */
    .day.disabled,
    .month.disabled,
    .year.disabled,
    .start-date-active.disabled {
        cursor: not-allowed;
        filter: alpha(opacity=65);
        -webkit-box-shadow: none;
        box-shadow: none;
        opacity: .65;
    }


    a:active,
    a:hover {
        outline: 0;
    }
</style>
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

                    <div class="row">
                        <div class="col-xs-4">
                            <select class="form-control select2"></select>
                        </div>
                        <div class="col-md-12 form-inline m-2">

                            <div class="input-group input-daterange col-md-4">
                                <input type="text" class="start-date form-control" value="2012-04-05">
                                <span class="input-group-addon">to</span>
                                <input type="text" class="end-date form-control" value="2012-04-19">
                            </div>
                        </div>
                        <table id="example" class="display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Charity</th>
                                    <th>Date</th>
                                    <th>Total Price</th>
                                    <th>Payment Status</th>
                                    <th>Fullfillment Status</th>
                                    <th>Customer</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
</section>
<script type="text/javascript">
    <?php if (session('not_allowed')) { ?>
        toastr.error('Sorry! You don&#39;t have permission to edit this product.', {
            timeOut: 5000
        });
    <?php } ?>

    $(document).ready(function() {
        $('.start-date').datepicker({
            templates: {
                leftArrow: '<i class="fa fa-chevron-left"></i>',
                rightArrow: '<i class="fa fa-chevron-right"></i>'
            },
            format: "dd/mm/yyyy",
            startDate: new Date(),
            keyboardNavigation: false,
            autoclose: true,
            todayHighlight: true,
            disableTouchKeyboard: true,
            orientation: "bottom auto"
        });

        $('.end-date').datepicker({
            templates: {
                leftArrow: '<i class="fa fa-chevron-left"></i>',
                rightArrow: '<i class="fa fa-chevron-right"></i>'
            },
            format: "dd/mm/yyyy",
            startDate: moment().add(1, 'days').toDate(),
            keyboardNavigation: false,
            autoclose: true,
            todayHighlight: true,
            disableTouchKeyboard: true,
            orientation: "bottom auto"

        });


        $('.start-date').datepicker().on("changeDate", function() {
            var startDate = $('.start-date').datepicker('getDate');
            var oneDayFromStartDate = moment(startDate).add(1, 'days').toDate();
            $('.end-date').datepicker('setStartDate', oneDayFromStartDate);
            $('.end-date').datepicker('setDate', oneDayFromStartDate);
        });

        $('.end-date').datepicker().on("show", function() {
            var startDate = $('.start-date').datepicker('getDate');
            $('.day.disabled').filter(function(index) {
                return $(this).text() === moment(startDate).format('D');
            }).addClass('active');
        });

        var buttonCommon = {

        };

        $('#example').DataTable({
            "order": [
                [0, 'desc']
            ],
            "scrollX": true,
            "processing": true,
            fixedHeader: true,
            orderCellsTop: true,
            searchable: true,
            "serverSide": true,
            "ajax": '/orders/show',
            'columns': [{
                "data": "id",

                "render": function(data, type, row) {
                    console.log(row);
                    return '<span>' + row.id + '</span>';
                }
            }, {
                "data": "charity_id",
                sortable: false,
                name: 'charity_id',
                "render": function(data, type, row) {
                    console.log(row);
                    var flag_charity_order = "HOLY LAND DATES";
                    if (data > 0) {
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
                sortable: false,
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span class="badge badge-secondary" id="email' + row.id + '">' + row.financial_status + '</span>';
                }
            }, {
                "data": "fulfillment_status",
                sortable: false,
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
                "data": "customer_id",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<span id="email' + row.id + '">' + (row?.customer?.first_name == undefined ? "" : row?.customer?.first_name) + ' ' + (row?.customer?.last_name == undefined ? "" : row?.customer?.last_name) + '</span>';
                }
            }, {
                "data": "fulfillment_status",
                "render": function(data, type, row) {
                    console.log(row);
                    return '<a href="<?php echo Adminurl('orderdetail/'); ?>' + row.id + '"><i class="fa fa-eye" id="view' + row.id + '"></i></a>';
                }
            }],
            dom: 'Bfrtip',
            buttons: [
                $.extend(true, {}, buttonCommon, {
                    extend: 'copyHtml5',
                    columns: [0, 1, 2, 3, 4, 5],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                }),
                $.extend(true, {}, buttonCommon, {
                    extend: 'excelHtml5',
                    columns: [0, 1, 2, 3, 4, 5],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                }),
                $.extend(true, {}, buttonCommon, {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                })
            ],
            initComplete: function() {
                this.api().columns(1).every(function() {
                    var column = this;
                    var select = $('.select2')
                        .on('change', function() {
                            var val = $(this).val();
                            alert(val);
                            column.search(this.value).draw();
                        });

                    // Only contains the *visible* options from the first page
                    //console.log(column.rows().data().unique().sort(), "id ka data");
                    console.log("rows", column.rows().data());
                    var data = column.rows().data();

                    // If I add extra data in my JSON, how do I access it here besides column.data?

                });

                this.api().columns(3).every(function() {
                    var column = this;


                    // Only contains the *visible* options from the first page
                    //console.log(column.rows().data().unique().sort(), "id ka data");
                    console.log("rows", column.rows().data());
                    var data = column.rows().data();
                    let sum = column
                        .data()
                        .reduce(function(a, b) {
                            var x = parseFloat(a) || 0;
                            var y = parseFloat(b) || 0;
                            return x + y;
                        }, 0);
                    console.log(sum);
                    alert(sum);
                    $(this.footer()).html(sum);
                    // If I add extra data in my JSON, how do I access it here besides column.data?

                });

            }
        });

        $('.select2').select2({
            ajax: {
                url: '<?php echo Adminurl('getCharitiesForFilter'); ?>',
                dataType: 'json',
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                processResults: function(data) {

                    return {
                        results: $.map(data, function(obj) {
                            return {
                                id: obj.id,
                                text: obj.text
                            };
                        })
                    };
                }
            },

        });
    });
</script>


@endsection