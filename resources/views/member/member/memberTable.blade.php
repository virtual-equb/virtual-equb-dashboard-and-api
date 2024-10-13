            <div class="row">
                      <div id="member_table_data_w" class="col-md-8">
                      </div> 
                      <p class="float-right" id="member_table_filter">
                          <input type="text" id="activeSearchText" placeholder="Search member name">
                          <button class="btn btn-default" id="clearActiveSearch"
                              onclick="clearActiveSearchEntry()">
                              Clear
                          </button>
                      </p>
                      </div>    
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
                                    onclick="members({{ $offset - $limit }},{{ $pageNumber - 1 }})"
                                    aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>
                        @endif
                        @if ($pageNumber > 3)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="members({{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                            </li>
                        @endif
                        @if ($pageNumber > 2)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="members({{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                            </li>
                        @endif
                        @if ($pageNumber > 1)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="members({{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                            </li>
                        @endif

                        <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                                <span class="sr-only">(current)</span></a></li>

                        @if ($offset + $limit < $totalMember)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="members({{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                            </li>
                        @endif
                        @if ($offset + 2 * $limit < $totalMember)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="members({{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                            </li>
                        @endif
                        @if ($offset + 3 * $limit < $totalMember)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="members({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
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
                                    onclick="members({{ $offset + $limit }},{{ $pageNumber + 1 }})"
                                    aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                        @endif

                    </ul>
                </nav>
            </div>
<script>
       $(function () {
        $.ajax({
              url: "{{ url('user/user') }}" + '/' + 0 + '/' + 1,
              type: 'get',
              success: function(data) {
                  $('#user_table_data').html(data);
              }
          });
        var table=$("#member-list-table").DataTable({
          "responsive": false, "lengthChange": false,"searching": false,"paging": false,"autoWidth": false,
          language: { 
            search: "",
            searchPlaceholder: "Search",},
          // "buttons": ["excel", "pdf", "print", "colvis"]
        });
        table.buttons().container().appendTo('#member_table_data_w');
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