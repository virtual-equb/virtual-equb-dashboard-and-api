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
                                        <h3>{{ $totalPendingPayment }}</h3>
                                        <p>Total Pending Transactions</p>
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
                                            <h4>Pending Payments</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="pending_payment_table_data" class="table-responsive">
                                            <table id="pending-payment-table" class="table table-bordered table-striped">
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
                                                    @foreach($pendingPayments as $index => $pending)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $pending->member->full_name}}</td>
                                                        <td>{{ $pending->member->phone}}</td>
                                                        <td>{{ $pending->payment_type}}</td>
                                                        <td>{{ number_format($pending->amount, 2) }}</td>
                                                        <td>{{ number_format($pending->creadit, 2) }}</td>
                                                        <td>{{ number_format($pending->balance, 2) }}</td>
                                                        <td>{{ $pending->status}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($pending->created_at)->format('M-j-Y') }}</td>
                                                        @if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant')
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-secondary btn-sm btn-flat dropdown-toggle" type="button" data-toggle="dropdown"> Menu <span class="caret"></span></button>
                                                                    <ul class="dropdown-menu p-4">
                                                                        @if (Auth::user()->role != 'finance')
                                                                            <li>
                                                                                <a href="javascript:void(0);" class="btn-sm btn btn-flat" onclick="openPaymentEditModal({{ $pending }})">
                                                                                    <span class="fa fa-edit"></span> Edit
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:void(0);" class="btn-sm btn btn-flat" onclick="openDeletePaymentModal({{ $pending }})">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:void(0);" class="btn-sm btn btn-flat" onclick="showPaymentProofModal({{ $pending }})">
                                                                                    <i class="fas fa-image"></i> Show Proof
                                                                                </a>
                                                                            </li>
                                                                            @if ($pending->status == 'pending' || $pending->status == 'unpaid')
                                                                                <li>
                                                                                    <a href="javascript:void(0);" class="btn-sm btn btn-flat" onclick="approvePayment({{ $pending }})">
                                                                                        <i class="fas fa-check"></i> Approve
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                            @if ($pending->status == 'pending')
                                                                                <li>
                                                                                    <a href="javascript:void(0);" class="btn-sm btn btn-flat" onclick="rejectPayment({{ $pending }})">
                                                                                        <i class="fas fa-times"></i> Reject
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                        @endif
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        @endif
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
            function openDeletePaymentModal(pending) {
                $('#payment_id').val(pending.id);
                $('#deletePaymentModal').modal('show');
                $('#deletePayment').attr('action', '/payment/deletePending/' + $('#payment_id').val())
            }

            function showPaymentProofModal(pending) {
                $('#payment_id').val(pending.id);
                $("#viewImage").attr("src", "/storage/" + pending.payment_proof);
                $('#paymentProofModal').modal('show');
            }

            function approvePayment(pending) {
                $('#payment_id').val(pending.id);
                $('#approvePaymentModal').modal('show');
                $('#approvePayment').attr('action', '/payment/approvePending/' + $('#payment_id').val())
            }

            function rejectPayment(pending) {
                $('#payment_id').val(pending.id);
                $('#rejectPaymentModal').modal('show');
                $('#rejectPayment').attr('action', '/payment/rejectPending/' + $('#payment_id').val())
            }

            function openDeleteAllPaymentModal(member, equb) {
                $('#member_id').val(member);
                $('#equb_id').val(equb);
                $('#deleteAllPaymentModal').modal('show');
                $('#deleteAllPayment').attr('action', 'payment/deleteAll/' + $('#member_id').val() + '/' + $('#equb_id').val())
            }

            function openDeleteLotteryModal(pending) {
                $('#lottery_id').val(pending.id);
                $.ajax({
                    url: '/getRemainingLotteryAmount/' + pending.equb_id,
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

            function openApproveLotteryModal(pending) {
                console.log("🚀 ~ file: memberList.blade.php:461 ~ openApproveLotteryModal ~ pending:", pending)
                $('#lottery_idd').val(pending.id);
                $('#openApproveLotteryModal').modal('show');
                $('#approveLottery').attr('action', 'equbTaker/equbTaker-change-status/approved/' + $('#lottery_idd').val())
            }

            function openPayLotteryModal(pending) {
                console.log("🚀 ~ file: memberList.blade.php:461 ~ openApproveLotteryModal ~ pending:", pending.id)
                $('#lottery_id_pay').val(pending.id);
                $('#openPayLotteryModal').modal('show');
                $('#payLottery').attr('action', 'equbTaker/equbTaker-change-status/paid/' + $('#lottery_id_pay').val())
            }

            function openPaymentEditModal(pending) {
                $('#payment_id').val(pending.id);
                $('#update_member_id').val(pending.member_id);
                $('#equb_id').val(pending.equb_id);
                $('#editPaymentModal').modal('show');
                $('#update_payment_type>option[value="' + pending.payment_type + '"]').prop('selected', true);
                $('#update_payment_amount').val(pending.amount);
                let total_amount = pending.equb.amount - pending.amount
                $('#update_payment_credit').val(total_amount);
                $('#update_payment_remark').val(pending.note);
                $('#update_payment_status>option[value="' + pending.status + '"]').prop('selected', true);
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
                $("#pending-payment-table").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "searching": true,
                    "autoWidth": false,
                    language: {
                        search: "",
                        searchPlaceholder: "Search",
                    },
                    "buttons": ["excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#pending_payment_table_data .col-md-6:eq(0)');
            });
        </script>
    @endSection
@endcan