              <div class="table-responsive">
                  <table id="member-list-table" class="table table-bordered table-striped" style="padding-bottom:100px">
                      <thead>
                          <tr>
                              {{-- <th></th> --}}
                              <th>No</th>
                              <th>Full Name</th>
                              <th>Phone</th>
                              <th>Gender</th>
                              <th>City</th>
                              <th>Sub City</th>
                              <th>Location</th>
                              <th>Status</th>
                              <th>Registered At </th>
                              <th style="width: 60px">Action </th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($members as $key => $item)
                              <?php
                              //   $address = json_decode($item->address);
                              //   dd($address);
                              ?>
                              <tr id="trm{{ $item['id'] }}">
                                  {{-- <td class="details-control_equb" id="{{ $item['id'] }}"></td> --}}
                                  <td>{{ $offset + $key + 1 }}</td>
                                  <th>{{ $item->full_name }}</th>
                                  <th>{{ $item->phone }}</th>
                                  <td>{{ $item->gender }}</td>
                                  <td>{{ $item->city }}</td>
                                  <td>{{ $item->subcity }}</td>
                                  <td>{{ $item->specific_location }}</td>
                                  <td>{{ $item->status }}</td>
                                  <td>
                                      <?php
                                      $toCreatedAt = new DateTime($item['created_at']);
                                      $createdDate = $toCreatedAt->format('M-j-Y');
                                      echo $createdDate; ?>
                                  </td>
                                  @if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant')
                                      <td>
                                          <div class='dropdown'>
                                              <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle'
                                                  type='button' data-toggle='dropdown'>Menu<span
                                                      class='caret'></span></button>
                                              <ul class='dropdown-menu p-4'>
                                                  {{-- @if (Auth::user()->role != 'finance') --}}
                                                      {{-- <li>
                                                          <button href="javascript:void(0);"
                                                              class="text-secondary btn btn-flat {{ $item->status == 'Deactive' ? 'disabled' : '' }}"
                                                              onclick="openEqubAddModal({{ $item }})"><i
                                                                  class="fas fa-plus-circle"></i> Add equb</button>

                                                      </li> --}}
                                                      @can('send member_notification')
                                                      <li>
                                                          <button href="javascript:void(0);"
                                                              class="text-secondary btn btn-flat"
                                                              onclick="sendNotificationModal({{ $item }})"><i
                                                                  class="fas fa-plus-circle"></i> Send
                                                              Notification</button>

                                                      </li>
                                                      @endcan
                                                      {{-- <li>
                                                          <button href="javascript:void(0);"
                                                              class="text-secondary btn btn-flat"
                                                              onclick="openEditModal({{ $item }})"><span
                                                                  class="fa fa-edit"> </span> Edit</button>
                                                      </li>
                                                      <li>
                                                          <button href="javascript:void(0);"
                                                              class="text-secondary btn btn-flat {{ $item->status == 'Deactive' ? 'disabled' : '' }}"
                                                              onclick="openDeleteModal({{ $item }})"><i
                                                                  class="fas fa-trash-alt"></i> Delete</button>

                                                      </li> --}}
                                                      @if ($item->status == 'Pending')
                                                       @can('approve member')
                                                          <li>
                                                              <a href="javascript:void(0);"
                                                                  class="text-secondary btn btn-flat"
                                                                  onclick="statusPendingChange({{ $item }},'Active')"
                                                                  style="margin-right:10px;" id="statuss"
                                                                  name="statuss"><i class="fab fa-shopware"></i>
                                                                  Approve
                                                              </a>
                                                          </li>
                                                        @endcan
                                                        @can('reject member')
                                                          <li>
                                                              <a href="javascript:void(0);"
                                                                  class="text-secondary btn btn-flat"
                                                                  onclick="statusPendingChange({{ $item }},'Deactive')"
                                                                  style="margin-right:10px;" id="statuss"
                                                                  name="statuss"><i class="fab fa-shopware"></i>
                                                                  Reject
                                                              </a>
                                                          </li>
                                                        @endcan
                                                      @endif
                                                      {{-- <li>
                                                          <a href="javascript:void(0);"
                                                              class="text-secondary btn btn-flat"
                                                              onclick="statusChange({{ $item }})"
                                                              style="margin-right:10px;" id="statuss"
                                                              name="statuss"><i class="fab fa-shopware"></i>
                                                              <?php if ($item->status == 'Active') {
                                                                  echo 'Deactivate';
                                                              } else {
                                                                  echo 'Activate';
                                                              }
                                                              ?>
                                                          </a>
                                                      </li> --}}
                                                  {{-- @endif --}}
                                                  {{-- <li>
                                                      <button href="javascript:void(0);"
                                                          class="text-secondary btn btn-flat"
                                                          onclick="openRateModal({{ $item }})"><i
                                                              class="fas fa-trash-alt"></i> Rate Member</button>

                                                  </li> --}}
                                              </ul>
                                          </div>
                                      </td>
                                  @endif
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
                  <form action="" method="post" id="deleteMember" name="deleteMember">
                      <div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog"
                          aria-labelledby="Delete" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <p class="modal-title" id="exampleModalLabel">Delete Member</p>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                      </button>
                                  </div>
                                  <div class="modal-body">

                                      @csrf
                                      @method('DELETE')
                                      <input id="id" name="id" hidden value="">
                                      <p class="text-center">Are you sure you want to delete this member?</p>
                                  </div>
                                  <div class="modal-footer">
                                      <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                      <button type="button" class="btn btn-secondary"
                                          data-dismiss="modal">Cancel</button>
                                  </div>

                              </div>
                          </div>
                      </div>
                  </form>
                  <form action="" method="post" id="updateStatus" name="updateStatus">
                      <div class="modal modal-danger fade" id="statusModal" tabindex="-1" role="dialog"
                          aria-labelledby="Delete" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <p class="modal-title" id="exampleModalLabel">Update member type status</p>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                      </button>
                                  </div>
                                  <div class="modal-body">
                                      @csrf
                                      @method('PUT')
                                      <input id="member_id" name="member_id" hidden value="">
                                      <p class="text-center">Are you sure you want to update status?</p>
                                  </div>
                                  <div class="modal-footer">
                                      <button type="submit" class="btn btn-sm btn-danger">update</button>
                                      <!-- onclick="statusSubmit()" -->
                                      <button type="button" class="btn btn-secondary"
                                          data-dismiss="modal">Cancel</button>

                                  </div>
                              </div>
                          </div>
                      </div>
                  </form>
                  <form action="" method="post" id="updatePendingStatus" name="updatePendingStatus">
                    <div class="modal modal-danger fade" id="statusPendingModal" tabindex="-1" role="dialog"
                        aria-labelledby="Delete" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <p class="modal-title" id="exampleModalLabel">Update member type status</p>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @csrf
                                    @method('PUT')
                                    <input id="member_id" name="member_id" hidden value="">
                                    <p class="text-center">Are you sure you want to update status?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-sm btn-danger">update</button>
                                    <!-- onclick="statusSubmit()" -->
                                    <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal">Cancel</button>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                  <div class="justify-content-end">
                      <nav aria-label="Page navigation" id="paginationDiv">
                          <ul class="pagination">

                              @if ($offset == 0 || $offset < 0)
                                  <li class="page-item disabled">
                                      <a class="page-link" href="javascript:void(0);" tabindex="-1">
                                          <span aria-hidden="true">&laquo;</span>
                                          <span class="sr-only">Previous</span>
                                      </a>
                                  </li>
                              @else
                                  <li class="page-item">
                                      <a class="page-link" href="javascript:void(0);"
                                          onclick="pendingMembers({{ $offset - $limit }},{{ $pageNumber - 1 }})"
                                          aria-label="Previous">
                                          <span aria-hidden="true">&laquo;</span>
                                          <span class="sr-only">Previous</span>
                                      </a>
                                  </li>
                              @endif
                              @if ($pageNumber > 3)
                                  <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                          onclick="pendingMembers({{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                                  </li>
                              @endif
                              @if ($pageNumber > 2)
                                  <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                          onclick="pendingMembers({{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                                  </li>
                              @endif
                              @if ($pageNumber > 1)
                                  <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                          onclick="pendingMembers({{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                                  </li>
                              @endif

                              <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                                      <span class="sr-only">(current)</span></a></li>

                              @if ($offset + $limit < $totalMember)
                                  <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                          onclick="pendingMembers({{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                                  </li>
                              @endif
                              @if ($offset + 2 * $limit < $totalMember)
                                  <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                          onclick="pendingMembers({{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                                  </li>
                              @endif
                              @if ($offset + 3 * $limit < $totalMember)
                                  <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                          onclick="pendingMembers({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
                                  </li>
                              @endif

                              @if ($offset + $limit == $totalMember || $offset + $limit > $totalMember)
                                  <li class="page-item disabled">
                                      <a class="page-link" href="javascript:void(0);" tabindex="-1">
                                          <span aria-hidden="true">&raquo;</span>
                                          <span class="sr-only">Next</span>
                                      </a>
                                  </li>
                              @else
                                  <li class="page-item">
                                      <a class="page-link" href="javascript:void(0);"
                                          onclick="pendingMembers({{ $offset + $limit }},{{ $pageNumber + 1 }})"
                                          aria-label="Next">
                                          <span aria-hidden="true">&raquo;</span>
                                          <span class="sr-only">Next</span>
                                      </a>
                                  </li>
                              @endif

                          </ul>
                      </nav>
                  </div>
              </div>
              <script>
                  $("#deleteMember").submit(function() {
                      $.LoadingOverlay("show");
                  });
                  $("#updateStatus").submit(function() {
                      $.LoadingOverlay("show");
                  });
                  //   $.LoadingOverlay("show");
                  $(function() {
                      $.ajax({
                          url: "{{ url('user/user') }}" + '/' + 0 + '/' + 1,
                          type: 'get',
                          success: function(data) {
                              $('#user_table_data').html(data);
                              //   $.LoadingOverlay("hide");
                          }
                      });
                      var table = $("#member-list-table").DataTable({
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
                      });

                      $('#member-list-table tbody').on('click', 'td.details-control_equb', function() {
                          var tr = $(this).closest('tr');
                          var inputId = $(this).prop("id");
                          var row = table.row(tr);
                          if (row.child.isShown()) {
                              row.child.hide();
                              tr.removeClass('shown')
                          } else {
                              row.child(loadHere).show();
                              $.ajax({
                                  url: "{{ url('member/show-member') }}" + '/' + inputId,
                                  type: 'get',
                                  success: function(data) {
                                      row.child(data).show();
                                      row.child.show();
                                      tr.addClass('shown');
                                  },
                                  error: function() {}
                              });

                          }

                      });
                  });
              </script>
