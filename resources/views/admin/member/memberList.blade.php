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
                                                                                                                                                                                                                                                                               }*/
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
                                        <div class="flex-wrap d-flex justify-content-between align-items-center w-100">
                                            <ul class="mb-2 nav nav-pills mb-md-0" id="custom-tabs-two-tab" role="tablist">
                                                <li class="nav-item nav-blue memberTab">
                                                    <a class="nav-link active" id="custom-tabs-two-member-tab"
                                                        data-toggle="pill" href="#custom-tabs-two-member" role="tab"
                                                        aria-controls="custom-tabs-two-member" aria-selected="true">
                                                        <span class="fa fa-list"></span> Member
                                                    </a>
                                                </li>
                                                <li class="nav-item paymentTab" id="payment-tab" style="display: none;">
                                                    <a class="nav-link" id="custom-tabs-two-messages-tab" data-toggle="pill"
                                                        href="#custom-tabs-two-messages" role="tab"
                                                        aria-controls="custom-tabs-two-messages" aria-selected="false">
                                                        <span class="fa fa-list"></span> Payment
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="flex-wrap d-flex">
                                                @can('view member')
                                                    <button type="button" class="mb-2 mr-2 btn btn-primary mb-md-0" id="lotteryDatec" data-toggle="modal" data-target="#lotteryDateCheckModal">
                                                        <i class="fa fa-check-square"></i> Check Lottery Date
                                                    </button>
                                                    <button type="button" class="mb-2 btn btn-primary mb-md-0" id="register" data-toggle="modal" data-target="#myModal">
                                                        <span class="fa fa-plus-circle"></span> Add member
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="custom-tabs-two-tabContent">
                                            <div class="tab-pane fade show active" id="custom-tabs-two-member"
                                                role="tabpanel" aria-labelledby="custom-tabs-two-member-tab">
                                                @include('admin/payment.addPayment')
                                                @include('admin/lottery.addLottery')
                                                @include('admin/equb.addEqub')
                                                @include('admin/member.addMember')
                                                <div class="mb-3 row">
                                                    <div class="col-12">
                                                        @include('components.filter', ['equbTypes' => $equbTypes])
                                                    </div>
                                                </div>
                                                <div id="member_table_data_w" class="col-md-8">

                                                </div>
                                                <div id="member_table_data">
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel"
                                                aria-labelledby="custom-tabs-two-profile-tab">

                                            </div>
                                            <div class="tab-pane fade" id="custom-tabs-two-messages" role="tabpanel"
                                                aria-labelledby="custom-tabs-two-messages-tab">

                                            </div>
                                            <div class="tab-pane fade" id="custom-tabs-two-settings" role="tabpanel"
                                                aria-labelledby="custom-tabs-two-settings-tab">
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
        @include('admin/equb.deleteEqub')
        @include('admin/payment.deletePayment')
        @include('admin/payment.deleteAllPayment')
        @include('admin/lottery.deleteLottery')
        @include('admin/member.editMember')
        @include('admin/member.checkLotteryDate')
        @include('admin/equb.editEqub')
        @include('admin/notification.sendNotification')
        @include('admin/payment.editPayment')
        @include('admin/lottery.editLottery')
        <div class="modal modal-danger fade" id="lotteryDetailModal" tabindex="-1" role="dialog"
            aria-labelledby="Delete" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <p class="modal-title" id="exampleModalLabel">Reserved Lottery Detail</p>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="lotteryDetail">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script>
            function loadMoreSearchMembers(searchInput, offsetVal, pageNumberVal) {
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('member/search-member') }}" + '/' + searchInput + '/' +
                            offsetVal + '/' +
                            pageNumberVal,
                        type: 'get',
                        success: function(data) {
                            $('#member_table_data').html(data);
                        }
                    });

                }
            }

            var statusSearchField = document.getElementById('statusSearchText');
            statusSearchField.addEventListener("change", function(e) {
                var memberSearchInput = statusSearchField.value;
                $.LoadingOverlay("show");
                searchForStatus(memberSearchInput);
            });

            function searchForStatus(searchInput) {
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('member/search-status') }}" + '/' + searchInput + '/0',
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

            function loadMoreSearchStatus(searchInput, offsetVal, pageNumberVal) {
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('member/search-status') }}" + '/' + searchInput + '/' +
                            offsetVal + '/' +
                            pageNumberVal,
                        type: 'get',
                        success: function(data) {
                            $('#member_table_data').html(data);
                        }
                    });

                }
            }

            var equbSearchField = document.getElementById('equbSearchText');
            equbSearchField.addEventListener("change", function(e) {
                var memberSearchInput = equbSearchField.value;
                $.LoadingOverlay("show");
                searchForEqub(memberSearchInput);
            });

            function searchForEqub(searchInput) {
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('member/search-equb') }}" + '/' + searchInput + '/0',
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

            function loadMoreSearchEqubs(searchInput, offsetVal, pageNumberVal) {
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('member/search-equb') }}" + '/' + searchInput + '/' +
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
                    url: "{{ url('member/clearSearchEntry') }}",
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
                        $.LoadingOverlay("hide");
                    }
                });

                // }
            }

            function members(offsetVal, pageNumberVal) {
                $.LoadingOverlay("show");
                $.ajax({
                    url: "{{ url('member/member') }}" + '/' + offsetVal + '/' + pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
                        $.LoadingOverlay("hide");
                    }
                });
            }

            function validateForm() {
                let lottery_date = document.getElementById('lottery_date').value;
                $.ajax({
                    url: "{{ url('member/equb-lottery-detail') }}" + '/' + lottery_date,
                    type: 'get',
                    success: function(form) {
                        // console.log(form);
                        if (form) {
                            $('#lotteryDetail').html(form);
                            $('#lotteryDetailModal').modal('show');
                        }
                    }
                });
            }

            function lotteryDateCheck() {
                let lottery_date = document.getElementById('lottery_date_check').value;
                $.ajax({
                    url: "{{ url('member/equb-lottery-detail') }}" + '/' + lottery_date,
                    type: 'get',
                    success: function(form) {
                        // console.log(form);
                        if (form) {
                            $('#lotteryDetail').html(form);
                            $('#lotteryDetailModal').modal('show');
                        }
                    }
                });
            }

            function validateFormForEqubUpdate() {
                let lottery_date = document.getElementById('update_lottery_date').value;
                $.ajax({
                    url: "{{ url('member/equb-lottery-detail') }}" + '/' + lottery_date,
                    type: 'get',
                    success: function(form) {
                        // console.log(form);
                        if (form) {
                            $('#lotteryDetail').html(form);
                            $('#lotteryDetailModal').modal('show');
                        }
                    }
                });
            }

            function statusChange(item) {
                $('#member_id').val(item.id);
                $('#statusModal').modal('show');
                $('#updateStatus').attr('action', "{{ url('member/updateStatus') }}" + '/' + $('#member_id').val());
            }

            function statusSubmit() {
                document.getElementById("updateStatus").submit();
            }

            function openDeletePaymentModal(item) {
                $('#payment_id').val(item.id);
                $('#deletePaymentModal').modal('show');
                $('#deletePayment').attr('action', 'payment/delete/' + $('#payment_id').val())
            }

            function showPaymentProofModal(item) {
                $('#payment_id').val(item.id);
                $("#viewImage").attr("src", "/storage/" + item.payment_proof);
                $('#paymentProofModal').modal('show');
            }

            function approvePayment(item) {
                $('#payment_id').val(item.id);
                $('#approvePaymentModal').modal('show');
                $('#approvePayment').attr('action', 'payment/approve/' + $('#payment_id').val())
            }

            function rejectPayment(item) {
                $('#payment_id').val(item.id);
                $('#rejectPaymentModal').modal('show');
                $('#rejectPayment').attr('action', 'payment/reject/' + $('#payment_id').val())
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
                        if (data < 0) {
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
                console.log("ðŸš€ ~ file: memberList.blade.php:461 ~ openApproveLotteryModal ~ item:", item)
                $('#lottery_idd').val(item.id);
                $('#openApproveLotteryModal').modal('show');
                $('#approveLottery').attr('action', 'equbTaker/equbTaker-change-status/approved/' + $('#lottery_idd').val())
            }

            function openPayLotteryModal(item) {
                console.log("ðŸš€ ~ file: memberList.blade.php:461 ~ openApproveLotteryModal ~ item:", item.id)
                $('#lottery_id_pay').val(item.id);
                $('#openPayLotteryModal').modal('show');
                $('#payLottery').attr('action', 'equbTaker/equbTaker-change-status/paid/' + $('#lottery_id_pay').val())
            }

            function openEqubDeleteModal(item) {
                $('#equb_id').val(item.id);
                $('#deleteEqubModal').modal('show');
                $('#deleteEqub').attr('action', 'member/equb-delete/' + $('#equb_id').val())
            }

           
            function openEditModal(item) {
                $('#m_id').val(item.id);
                $('#editMemberModal').modal('show');
                $('#update_full_name').val(item.full_name);
                $('#update_phone').val(item.phone);
                $('#update_email').val(item.email);
                $('#update_woreda').val(item.woreda);
                $('#update_location').val(item.specific_location);
                $('#update_housenumber').val(item.house_number);
                $('#update_gender > option[value="' + item.gender + '"]').prop('selected', true);
                
                // Set the selected city
                $('#select-city > option[value="' + item.city + '"]').prop('selected', true);

                // Fetch sub-cities based on the selected city
                var cityId = item.city; // Assuming item.city contains the city ID
                $('#subcity').empty().append('<option value="">Select Sub-City</option>');
                $('#addSubcity').hide();

                if (cityId) {
                    $.ajax({
                        url: '/subcities/city/' + encodeURIComponent(cityId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.length > 0) {
                                $.each(data, function(index, subcity) {
                                    $('#subcity').append('<option value="' + subcity.id + '">' + subcity.name + '</option>');
                                });
                                // Set the default sub-city based on the item data
                                if (item.subcity) { // Assuming item.subcity contains the sub-city ID
                                    $('#subcity > option[value="' + item.subcity + '"]').prop('selected', true);
                                }
                                $('#addSubcity').show();
                            } else {
                                $('#addSubcity').hide();
                            }
                        },
                        error: function() {
                            alert('Failed to retrieve sub-cities.');
                            $('#addSubcity').hide();
                        }
                    });
                } else {
                    $('#addSubcity').hide();
                }

                // Set the profile picture preview dynamically
                if (item.profile_photo_path) {
                    $('#profilePicPreview').attr('src', '{{ asset('storage') }}/' + item.profile_photo_path);
                } else {
                    $('#profilePicPreview').attr('src', '{{ asset('storage/profile_pictures/default.png') }}'); // Default image path
                }

                $('#updateMember').attr('action', 'member/update/' + $('#m_id').val());
            }

            function openRateModal(item) {
                console.log("ðŸš€ ~ file: memberList.blade.php:495 ~ openRateModal ~ item:", item)
                // var addressObject = JSON.parse(item.address);
                $('#m_id').val(item.id);
                $('#rateMemberModal').modal('show');
                $('#rating').val(item.rating);

                $('#rateMember').attr('action', 'member/rate/' + $('#m_id').val());
            }

            function openPaymentTab(equb) {
                $('#custom-tabs-two-messages').html(loadHere);
                $('#custom-tabs-two-member-tab').removeClass('active');
                $('#payment-tab').css('display', 'inline');
                $('#custom-tabs-two-messages-tab').addClass('active');
                $('#custom-tabs-two-member').removeClass('active');
                $('#custom-tabs-two-messages').addClass('show active');
                $.ajax({
                    url: `payment/${equb.member_id}/${equb.id}`,
                    method: 'get',
                    success: function(form) {
                        // console.log(form);
                        $('#custom-tabs-two-messages').html(form);
                    }
                });
            }

            function payments(equb, offsetVal, pageNumberVal) {
                $('#custom-tabs-two-member-tab').removeClass('active');
                $('#payment-tab').css('display', 'inline');
                $('#custom-tabs-two-messages-tab').addClass('active');
                $('#custom-tabs-two-member').removeClass('active');
                $('#custom-tabs-two-messages').addClass('show active');
                $.ajax({
                    url: `payment/show-payment/${equb.member_id}/${equb.id}/${offsetVal}/${pageNumberVal}`,
                    method: 'get',
                    success: function(form) {
                        // console.log(form);
                        $('#custom-tabs-two-messages').html(form);
                    }
                });
            }

            function openEqubEditModal(equb) {
                var datePickerOptions = {
                    format: 'yyyy-mm-dd',
                    clearBtn: true,
                    multidate: true,
                    todayHighlight: true
                };
                $('#update_equb_id').val(equb.id);
                $('#editEqubTypeModal').modal('show');
                $('#update_member_id').val(equb.member_id);
                $('#update_equb_type').val(equb.equb_type.id);
                const lotteryDate = document.getElementById("update_equb_lottery_date_div");
                const type = equb.equb_type.type;
                const timeline = document.getElementById("update_timeline_div");
                if (type !== "Automatic") {
                    lotteryDate.classList.remove("d-none");
                    lotteryDate.required = true;
                } else {
                    lotteryDate.classList.add("d-none");
                    lotteryDate.required = false;
                }
                if (type !== "Automatic") {
                    lotteryDate.classList.remove("d-none");
                    timeline.classList.remove("d-none");
                    lotteryDate.required = true;
                } else {
                    var equbTypeStartDate = $(this).find("option:selected").data("startdate");
                    var equbTypeEndDate = $(this).find("option:selected").data("enddate");
                    $('#update_start_date').datepicker('setDate', new Date(equbTypeStartDate));
                    $('#update_start_date').datepicker('destroy');
                    $('#update_end_date').datepicker('setDate', new Date(equbTypeEndDate));
                    $('#update_end_date').datepicker('destroy');
                    lotteryDate.classList.add("d-none");
                    timeline.classList.add("d-none");
                    lotteryDate.required = false;
                }
                $("#update_lottery_date").datepicker("destroy");
                $("#update_lottery_date").datepicker(datePickerOptions);
                $('#update_round').val(equb.round);
                $('#update_amount').val(equb.amount);
                $('#update_timeline').val(equb.timeline);
                var startdate = new Date(equb.start_date);
                startdate = moment(startdate);
                startdate = startdate.format("YYYY-MM-DD");
                var enddate = new Date(equb.end_date);
                enddate = moment(enddate);
                enddate = enddate.format("YYYY-MM-DD");
                $('#update_total_amount').val(equb.total_amount);
                $('#update_start_date').val(startdate);
                $('#update_end_date').val(enddate);
                var lottery_date = new Date(equb.lottery_date);
                lottery_date = moment(lottery_date);
                lottery_date = lottery_date.format("YYYY-MM-DD");
                $('#update_lottery_date').val(equb.lottery_date);
                $('#updateEqub').attr('action', 'member/equb-update/' + $('#update_equb_id').val());
            }

            function openEqubAddModal(item) {
                $('#member_id').val(item.id);
                $('#addEqubModal').modal('show');
            }

            function sendNotificationModal(item) {
                console.log("ðŸš€ ~ file: memberList.blade.php:637 ~ sendNotificationModal ~ item:", item)
                $('#m_phone').val(item.phone);
                $('#sendNotificationModal').modal('show');
            }

            function changeCreadit() {
                let amount = document.getElementById('amount').value;
                let equb_amount = document.getElementById('equb_amount').value;
                let diff = equb_amount - amount;
                $('#creadit').val(diff);
            }

            $("#timeline").on("change", function() {
                let amount = document.getElementById('amount_per_day').value;
                var timeline = $(this).find("option:selected").data("info");
                let startdate = document.getElementById('start_date').value;
                let enddate = document.getElementById('end_date').value;
                var date = new Date(startdate);
                date.setDate(date.getDate() + timeline);
                $('#end_date').datepicker('setDate', new Date(date));
                $('#end_date').datepicker('destroy');
                getExpectedTotal();
            });

            $("#update_timeline").on("change", function() {
                let amount = document.getElementById('update_amount').value;
                var timeline = $(this).find("option:selected").data("info");
                let startdate = document.getElementById('update_start_date').value;
                let enddate = document.getElementById('update_end_date').value;
                var date = new Date(startdate);
                date.setDate(date.getDate() + timeline);
                $('#update_end_date').datepicker('setDate', new Date(date));
                $('#update_end_date').datepicker('destroy');
                getExpectedTotalForUpdate();
            });

            function checkTimeline() {
                let timeline = document.getElementById('timeline');
                let equb_type_id = document.getElementById('equb_type_id');
                var equbTypeRote = $("#equb_type_id").find("option:selected").data("rote");
                const options = timeline.options;
                if (equbTypeRote === 'Daily') {
                    for (var i = 1; i < options.length; i++) {
                        options[i].disabled = false;
                        if (options[i].value !== "105" && options[i].value !== "210" && options[i].value !== "315" && options[i]
                            .value !== "420") {
                            options[i].disabled = true;
                        }
                    }
                } else if (equbTypeRote === 'Weekly') {
                    for (var i = 1; i < options.length; i++) {
                        options[i].disabled = false;
                        if (options[i].value !== "350" && options[i].value !== "700" && options[i].value !== "1050") {
                            options[i].disabled = true;
                        }
                    }
                } else if (equbTypeRote === 'Monthly') {
                    for (var i = 1; i < options.length; i++) {
                        options[i].disabled = false;
                        if (options[i].value !== "365" && options[i].value !== "730" && options[i].value !== "1095") {
                            options[i].disabled = true;
                        }
                    }
                }
            }

            function checkTimelineForEqubUpdate() {
                let timeline = document.getElementById('update_timeline');
                let equb_type_id = document.getElementById('update_equb_type');
                var equbTypeRote = $("#update_equb_type").find("option:selected").data("rote");
                const options = timeline.options;
                if (equbTypeRote === 'Daily') {
                    for (var i = 1; i < options.length; i++) {
                        options[i].disabled = false;
                        if (options[i].value !== "105" && options[i].value !== "210" && options[i].value !== "315" && options[i]
                            .value !== "420") {
                            options[i].disabled = true;
                        }
                    }
                } else if (equbTypeRote === 'Weekly') {
                    for (var i = 1; i < options.length; i++) {
                        options[i].disabled = false;
                        if (options[i].value !== "350" && options[i].value !== "700" && options[i].value !== "1050") {
                            options[i].disabled = true;
                        }
                    }
                } else if (equbTypeRote === 'Monthly') {
                    for (var i = 1; i < options.length; i++) {
                        options[i].disabled = false;
                        if (options[i].value !== "365" && options[i].value !== "730" && options[i].value !== "1095") {
                            options[i].disabled = true;
                        }
                    }
                }
            }

            function getExpectedTotal() {
                let amount = document.getElementById('amount_per_day').value;
                let startdate = document.getElementById('start_date').value;
                let enddate = document.getElementById('end_date').value;
                var quota = $("#equb_type_id").find("option:selected").data("quota");
                var type = $("#equb_type_id").find("option:selected").data("info");
                var expectedTotal = 0
                if (type === 'Automatic') {
                    expectedTotal = quota * amount;
                } else {
                    startdate = new Date(startdate);
                    enddate = new Date(enddate);
                    // let dateDiff = parseInt((enddate - startdate) / (1000 * 60 * 60 * 24), 10);
                    var timeDiff = Math.abs(enddate - startdate);
                    var dateDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    expectedTotal = dateDiff * amount;
                }
                $('#total_amount').val(expectedTotal);
            }

            function getExpectedTotalForUpdate() {
                let amount = document.getElementById('update_amount').value;
                let startdate = document.getElementById('update_start_date').value;
                let enddate = document.getElementById('update_end_date').value;
                var quota = $("#update_equb_type").find("option:selected").data("quota");
                var type = $("#update_equb_type").find("option:selected").data("info");
                if (type === 'Automatic') {
                    expectedTotal = quota * amount;
                } else {
                    startdate = new Date(startdate);
                    enddate = new Date(enddate);
                    var timeDiff = Math.abs(enddate - startdate);
                    var dateDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    // let dateDiff = parseInt((enddate - startdate) / (1000 * 60 * 60 * 24), 10);
                    expectedTotal = dateDiff * amount;
                }
                $('#update_total_amount').val(expectedTotal);
            }

            function openPaymentModal(item) {
                $('#member_payment_id').val(item.member_id);
                $('#equb_payment_id').val(item.id);
                $('#equb_amount').val(item.amount);
                $('#amount').val(item.amount);
                let creadit = item.amount - $('#amount').val();
                $('#creadit').val(creadit);
                $('#myModal3').modal('show');
            }

            function openLotteryModal(item) {
                let index = item.equb_takers.length - 1;
                let equbTakers = item.equb_takers;
                if (equbTakers.length > 0) {
                    let remainingAmount = item.equb_takers[index].remaining_amount;
                    $('#lottery_amount').val(remainingAmount);
                } else {
                    $('#lottery_amount').val(item.total_amount);
                }
                $('#member_lottery_id').val(item.member_id);
                $('#equb_lottery_id').val(item.id);
                $.ajax({
                    url: '/getRemainingLotteryAmount/' + item.id,
                    method: 'get',
                    success: function(data) {
                        if (data < 0) {
                            $('#lotteryPaymentButton').addClass('disabled');
                            $('#lotteryPaymentButton').prop('disabled', true);
                            $('#lotteryEdit').addClass('disabled');
                            $('#lotteryEdit').prop('disabled', true);
                            $('#lotteryDelete').addClass('disabled');
                            $('#lotteryDelete').prop('disabled', true);
                        } else {
                            $('#lotteryModal').modal('show');
                        }
                    }
                });

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
                $('#updatePayment').attr('action', 'payment/updatePayment/' + $('#update_member_id').val() + '/' + $('#equb_id')
                    .val() + '/' + $('#payment_id').val());
            }

            function getCredit() {
                let daily_amount = 0;
                let equb_id = document.getElementById('equb_id').value;
                $.ajax({
                    url: "{{ url('/getDailyPaidAmount') }}" + '/' + equb_id,
                    type: 'get',
                    success: function(data) {
                        daily_amount = data;
                        let amount = document.getElementById('update_payment_amount').value;
                        // console.log('amount', amount);
                        let total_amount = daily_amount - amount;
                        $('#update_payment_credit').val(total_amount);
                        // console.log('daily_amount', daily_amount);
                    },
                    error: function() {
                        // console.log("Error Occurred");
                    }
                });

            }

            function openLotteryPaymentMenu(item) {
                // console.log("object", item);
                $('#lottery_id').val(item.id);
                $('#update_member_id').val(item.member_id);
                $('#equb_id').val(item.equb_id);
                $.ajax({
                    url: '/getRemainingLotteryAmount/' + item.equb_id,
                    method: 'get',
                    success: function(data) {
                        if (data < 0) {
                            $('#lotteryPaymentButton').addClass('disabled');
                            $('#lotteryPaymentButton').prop('disabled', true);
                            $('#lotteryEdit').addClass('disabled');
                            $('#lotteryEdit').prop('disabled', true);
                            $('#lotteryDelete').addClass('disabled');
                            $('#lotteryDelete').prop('disabled', true);
                        } else {}
                    }
                });
            }

            function openLotteryPaymentEditModal(item) {
                // console.log("object", item);
                $('#lottery_id').val(item.id);
                $('#update_member_id').val(item.member_id);
                $('#equb_id').val(item.equb_id);
                $('#editLotteryPaymentModal').modal('show');
                $('#update_lottery_payment_type>option[value="' + item.payment_type + '"]').prop('selected', true);
                $('#update_lottery_amount').val(item.amount);
                $('#update_lottery_status>option[value="' + item.status + '"]').prop('selected', true);
                $('#update_lottery_cheque_amount').val(item.cheque_amount);
                $('#update_lottery_cheque_bank_name').val(item.cheque_bank_name);
                if (item.paid_date) {
                    var paidDate = new Date(item.paid_date);
                    paidDate = moment(paidDate);
                    paidDate = paidDate.format("YYYY-MM-DD");
                    $('#update_paid_date').val(paidDate);
                }
                $('#updateLotteryPayment').attr('action', 'equbTaker/updateLottery/' + $('#update_member_id').val() + '/' + $(
                    '#equb_id').val() + '/' + $('#lottery_id').val());
            }

            function removeTabs() {
                $('#custom-tabs-two-messages-tab').css('display', 'none');
                $('#custom-tabs-two-messages').removeClass('active');
            }
            $(function() {
                $.LoadingOverlay("show");
                $('#settingNavm').addClass('menu-is-opening menu-open');
                $('#nav-mem').addClass('active');
                $('#mem').addClass('active');
                $.ajax({
                    url: "{{ url('member/member') }}" + '/' + 0 + '/' + 1,
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
                        $.LoadingOverlay("hide");
                    }
                });
                $("#update_start_date").datetimepicker({
                    'format': "YYYY-MM-DD",
                }).on('dp.change', function(e) {
                    let amount = document.getElementById('update_amount').value;
                    let startdate = document.getElementById('update_start_date').value;
                    let enddate = document.getElementById('update_end_date').value;
                    let timelineInput = document.getElementById('update_timeline');
                    startdate = new Date(startdate);
                    enddate = new Date(enddate);
                    // let dateDiff = parseInt((enddate - startdate) / (1000 * 60 * 60 * 24), 10);
                    var timeDiff = Math.abs(enddate - startdate);
                    var dateDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    var expectedTotal = dateDiff * amount;
                    $('#update_total_amount').val(expectedTotal);
                    timelineInput.options[0].selected = true;
                });
                $("#update_end_date").datetimepicker({
                    'format': "YYYY-MM-DD",
                }).on('dp.change', function(e) {
                    let amount = document.getElementById('update_amount').value;
                    let startdate = document.getElementById('update_start_date').value;
                    let enddate = document.getElementById('update_end_date').value;
                    startdate = new Date(startdate);
                    enddate = new Date(enddate);
                    // let dateDiff = parseInt((enddate - startdate) / (1000 * 60 * 60 * 24), 10);
                    var timeDiff = Math.abs(enddate - startdate);
                    var dateDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    var expectedTotal = dateDiff * amount;
                    $('#update_total_amount').val(expectedTotal);
                });
                $("#update_lottery_date").datepicker({
                    format: 'yyyy-mm-dd',
                    multidate: true,
                    clearBtn: true,
                    todayHighlight: true,
                });
                $("#start_date").datetimepicker({
                    'format': "YYYY-MM-DD",
                }).on('dp.change', function(e) {
                    let amount = document.getElementById('amount_per_day').value;
                    let startdate = document.getElementById('start_date').value;
                    let enddate = document.getElementById('end_date').value;
                    let timelineInput = document.getElementById('timeline');
                    startdate = new Date(startdate);
                    enddate = new Date(enddate);
                    // let dateDiff = parseInt((enddate - startdate) / (1000 * 60 * 60 * 24), 10);
                    var timeDiff = Math.abs(enddate - startdate);
                    var dateDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    var expectedTotal = dateDiff * amount;
                    $('#total_amount').val(expectedTotal);
                    timelineInput.options[0].selected = true;
                });

                var datePickerOptions = {
                    format: 'yyyy-mm-dd',
                    clearBtn: true,
                    multidate: true,
                    todayHighlight: true
                };
                $("#lottery_date").datepicker(datePickerOptions);
                document.getElementById('type').addEventListener('change', function() {
                const selectedType = this.value;
                const timeline = document.getElementById('timeline_div'); // Assuming this is the correct ID for the timeline div

                // Clear existing options in equb_type_id based on selected type
                const equbTypeSelect = document.getElementById('equb_type_id');
                equbTypeSelect.innerHTML = '<option value="">Choose...</option>';

                // Update timeline visibility based on selected type
                if (selectedType === "Manual") {
                    timeline.classList.remove("d-none");
                } else if (selectedType === "Seasonal" || selectedType === "Automatic") {
                    timeline.classList.add("d-none");
                }

                    // Populate equb_type_id based on selected type
                    @foreach ($equbTypes as $equbType)
                        if (selectedType === "{{ $equbType->type }}") {
                            const option = document.createElement('option');
                            option.value = "{{ $equbType->id }}";
                            option.textContent = "{{ $equbType->name }} round {{ $equbType->round }}";
                            option.setAttribute('data-info', "{{ $equbType->type }}");
                            option.setAttribute('data-startdate', "{{ $equbType->start_date }}");
                            option.setAttribute('data-enddate', "{{ $equbType->end_date }}");
                            option.setAttribute('data-rote', "{{ $equbType->rote }}");
                            option.setAttribute('data-quota', "{{ $equbType->quota }}");
                            option.setAttribute('data-amount', "{{ $equbType->amount }}");
                            option.setAttribute('data-expected-total', "{{ $equbType->expected_total }}");
                            equbTypeSelect.appendChild(option);
                        }
                    @endforeach
                });

                $(document).ready(function() {
                    const selectBox = document.getElementById("equb_type_id");
                    const updateSelectBox = document.getElementById("equb_type");
                    const lotteryDate = document.getElementById("equb_lottery_date_div");
                    const timeline = document.getElementById("timeline_div");
                    $("#equb_type_id").on("change", function() {
                        var type = $(this).find("option:selected").data("info");
                        if (type === "Manual" || type === "Manual") {
                            lotteryDate.classList.remove("d-none");
                            timeline.classList.remove("d-none");
                            lotteryDate.required = true;
                        } else {
                            var equbTypeStartDate = $(this).find("option:selected").data("startdate");
                            var equbTypeEndDate = $(this).find("option:selected").data("enddate");
                            $('#start_date').datepicker('setDate', new Date(equbTypeStartDate));
                            $('#start_date').datepicker('destroy');
                            $('#end_date').datepicker('setDate', new Date(equbTypeEndDate));
                            $('#end_date').datepicker('destroy');
                            lotteryDate.classList.add("d-none");
                            timeline.classList.add("d-none");
                            lotteryDate.required = false;
                        }
                        $("#lottery_date").datepicker("destroy");
                        $("#lottery_date").datepicker(datePickerOptions);
                    });
                    $("#update_equb_type").on("change", function() {
                        var type = $(this).find("option:selected").data("info");
                        const lotteryDate = document.getElementById("update_equb_lottery_date_div");
                        const timeline = document.getElementById("update_timeline_div");
                        if (type !== "Automatic") {
                            lotteryDate.classList.remove("d-none");
                            timeline.classList.remove("d-none");
                            lotteryDate.required = true;
                        } else {
                            var equbTypeStartDate = $(this).find("option:selected").data("startdate");
                            var equbTypeEndDate = $(this).find("option:selected").data("enddate");
                            $('#update_start_date').datepicker('setDate', new Date(equbTypeStartDate));
                            $('#update_start_date').datepicker('destroy');
                            $('#update_end_date').datepicker('setDate', new Date(equbTypeEndDate));
                            $('#update_end_date').datepicker('destroy');
                            lotteryDate.classList.add("d-none");
                            timeline.classList.add("d-none");
                            lotteryDate.required = false;
                        }
                        $("#update_lottery_date").datepicker("destroy");
                        $("#update_lottery_date").datepicker(datePickerOptions);
                    });
                });
                $("#lottery_date_check").datepicker({
                    format: 'yyyy-mm-dd',
                    //multidate: true,
                    clearBtn: true,
                    todayHighlight: true,
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
                $('#updateLotteryPayment').validate({
                    onfocusout: false,
                    rules: {
                        update_lottery_payment_type: {
                            required: true,
                            minlength: 1,
                            maxlength: 30,
                        },
                        update_lottery_amount: {
                            required: true,
                            maxlength: 10,
                        },
                        update_lottery_status: {
                            required: true,
                        },
                    },
                    messages: {
                        update_lottery_payment_type: {
                            required: "Select payment type",
                            minlength: "payment type must be more than 1 characters long",
                            maxlength: "payment type must be less than 30 characters long",
                        },
                        update_lottery_amount: {
                            required: "Please enter a lottery amount",
                            maxlength: "Lottery amount must be less than or equal to 10 number",
                        },
                        update_lottery_status: {
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
                        const myButton = document.getElementById('updateLotteryBtn');
                        myButton.disabled = true;
                    }

                });
                $('#addLottery').validate({
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
                        const myButton = document.getElementById('addLotteryBtn');
                        myButton.disabled = true;
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
                $('#updateEqub').validate({

                    rules: {
                        equb_type_id: {
                            required: true,
                            minlength: 1,
                            maxlength: 30,
                            remote: {
                                url: '{{ url('equbTypeCheckForUpdate') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    equb_type_id: function() {
                                        return $('#updateEqub :input[name="equb_type_id"]').val();
                                    },
                                    member_id: function() {
                                        return $('#updateEqub :input[name="member_id"]').val();
                                    },
                                    equb_id: function() {
                                        return $('#updateEqub :input[name="equb_id"]').val();
                                    }
                                }
                            },

                        },
                        round: {
                            required: true,
                            maxlength: 10,
                        },
                        amount: {
                            required: true,
                            maxlength: 10,
                        },
                        total_amount: {
                            required: true,
                            maxlength: 10,
                        },
                        start_date: {
                            required: true,
                            date: true,
                        },
                        timeline: {
                            required: true,
                        },
                        end_date: {
                            required: true,
                            date: true,
                            // greaterThan: "#update_start_date",
                        },
                        lottery_date: {
                            required: true,
                            remote: {
                                url: '{{ url('lotteryDateCheckForUpdate') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    lotteryDate: function() {
                                        return $('#updateEqub :input[name="update_lottery_date"]').val();
                                    },
                                    equb_id: function() {
                                        return $('#updateEqub :input[name="equb_id"]').val();
                                    },
                                }
                            },
                        },
                    },
                    messages: {
                        equb_type_id: {
                            required: "Select equb type",
                            remote: "Equb type already exist",
                            maxlength: "Equb type must be less than 30 characters long",
                        },
                        round: {
                            required: "Please enter a round",
                            maxlength: "Round must be less than or equal to 10 number",
                        },
                        amount: {
                            required: "Please enter a amount",
                            maxlength: "Amount must be less than or equal to 10 number",
                        },
                        total_amount: {
                            required: "Please enter a expected total",
                            maxlength: "Expected total must be less than or equal to 10 number",
                        },
                        start_date: {
                            required: "Please enter a start date",
                            date: "Please enter proper date",
                        },
                        timeline: {
                            required: "Please select a timeline",
                        },
                        end_date: {
                            required: "Please enter a end date",
                            date: "Please enter proper date",
                            // greaterThan: "End date must be greater than start date",
                        },
                        lottery_date: {
                            required: "Please enter a Lottery date",
                            remote: "This date is rejected date"
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
                        const myButton = document.getElementById('updateEqubBtn');
                        myButton.disabled = true;
                    }

                });
                $('#start_date').on('focus', function() {
                    // Clear existing error messages
                    $(this).removeClass('is-invalid');
                    $(this).closest('.form-group').find('.invalid-feedback').remove();
                });
                $('#lottery_date').on('focus', function() {
                    // Clear existing error messages
                    $(this).removeClass('is-invalid');
                    $(this).closest('.form-group').find('.invalid-feedback').remove();
                });
                $('#end_date').on('input', function() {
                    clearErrorMessages($(this));
                });

                // Function to clear error messages
                function clearErrorMessages(element) {
                    element.removeClass('is-invalid');
                    element.closest('.form-group').find('.invalid-feedback').remove();
                }

                // Trigger error message clearing when dependent fields are updated
                $('#timeline').on('change', function() {
                    $('#end_date').trigger('input');
                });
                $('#total_amount').on('input', function() {
                    clearErrorMessages($(this));
                });

                // Function to clear error messages
                function clearErrorMessages(element) {
                    element.removeClass('is-invalid');
                    element.closest('.form-group').find('.invalid-feedback').remove();
                }

                // Trigger error message clearing when dependent fields are updated
                $('#amount_per_day').on('change', function() {
                    $('#total_amount').trigger('input');
                });

                $('#addEqub').validate({
                    onfocusout: false,
                    rules: {
                        equb_type_id: {
                            required: true,
                            minlength: 1,
                            maxlength: 30,
                            remote: {
                                url: '{{ url('equbTypeCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    equb_type_id: function() {
                                        return $('#addEqub :input[name="equb_type_id"]').val();
                                    },
                                    member_id: function() {
                                        return $('#addEqub :input[name="member_id"]').val();
                                    },
                                }
                            },

                        },
                        amount: {
                            required: true,
                            maxlength: 10,
                        },
                        total_amount: {
                            required: true,
                            maxlength: 10,
                        },
                        start_date: {
                            required: true,
                            date: true,
                        },
                        timeline: {
                            required: true
                        },
                        end_date: {
                            required: true,
                            date: true,
                            // greaterThan: "#start_date",
                        },
                        lottery_date: {
                            required: true,
                            remote: {
                                url: '{{ url('lotteryDateCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    lotteryDate: function() {
                                        return $('#addEqub :input[name="lottery_date"]').val();
                                    },
                                }
                            },
                        },
                    },
                    messages: {
                        equb_type_id: {
                            required: "Select equb type",
                            remote: "Equb type already exist",
                            maxlength: "Equb type must be less than 30 characters long",
                        },
                        amount: {
                            required: "Please enter a amount",
                            maxlength: "Amount must be less than or equal to 10 number",
                        },
                        total_amount: {
                            required: "Please enter a total expected total",
                            maxlength: "Expected total must be less than or equal to 10 number",
                        },
                        start_date: {
                            required: "Please enter a start date",
                            date: "Please enter proper date",
                        },
                        timeline: {
                            required: "Please select a timeline"
                        },
                        end_date: {
                            required: "Please enter a end date",
                            date: "Please enter proper date",
                            // greaterThan: "End date must be greater than start date",
                        },
                        lottery_date: {
                            required: "Please enter a lottery date",
                            remote: "This date is rejected date",
                        },
                    },
                    errorElement: 'span',
                    // errorPlacement: function(error, element) {
                    //     error.addClass('invalid-feedback');
                    //     element.closest('.form-group').append(error);
                    //     $.LoadingOverlay("hide");
                    // },
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        var errorContainer = element.closest('.form-group').find('.invalid-feedback');
                        if (errorContainer.length) {
                            errorContainer.html(error);
                        } else {
                            element.closest('.form-group').append(error);
                        }
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
                        const myButton = document.getElementById('addEqubBtn');
                        myButton.disabled = true;
                    }

                });
                $('#updateMember').validate({
                    onfocusout: false,
                    rules: {
                        full_name: {
                            required: true,
                            minlength: 2,
                            maxlength: 40,
                            pattern: /^[a-zA-Z ]+$/,
                        },
                        phone: {
                            required: true,
                            minlength: 13,
                            maxlength: 13,
                            // digits: true,
                            pattern: /^[+][2][5][1][9][0-9]{8}$/,
                            remote: {
                                url: '{{ url('phoneCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    phone: function() {
                                        return $('#updateMember :input[name="phone"]').val();
                                    },
                                    m_id: function() {
                                        return $('#updateMember :input[name="m_id"]').val();
                                    }
                                }
                            },
                        },
                        gender: {
                            required: true,
                        },
                    },
                    messages: {
                        full_name: {
                            required: "Please enter a full name",
                            minlength: "Full name must be more than 2 characters long",
                            maxlength: "Full name must be less than 40 characters long",
                            pattern: "Please enter alphabet only"
                        },
                        phone: {
                            required: "Please enter a phone number",
                            minlength: "Phone must be 13 characters",
                            maxlength: "Phone must be 13 characters",
                            // digits: "phone must be number",
                            remote: "phone already exist",
                            pattern: "Phone must be in +251... format"
                        },
                        gender: {
                            required: "Select gender",
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
                        const myButton = document.getElementById('updateSubmitBtn');
                        myButton.disabled = true;
                    }

                });

                $('#addMember').validate({
                    onfocusout: false,
                    rules: {
                        full_name: {
                            required: true,
                            minlength: 2,
                            maxlength: 40,
                            pattern: /^[a-zA-Z ]+$/,
                        },
                        phone: {
                            required: true,
                            minlength: 13,
                            maxlength: 13,
                            // digits: true,
                            pattern: /^[+][2][5][1][9][0-9]{8}$/,
                            // pattern: /^([+][2][5][1]([7]|[9])[0-9]{8}$)|[+][2][5][1][9][0-9]{8}$/,
                            remote: {
                                url: '{{ url('phoneCheck') }}',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                data: {
                                    phone: function() {
                                        return $('#addMember :input[name="phone"]').val();
                                    }
                                }
                            },
                        },
                        gender: {
                            required: true,
                        },
                    },
                    messages: {
                        full_name: {
                            required: "Please enter a full name",
                            minlength: "Full name must be more than 2 characters long",
                            maxlength: "Full name must be less than 40 characters long",
                            pattern: "Please enter alphabet only"
                        },
                        phone: {
                            required: "Please enter a phone number",
                            minlength: "Phone must be 13 characters",
                            maxlength: "Phone must be 13 characters",
                            // digits: "phone must be number",
                            remote: "phone already exist",
                            pattern: "Phone must be in +251... format"
                        },
                        gender: {
                            required: "Select gender",
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
                        const myButton = document.getElementById('submitBtn');
                        myButton.disabled = true;
                    }

                });
            });
        </script>
    @endSection
