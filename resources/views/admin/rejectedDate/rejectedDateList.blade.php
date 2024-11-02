{{-- @if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'assistant' ||
        Auth::user()->role == 'it') --}}
    @extends('layouts.app')
 
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
                                            <li class="nav-item nav-blue addOffDateTab">
                                                <a class="nav-link active" id="custom-tabs-two-member-tab"
                                                    data-toggle="pill" href="#custom-tabs-two-member" role="tab"
                                                    aria-controls="custom-tabs-two-member" aria-selected="true"> <span
                                                        class="fa fa-list"> </span> Off Date</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="custom-tabs-two-tabContent">
                                            <div class="tab-pane fade show active" id="custom-tabs-two-member"
                                                role="tabpanel" aria-labelledby="custom-tabs-two-member-tab">
                                                @include('admin/rejectedDate.addRejectedDate')
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
                                                        @foreach ($rejectedDate as $item)
                                                            <tr>
                                                                <td>{{ $item->id }}</td>
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
                                                                                data-toggle='dropdown'>Menu<span
                                                                                    class='caret'></span></button>
                                                                            <ul class='dropdown-menu p-4'>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="btn-sm btn btn-flat"
                                                                                        onclick="openEditModal({{ $item }})"
                                                                                        style="margin-right:10px;"><span
                                                                                            class="fa fa-edit"> </span>
                                                                                        Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="btn-sm btn btn-flat"
                                                                                        onclick="openDeleteModal({{ $item }})"><i
                                                                                            class="fas fa-trash-alt"></i>
                                                                                        Delete</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </td>
                                                                @endif
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
                                <p class="text-center">Are you sure you want to delete this rejected date type?</p>
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
                // $('#nav-user').addClass('menu-is-opening menu-open active');
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
                    "buttons": ["excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#offDate-list-table_wrapper .col-md-6:eq(0)')
                $('#offDate-list-table_filter').prepend(
                    `@if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant')<button type="button" class=" btn btn-primary addOffDate" id="register" data-toggle="modal" data-target="#addOffDateModal" style="margin-right: 30px;"> <span class="fa fa-plus-circle"> </span> Add Off Date</button>@endif`
                )
            });
        </script>
    @endSection
{{-- @endif --}}
