@can('view rejected_date')
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
    @section('content')
        <div class="wrapper">
            <div class="content-wrapper">
                <section class="content">
                    <div class="container-fluid">
                        <div class="row mt-2">
                            <div class="col-lg-6 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $totalOffDate }}</h3>
                                        <p>Total Off Dates</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-pie-graph"></i>
                                    </div>
                                    <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $totalOffDateAfterDay }}</h3>
                                        <p>Upcoming Off Dates</p>
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
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Off Date
                                            <button type="button" class="btn btn-primary float-right" id="register" data-toggle="modal" data-target="#addOffDateModal"> <span class="fa fa-plus-circle"> </span> Add Off Date</button>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="off_date_table_data" class="table-responsive">
                                            <table id="offDate-list-table" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Date</th>
                                                        <th>Description</th>
                                                        <th>Registered At </th>
                                                        <th style="width: 50px">Action </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($rejectedDate as $index => $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                <?php
                                                                $toCreatedAt = new DateTime($item['rejected_date']);
                                                                $createdDate = $toCreatedAt->format('M-j-Y');
                                                                echo $createdDate; ?>
                                                            </td>
                                                            <td>{{ $item->description }}</td>
                                                            <td>
                                                                <?php
                                                                $toCreatedAt = new DateTime($item['created_at']);
                                                                $createdDate = $toCreatedAt->format('M-j-Y');
                                                                echo $createdDate; ?>
                                                            </td>
                                                            @if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant')
                                                                <td>
                                                                    <div class='dropdown'>
                                                                        <button
                                                                            class='btn btn-secondary btn-sm btn-flat dropdown-toggle'
                                                                            type='button' 
                                                                            data-toggle='dropdown'>Menu
                                                                            <span class='caret'></span>
                                                                        </button>
                                                                        <ul class='dropdown-menu dropdown-menu-right p-4'>
                                                                            @can('update rejected_date')
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="btn-sm btn btn-flat"
                                                                                    onclick="openEditModal({{ $item }})"
                                                                                    style="margin-right:10px;"><span
                                                                                        class="fa fa-edit"> </span>
                                                                                    Edit</a>
                                                                            </li>
                                                                            @endcan
                                                                            @can('delete rejected_date')
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="btn-sm btn btn-flat"
                                                                                    onclick="openDeleteModal({{ $item }})"><i
                                                                                        class="fas fa-trash-alt"></i>
                                                                                    Delete</a>
                                                                            </li>
                                                                            @endcan
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

        <div class="table-responsive">
            <div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <p class="modal-title" id="exampleModalLabel">Delete Off Date</p>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" id="deleteOffDate">
                                @csrf
                                @method('DELETE')
                                <input id="id" name="id" hidden value="">
                                <p class="text-center">Are you sure you want to delete this off date value ?</p>
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

        @include('admin/rejectedDate.addRejectedDate')
        @include('admin/rejectedDate.editRejectedDate')
    @endsection
    
    @section('scripts')
        <script>
            $("#addOffDate").submit(function() {
                $.LoadingOverlay("show");
            });
            $("#updateOffDate").submit(function() {
                $.LoadingOverlay("show");
            });
            $("#deleteOffDate").submit(function() {
                $.LoadingOverlay("show");
            });

            function openDeleteModal(item) {
                $('#id').val(item.id);
                $('#deleteModal').modal('show');
                $('#deleteOffDate').attr('action', 'rejectedDate/delete/' + $('#id').val())
            }

            function openEditModal(item) {
                $('#off_date_id').val(item.id);
                $('#editRejectedDateModal').modal('show');
                var offDate = new Date(item.rejected_date);
                offDate = moment(offDate);
                offDate = offDate.format("YYYY-MM-DD");
                $('#update_rejected_date').val(offDate);
                $('#update_description').val(item.description);
                $('#updateOffDate').attr('action', 'rejectedDate/update/' + $('#off_date_id').val())
            }

            $(function() {
                $("#update_rejected_date").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
                $("#rejected_date").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
                $('#addOffDate').validate({
                    onfocusout: false,
                    rules: {
                        rejected_date: {
                            required: true,
                            date: true,
                            remote: {
                                url: '{{ url('offDateCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    equb_type_id: function() {
                                        return $('#addOffDate :input[name="rejected_date"]').val();
                                    },
                                }
                            },

                        },
                    },
                    messages: {
                        rejected_date: {
                            required: "Please enter off date value",
                            date: "Please enter proper date",
                            remote: "Off date allready exist",
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
                $('#updateOffDate').validate({
                    onfocusout: false,
                    rules: {
                        rejected_date: {
                            required: true,
                            date: true,
                            remote: {
                                url: '{{ url('updateoffDateCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    rejected_date: function() {
                                        return $('#updateOffDate :input[name="rejected_date"]').val();
                                    },
                                    off_date_id: function() {
                                        return $('#updateOffDate :input[name="off_date_id"]').val();
                                    },
                                }
                            },

                        },
                    },
                    messages: {
                        rejected_date: {
                            required: "Please enter a off date",
                            date: "Please enter proper date",
                            remote: "Off date allready exist",
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
                $('#offDate').addClass('active');
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
                    @can('export off_date_data')
                        "buttons": [
                            {
                                extend: 'excel',
                                exportOptions: {
                                    columns: ':visible',
                                    columns: function (index, data, node) {
                                        return index !== 4;
                                    }
                                }
                            },
                            {
                                extend: 'pdf',
                                exportOptions: {
                                    columns: ':visible',
                                    columns: function (index, data, node) {
                                        return index !== 4;
                                    }
                                }
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: ':visible',
                                    columns: function (index, data, node) {
                                        return index !== 4;
                                    }
                                }
                            },
                            'colvis'
                        ]
                    @else
                        "buttons": []
                    @endcan
                }).buttons().container().appendTo('#off_date_table_data .col-md-6:eq(0)');
            });
        </script>
    @endSection
@endcan
