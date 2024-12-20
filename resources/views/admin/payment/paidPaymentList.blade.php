@can('view payment')
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

            .form-group.required .control-label:after {
                content: "*";
                color: red;
            }

            div.dataTables_wrapper div.dataTables_paginate {
                margin: 0;
                display: none;
                white-space: nowrap;
                text-align: right;
            }

            div.dataTables_wrapper div.dataTables_info {
                padding-top: 0.85em;
                display: none;
            }

            @media (max-width: 768px) {
                .addMember {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .checkLottery {
                    margin-bottom: 20px;
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .search {
                    width: 100%;
                    margin-bottom: 20px;
                }
            }

            @media (max-width: 768px) {
                .clear {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .searchandClear {
                    margin-bottom: 20px;
                    width: 100%;
                }
            }

            @media (max-width:760px) {
                .searchEqubandClear {
                    margin-bottom: 20px;
                    width: 30%;
                }
            }

            @media (max-width: 768px) {
                .checkLotteryandAddMember {
                    margin-bottom: 20px;
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .paymentTab {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .memberTab {
                    width: 100%;
                }
            }

            @media (max-width: 575.98px) {
                #payment-list-table_in_tab {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }

                .table-responsive-sm>.table-bordered {
                    border: 0;
                }
            }
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
                                            <li class="nav-item nav-blue memberTab">
                                                <a class="nav-link active" id="custom-tabs-two-member-tab"
                                                    data-toggle="pill" href="#custom-tabs-two-member" role="tab"
                                                    aria-controls="custom-tabs-two-member" aria-selected="true">
                                                    <span class="fa fa-list"> </span> Paid Payments</a>
                                            </li>
                                            <li class="nav-item paymentTab" id="payment-tab" style="display: none;">
                                                <a class="nav-link" id="custom-tabs-two-messages-tab" data-toggle="pill"
                                                    href="#custom-tabs-two-messages" role="tab"
                                                    aria-controls="custom-tabs-two-messages" aria-selected="false">
                                                    <span class="fa fa-list"> </span> Payment</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="custom-tabs-two-tabContent">
                                            <div class="tab-pane fade show active" id="custom-tabs-two-member"
                                                role="tabpanel" aria-labelledby="custom-tabs-two-member-tab">
                                                @include('admin/payment.addPayment')
                                                {{-- @include('admin/lottery.addLottery')
                                                @include('admin/equb.addEqub')
                                                @include('admin/member.addMember') --}}
                                                <div class="float-left checkLotteryandAddMember" id="member_table_filter">
                                                    {{-- @if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant' && Auth::user()->role != 'finance')
                                                        <button type="button" class=" btn btn-primary checkLottery"
                                                            id="lotteryDatec" data-toggle="modal"
                                                            data-target="#lotteryDateCheckModal"
                                                            style="margin-right: 30px;"> <i class="fa fa-check-square"></i>
                                                            Check Lottery Date</button>
                                                        <button type="button" class=" btn btn-primary addMember"
                                                            id="register" data-toggle="modal" data-target="#myModal"
                                                            style="margin-right: 30px;"> <span class="fa fa-plus-circle">
                                                            </span> Add member</button>
                                                    @endif --}}
                                                </div>
                                                <div class="row">
                                                    <div class="col-7"></div>
                                                    <div class="float-right searchandClear row col-5 mb-2"
                                                        id="member_table_filter">
                                                        <input class="form-control col-10" type="text"
                                                            id="memberSearchText" placeholder="Search Member"
                                                            class="search">
                                                        <button class="btn btn-default clear col-2" id="clearActiveSearch"
                                                            onclick="clearSearchEntry()">
                                                            Clear
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="member_table_data_w" class="col-md-8">

</div>
<div id="member_table_data">
</div>
                                            </div> <!-- Closing the tab-pane -->
                                            <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel"
                                                aria-labelledby="custom-tabs-two-profile-tab">

                                            </div>
                                            <div class="tab-pane fade" id="custom-tabs-two-messages" role="tabpanel"
                                                aria-labelledby="custom-tabs-two-messages-tab">

                                            </div>
                                            <div class="tab-pane fade" id="custom-tabs-two-settings" role="tabpanel"
                                                aria-labelledby="custom-tabs-two-settings-tab">
                                            </div>
                                        </div> <!-- Closing the tab-content -->
                                    </div> <!-- Closing the card-body -->
                                </div> <!-- Closing the card -->
                            </div> <!-- Closing the col-12 -->
                        </div> <!-- Closing the row -->
                    </div> <!-- Closing the container-fluid -->
                </section> <!-- Closing the content -->
            </div> <!-- Closing the content-wrapper -->
        </div> <!-- Closing the wrapper -->
        @include('admin/payment.deletePayment')
        @include('admin/equb.deleteEqub')
        @include('admin/payment.deleteAllPayment')
        @include('admin/payment.editPayment')
        {{-- @include('admin/lottery.deleteLottery')
        @include('admin/member.editMember')
        @include('admin/member.checkLotteryDate')
        @include('admin/equb.editEqub')
        @include('admin/notification.sendNotification')
        @include('admin/lottery.editLottery') --}}
    @endsection
    @section('scripts')
        <script>
            var memberSearchField = document.getElementById('memberSearchText');
            memberSearchField.addEventListener("keydown", function(e) {
                var memberSearchInput = memberSearchField.value;
                if (e.keyCode === 13) { //checks whether the pressed key is "Enter"
                    $.LoadingOverlay("show");
                    searchForMember(memberSearchInput);
                }
            });

            function searchForMember(searchInput) {
                console.log("🚀 ~ searchForMember ~ searchInput:", searchInput)
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('payment/search-paid-payment') }}" + '/' + searchInput + '/0',
                        type: 'get',
                        success: function(data) {
                            $('#member_table_data').html(data);
                            $.LoadingOverlay("hide");
                        }
                    });
                } else {
                    clearSearchEntry();
                    $.LoadingOverlay("hide");
                }
            }

            function loadMoreSearchMembers(searchInput, offsetVal, pageNumberVal) {
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('payment/search-paid-payment') }}" + '/' + searchInput + '/' +
                            offsetVal + '/' +
                            pageNumberVal,
                        type: 'get',
                        success: function(data) {
                            $('#member_table_data').html(data);
                        }
                    });

                }
            }

            function clearSearchEntry() {
                $.LoadingOverlay("show");
                var searchInput = document.getElementById('memberSearchText').value;
                // if (searchInput != "") {
                document.getElementById('memberSearchText').value = "";
                $.ajax({
                    url: "{{ url('payment/clearPendingSearchEntry') }}",
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
                        $.LoadingOverlay("hide");
                    }
                });

                // }
            }

            function pendingMembers(offsetVal, pageNumberVal) {
                $.LoadingOverlay("show");
                $.ajax({
                    url: "{{ url('payment/show-paid-payment') }}" + '/' + offsetVal + '/' + pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
                        $.LoadingOverlay("hide");
                    }
                });
            }

            function searchPendingMembers(offsetVal, pageNumberVal) {
                $.LoadingOverlay("show");
                $.ajax({
                    url: "{{ url('payment/show-paid-payment') }}" + '/' + offsetVal + '/' + pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
                        $.LoadingOverlay("hide");
                    }
                });
            }

            function statusSubmit() {
                document.getElementById("updateStatus").submit();
            }

            function openDeletePaymentModal(item) {
                $('#payment_id').val(item.id);
                $('#deletePaymentModal').modal('show');
                $('#deletePayment').attr('action', '/payment/deletePending/' + $('#payment_id').val())
            }

            function showPaymentProofModal(item) {
                $('#payment_id').val(item.id);
                $("#viewImage").attr("src", "/storage/" + item.payment_proof);
                $('#paymentProofModal').modal('show');
            }

            function approvePayment(item) {
                $('#payment_id').val(item.id);
                $('#approvePaymentModal').modal('show');
                $('#approvePayment').attr('action', '/payment/approvePending/' + $('#payment_id').val())
            }

            function rejectPayment(item) {
                $('#payment_id').val(item.id);
                $('#rejectPaymentModal').modal('show');
                $('#rejectPayment').attr('action', '/payment/rejectPending/' + $('#payment_id').val())
            }

            function openDeleteAllPaymentModal(member, equb) {
                $('#member_id').val(member);
                $('#equb_id').val(equb);
                $('#deleteAllPaymentModal').modal('show');
                $('#deleteAllPayment').attr('action', 'payment/deleteAll/' + $('#member_id').val() + '/' + $('#equb_id').val())
            }

            function openDeleteLotteryModal(item) {
                $('#lottery_id').val(item.id);
                $.ajax({
                    url: '/getRemainingLotteryAmount/' + item.equb_id,
                    method: 'get',
                    success: function(data) {
                        // console.log(data)
                        if (data == 0) {
                            // console.log(data);
                            $('#lotteryPaymentButton').addClass('disabled');
                            $('#lotteryPaymentButton').prop('disabled', true);
                            $('#lotteryEdit').addClass('disabled');
                            $('#lotteryEdit').prop('disabled', true);
                            $('#lotteryDelete').addClass('disabled');
                            $('#lotteryDelete').prop('disabled', true);
                        } else {
                            $('#openDeleteLotteryModal').modal('show');
                        }
                    }
                });
                $('#deleteLottery').attr('action', 'equbTaker/equbTaker-delete/' + $('#lottery_id').val())
            }

            function openApproveLotteryModal(item) {
                console.log("🚀 ~ file: memberList.blade.php:461 ~ openApproveLotteryModal ~ item:", item)
                $('#lottery_idd').val(item.id);
                $('#openApproveLotteryModal').modal('show');
                $('#approveLottery').attr('action', 'equbTaker/equbTaker-change-status/approved/' + $('#lottery_idd').val())
            }

            function openPayLotteryModal(item) {
                console.log("🚀 ~ file: memberList.blade.php:461 ~ openApproveLotteryModal ~ item:", item.id)
                $('#lottery_id_pay').val(item.id);
                $('#openPayLotteryModal').modal('show');
                $('#payLottery').attr('action', 'equbTaker/equbTaker-change-status/paid/' + $('#lottery_id_pay').val())
            }

            function openPaymentEditModal(item) {
                // console.log(item.note)
                $('#payment_id').val(item.id);
                $('#update_member_id').val(item.member_id);
                $('#equb_id').val(item.equb_id);
                $('#editPaymentModal').modal('show');
                $('#update_payment_type>option[value="' + item.payment_type + '"]').prop('selected', true);
                $('#update_payment_amount').val(item.amount);
                let total_amount = item.equb.amount - item.amount
                $('#update_payment_credit').val(total_amount);
                $('#update_payment_remark').val(item.note);
                $('#update_payment_status>option[value="' + item.status + '"]').prop('selected', true);
                $('#updatePayment').attr('action', '/payment/updatePendingPayment/' + $('#update_member_id').val() + '/' + $(
                        '#equb_id')
                    .val() + '/' + $('#payment_id').val());
            }

            $(function() {
                $.LoadingOverlay("show");
                $('#settingNavp').addClass('menu-is-opening menu-open');
                $('#pendingPayments').addClass('active');
                $('#pay').addClass('active');
                $.ajax({
                    url: "{{ url('payment/show-paid-payment') }}" + '/' + 0 + '/' + 1,
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
                        $.LoadingOverlay("hide");
                    }
                });

                $('#addpayment').validate({
                    onfocusout: false,
                    rules: {
                        payment_type: {
                            required: true,
                            minlength: 1,
                            maxlength: 30,
                        },
                        amount: {
                            required: true,
                            maxlength: 10,
                        },
                        status: {
                            required: true,
                        },
                    },
                    messages: {
                        payment_type: {
                            required: "Select payment type",
                            minlength: "payment type must be more than 1 characters long",
                            maxlength: "payment type must be less than 30 characters long",
                        },
                        amount: {
                            required: "Please enter a amount",
                            maxlength: "amount must be less than or equal to 10 number",
                        },
                        status: {
                            required: "Select status",
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
                $('#updatePayment').validate({
                    onfocusout: false,
                    rules: {
                        update_payment_type: {
                            required: true,
                            minlength: 1,
                            maxlength: 30,
                        },
                        update_amount: {
                            required: true,
                            maxlength: 10,
                        },
                        update_status: {
                            required: true,
                        },
                    },
                    messages: {
                        update_payment_type: {
                            required: "Select payment type",
                            minlength: "payment type must be more than 1 characters long",
                            maxlength: "payment type must be less than 30 characters long",
                        },
                        update_amount: {
                            required: "Please enter a amount",
                            maxlength: "amount must be less than or equal to 10 number",
                        },
                        update_status: {
                            required: "Select status",
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
                        const myButton = document.getElementById('addLotteryBtn');
                        myButton.disabled = true;
                    }

                });
            });
        </script>
    @endSection
@endcan