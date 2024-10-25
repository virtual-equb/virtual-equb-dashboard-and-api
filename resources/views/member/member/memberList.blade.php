{{-- @role ('Member') --}}
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
        td.details-control_lottery {
            background: url("{{ url('images/plus20.webp') }}") no-repeat center center;
            cursor: pointer;
        }
        tr.shown td.details-control_lottery {
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
        
         @media (max-width: 768px) {
              .table.table-bordered.dataTable {
               padding-right: 2px;
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
            @media (max-width: 768px) {
              .col-md-6 {
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
                <div class="table-responsive">
                <table id="member-list-table" class="table table-bordered table-striped">
                  <thead >
                  <tr>
                    <th></th>
                    <th>No</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Registered At </th>
                  </tr>
                  </thead>
                     <tbody>
                        @foreach ($members as $key => $item)
                             <tr  id="trm{{$item['id']}}">
                                <td class="details-control_equb" id="{{ $item['id'] }}"></td>
                                <td>{{$key+1}}</td>
                                <td>{{ $item->full_name }}</td>
                                <td>{{ $item->phone}}</td>
                                <td>{{ $item->gender}}</td>
                                <td>{{ $item->status}}</td>
                                <td>
                                    <?php
                                    $toCreatedAt= new DateTime($item['created_at']);
                                    $createdDate = $toCreatedAt->format("M-j-Y");
                                    echo $createdDate;?>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                </table>
                <div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <p class="modal-title" id="exampleModalLabel">Delete Member</p>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            <form action="" method="post" id="deleteMember">
                                @csrf
                                @method('DELETE')
                                 <input id="id" name="id" hidden value="">
                                <p class="text-center">Are you sure you want to delete this member?</p>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
              <div class="modal modal-danger fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
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
                                 <input id="member_id" name="member_id" hidden value="">
                                <p class="text-center">Are you sure you want to update status?</p>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-sm btn-danger">update</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            </div>
                            </form>
                        </div>
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
  @endsection

  @section('scripts')

    <script>
        function openPaymentTab(equb){

                $('#custom-tabs-two-member-tab').removeClass('active');
                $('#payment-tab').css('display', 'inline');
                $('#custom-tabs-two-messages-tab').addClass('active');
                $('#custom-tabs-two-member').removeClass('active');
                $('#custom-tabs-two-messages').addClass('show active');
                $.ajax({
                    url: `payment/${equb.member_id}/${equb.id}`,
                    method: 'get',
                    success: function(form) {
                        console.log(form);
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
                        // console.log(form);
                        $('#custom-tabs-two-messages').html(form);
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
        function removeTabs() {
         $('#custom-tabs-two-messages-tab').css('display', 'none');
         $('#custom-tabs-two-messages').removeClass('active');

        }
        $(function () {
           $('#nav-mem').addClass('menu-is-opening menu-open');
           $('#mem').addClass('active');
        var table=$("#member-list-table").DataTable({
          "responsive": false, "lengthChange": false,"searching": true, "autoWidth": false,
          language: {
            search: "",
            searchPlaceholder: "Search",},
          //"buttons": ["excel", "pdf", "print", "colvis"]
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
                    }
                });

           }

          });
      });
    </script>
  @endSection
  {{-- @endrole --}}
