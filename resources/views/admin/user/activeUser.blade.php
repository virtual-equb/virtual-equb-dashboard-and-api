<table id="activeUser-list-table" class="table table-bordered table-striped ">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Role</th>
            <th>Status</th>
            <th>Register At </th>
            <th style="width:60px">Action </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($activeUsers as $key => $item)
            <tr>
                <td>{{ $offset + $key + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->phone_number }}</td>
                <td>{{ $item->gender }}</td>
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
                {{-- @if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant') --}}
                    <td>

                        <div class='dropdown'>
                            <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button'
                                data-toggle='dropdown'>Menu<span class='caret'></span></button>
                            <ul class='dropdown-menu p-4'>
                                <li><button href='javascript:void(0);'
                                        class="text-secondary btn btn-flat"
                                        onclick="openEditTab({{ $item }})">
                                        <span class="fas fa-edit "></span> Edit</button>
                                </li>
                                <li><button href='javascript:void(0);' class="text-secondary btn btn-flat"
                                        {{-- > --}} onclick="resetPassword({{ $item }})">
                                        <span class="fas fa-edit "></span> Reset Password</button>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="text-secondary btn btn-flat"
                                        onclick="openDeleteUserModal({{ $item }})"><i
                                            class="fas fa-trash-alt"></i>
                                        Delete</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="text-secondary btn btn-flat"
                                        onclick="openDeactivatedModal({{ $item }})" id="statuss"
                                        name="statuss"><i class="fab fa-shopware"></i> Deactivate</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                {{-- @endif --}}
            </tr>
        @endforeach
    </tbody>
</table>
<div class="modal modal-danger fade" id="deactivatedModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <p class="modal-title" id="exampleModalLabel">Deactivate user </p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="updateDeactivatedUser">
                <div class="modal-body">
                    @csrf
                    @method('put')
                    <input id="active_user_id" name="active_user_id" hidden value="">
                    <p class="text-center">Are you sure you want to deactivate this user?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-danger">Deactivate</button>
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
                        onclick="users({{ $offset - $limit }},{{ $pageNumber - 1 }})" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
            @endif
            @if ($pageNumber > 3)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="users({{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                </li>
            @endif
            @if ($pageNumber > 2)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="users({{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                </li>
            @endif
            @if ($pageNumber > 1)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="users({{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                </li>
            @endif

            <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                    <span class="sr-only">(current)</span></a></li>

            @if ($offset + $limit < $totalUser)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="users({{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                </li>
            @endif
            @if ($offset + 2 * $limit < $totalUser)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="users({{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                </li>
            @endif
            @if ($offset + 3 * $limit < $totalUser)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="users({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
                </li>
            @endif

            @if ($offset + $limit == $totalUser || $offset + $limit > $totalUser)
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:void(0);" tabindex="-1">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);"
                        onclick="users({{ $offset + $limit }},{{ $pageNumber + 1 }})" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            @endif

        </ul>
    </nav>
</div>
<script type="text/javascript">
    $("#updateDeactivatedUser").submit(function() {
        $.LoadingOverlay("show");
    });
    $(function() {
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
    });
</script>
