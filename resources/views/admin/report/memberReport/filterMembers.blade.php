
         <table id="member-table" class="table table-bordered table-striped ">
             <thead>
                 <tr>
                     <th></th>
                     <th>Full Name</th>
                     <th>Phone</th>
                     <th>Gender</th>
                     <th>Status</th>
                     <th>Registered At </th>
                 </tr>
             </thead>
             <tbody>
                 @foreach ($members as $key => $item)
                     <tr id="trm{{ $item['id'] }}">
                         <td>{{ $offset + $key + 1 }}</td>
                         <td>{{ $item->full_name }}</td>
                         <td>{{ $item->phone }}</td>
                         <td>{{ $item->gender }}</td>
                         <td>{{ $item->status }}</td>
                         <td>
                             <?php
                             $toCreatedAt = new DateTime($item['created_at']);
                             $createdDate = $toCreatedAt->format('M-j-Y');
                             echo $createdDate; ?>
                         </td>
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
                                 onclick="members({{ $offset - $limit }},{{ $pageNumber - 1 }})" aria-label="Previous">
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
                                 onclick="members({{ $offset + $limit }},{{ $pageNumber + 1 }})" aria-label="Next">
                                 <span aria-hidden="true">&raquo;</span>
                                 <span class="sr-only">Next</span>
                             </a>
                         </li>
                     @endif

                 </ul>
             </nav>
         </div>

         <script>
             $(function() {
                 $("#member-table").DataTable({
                     "responsive": false,
                     "lengthChange": false,
                     "searching": true,
                     "paging": false,
                     "autoWidth": false,
                     language: {
                         search: "",
                         searchPlaceholder: "Search",
                     },
                     @can('export reports_data')
                     "buttons": ["excel", "pdf", "print", "colvis"]
                     @else 
                     "buttons": []
                     @endcan
                 }).buttons().container().appendTo('#member-table_wrapper .col-md-6:eq(0)');
             });
         </script>

