@can('view notification')
    @extends('layouts.app')
    @section('styles')
        <style type="text/css">
            div.dataTables_wrapper div.dataTables_info {
                padding-top: 0.85em;
                display: none;
            }

            @media (max-width: 575.98px) {
                #offDate-list-table {
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
                .addOffDate {
                    margin-bottom: 20px;
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .addOffDateTab {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .col-md-6 {
                    margin-bottom: 20px;
                    width: 100%;
                    padding-left: 0px;
                    padding-right: 0px;
                    float: left;
                }
            }
        </style>
    @endsection
    @section('styles')
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
                                        <h3>{{ $totalNotification }}</h3>
                                        <p>Total Notification</p>
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
                                        <h3>{{ $totalApprovedNotification }}</h3>
                                        <p>Approved Notification</p>
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
                                        <h3>{{ $totalPendingNotification }}</h3>
                                        <p>Pending Notification</p>
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
                                        <h5>Notification
                                            <button type="button" class=" btn btn-primary float-right" id="register" data-toggle="modal" data-target="#addNotificationModal"> <span class="fa fa-plus-circle"> </span> Send Notification</button>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="custom-tabs-two-tabContent">
                                            <table id="offDate-list-table" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Title</th>
                                                        <th>Body</th>
                                                        <th>Equb Type</th>
                                                        <th>Phone</th>
                                                        <th>Method</th>
                                                        <th>Status</th>
                                                        <th>Created At </th>
                                                        <th style="width: 50px">Action </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($notifications as $index => $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->title }}</td>
                                                            <td>{{ $item->body }}</td>
                                                            <td>{{ $item->equbType ? $item->equbType->name : '' }}
                                                            <td>{{ $item->phone }}</td>
                                                            <td>
                                                                @if ($item->method == 'sms')
                                                                    SMS
                                                                @elseif ($item->method == 'notification')
                                                                    Notification
                                                                @elseif ($item->method == 'both')
                                                                    Both
                                                                @endif
                                                            </td>
                                                            </td>
                                                            <td>
                                                                @if ($item->status == 'pending')
                                                                    Pending
                                                                @elseif ($item->status == 'approved')
                                                                    Approved
                                                                @endif
                                                            </td>
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
                                                                        <ul class='dropdown-menu dropdown-menu-right p-4'>
                                                                            @if ($item->status === 'pending')
                                                                                @can('approve notification')
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="btn-sm btn btn-flat"
                                                                                        onclick="openApproveModal({{ $item }})"
                                                                                        style="margin-right:10px;"><span
                                                                                            class="fa fa-check"> </span>
                                                                                        Approve</a>
                                                                                </li>
                                                                                @endcan
                                                                                @can('update notification')
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="btn-sm btn btn-flat"
                                                                                        onclick="openEditPendingModal({{ $item }})"
                                                                                        style="margin-right:10px;"><span
                                                                                            class="fa fa-edit"> </span>
                                                                                        Edit</a>
                                                                                </li>
                                                                                @endcan
                                                                            @endif
                                                                            @can('resend notification')
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="btn-sm btn btn-flat"
                                                                                    onclick="openEditModal({{ $item }})"
                                                                                    style="margin-right:10px;"><span
                                                                                        class="fa fa-edit"> </span>
                                                                                    Resend</a>
                                                                            </li>
                                                                            @endcan
                                                                            @if ($item->status === 'pending')
                                                                            @can('delete notification')
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="btn-sm btn btn-flat"
                                                                                        onclick="openDeleteModal({{ $item }})"><i
                                                                                            class="fas fa-trash-alt"></i>
                                                                                        Delete</a>
                                                                                </li>
                                                                            @endcan
                                                                            @endif
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

        <div class="table-responsive">
            <div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <p class="modal-title" id="exampleModalLabel">Delete rejected date type</p>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" id="deleteOffDate">
                                @csrf
                                @method('DELETE')
                                <input id="id" name="id" hidden value="">
                                <p class="text-center">Are you sure you want to delete this notification?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal modal-danger fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="Approve"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <p class="modal-title" id="exampleModalLabel">Approve Notifications</p>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" id="approveNotification">
                                @csrf
                                @method('POST')
                                <input id="id" name="id" hidden value="">
                                <p class="text-center">Are you sure you want to approve this notification?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @can('create notification')
            @include('admin/notification.sendNotification')
        @endcan
        @include('admin/notification.resendNotification')
    @endsection

    @section('scripts')
        <script>
            $("#addNotification").submit(function() {
                $.LoadingOverlay("show");
            });
            $("#resendNotification").submit(function() {
                $.LoadingOverlay("show");
            });
            $("#deleteOffDate").submit(function() {
                $.LoadingOverlay("show");
            });

            function openDeleteModal(item) {
                $('#id').val(item.id);
                $('#deleteModal').modal('show');
                $('#deleteOffDate').attr('action', 'notification/delete/' + $('#id').val())
            }

            function openApproveModal(item) {
                $('#id').val(item.id);
                $('#approveModal').modal('show');
                $('#approveNotification').attr('action', 'notification/approve/' + $('#id').val())
            }

            function openEditModal(item) {
                // console.log("🚀 ~ file: notificationList.blade.php:177 ~ openEditModal ~ item:", item)
                $('#notification_id').val(item.id);
                $('#resendNotificationModal').modal('show');
                var title = item.title;
                $('#update_title').val(title);
                $('#update_body').val(item.body);
                $('#update_equb_type').val(item.equb_type_id);
                $('#update_method').val(item.method);
                $('#resendNotification').attr('action', 'notification/update/' + $('#notification_id').val())
            }
            function openEditPendingModal(item) {
                // console.log("🚀 ~ file: notificationList.blade.php:177 ~ openEditModal ~ item:", item)
                $('#notification_id_pending').val(item.id);
                $('#editPendingNotificationModal').modal('show');
                var title = item.title;
                $('#update_title_pending').val(title);
                $('#update_body_pending').val(item.body);
                $('#update_equb_type_pending').val(item.equb_type_id);
                $('#update_method_pending').val(item.method);
                $('#editPendingNotification').attr('action', 'notification/updatePending/' + $('#notification_id_pending').val())
            }

            $(function() {
                $('#addNotification').validate({
                    onfocusout: false,
                    rules: {
                        title: {
                            required: true,
                        },
                        body: {
                            required: true,
                        },
                    },
                    messages: {
                        title: {
                            required: "Please enter a title"
                        },
                        body: {
                            required: "Please enter a body"
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
                    }

                });
                $('#resendNotification').validate({
                    onfocusout: false,
                    rules: {
                        update_title: {
                            required: true,
                        },
                        update_body: {
                            required: true,
                        },
                    },
                    messages: {
                        update_title: {
                            required: "Please enter a title",
                        },
                        update_body: {
                            required: "Please enter a body",
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
                    }

                });
                $('#nav-user').addClass('menu-is-opening menu-open active');
                $('#notification').addClass('active');
                $('#nav-u').addClass('active');
                $("#offDate-list-table").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "searching": true,
                    "autoWidth": false,
                    language: {
                        search: "",
                        searchPlaceholder: "Search",
                    },
                    @can('export notification_data')
                        "buttons": ["excel", "pdf", "print", "colvis"]
                    @else
                    "buttons": []
                    @endcan
                }).buttons().container().appendTo('#offDate-list-table_wrapper .col-md-6:eq(0)')
            });
        </script>
    @endSection
@endcan
