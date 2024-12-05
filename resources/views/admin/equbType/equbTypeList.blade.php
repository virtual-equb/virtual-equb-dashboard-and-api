@can('update equb_type')
@extends('layouts.app')
    @section('styles')
        <style type="text/css">
            td.details-control_equb {
                background: url("{{ url('images/plus20.webp') }}") no-repeat center center;
                cursor: pointer;
            }

            tr.shown td.details-control_equb {
                background: url("{{ url('images/minus20.webp') }}") no-repeat center center;
            }

            td.details-control_payment {
                background: url("{{ url('images/plus20.webp') }}") no-repeat center center;
                cursor: pointer;

            }

            tr.shown td.details-control_payment {
                background: url("{{ url('images/minus20.webp') }}") no-repeat center center;
            }

            div.dataTables_wrapper div.dataTables_info {
                padding-top: 0.85em;
                display: none;
            }

            .form-group.required .control-label:after {
                content: "*";
                color: red;
            }

            .modaloff6 {
                visibility: hidden;
            }

            @media (max-width: 2000px) {
                #equbType-list-table {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }

                .table-responsive-sm>.table-bordered {
                    border: 0;
                }
            }

            @media (max-width: 768px) {
                .addEqub {
                    margin-bottom: 20px;
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .equbTypeTab {
                    width: 100%;
                }
            }

            /*@media (max-width: 768px) {                                                                                                              }*/
        </style>
    @endsection
    @section('content')
    
        <div class="wrapper">
            <div class="content-wrapper">
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card ">
                                    <div class="card-header">
                                        <ul class="nav nav-pills" id="custom-tabs-two-tab" role="tablist">
                                            <li class="nav-item nav-blue equbTypeTab">
                                                <a class="nav-link active" id="custom-tabs-two-member-tab"
                                                    data-toggle="pill" href="#custom-tabs-two-member" role="tab"
                                                    aria-controls="custom-tabs-two-member" aria-selected="true"> 
                                                    <span class="fa fa-list"></span> 
                                                    Equb Type
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="custom-tabs-two-tabContent">
                                            <div class="tab-pane fade show active" id="custom-tabs-two-member"
                                                role="tabpanel" aria-labelledby="custom-tabs-two-member-tab">
                                                @can('create equb_type') 
                                                    @include('admin/equbType.addEqubType')
                                                @endcan
                                                <table id="equbType-list-table" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <td>Image</td>
                                                            <th>Equb</th>
                                                            <th>Name</th>
                                                            <th>Round</th>
                                                            <th>Rote</th>
                                                            <th>Type</th>
                                                            <th>Space Left (Quota)</th>
                                                            <th>Expected members (Quota)</th>
                                                            <th>Total joined members (Quota)</th>
                                                            <th>Lottery Date</th>
                                                            <th>Equb Amount (Birr)</th>
                                                            <th>Expected Amount (Birr)</th>
                                                            <th>Remark</th>
                                                            <th>Status</th>
                                                            <th>Registered At </th>
                                                            <th style="width:60px">Action </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($equbTypes as $key => $item)
                                                            <tr>
                                                                <td>{{ $key + 1 }}</td>
                                                               
                                                                <td style="background-color: rgb(76, 175, 80); width: 60px; text-align: center;">
                                                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" 
                                                                        style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                                                </td>
                                                                <td>{{ $item->mainEqub->name ?? 'N/A'}}</td>
                                                                <td>{{ $item->name }}</td>
                                                                <td>{{ $item->round }}</td>
                                                                <td>{{ $item->rote }}</td>
                                                                <td>{{ $item->type }}</td>
                                                                <td>{{ $item->remaining_quota }}</td>
                                                                <td>{{ $item->expected_members }}</td>
                                                                <td>{{ $item->total_members }}</td>
                                                                <td>
                                                                    <?php
                                                                    if ($item['lottery_date']) {
                                                                        $lottery_date = new DateTime($item['lottery_date']);
                                                                        $lotteryDate = $lottery_date->format('M-j-Y');
                                                                        echo $lotteryDate;
                                                                    } else {
                                                                        echo '';
                                                                    } ?>
                                                                </td>
                                                                <td>{{ $item->amount }}</td>
                                                                <td>{{ $item->total_amount }}</td>
                                                                <td>{{ $item->remark }}</td>
                                                                <td>{{ $item->status }}</td>
                                                                <td>
                                                                    <?php
                                                                    $toCreatedAt = new DateTime($item['created_at']);
                                                                    $createdDate = $toCreatedAt->format('M-j-Y');
                                                                    echo $createdDate; ?>
                                                                </td>
                                                                    <td>

                                                                        <div class='dropdown'>
                                                                            <button
                                                                                class='btn btn-secondary btn-sm btn-flat dropdown-toggle'
                                                                                type='button'
                                                                                data-toggle='dropdown'>Menu<span
                                                                                    class='caret'></span></button>
                                                                            <ul class='dropdown-menu p-4'>
                                                                                @can('update equb_type')
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="text-secondary btn btn-flat"
                                                                                        onclick="openEditModal({{ $item }})"><span
                                                                                            class="fa fa-edit"> </span>
                                                                                        Edit</a>
                                                                                </li>
                                                                                @endcan
                                                                                @can('delete equb_type')
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="text-secondary btn btn-flat"
                                                                                        onclick="openDeleteModal({{ $item }})"><i
                                                                                            class="fas fa-trash-alt"></i>
                                                                                        Delete</a>
                                                                                </li>
                                                                                @endcan
                                                                                @can('deactivate equb_type')
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="text-secondary btn btn-flat"
                                                                                        onclick="statusChange({{ $item }})"
                                                                                        style="margin-right:10px;"
                                                                                        id="statuss" name="statuss"><i
                                                                                            class="fab fa-shopware"></i>
                                                                                        <?php if ($item->status == 'Active') {
                                                                                            echo 'Deactivate';
                                                                                        } else {
                                                                                            echo 'Activate';
                                                                                        }
                                                                                        ?>
                                                                                    </a>
                                                                                </li>
                                                                                @endcan
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="text-secondary btn btn-flat view-icon"
                                                                                        equb-type-image="{{ $item?->image }}"
                                                                                        equb-type-id="{{ $item?->id }}"><i
                                                                                            class="fas fa-image"></i>
                                                                                        View Icon</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel"
                                                aria-labelledby="custom-tabs-two-profile-tab">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="table-responsive">
            <div class="modal modal-danger fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <p class="modal-title" id="exampleModalLabel">Update equb type status</p>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" method="post" id="updateStatus">
                            <div class="modal-body">
                                @csrf
                                @method('PUT')
                                <input id="equbType_id" name="equbType_id" hidden value="">
                                <p class="text-center">Are you sure you want to update status?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-sm btn-danger">Update</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <p class="modal-title" id="exampleModalLabel">Delete Equb type</p>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" id="deletePayment">
                                @csrf
                                @method('DELETE')
                                <input id="id" name="id" hidden value="">
                                <p class="text-center">Are you sure you want to delete this Equb type?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin/equbType.editEqubType')
    @endsection
    @section('scripts')
        <script>
$(document).ready(function() {
    // Set Expected Members to 100 and make it unchangeable
    $('#expected_members').val(105).prop('readonly', true).parent().removeClass('d-none');

    // Show or hide fields based on the selected type
    $("#type").on("change", function() {
        var type = $(this).find("option:selected").val();

        if (type === "Automatic") {
            startDate.classList.remove("d-none");
            endDate.classList.remove("d-none");
            quota.classList.remove("d-none");
            amount.classList.remove("d-none");
            members.classList.remove("d-none");
            expectedMembers.classList.remove("d-none"); // Show expected members
            $('#total_amount').parent().removeClass('d-none'); // Show total amount field

            // Set required attributes
            startDate.required = true;
            endDate.required = true;
            quota.required = true;
            amount.required = true;
            members.required = true;
            expectedMembers.required = true;

        } else if (type === "Seasonal") {
            startDate.classList.remove("d-none");
            endDate.classList.remove("d-none");
            amount.classList.remove("d-none"); // Show amount input

            // Remove expected members from the DOM
            $("#expected_members").parent().remove(); // Remove expected members input from DOM

            // Remove total amount from the DOM
            $('#total_amount').parent().remove(); // Remove total amount field from DOM

            // Hide total amount display
            totalAmountDisplay.innerText = ''; // Clear total amount display

            // Set required attributes for seasonal
            startDate.required = true;
            amount.required = true;

            // Event listener for start_date change
            $("#start_date").on("change", function() {
                let startDateValue = $(this).val(); // Get the value of the start date input
                if (startDateValue) {
                    let date = new Date(startDateValue); // Create a Date object
                    date.setDate(date.getDate() + 21); // Add 21 days for Seasonal

                    // Set the end date in the datepicker
                    $('#end_date').datepicker('setDate', date);
                }
            });

        } else {
            lotteryDate.classList.add("d-none");
            startDate.classList.add("d-none");
            endDate.classList.add("d-none");
            quota.classList.add("d-none");
            amount.classList.add("d-none");
            members.classList.add("d-none");
            expectedMembers.classList.add("d-none"); // Ensure expected members are hidden
            $('#total_amount').parent().removeClass('d-none'); // Show total amount field again if needed

            // Remove required attributes
            startDate.required = false;
            endDate.required = false;
            quota.required = false;
            amount.required = false;
            members.required = false;
            expectedMembers.required = false;

            // Clear total amount display
            totalAmountDisplay.innerText = '';
        }
    });

    // Calculate Total Amount based on Amount
    $('#amount').on('input', function() {
        const amountValue = parseFloat($('#amount').val()) || 0;
        const expectedMembersValue = 100; // Fixed expected members
        const totalAmount = amountValue * expectedMembersValue; // Calculate total amount

        $('#total_amount').val(totalAmount); // Update Total Amount
    });

    // Existing code for viewing icon
    $(document).on('click', '.view-icon', function(e) {
        e.preventDefault();

        var adminId = $(this).attr('equb-type-id');
        var image = $(this).attr('equb-type-image');

        $("#viewImage").attr("src", "/storage/" + image);
        $('#modaloff6').modal('show');
    });

    $('.textareaa').summernote();

    const selectBox = document.getElementById("type");
    const lotteryDate = document.getElementById("lottery_date_div");
    const startDate = document.getElementById("start_date_div");
    const endDate = document.getElementById("end_date_div");
    const quota = document.getElementById("quota_div");
    const rote = document.getElementById("rote");
    const amount = document.getElementById("amount_div");
    const members = document.getElementById('members_div');
    const expectedMembers = document.getElementById('expected_members_div');
    const totalAmountDisplay = document.getElementById('total_amount_display'); // Element to show total amount

    // Additional event listener for type change
    $("#type").on("change", function() {
        var type = $(this).find("option:selected").val();

        if (type === "Automatic") {
            startDate.classList.remove("d-none");
            endDate.classList.remove("d-none");
            quota.classList.remove("d-none");
            amount.classList.remove("d-none");
            members.classList.remove("d-none");
            expectedMembers.classList.remove("d-none"); // Show expected members
            $('#total_amount').parent().removeClass('d-none'); // Show total amount field

            // Set required attributes
            startDate.required = true;
            endDate.required = true;
            quota.required = true;
            amount.required = true;
            members.required = true;
            expectedMembers.required = true;

        } else if (type === "Seasonal") {
            startDate.classList.remove("d-none");
            endDate.classList.remove("d-none");
            amount.classList.remove("d-none"); // Show amount input

            // Remove expected members and total amount from the DOM
            $("#expected_members").parent().remove(); // Remove expected members input from DOM
            $('#total_amount').parent().remove(); // Remove total amount field from DOM

            // Hide total amount display
            totalAmountDisplay.innerText = ''; // Clear total amount display

            // Set required attributes for seasonal
            startDate.required = true;
            amount.required = true;

            // Event listener for start_date change
            $("#start_date").on("change", function() {
                let startDateValue = $(this).val(); // Get the value of the start date input
                if (startDateValue) {
                    let date = new Date(startDateValue); // Create a Date object
                    date.setDate(date.getDate() + 21); // Add 21 days for Seasonal

                    // Set the end date in the datepicker
                    $('#end_date').datepicker('setDate', date);
                }
            });

        } else {
            lotteryDate.classList.add("d-none");
            startDate.classList.add("d-none");
            endDate.classList.add("d-none");
            quota.classList.add("d-none");
            amount.classList.add("d-none");
            members.classList.add("d-none");
            expectedMembers.classList.add("d-none"); // Ensure expected members are hidden
            $('#total_amount').parent().removeClass('d-none'); // Show total amount field again if needed

            // Remove required attributes
            startDate.required = false;
            endDate.required = false;
            quota.required = false;
            amount.required = false;
            members.required = false;
            expectedMembers.required = false;

            // Clear total amount display
            totalAmountDisplay.innerText = '';
        }
    });

    function updateExpectedMembers() {
        // Assuming members input is a number, display it in expected members
        expectedMembers.value = members.value; // Adjust logic as needed
    }

    function updateTotalAmount() {
        const amountValue = parseFloat(amount.value) || 0; // Get the amount value
        const quotaValue = parseInt(quota.value) || 0; // Get the quota value
        const totalAmount = amountValue * quotaValue; // Calculate total amount
    }
});


            $(document).ready(function() {
            // Initialize the jQuery UI datepicker for the end date
            $('#end_date').datepicker();

            // Event listener for start_date change
            $("#start_date").on("change", function() {
    let startDateValue = $(this).val(); // Get the value of the start date input
    let type = $("#type").find("option:selected").val(); // Get the selected type value

    if (startDateValue) {
        let date = new Date(startDateValue); // Create a Date object

        // Determine end date based on the selected type
        if (type === "Automatic") {
            date.setDate(date.getDate() + 105); // Add 105 days for Automatic
        } else if (type === "Seasonal") {
            date.setDate(date.getDate() + 21); // Add 21 days for Seasonal
        }

        // Set the end date in the datepicker
        $('#end_date').datepicker('setDate', date);
    }
});
        });
            $("#update_quota").on("keyup", function() {
                let startdate = document.getElementById('update_start_date').value;
                let quota = document.getElementById('update_quota').value;
                var date = new Date(startdate);
                date.setDate(date.getDate() + (7 * quota));
                $('#update_end_date').datepicker('setDate', new Date(date));
                $('#update_end_date').datepicker('destroy');
            });

            $(document).ready(function() {
                const selectBox = document.getElementById("update_type");
                const lotteryDate = document.getElementById("update_lottery_date_div");
                const startDate = document.getElementById("update_start_date_div");
                const endDate = document.getElementById("update_end_date_div");
                const quota = document.getElementById("update_quota_div");
                const update_rote = document.getElementById("update_rote");
                const amount = document.getElementById("update_amount_div");
                const members = document.getElementById("update_members_div");
                const total_amount = document.getElementById("update_total_amount_div");
                const total_members = document.getElementById("update_total_members_div");
                const update_options = update_rote.options;
                $("#update_type").on("change", function() {
                    var type = $(this).find("option:selected").val();
                    if (type === "Automatic") {
                        lotteryDate.classList.remove("d-none");
                        startDate.classList.remove("d-none");
                        endDate.classList.remove("d-none");
                        quota.classList.remove("d-none");
                        amount.classList.remove("d-none");
                        members.classList.remove("d-none");
                        //for (var i = 1; i < update_options.length; i++) {
                        //    update_options[i].disabled = false;
                        //   if (update_options[i].value !== "Weekly") {
                        //      update_options[i].disabled = true;
                        //  }
                        // }
                        // lotteryDate.required = true;
                        startDate.required = true;
                        endDate.required = true;
                        quota.required = true;
                        amount.required = true;
                        members.required = true;
                    } else {
                        lotteryDate.classList.add("d-none");
                        startDate.classList.add("d-none");
                        endDate.classList.add("d-none");
                        quota.classList.add("d-none");
                        //for (var i = 1; i < update_options.length; i++) {
                        //   update_options[i].disabled = false;
                        //   if (update_options[i].value !== "Daily") {
                        //      update_options[i].disabled = true;
                        //  }
                        //}
                        lotteryDate.required = false;
                        startDate.required = false;
                        endDate.required = false;
                        quota.required = false;
                        amount.required = false;
                        members.required = false;
                    }
                });
                $("#start_date").datetimepicker({
                    'format': "YYYY-MM-DD",
                    'minDate': new Date(),
                }).on('dp.change', function(e) {
                    const quota = document.getElementById('quota');
                    quota.value = '';
                });

            });
            var datePickerOptions = {
                format: 'yyyy-mm-dd',
                clearBtn: true,
                // multidate: true,
                todayHighlight: true,
                startDate: new Date()
            };
            $("#lottery_date").datepicker(datePickerOptions);
            // $("#start_date").datepicker(datePickerOptions);
            $("#end_date").datepicker(datePickerOptions);
            var updatedatePickerOptions = {
                format: 'yyyy-mm-dd',
                clearBtn: true,
                // multidate: true,
                todayHighlight: true,
                startDate: new Date()
            };
            $("#update_lottery_date").datepicker(updatedatePickerOptions);
            // $("#update_start_date").datepicker(updatedatePickerOptions);
            $("#update_end_date").datepicker(updatedatePickerOptions);

            $("#update_start_date").datetimepicker({
                'format': "YYYY-MM-DD",
                'minDate': new Date(),
            }).on('dp.change', function(e) {
                const update_quota = document.getElementById('update_quota');
                update_quota.value = '';
            });

            function openDeleteModal(item) {
                $('#id').val(item.id);
                $('#deleteModal').modal('show');
                $('#deletePayment').attr('action', 'equbType/delete/' + $('#id').val())
            }

            function statusChange(item) {
                $('#equbType_id').val(item.id);
                $('#statusModal').modal('show');
                $('#updateStatus').attr('action', "{{ url('equbType/updateStatus') }}" + '/' + $('#equbType_id').val());

            }
            function openEditModal(item) {
    $('#did').val(item.id);
    $('#editEqubTypeModal').modal('show');
    
    // Populate fields with item data
    $('#update_main_equb').val(item.main_equb.id); // Ensure this field is populated
    $('#update_name').val(item.name);
    $('#update_round').val(item.round);
    $('#update_rote').val(item.rote);
    $('#update_type').val(item.type);
    $('#update_remark').val(item.remark);
    $('#amount').val(item.amount); 
    $('#member').val(item.expected_members);
    $('#total_amount').val(item.total_amount);
    $('#total_members').val(item.total_expected_members);
    $('#update_lottery_date').val(item.lottery_date);
    $('#update_start_date').datepicker('setDate', new Date(item.start_date));
    $('#update_end_date').datepicker('setDate', new Date(item.end_date));
    $('#update_quota').val(item.quota);
    $('#update_terms').summernote('code', item.terms);

    // Manage visibility and required fields based on type
    const fieldsToToggle = {
        "Automatic": [
            "#update_lottery_date_div", 
            "#update_start_date_div", 
            "#update_end_date_div", 
            "#update_quota_div", 
            "#update_amount_div", 
            "#update_members_div"
        ],
        "Manual": [
            "#update_amount_div", 
            "#update_members_div"
        ]
    };

    const isAutomatic = item.type === "Automatic";
    fieldsToToggle["Automatic"].forEach(selector => {
        $(selector).toggleClass("d-none", !isAutomatic);
        $(selector).find('input, select').prop('required', isAutomatic);
    });

    fieldsToToggle["Manual"].forEach(selector => {
        $(selector).toggleClass("d-none", isAutomatic);
        $(selector).find('input, select').prop('required', !isAutomatic);
    });

    // Set the form action for updating
    $('#updateEqubType').attr('action', 'equbType/update/' + $('#did').val());
}



            function editEqubTypeValidation() {
                $('#updateEqubType').validate({
                    onfocusout: false,
                    rules: {
                        update_main_equb:{
                            required: true,
                            minlength: 1,
                            maxlength: 30, 
                        },
                        update_name: {
                            required: true,
                            minlength: 1,
                            maxlength: 30,

                        },
                        update_round: {
                            required: true,
                            number: true,
                        },
                        update_rote: {
                            required: true,
                        },
                        update_type: {
                            required: true,
                            remote: {
                                url: '{{ url('nameCheckForUpdate') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    update_main_equb: function() {
                                        return $('#updateEqubType :input[name="update_main_equb"]').val();
                                    },
                                    update_name: function() {
                                        return $('#updateEqubType :input[name="update_name"]').val();
                                    },
                                    update_round: function() {
                                        return $('#updateEqubType :input[name="update_round"]').val();
                                    },
                                    did: function() {
                                        return $('#updateEqubType :input[name="did"]').val();
                                    },

                                }
                            },
                        },
                        update_description: {

                        },
                        update_status: {
                            required: true,
                        },
                    },
                    messages: {
                        update_main_equb: {
                            required: "Please select Equb",
                        },
                        update_name: {
                            required: "Please enter a name",
                            minlength: "Name must be more than 1 characters long",
                            maxlength: "Name must be less than 30 characters long",
                        },
                        update_round: {
                            required: "Please enter a round",
                            number: "Please enter number",
                            // remote: "Equb Type already exist, Please check equb name and round",
                        },
                        update_status: {
                            required: "Please enter status",
                        },
                        update_rote: {
                            required: "Please select a rote",
                        },
                        update_type: {
                            required: "Please select a type",
                            remote: "Equb Type already exist",
                        },
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                        $.LoadingOverlay("hide");
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                        $.LoadingOverlay("hide");
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                        $.LoadingOverlay("hide");
                    },
                    submitHandler: function(form) {
                        form.submit();
                        $.LoadingOverlay("show");
                    }

                });
            }
            // $("#addEqubType").submit(function() {
            //     $.LoadingOverlay("show");
            // });
            // $("#updateEqubType").submit(function() {
            //     $.LoadingOverlay("show");
            // });
            $("#updateStatus").submit(function() {
                $.LoadingOverlay("show");
            });
            $("#deletePayment").submit(function() {
                $.LoadingOverlay("show");
            });

            function addEqubTypeValidation() {
                $('#addEqubType').validate({
                    onfocusout: false,
                    rules: {
                        main_equb: {
                            required: true,
                        },
                        name: {
                            required: true,
                            minlength: 1,
                            maxlength: 30,

                        },
                        round: {
                            required: true,
                            number: true,
                        },
                        rote: {
                            required: true,
                        },
                        type: {
                            required: true,
                            remote: {
                                url: '{{ url('nameCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    main_equb: function() {
                                        return $('#addEqubType :input[name="main_equb"]').val();
                                    },
                                    name: function() {
                                        return $('#addEqubType :input[name="name"]').val();
                                    },
                                    round: function() {
                                        return $('#addEqubType :input[name="round"]').val();
                                    },
                                    type: function() {
                                        return $('#addEqubType :input[name="type"]').val();
                                    },
                                    rote: function() {
                                        return $('#addEqubType :input[name="rote"]').val();
                                    },

                                }
                            },
                        },
                        // lottery_date: {
                        //     required: true,
                        // },
                        start_date: {
                            required: true,
                        },
                        quota: {
                            required: true,
                        },
                        description: {

                        },
                        status: {
                            required: true,
                        },
                    },
                    messages: {
                        round: {
                            required: "Please select Equb",
                        },
                        name: {
                            required: "Please enter a name",
                            minlength: "Name must be more than 1 characters long",
                            maxlength: "Name must be less than 30 characters long",
                        },
                        round: {
                            required: "Please enter a round",
                            number: "Please enter number",
                        },
                        rote: {
                            required: "Please select a rote",
                        },
                        type: {
                            required: "Please select a type",
                            remote: "Equb Type already exist",
                        },
                        // lottery_date: {
                        //     required: "Please select a lottery date",
                        // },
                        start_date: {
                            required: "Please select a start date",
                        },
                        quota: {
                            required: "Please enter a quota",
                        },
                        status: {
                            required: "Please enter status",
                        },
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                        $.LoadingOverlay("hide");
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                        $.LoadingOverlay("hide");
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                        $.LoadingOverlay("hide");
                    },
                    submitHandler: function(form) {
                        form.submit();
                        $.LoadingOverlay("show");
                    }

                });
            }
            $(function() {
                $('#addEqubType').validate({
                    onfocusout: false,
                    rules: {
                        main_equb: {
                            required: true,
                        },
                        name: {
                            required: true,
                            minlength: 1,
                            maxlength: 30,

                        },
                        round: {
                            required: true,
                            number: true,
                        },
                        description: {

                        },
                        rote: {
                            required: true,
                        },
                        type: {
                            required: true,
                            remote: {
                                url: '{{ url('nameCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    main_equb: function() {
                                        return $('#addEqubType :input[name="main_equb"]').val();
                                    },
                                    name: function() {
                                        return $('#addEqubType :input[name="name"]').val();
                                    },
                                    round: function() {
                                        return $('#addEqubType :input[name="round"]').val();
                                    },
                                    type: function() {
                                        return $('#addEqubType :input[name="type"]').val();
                                    },
                                    rote: function() {
                                        return $('#addEqubType :input[name="rote"]').val();
                                    },
                                }
                            },
                        },
                        // lottery_date: {
                        //     required: true,
                        // },
                        status: {
                            required: true,
                        },
                    },
                    messages: {
                        main_equb: {
                            required: "Please select a Equb",
                            remote: "Equb already exist",
                        },
                        name: {
                            required: "Please enter a name",
                            minlength: "Name must be more than 1 characters long",
                            maxlength: "Name must be less than 30 characters long",
                        },
                        round: {
                            required: "Please enter a round",
                            number: "Please enter number",
                        },
                        rote: {
                            required: "Please select a rote",
                        },
                        type: {
                            required: "Please select a type",
                            remote: "Equb Type already exist",
                        },
                        // lottery_date: {
                        //     required: "Please select a lottery date",
                        // },
                        status: {
                            required: "Please enter status",
                        },
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                        $.LoadingOverlay("hide");
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                        $.LoadingOverlay("hide");
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                        $.LoadingOverlay("hide");
                    },
                    submitHandler: function(form) {
                        form.submit();
                        $.LoadingOverlay("show");
                    }

                });
                $('#updateEqubType').validate({
                    onfocusout: false,
                    rules: {
                        update_main_equb: {
                            required: true
                        },
                        update_name: {
                            required: true,
                            minlength: 1,
                            maxlength: 30,

                        },
                        update_round: {
                            required: true,
                            number: true,
                        },
                        update_description: {

                        },
                        update_rote: {
                            required: true,
                        },
                        update_type: {
                            required: true,
                            remote: {
                                url: '{{ url('nameCheckForUpdate') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    update_main_equb: function() {
                                        return $('#updateEqubType :input[name="main_equb"]').val();
                                    },
                                    update_name: function() {
                                        return $('#updateEqubType :input[name="update_name"]').val();
                                    },
                                    update_round: function() {
                                        return $('#updateEqubType :input[name="update_round"]').val();
                                    },
                                    update_rote: function() {
                                        return $('#updateEqubType :input[name="update_rote"]').val();
                                    },
                                    update_type: function() {
                                        return $('#updateEqubType :input[name="update_type"]').val();
                                    },
                                    did: function() {
                                        return $('#updateEqubType :input[name="did"]').val();
                                    },

                                }
                            },
                        },
                        update_status: {
                            required: true,
                        },
                    },
                    messages: {
                        update_main_equb: {
                            required: "Please select a Equb",
                            remote: "Equb already exist",
                        },
                        update_name: {
                            required: "Please enter a name",
                            minlength: "Name must be more than 1 characters long",
                            maxlength: "Name must be less than 30 characters long",
                        },
                        update_round: {
                            required: "Please enter a round",
                            number: "Please enter number",
                            // remote: "Equb Type already exist, Please check equb name and round",
                        },
                        update_rote: {
                            required: "Please select a rote",
                        },
                        update_type: {
                            required: "Please select a type",
                            remote: "Equb Type already exist",
                        },
                        update_status: {
                            required: "Please enter status",
                        },
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                        $.LoadingOverlay("hide");
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                        $.LoadingOverlay("hide");
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                        $.LoadingOverlay("hide");
                    },
                    submitHandler: function(form) {
                        form.submit();
                        $.LoadingOverlay("show");
                    }

                });
                $('#nav-ety').addClass('menu-is-opening menu-open');
                $('#et').addClass('active');
                $("#equbType-list-table").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "searching": true,
                    "autoWidth": false,
                    language: {
                        search: "",
                        searchPlaceholder: "Search",
                    },
                    //"buttons": ["excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#equbType-list-table_wrapper .col-md-6:eq(0)')
                $('#equbType-list-table_filter').prepend(
                    `<button type="button" class=" btn btn-primary addEqub" id="register" data-toggle="modal" data-target="#myModal" style="margin-right: 20px;"> <span class="fa fa-plus-circle"> </span> Add </button>`
                )
                $('#equbType-list-table_filter').prepend(
                    `<button type="button" class=" btn btn-primary addEqub" id="draw" data-toggle="modal" data-target="#drawModal" style="margin-right: 20px;"> <span class="fa fa-random"> </span> Draw Todays Winner</button>`
                )

                $("#DeactiveEqubType-list-table").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "searching": true,
                    "autoWidth": false,
                    language: {
                        search: "",
                        searchPlaceholder: "Search",
                    },
                    "buttons": ["excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#DeactiveEqubType-list-table_wrapper .col-md-6:eq(0)')
            });
        </script>
@endSection
@endcan