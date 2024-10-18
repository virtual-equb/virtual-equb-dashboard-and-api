@if (Auth::user()->role == 'equb_collector')
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
            content:"*";
            color:red;
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
          @media (max-width: 575.98px) {
              #payment-list-table_in_tab {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
              }
              .table-responsive-sm > .table-bordered {
                border: 0;
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
                    <a class="nav-link active" id="custom-tabs-two-member-tab" data-toggle="pill" href="#custom-tabs-two-member" role="tab" aria-controls="custom-tabs-two-member" aria-selected="true"> <span class="fa fa-list"> </span>  Member</a>
                  </li>
                   <li class="nav-item paymentTab" id="payment-tab" style="display: none;">
                    <a class="nav-link" id="custom-tabs-two-messages-tab" data-toggle="pill" href="#custom-tabs-two-messages" role="tab" aria-controls="custom-tabs-two-messages" aria-selected="false"><span class="fa fa-list"> </span> Payment</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-two-member" role="tabpanel" aria-labelledby="custom-tabs-two-member-tab">
                     @include('equbCollecter/payment.addPayment')
                   <div class="table-responsive">
                      <div class="float-right searchandClear" id="member_table_filter">
                          <input type="text" id="memberSearchText" placeholder="Search" class="search">
                          <button class="btn btn-default clear" id="clearActiveSearch"
                              onclick="clearSearchEntry()">
                              Clear
                          </button>
                      </div>
                      <div id="member_table_data_w" class="col-md-8">

                     </div>
                      <div class="table-responsive">
                      <div id="member_table_data">


                      </div>
                  </div>
                  </div>

                  </div>
                  <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">

                  </div>
                  <div class="tab-pane fade" id="custom-tabs-two-messages" role="tabpanel" aria-labelledby="custom-tabs-two-messages-tab">

                  </div>
                  <div class="tab-pane fade" id="custom-tabs-two-settings" role="tabpanel" aria-labelledby="custom-tabs-two-settings-tab">

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
 @include('equbCollecter/payment.deletePayment')
 @include('equbCollecter/payment.editPayment')
  @endsection

  @section('scripts')

    <script>
           var memberSearchField = document.getElementById('memberSearchText');
          memberSearchField.addEventListener("keydown", function(e) {
              var memberSearchInput = memberSearchField.value;
              if (e.keyCode === 13) { //checks whether the pressed key is "Enter"
                  searchForMember(memberSearchInput);
              }
          });
      function searchForMember(searchInput) {
          if (searchInput != "") {
              $.ajax({
                  url: "{{ url('member/search-member') }}" + '/' + searchInput + '/0',
                  type: 'get',
                  success: function(data) {
                      $('#member_table_data').html(data);
                  }
              });
          }
      }
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
      function clearSearchEntry() {
                var searchInput = document.getElementById('memberSearchText').value;
                if (searchInput != "") {
                    document.getElementById('memberSearchText').value = "";
                    searchInput = null;
                    $.ajax({
                        url: "{{ url('member/search-member') }}" + '/' + searchInput + '/0',
                        type: 'get',
                        success: function(data) {
                            $('#member_table_data').html(data);
                        }
                    });

                }
            }
      function members(offsetVal, pageNumberVal) {
                $.ajax({
                    url: "{{ url('member/member') }}" + '/' + offsetVal + '/' + pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
                    }
                });
            }
       function validateForm(){
        $('.form-horizontal form-group nn').validate({
                onfocusout: false,
                rules: {
                    lottery_date: {
                        remote: {
                            url: '{{url('dateEqubLotteryCheck') }}',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            type: "post",
                            data: {
                                lottery_date: function() {
                                    return $('#addEqub :input[name="lottery_date"]').val();
                                },

                            }
                        },
                    },
                },
                 messages : {
                     lottery_date : {
                         remote : "Lottery date must be greater than current date"
                     },
                },
                 errorElement: 'span',
                    errorPlacement: function (error, element) {
                      error.addClass('invalid-feedback');
                      element.closest('.form-group').append(error);
                    },
                    highlight: function (element, errorClass, validClass) {
                      $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                      $(element).removeClass('is-invalid');
                    },
                submitHandler: function(form) {
                form.submit();
                }

            });
       }
        function openDeletePaymentModal(item){
          $('#payment_id').val(item.id );
          $('#deletePaymentModal').modal('show');
          $('#deletePayment').attr('action',  'payment/delete/'+$('#payment_id').val())
        }
        function openPaymentTab(equb){
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
                        $('#custom-tabs-two-messages').html(form);
                    }
                });
            }
        function payments(equb,offsetVal, pageNumberVal){
                $('#custom-tabs-two-member-tab').removeClass('active');
                $('#payment-tab').css('display', 'inline');
                $('#custom-tabs-two-messages-tab').addClass('active');
                $('#custom-tabs-two-member').removeClass('active');
                $('#custom-tabs-two-messages').addClass('show active');
                 $.ajax({
                    url: `payment/show-payment/${equb.member_id}/${equb.id}/${offsetVal}/${pageNumberVal}`,
                    method: 'get',
                    success: function(form) {
                        $('#custom-tabs-two-messages').html(form);
                    }
                });
            }
        function changeCreadit(){
          let amount=document.getElementById('amount').value;
          let equb_amount=document.getElementById('equb_amount').value;
        //   console.log("amount",amount);
        //   console.log("equb_amount",equb_amount);
          let diff = equb_amount - amount;
           $('#creadit').val(diff);
        }
        function getCredit()
        {
          let daily_amount = 0;
            let equb_id=document.getElementById('equb_id').value;
            $.ajax({
                url: "{{ url('/getDailyPaidAmount') }}"+'/'+equb_id,
                type: 'get',
                    success: function(data){
                        daily_amount = data;
                        let amount=document.getElementById('update_payment_amount').value;
                        // console.log('amount',amount);
                        let total_amount = daily_amount - amount;
                        $('#update_payment_credit').val(total_amount);
                    //   console.log('daily_amount',daily_amount);
                   },
                    error: function(){
                    }
                });

        }
        function getExpectedTotal(){
            let amount=document.getElementById('amount_per_day').value;
            let startdate=document.getElementById('start_date').value;
            let enddate=document.getElementById('end_date').value;
             startdate = new Date(startdate);
             enddate = new Date(enddate);
            let dateDiff = parseInt((enddate - startdate) / (1000 * 60 * 60 * 24), 10);
            var expectedTotal = dateDiff * amount;
            $('#total_amount').val(expectedTotal);
        }
        function getExpectedTotalForUpdate(){
            let amount=document.getElementById('update_amount').value;
            let startdate=document.getElementById('update_start_date').value;
            let enddate=document.getElementById('update_end_date').value;
             startdate = new Date(startdate);
             enddate = new Date(enddate);
            let dateDiff = parseInt((enddate - startdate) / (1000 * 60 * 60 * 24), 10);
            var expectedTotal = dateDiff * amount;
            $('#update_total_amount').val(expectedTotal);
        }
         function openPaymentModal(item){
          $('#member_payment_id').val(item.member_id);
          $('#equb_payment_id').val(item.id);
          $('#equb_amount').val(item.amount);
          $('#amount').val(item.amount);
          let creadit = item.amount - $('#amount').val();
          $('#creadit').val(creadit);
          $('#myModal3').modal('show');
        }
        function openPaymentEditModal(item){
          $('#payment_id').val(item.id );
          $('#update_member_id').val(item.member_id );
          $('#equb_id').val(item.equb_id );
          $('#editPaymentModal').modal('show');
          $('#update_payment_type>option[value="' + item.payment_type + '"]').prop('selected', true);
          $('#update_payment_amount').val(item.amount);
           let total_amount = item.equb.amount-item.amount
          $('#update_payment_credit').val(total_amount);
          $('#update_payment_status>option[value="' + item.status + '"]').prop('selected', true);
          $('#updatePayment').attr('action',  'payment/updatePayment/'+$('#update_member_id').val()+'/'+$('#equb_id').val()+'/'+$('#payment_id').val());
        }
        function removeTabs() {
         $('#custom-tabs-two-messages-tab').css('display', 'none');
         $('#custom-tabs-two-messages').removeClass('active');

        }

        $(function () {
                $.ajax({
                    url: "{{ url('member/member') }}" + '/' + 0 + '/' + 1,
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
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
                 messages : {
                     payment_type :{
                         required : "Select payment type",
                         minlength : "payment type must be more than 1 characters long",
                         maxlength : "payment type must be less than 30 characters long",
                     },
                     amount :{
                         required : "Please enter a amount",
                         maxlength : "amount must be less than or equal to 10 number",
                     },
                     status : {
                         required : "Select status",
                     },
                },
                 errorElement: 'span',
                    errorPlacement: function (error, element) {
                      error.addClass('invalid-feedback');
                      element.closest('.form-group').append(error);
                    },
                    highlight: function (element, errorClass, validClass) {
                      $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                      $(element).removeClass('is-invalid');
                    },
                submitHandler: function(form) {
                form.submit();
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
                 messages : {
                     update_payment_type :{
                         required : "Select payment type",
                         minlength : "payment type must be more than 1 characters long",
                         maxlength : "payment type must be less than 30 characters long",
                     },
                     update_amount :{
                         required : "Please enter a amount",
                         maxlength : "amount must be less than or equal to 10 number",
                     },
                     update_status : {
                         required : "Select status",
                     },
                },
                 errorElement: 'span',
                    errorPlacement: function (error, element) {
                      error.addClass('invalid-feedback');
                      element.closest('.form-group').append(error);
                    },
                    highlight: function (element, errorClass, validClass) {
                      $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                      $(element).removeClass('is-invalid');
                    },
                submitHandler: function(form) {
                form.submit();
                }

            });
           $('#nav-mem').addClass('menu-is-opening menu-open');
           $('#mem').addClass('active');
        var table=$("#member-list-table").DataTable({
          "responsive": false, "lengthChange": false,"searching": false, "autoWidth": false,
          language: {
            search: "",
            searchPlaceholder: "Search",},
          // "buttons": ["excel", "pdf", "print", "colvis"]
        });
        table.buttons().container().appendTo('#member-list-table_wrapper .col-md-6:eq(0)');
          $('#member-list-table tbody').on('click', 'td.details-control_equb', function() {
              var tr = $(this).closest('tr');
              var inputId = $(this).prop("id");
              var row = table.row(tr);
              if (row.child.isShown()) {
                  row.child.hide();
                  tr.removeClass('shown')
              } else {
                var loadHere = '<br><div id="loading" class="row d-flex justify-content-center"><div class="row"><img src="' +
                "{{ url('images/loading.gif') }}" + '"/></div></div>';
                row.child(loadHere).show();
              $.ajax({
                url: "{{ url('member/show-member') }}"+'/'+inputId,
                type: 'get',
                    success: function(data){
                        row.child(data).show();
                        row.child.show();
                        tr.addClass('shown');
                   },
                    error: function(){
                    //   console.log("Error Occurred");
                    }
                });

           }

          });
      });
    </script>
  @endSection
  @endif
