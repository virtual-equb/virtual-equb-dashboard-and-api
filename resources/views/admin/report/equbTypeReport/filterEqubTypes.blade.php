
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
                    @can('export reports_data')
                    "buttons": ["excel", "pdf", "print", "colvis"]
                    @else 
                    "buttons": []
                    @endcan
                }).buttons().container().appendTo('#equbType-table_wrapper .col-md-6:eq(0)');
            });
        </script>
