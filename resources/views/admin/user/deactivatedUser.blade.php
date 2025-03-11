@can('deactivate user')
    <div id="deactiveUser_table_data" class="table-responsive">
        <table id="deactiveUser-list-table" class="table table-bordered table-striped ">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Role</th>
                    <th>Role Guard</th>
                    <th>Status</th>
                    <th>Register At </th>
                    <th style="width:50px">Action </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deactivatedUsers as $index => $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->phone_number }}</td>
                        <td>{{ $item->gender }}</td>
                        <td>
                        @foreach($item->roles as $guard)
                            <div class="badge badge-primary">
                                {{ $guard->guard_name}}
                            </div>
                        @endforeach
                        </td>
                        <td>
                        @if (!empty($item->roles))
                            @foreach ($item->roles as $role)
                                <span class="badge badge-primary">{{ $role->name }}</span>
                            @endforeach
                        @endif
                        </td>
                        <td>{{ $item->enabled ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <?php
                            $toCreatedAt = new DateTime($item['created_at']);
                            $createdDate = $toCreatedAt->format('M-j-Y');
                            echo $createdDate; ?>
                        </td>
                            <td>
                                <div class='dropdown'>
                                    <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button'
                                        data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                    <ul class='dropdown-menu dropdown-menu-right p-4'>
                                    @can('update user')
                                        <li><button href='javascript:void(0);'
                                                class="text-secondary btn btn-link {{ $item->role == 'member' ? 'disabled' : '' }}"
                                                onclick="openEditTab({{ $item }})">
                                                <span class="fas fa-edit "></span> Edit</button>
                                        </li>
                                        @endcan
                                    @can('delete user')
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-link {{ $item->role == 'member' ? 'disabled' : '' }}"
                                                onclick="openDeleteUserModal({{ $item }})"><i
                                                    class="fas fa-trash-alt"></i> Delete</a>
                                        </li>
                                        @endcan
                                        @can('activate user')
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-link {{ $item->role == 'member' ? 'disabled' : '' }}"
                                                onclick="openActivatedModal({{ $item }})" id="statuss"
                                                name="statuss"><i class="fab fa-shopware"></i> Activate</a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

      <div class="modal modal-danger fade" id="ActivatedModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
          aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <p class="modal-title" id="exampleModalLabel">Activate user</p>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <form action="" method="post" id="updateActiveUser">
                      <div class="modal-body">
                          @csrf
                          @method('put')
                          <input id="deactivated_user_id" name="deactivated_user_id" hidden value="">
                          <p class="text-center">Are you sure you want to activate this user?</p>
                      </div>
                      <div class="modal-footer">
                          <button type="submit" class="btn btn-sm btn-danger">Activate</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                      </div>
                  </form>
              </div>
          </div>
      </div>

      <script type="text/javascript">
          $("#updateActiveUser").submit(function() {
              $.LoadingOverlay("show");
          });
          $(function() {
              $("#deactiveUser-list-table").DataTable({
                  "responsive": false,
                  "lengthChange": false,
                  "searching": true,
                  "autoWidth": false,
                  language: {
                      search: "",
                      searchPlaceholder: "Search",
                  },
                  @can('export user_data')
                     "buttons": ["excel", "pdf", "print", "colvis"]
                  @else
                    "buttons": []
                  @endcan
              }).buttons().container().appendTo('#deactiveUser_table_data .col-md-6:eq(0)')
          });
      </script>
@endcan