@if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'assistant' ||
        Auth::user()->role == 'it')
    <div class="row justify-content-center">
        <p class="card-title">Details on {{ $activityLogs[0]->type }} logs</p>
    </div>
    <table id="logs-detail" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Action on</th>
                <th>Action By</th>
                <th>Action</th>
                <th>Role</th>
                <th>Date</th>
            </tr>

        </thead>
        <tbody>

            @foreach ($activityLogs as $key => $activityLog)
                <tr>
                    <td>{{ $offset + $key + 1 }}</td>
                    <td>{{ $activityLog->type }}</td>
                    <td>{{ $activityLog->username }}</td>
                    <td>{{ $activityLog->action }}</td>
                    <td>{{ $activityLog?->role }}</td>
                    <td>
                        <?php
                        $toCreatedAt = new DateTime($activityLog['created_at']);
                        $createdDate = $toCreatedAt->format('M-j-Y');
                        echo $createdDate; ?>
                    </td>
            @endforeach

        </tbody>
    </table>
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
                            onclick="viewLogsPaginate({{ $offset - $limit }},{{ $pageNumber - 1 }})"
                            aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                @endif
                @if ($pageNumber > 3)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="viewLogsPaginate({{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                    </li>
                @endif
                @if ($pageNumber > 2)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="viewLogsPaginate({{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                    </li>
                @endif
                @if ($pageNumber > 1)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="viewLogsPaginate({{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                    </li>
                @endif

                <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                        <span class="sr-only">(current)</span></a></li>

                @if ($offset + $limit < $totalLoge)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="viewLogsPaginate({{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                    </li>
                @endif
                @if ($offset + 2 * $limit < $totalLoge)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="viewLogsPaginate({{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                    </li>
                @endif
                @if ($offset + 3 * $limit < $totalLoge)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="viewLogsPaginate({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
                    </li>
                @endif

                @if ($offset + $limit == $totalLoge || $offset + $limit > $totalLoge)
                    <li class="page-item disabled">
                        <a class="page-link" href="javascript:void(0);" tabindex="-1">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);"
                            onclick="viewLogsPaginate({{ $offset + $limit }},{{ $pageNumber + 1 }})"
                            aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
    </div>


    <script type="text/javascript">
        $('#logs-detail').DataTable({
            "responsive": false,
            "lengthChange": false,
            "searching": false,
            "autoWidth": true,
            "paging": false,
            "buttons": ["colvis"],
            language: {
                search: "",
                searchPlaceholder: "Search",
            },
            "buttons": ["excel", "pdf", "print", "colvis"]

        }).buttons().container().appendTo('#logs-detail_wrapper .col-md-6:eq(0)');
    </script>
@endif
