    @if (Auth::user()->role == 'admin' ||
            Auth::user()->role == 'general_manager' ||
            Auth::user()->role == 'operation_manager' ||
            Auth::user()->role == 'finance' ||
            Auth::user()->role == 'assistant' ||
            Auth::user()->role == 'it')
        <table id="equbType-table" class="table table-bordered table-striped ">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Registered At </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equbTypes as $key => $item)
                    <tr id="trm{{ $item['id'] }}">
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->start_date }}</td>
                        <td>{{ $item->end_date }}</td>
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
        <script>
            $(function() {
                $("#equbType-table").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "searching": false,
                    "autoWidth": false,
                    language: {
                        search: "",
                        searchPlaceholder: "Search",
                    },
                    // "buttons": ["excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#equbType-table_wrapper .col-md-6:eq(0)');
            });
        </script>
    @endif
