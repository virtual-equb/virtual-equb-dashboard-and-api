
<table id="equb-table" class="table table-bordered table-striped ">
          <thead>
              <tr>
                  <th>No</th>
                  <th>Member</th>
                  <th>Equb Type</th>
                  <th>Amount in Birr</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Expected Total</th>
                  <th>Lottery Date</th>
              </tr>
          </thead>
          <tbody>
              @foreach ($equbs as $key => $item)
                  <tr id="trm{{ $item['id'] }}">
                      <td>{{ $offset + $key + 1 }}</td>
                      <td>{{ $item->member->full_name }}</td>
                      <td>{{ $item->equbType->name }} - Round {{ $item->equbType->round }}</td>
                      <td> {{ number_format($item->amount) }}</td>
                      <td>
                          <?php
                          $toCreatedAt = new DateTime($item['start_date']);
                          $createdDate = $toCreatedAt->format('M-j-Y');
                          echo $createdDate; ?>
                      </td>
                      <td>
                          <?php
                          $toCreatedAt = new DateTime($item['end_date']);
                          $createdDate = $toCreatedAt->format('M-j-Y');
                          echo $createdDate; ?>
                      </td>
                      <td> {{ number_format($item->total_amount) }}</td>
                      <td>
                          <?php
                          foreach (explode(',', $item->lottery_date) as $lottery_date) {
                              $toCreatedAt = new DateTime($lottery_date);
                              $createdDate = $toCreatedAt->format('M-j-Y');
                              echo $createdDate;
                              echo '<br>';
                          }
                          ?>
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
                              onclick="equbs({{ $offset - $limit }},{{ $pageNumber - 1 }})" aria-label="Previous">
                              <span aria-hidden="true">&laquo;</span>
                              <span class="sr-only">Previous</span>
                          </a>
                      </li>
                  @endif
                  @if ($pageNumber > 3)
                      <li class="page-item"><a class="page-link" href="javascript:void(0);"
                              onclick="equbs({{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                      </li>
                  @endif
                  @if ($pageNumber > 2)
                      <li class="page-item"><a class="page-link" href="javascript:void(0);"
                              onclick="equbs({{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                      </li>
                  @endif
                  @if ($pageNumber > 1)
                      <li class="page-item"><a class="page-link" href="javascript:void(0);"
                              onclick="equbs({{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                      </li>
                  @endif

                  <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                          <span class="sr-only">(current)</span></a></li>

                  @if ($offset + $limit < $totalEqub)
                      <li class="page-item"><a class="page-link" href="javascript:void(0);"
                              onclick="equbs({{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                      </li>
                  @endif
                  @if ($offset + 2 * $limit < $totalEqub)
                      <li class="page-item"><a class="page-link" href="javascript:void(0);"
                              onclick="equbs({{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                      </li>
                  @endif
                  @if ($offset + 3 * $limit < $totalEqub)
                      <li class="page-item"><a class="page-link" href="javascript:void(0);"
                              onclick="equbs({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
                      </li>
                  @endif

                  @if ($offset + $limit == $totalEqub || $offset + $limit > $totalEqub)
                      <li class="page-item disabled">
                          <a class="page-link" href="javascript:void(0);" tabindex="-1">
                              <span aria-hidden="true">&raquo;</span>
                              <span class="sr-only">Next</span>
                          </a>
                      </li>
                  @else
                      <li class="page-item">
                          <a class="page-link" href="javascript:void(0);"
                              onclick="equbs({{ $offset + $limit }},{{ $pageNumber + 1 }})" aria-label="Next">
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
              $("#equb-table").DataTable({
                  "responsive": false,
                  "lengthChange": false,
                  "searching": true,
                  "paging": false,
                  "autoWidth": false,
                  language: {
                      search: "",
                      searchPlaceholder: "Search",
                  },
                  "buttons": ["excel", "pdf", "print", "colvis"]
              }).buttons().container().appendTo('#equb-table_wrapper .col-md-6:eq(0)');
          });
      </script>
