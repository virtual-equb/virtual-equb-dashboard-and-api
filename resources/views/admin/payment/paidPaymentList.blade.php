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
                    <div class="row mt-2">
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totalPaidPayment }}</h3>
                                    <p>Total Paid Transactions</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $totalOfflinePayment }}</h3>
                                    <p>Offline Payment Transaction</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $totalOnlinePayment }}</h3>
                                    <p>Online Payment Transaction</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card ">
                                <div class="card-header">
                                    <h4>Paid Payments</h4>
                                </div>
                                <div class="card-body">
                                    <div id="paid_payment_table_data" class="table-responsive">
                                        <table id="paid-payment-table" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Full Name</th>
                                                    <th>Phone</th>
                                                    <th>Payment Type</th>
                                                    <th>Amount</th>
                                                    <th>Credit</th>
                                                    <th>Balance</th>
                                                    <th>Status</th>
                                                    <th>Registered At</th>
                                                    <th style="width: 50px">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($paidPayments as $index => $paid)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $paid->member->full_name}}</td>
                                                    <td>{{ $paid->member->phone}}</td>
                                                    <td>{{ $paid->payment_type}}</td>
                                                    <td>{{ number_format($paid->amount, 2) }}</td>
                                                    <td>{{ number_format($paid->creadit, 2) }}</td>
                                                    <td>{{ number_format($paid->balance, 2) }}</td>
                                                    <td>{{ $paid->status}}</td>
                                                    <td>{{ \Carbon\Carbon::parse($paid->created_at)->format('M-j-Y') }}</td>
                                                    <td>
                                                        <div class='dropdown'>
                                                            <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                                            <ul class='dropdown-menu p-4'>
                                                                <li>
                                                                    <a href="javascript:void(0);" class="btn-sm btn btn-flat"
                                                                        onclick="openPaymentEditModal({{ json_encode($paid) }})">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:void(0);" class="btn-sm btn btn-flat"
                                                                        onclick="openDeletePaymentModal({{ json_encode($paid) }})">
                                                                        <i class="fas fa-trash-alt"></i> Delete
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:void(0);" class="btn-sm btn btn-flat"
                                                                        onclick="showPaymentProofModal({{ json_encode($paid) }})">
                                                                        <i class="fas fa-eye"></i> View Proof
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @include('admin/payment.deletePayment')
    @include('admin/payment.editPayment')
@endsection

@section('scripts')
    <script>
        function openDeletePaymentModal(paid) {
            $('#payment_id').val(paid.id);
            $('#deletePaymentModal').modal('show');
            $('#deletePayment').attr('action', '/payment/deletePending/' + $('#payment_id').val())
        }

        function showPaymentProofModal(paid) {
            $('#payment_id').val(paid.id);
            $("#viewImage").attr("src", "/storage/" + paid.payment_proof);
            $('#paymentProofModal').modal('show');
        }

        function approvePayment(paid) {
            $('#payment_id').val(paid.id);
            $('#approvePaymentModal').modal('show');
            $('#approvePayment').attr('action', '/payment/approvePending/' + $('#payment_id').val())
        }

        function rejectPayment(paid) {
            $('#payment_id').val(paid.id);
            $('#rejectPaymentModal').modal('show');
            $('#rejectPayment').attr('action', '/payment/rejectPending/' + $('#payment_id').val())
        }

        function openDeleteAllPaymentModal(member, equb) {
            $('#member_id').val(member);
            $('#equb_id').val(equb);
            $('#deleteAllPaymentModal').modal('show');
            $('#deleteAllPayment').attr('action', 'payment/deleteAll/' + $('#member_id').val() + '/' + $('#equb_id').val())
        }

        function openDeleteLotteryModal(paid) {
            $('#lottery_id').val(paid.id);
            $.ajax({
                url: '/getRemainingLotteryAmount/' + paid.equb_id,
                method: 'get',
                success: function(data) {
                    if (data == 0) {
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

        function openApproveLotteryModal(paid) {
            console.log("🚀 ~ file: memberList.blade.php:461 ~ openApproveLotteryModal ~ paid:", paid)
            $('#lottery_idd').val(paid.id);
            $('#openApproveLotteryModal').modal('show');
            $('#approveLottery').attr('action', 'equbTaker/equbTaker-change-status/approved/' + $('#lottery_idd').val())
        }

        function openPayLotteryModal(paid) {
            console.log("🚀 ~ file: memberList.blade.php:461 ~ openApproveLotteryModal ~ paid:", paid.id)
            $('#lottery_id_pay').val(paid.id);
            $('#openPayLotteryModal').modal('show');
            $('#payLottery').attr('action', 'equbTaker/equbTaker-change-status/paid/' + $('#lottery_id_pay').val())
        }

        function openPaymentEditModal(paid) {
            $('#payment_id').val(paid.id);
            $('#update_member_id').val(paid.member_id);
            $('#equb_id').val(paid.equb_id);
            $('#editPaymentModal').modal('show');
            $('#update_payment_type>option[value="' + paid.payment_type + '"]').prop('selected', true);
            $('#update_payment_amount').val(paid.amount);
            let total_amount = paid.equb.amount - paid.amount
            $('#update_payment_credit').val(total_amount);
            $('#update_payment_remark').val(paid.note);
            $('#update_payment_status>option[value="' + paid.status + '"]').prop('selected', true);
            $('#updatePayment').attr('action', '/payment/updatePendingPayment/' + $('#update_member_id').val() + '/' + $(
                    '#equb_id')
                .val() + '/' + $('#payment_id').val());
        }

        $(function() {
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

            $("#paid-payment-table").DataTable({
                "responsive": false,
                "lengthChange": false,
                "searching": true,
                "autoWidth": false,
                language: {
                    search: "",
                    searchPlaceholder: "Search",
                },
                "buttons": ["excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#paid_payment_table_data .col-md-6:eq(0)');
        });
    </script>
@endSection
@endcan