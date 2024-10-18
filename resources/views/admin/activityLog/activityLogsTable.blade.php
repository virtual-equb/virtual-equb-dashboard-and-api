<table id="logs" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Type</th>
            <th>Action Number</th>
            <th style="width:150px">Action</th>
        </tr>
    </thead>
    <tbody>


        @foreach ($countedTypes as $key => $countedType)
            <tr>
                <td> {{ $key + 1 }}</td>
                <td> On {{ $countedType->type }}</td>
                <td>{{ $countedType->total }}</td>
                <td><button class="btn btn-secondary"
                        onclick="viewLogs('{{ $countedType->getRawOriginal('type') }}')">
                        View
                        Logs</button></td>
            </tr>
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
                        onclick="activityLogs({{ $offset - $limit }},{{ $pageNumber - 1 }})" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
            @endif
            @if ($pageNumber > 3)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="activityLogs({{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                </li>
            @endif
            @if ($pageNumber > 2)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="activityLogs({{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                </li>
            @endif
            @if ($pageNumber > 1)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="activityLogs({{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                </li>
            @endif

            <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                    <span class="sr-only">(current)</span></a></li>

            @if ($offset + $limit < $totalTypes)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="activityLogs({{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                </li>
            @endif
            @if ($offset + 2 * $limit < $totalTypes)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="activityLogs({{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                </li>
            @endif
            @if ($offset + 3 * $limit < $totalTypes)
                <li class="page-item"><a class="page-link" href="javascript:void(0);"
                        onclick="activityLogs({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
                </li>
            @endif

            @if ($offset + $limit == $totalTypes || $offset + $limit > $totalTypes)
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:void(0);" tabindex="-1">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);"
                        onclick="activityLogs({{ $offset + $limit }},{{ $pageNumber + 1 }})" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            @endif

        </ul>
    </nav>
</div>
