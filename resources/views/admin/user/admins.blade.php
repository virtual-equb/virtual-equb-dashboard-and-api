@can('create user')
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

            @media (max-width: 1400px) {
                #activeUser-list-table {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }

                .table-responsive-sm>.table-bordered {
                    border: 0;
                }
            }

            @media (max-width: 1400px) {
                #deactiveUser-list-table {
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
                .activeUser {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .deactiveUser {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .addUser {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .updateUser {
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
                        <div class="row">
                            <div class="col-12">
                                <div class="card ">
                                    <div class="card-header">
                                        <ul class="nav nav-pills" id="custom-tabs-two-tab" role="tablist">
                                            <li class="nav-item nav-blue activeUser">
                                                <a class="nav-link active" id="custom-tabs-two-member-tab"
                                                    data-toggle="pill" href="#custom-tabs-two-member" role="tab"
                                                    aria-controls="custom-tabs-two-member" aria-selected="true"
                                                    onclick="removeTabs();"><span class="fa fa-list"> </span> Active
                                                    User</a>
                                            </li>
                                            <li class="nav-item deactiveUser">
                                                <a class="nav-link " id="custom-tabs-two-messages-tab" data-toggle="pill"
                                                    href="#custom-tabs-two-messages" role="tab"
                                                    aria-controls="custom-tabs-two-messages" aria-selected="false"
                                                    onclick="removeTabs();"><span class="fa fa-list"> </span>Deactivated
                                                    User</a>
                                            </li>
                                            @can('create user')
                                                <li class="nav-item addUser">
                                                    <a class="nav-link" id="custom-tabs-two-settings-tab" data-toggle="pill"
                                                        href="#custom-tabs-two-settings" role="tab"
                                                        aria-controls="custom-tabs-two-settings" aria-selected="false"
                                                        onclick="removeTabs();"> <span class="fa fa-plus-circle"></span> Add
                                                        User</a>
                                                </li>
                                            @endcan
                                            
                                            <div class="float-right">
                                                @include('rolePermission.nav-links')
                                            </div>
                                            
                                            <li class="nav-item updateUser" id="update-equb_taker-div"
                                                style="display: none;">
                                                <a class="nav-link" id="custom-tabs-two-profile-tab" data-toggle="pill"
                                                    href="#custom-tabs-two-profile" role="tab"
                                                    aria-controls="custom-tabs-two-profile" aria-selected="false"> <span
                                                        class="fa fa-list"> </span> Update User</a>
                                            </li>
                                            <div class="float-right searchandClear row col-4 offset-md-2"
                                                        id="member_table_filter">
                                                        <input class="form-control col-10" type="text"
                                                            id="memberSearchText" placeholder="Search User"
                                                            class="search">
                                                        <button class="btn btn-default clear col-2" id="clearActiveSearch"
                                                            onclick="clearSearchEntry()">
                                                            Clear
                                                        </button>
                                            </div>
                                            
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="custom-tabs-two-tabContent">
                                            <div class="tab-pane fade show active" id="custom-tabs-two-member"
                                                role="tabpanel" aria-labelledby="custom-tabs-two-member-tab">
                                                <div id="user_table_data">

                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="custom-tabs-two-messages" role="tabpanel"
                                                aria-labelledby="custom-tabs-two-messages-tab">
                                                <div id="deactive_user_table_data">

                                                </div>
                                            </div>
                                            @can('create user')
                                            <div class="tab-pane fade" id="custom-tabs-two-settings" role="tabpanel"
                                                aria-labelledby="custom-tabs-two-settings-tab">
                                                @include('admin/user.addUser')
                                            </div>
                                            @endcan
                                            <div class="tab-pane fade " id="update-equb_taker" role="tabpanel"
                                                aria-labelledby="update-equb_taker-tab">
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
        @include('admin/user.deleteUser')
        @include('admin/user.resetPassword')
    @endsection
    @section('scripts')
        <script type="text/javascript">
        var memberSearchField = document.getElementById('memberSearchText');
            memberSearchField.addEventListener("keydown", function(e) {
                var memberSearchInput = memberSearchField.value;
                if (e.keyCode === 13) { //checks whether the pressed key is "Enter"
                    $.LoadingOverlay("show");
                    searchForMember(memberSearchInput);
                }
            });

            function searchForMember(searchInput) {
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('user/search-user') }}" + '/' + searchInput + '/0',
                        type: 'get',
                        success: function(data) {
                            $('#user_table_data').html(data);
                            $.LoadingOverlay("hide");
                        }
                    });
                } else {
                    clearSearchEntry();
                    $.LoadingOverlay("hide");
                }
            }
            function users(offsetVal, pageNumberVal) {
                $.ajax({
                    url: "{{ url('user/user') }}" + '/' + offsetVal + '/' + pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#user_table_data').html(data);
                    }
                });
            }
            function loadMoreSearchUsers(searchInput, offsetVal, pageNumberVal) {
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('user/search-user') }}" + '/' + searchInput + '/' +
                            offsetVal + '/' +
                            pageNumberVal,
                        type: 'get',
                        success: function(data) {
                            $('#user_table_data').html(data);
                        }
                    });

                }
            }
            function deactiveUsers(offsetVal, pageNumberVal) {
                $.ajax({
                    url: "{{ url('user/deactiveUser') }}" + '/' + offsetVal + '/' + pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#deactive_user_table_data').html(data);
                    }
                });
            }

            function getEqubType(sel, cb = $.noop) {
                const memberId = sel.value;
                $.ajax({
                    url: 'member/get-equbs/' + memberId,
                    method: 'get',
                    success: function(response) {
                        let option = "<option value=''> Choose...</option>";
                        response.equbs.map(function(equb) {
                            option +=
                                `<option value="${equb.id}"> ${equb.equb_type.name}</option>`;
                        });
                        $('#equb').empty();
                        $('#equb').append(option);
                        $('#equb_id').empty();
                        $('#equb_id').append(option);
                    }
                });
            }

            function openEditTab(item) {
                $('#custom-tabs-two-member-tab').removeClass('active');
                $('#custom-tabs-two-messages-tab').removeClass('active');
                $('#custom-tabs-two-settings-tab').removeClass('active');

                $('#custom-tabs-two-member').removeClass('active');
                $('#custom-tabs-two-messages').removeClass('active');
                $('#custom-tabs-two-settings').removeClass('active');

                $('#update-equb_taker-div').css('display', 'inline');
                $('#update-equb_taker-div').addClass('show active');
                $('#update-equb_taker').addClass('show active');
                $.ajax({
                    url: `user/edit/${item.id}`,
                    method: 'get',
                    success: function(form) {
                        $('#update-equb_taker').html(form);
                    }
                });

            }

            function openDeleteUserModal(item) {
                $('#user_id').val(item.id);
                $('#deleteUserModal').modal('show');
                $('#deleteUser').attr('action', 'user/delete/' + $('#user_id').val())
            }

            function openDeactivatedModal(item) {
                $('#active_user_id').val(item.id);
                $('#deactivatedModal').modal('show');
                $('#updateDeactivatedUser').attr('action', "{{ url('user/deactivateUser') }}" + '/' + $('#active_user_id')
                    .val());
            }

            function openActivatedModal(item) {
                $('#deactivated_user_id').val(item.id);
                $('#ActivatedModal').modal('show');
                $('#updateActiveUser').attr('action', "{{ url('user/activateUser') }}" + '/' + $('#deactivated_user_id').val());
            }

            function openEditModal(item) {
                $('#id').val(item.id);
                $('#editUserModal').modal('show');
                $('#update_name').val(item.name);
                $('#update_email').val(item.email);
                $('#update_phone').val(item.phone_number);

                $('#update_genderr>option[value="' + item.gender + '"]').prop('selected', true);
                $('#update_role>option[value="' + item.role + '"]').prop('selected', true);
                $('#updateUser').attr('action', 'user/update/' + $('#id').val());
            }

            function resetPassword(item) {
                $('#u_id').val(item.id);
                $('#resetPasswordModal').modal('show');
            }

            function removeTabs() {
                $('#update-equb_taker-div').css('display', 'none');
            }
            $("#addUserForm").submit(function() {
                $.LoadingOverlay("show");
            });
            $("#deleteUser").submit(function() {
                $.LoadingOverlay("show");
            });

            function edit() {
                $('#editUserForm').validate({
                    onfocusout: false,
                    rules: {
                        name: {
                            required: true,
                            minlength: 2,
                            maxlength: 40,
                            pattern: /^[a-zA-Z ]+$/,
                        },
                        email: {
                            required: true,
                            email: true,
                            maxlength: 250,
                            remote: {
                                url: '{{ url('emailCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    email: function() {
                                        return $('#editUserForm :input[name="email"]').val();
                                    },
                                    user_id: function() {
                                        return $('#editUserForm :input[name="user_id"]').val();
                                    }
                                }
                            },
                        },
                        phone_number: {
                            required: true,
                            minlength: 13,
                            maxlength: 13,
                            // digits: true,
                            remote: {
                                url: '{{ url('userPhoneCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    phone: function() {
                                        return $('#editUserForm :input[name="phone_number"]').val();
                                    },
                                    user_id: function() {
                                        return $('#editUserForm :input[name="user_id"]').val();
                                    }
                                }
                            },
                        },
                        gender: {
                            required: true,
                        },
                        role: {
                            required: true,
                        },
                    },
                    messages: {
                        name: {
                            required: "Please enter a full name",
                            minlength: "Full name must be more than 2 characters long",
                            maxlength: "Full name must be less than 40 characters long",
                            pattern: "Please enter alphabet only"
                        },
                        email: {
                            required: "Please enter a email",
                            email: "Please ennter correct email format ",
                            maxlength: "Email must be less than or equal to 10 number",
                            digits: "Email must be number",
                            remote: "Email already exist",
                        },
                        phone_number: {
                            required: "Please enter a phone",
                            minlength: "Phone must be more than 9 number long",
                            maxlength: "Phone must be less than or equal to 10 number",
                            digits: "phone must be number",
                            remote: "phone already exist",
                        },
                        gender: {
                            required: "Selecte gender",
                        },
                        role: {
                            required: "Selecte role",
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

            function addUser() {
                $('#addUserForm').validate({
                    onfocusout: false,
                    rules: {
                        name: {
                            required: true,
                            minlength: 2,
                            maxlength: 40,
                            pattern: /^[a-zA-Z ]+$/,
                        },
                        email: {
                            required: true,
                            email: true,
                            maxlength: 250,
                            remote: {
                                url: '{{ url('emailCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    email: function() {
                                        return $('#addUserForm :input[name="email"]').val();
                                    }
                                }
                            },
                        },
                        phone_number: {
                            required: true,
                            minlength: 13,
                            maxlength: 13,
                            // digits: true,
                            remote: {
                                url: '{{ url('userPhoneCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    phone: function() {
                                        return $('#addUserForm :input[name="phone_number"]').val();
                                    }
                                }
                            },
                        },
                        gender: {
                            required: true,
                        },
                        role: {
                            required: true,
                        },
                    },
                    messages: {
                        name: {
                            required: "Please enter a full name",
                            minlength: "Full name must be more than 2 characters long",
                            maxlength: "Full name must be less than 40 characters long",
                            pattern: "Please enter alphabet only"
                        },
                        email: {
                            required: "Please enter a email",
                            email: "Please ennter correct email format ",
                            maxlength: "Email must be less than or equal to 10 number",
                            digits: "Email must be number",
                            remote: "Email already exist",
                        },
                        phone_number: {
                            required: "Please enter a phone",
                            minlength: "Phone must be more than 9 number long",
                            maxlength: "Phone must be less than or equal to 10 number",
                            digits: "phone must be number",
                            remote: "phone already exist",
                        },
                        gender: {
                            required: "Selecte gender",
                        },
                        role: {
                            required: "Selecte role",
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
                $.ajax({
                    url: "{{ url('user/user') }}" + '/' + 0 + '/' + 1,
                    type: 'get',
                    success: function(data) {
                        $('#user_table_data').html(data);
                    }
                });
                $.ajax({
                    url: "{{ url('user/deactiveUser') }}" + '/' + 0 + '/' + 1,
                    type: 'get',
                    success: function(data) {
                        // $.LoadingOverlay("show");
                        $('#deactive_user_table_data').html(data);
                    }
                });
                document.getElementById("resetPasswordModal").onsubmit = function() {
                    if ($("#resetPassword").valid()) {
                        $('#resetPasswordModal').modal('hide');
                    }
                }


                //    $('#nav-user').addClass('menu-is-opening menu-open active');
                $('#adminNav').addClass('active');
                //    $('#nav-u').addClass('active');
                var table = $("#equbTaker-list-table").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "searching": false,
                    "autoWidth": false,
                    "bSort": false,
                    "bDestroy": true,
                    language: {
                        search: "",
                        searchPlaceholder: "Search",
                    },
                    "buttons": ["excel", "pdf", "print", "colvis"]
                });
                table.buttons().container().appendTo('#equbTaker-list-table_wrapper .col-md-6:eq(0)');
                $('#equbTaker-list-table_filter').prepend(
                    `<button type="button" class=" btn btn-primary" id="register" data-toggle="modal" data-target="#addEqubTakerModal" style="margin-right: 30px;"> <span class="fa fa-plus-circle"> </span>Add equbTaker</button>`
                )
                $("#activeUser-list-table").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "searching": false,
                    "paging": false,
                    "autoWidth": false,
                    language: {
                        search: "",
                        searchPlaceholder: "Search",
                    },
                    "buttons": ["excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#activeUser-list-table_wrapper .col-md-6:eq(0)')
                $("#deactiveUser-list-table").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "searching": false,
                    "paging": false,
                    "autoWidth": false,
                    language: {
                        search: "",
                        searchPlaceholder: "Search",
                    },
                    "buttons": ["excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#deactiveUser-list-table_wrapper .col-md-6:eq(0)')
            });
        </script>
@endsection
@endcan