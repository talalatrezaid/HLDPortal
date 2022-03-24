@extends('admin.layouts.app')
@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-9">
                <h1 class="m-0 text-dark"><a href="<?php echo Adminurl('shippingslots'); ?>">Shipping Slots</a> <span>@if(count($slots)>1) Count: {{$slots->total()}} @endif</span></h1>
            </div>

        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">

        <div class="row" id="overlay" style="display: none;">
            <img width="150" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" alt="Loading" />
        </div>
        <div class="row" id="overlay2" style="display: none;">
            <img width="150" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" alt="Loading" />
        </div>



        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <b>Slots</b>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#exampleModal">
                            Add Slot
                        </button>
                    </div>
                    <?php $max = 0; ?>

                    @if (session('success'))
                    <div class="alert alert-success">
                        <p>{{ session('success') }}</p>

                    </div>
                    @endif

                    <table class="table table-bordered m-3" id="store_product_table">
                        <thead>
                            <tr>
                                {{-- <th scope="col">--}}
                                {{-- <input  type="checkbox" value="-1" id="select_all_checkboxes" />--}}
                                {{-- </th>--}}
                                <th scope="col">Id</th>
                                <th scope="col">Min Weight(kg)</th>
                                <th scope="col">Max Weight(kg)</th>
                                <th scope="col">Charges</th>
                                <th scope="col">Action</th>
                                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

                            </tr>
                        </thead>
                        <tbody>

                            @if(count($slots)<1) <tr class="trbody">
                                <td colspan="5" align="center">
                                    <h4>No slot found</h4>
                                </td>
                                </tr>
                                @else
                                @foreach($slots as $product)


                                <tr class="trbody">
                                    {{-- <td scope="row">--}}
                                    {{-- <input  type="checkbox" value="{{$product->id}}" data-product_name="{{$product->title}}" id="flexCheckDefault" />--}}
                                    {{-- </td>--}}
                                    <th scope="col">{{$product->id}}</th>
                                    <td class="product_name_data">{{$product->min_weight}} kg</td>
                                    <td>
                                        <?php
                                        if ($product->max_weight == -1) {
                                            echo "or above";
                                        } else { ?>
                                            {{$product->max_weight}}
                                            <?php $max = $product->max_weight; ?> kg
                                        <?php } ?>
                                    </td>
                                    <td>£{{$product->charges}}</td>
                                    <td>
                                        <a href="javascript:edit({{$product->id}},{{$product->min_weight}},{{$product->max_weight}},{{$product->charges}})">Edit</a>
                                        <form method="POST" action="{{route('shippingslots.destroy',$product->id)}}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}

                                            <div class="form-group">
                                                <input type="submit" class="btn btn-sm btn-danger" value="Delete">
                                            </div>
                                        </form>
                                    </td>
                                </tr>

                                @endforeach
                                @endif

                        </tbody>
                    </table>

                    @if(count($slots)>1)
                    {{--show pagination links by laravel and bootstrap--}}
                    {{ $slots->links() }}
                    @endif
                </div>
            </div>



            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Free Delivery Condition
                    </div>
                    <div class="card-body">

                        <p>Shipping will be FREE for all orders above provided amount below.</p>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Amount</label>
                            <input type="number" class="form-control" id="amount" value="{{$settings->shipping_charges}}" placeholder="Provide an amount" step="0.01" required />
                        </div>

                        <input type="button" class="btn btn-primary" value="Update" onclick="update_free_amount()" id="update_free_amount" />
                    </div>
                </div>

            </div>

            <input type="hidden" id="hidden_max_value" name="hidden_max_value" value="<?php echo $max; ?>" />

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Slot</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" method="post" class="needs-validation" novalidate>
                            @csrf
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Min Weight(kg)</label>
                                    <input type="number" class="form-control" id="min_weight" placeholder="Enter Min Weight" step="0.01" required />
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Max Weight(kg)</label>
                                    <input type="number" class="form-control" id="max_weight" placeholder="Enter Max Weight" step="0.01" required />
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Charges</label>
                                    <input type="number" class="form-control" id="charges" placeholder="Enter Charges" step="0.01" required />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="alert alert-danger" id="error"></div>
                                <button class="btn-secandary btn" id="loading">please wait...</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Modal -->
            <div class="modal fade" id="editmodel" tabindex="-1" role="dialog" aria-labelledby="editmodelModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editmodelLabel">Edit Slot</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" method="post" class="needs-validation-edit" novalidate>
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Min Weight(kg)</label>
                                    <input name="hidden_id" type="hidden" class="form-control" id="edit_hidden_id" value="0" required />
                                    <input type="number" class="form-control" value="0" id="edit_min_weight" placeholder="Enter Min Weight" step="0.01" required />
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Max Weight(kg)</label>
                                    <input type="number" class="form-control" value="0" id="edit_max_weight" placeholder="Enter Max Weight" step="0.01" required />
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Charges</label>
                                    <input type="number" class="form-control" value="0" id="edit_charges" placeholder="Enter Charges" step="0.01" required />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="alert alert-danger" id="edit_error"></div>
                                <button class="btn-secandary btn" id="edit_loading">please wait...</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>


<script>
    $(document).ready(function() {});

    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    $("#error").html("");
                    var error = 0;
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();

                    } else {
                        event.preventDefault();
                        let hidden_value = $("#hidden_max_value").val();

                        let min_weight = $("#min_weight").val();
                        let max_weight = $("#max_weight").val();
                        let charges = $("#charges").val();
                        if (min_weight < 0) {
                            //     $("#min_weight").addClass("invalid-feedback");
                            $("#error").html("New slot minimum value must be zero or greator than " + hidden_value);
                            error = 1;

                            return false;
                        }

                        if (min_weight > max_weight && max_weight != -1) {
                            //     $("#min_weight").addClass("invalid-feedback");
                            $("#error").html("Max weight must be greator then minmum weight");
                            error = 1;
                            return false;
                        }
                        if (error == 1) {
                            return false;
                        } else {
                            if (charges == 0) {
                                $("#error").html("Charges must be greator than 0.");
                                return false;
                            } else {
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $.ajax({
                                    type: 'POST',
                                    url: "{{ route('shippingslots.store') }}",
                                    data: {
                                        min_weight: min_weight,
                                        max_weight: max_weight,
                                        charges: charges
                                    },
                                    success: function(data) {
                                        if (data.hasOwnProperty("success")) {
                                            alert(data.message);
                                            window.location.reload();
                                        } else {
                                            $("#error").html("There is must be an error.");
                                        }
                                    }
                                });
                            }
                        }
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            //edit form submission
            var forms_edit = document.getElementsByClassName('needs-validation-edit');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms_edit, function(form) {
                form.addEventListener('submit', function(event) {

                    $("#edit_error").html("");
                    var error = 0;
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();

                    } else {
                        event.preventDefault();
                        let hidden_id = $("#edit_hidden_id").val();

                        let min_weight = $("#edit_min_weight").val();
                        let max_weight = $("#edit_max_weight").val();

                        let charges = $("#edit_charges").val();

                        if (min_weight < 0) {
                            //     $("#min_weight").addClass("invalid-feedback");
                            $("#edit_error").html("New slot minimum value must be zero or greator than " + hidden_value);
                            error = 1;
                            return false;
                        }

                        if (min_weight >= max_weight) {
                            //     $("#min_weight").addClass("invalid-feedback");
                            $("#edit_error").html("Max weight must be greator then minmum weight");
                            error = 1;
                            return false;
                        }
                        if (error == 1) {
                            return false;
                        } else {
                            if (charges == 0) {
                                $("#edit_error").html("Charges must be greator than 0.");
                                return false;
                            } else {
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $.ajax({
                                    type: 'POST',
                                    url: "{{ route('updateSlot') }}",
                                    data: {
                                        id: hidden_id,
                                        min_weight: min_weight,
                                        max_weight: max_weight,
                                        charges: charges
                                    },
                                    success: function(data) {
                                        if (data.hasOwnProperty("success")) {
                                            alert(data.message);
                                            window.location.reload();
                                        } else {
                                            $("#edit_error").html("There is must be an error.");
                                        }
                                    }
                                });
                            }
                        }
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    function update_free_amount() {

        let amount = $("#amount").val();
        if (amount > 0) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: "{{ route('updateShippingFreeAmount') }}",
                data: {
                    amount: amount
                },
                success: function(data) {
                    if (data.hasOwnProperty("success")) {
                        alert(data.message);
                        return false;
                        window.location.reload();
                    } else {
                        $("#edit_error").html("There is must be an error.");
                    }
                }
            });
        } else {
            alert("please provide a valid amount");
        }
    }

    function edit(id, min_weight, max_weight, charges) {
        $("#edit_hidden_id").val(id);
        $("#edit_min_weight").val(min_weight);
        $("#edit_max_weight").val(max_weight);
        $("#edit_charges").val(charges);
        //        $("edit_error").val(error)
        //      $("edit_loading").val(loading)
        $('#editmodel').modal('show');

    }
</script>
@endsection