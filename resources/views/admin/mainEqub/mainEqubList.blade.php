@can('view main_equb')
@extends('layouts.app')

@section('styles')
<style type="text/css">
    .details-control {
        background: url("{{ url('images/plus20.webp') }}") no-repeat center center;
        cursor: pointer;
    }
    tr.shown .details-control {
        background: url("{{ url('images/minus20.webp') }}") no-repeat center center;
    }
    .form-group.required .control-label:after {
        content: "*";
        color: red;
    }
    .table-responsive {
        overflow-x: auto;
    }
    @media (max-width: 768px) {
        .responsive-input {
            width: 100%;
            margin-bottom: 20px;
        }
    }
    div.dataTables_wrapper div.dataTables_info {
        padding-top: 0.85em;
        display: none;
    }
</style>
@endsection

@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row mt-2">
                    <div class="col-lg-4 col-md-4 col-12">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalMainEqub }}</h3>
                                <p>Total Equbs</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-12">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $totalActiveEqub }}</h3>
                                <p>Active Equb</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-12">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $totalInactiveEqub }}</h3>
                                <p>Inactive Equb</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Main Equb
                                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addMainEqubModal"> <span class="fa fa-plus-circle"> </span> Add Main Equb</button>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="equb_table_data" class="table-responsive">
                                    <table  id="equbTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Main Equbs</th>
                                                <th>Status</th>
                                                <th style="width: 50px">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mainEqubs as $index => $equb)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td style="background-color: rgb(76, 175, 80); width: 60px; text-align: center;">
                                                        <img src="{{ asset('storage/' . $equb->image) }}" alt="{{ $equb->name }}" 
                                                            style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                                    </td>
                                                    <td>{{ $equb->name }}</td>
                                                    <td>
                                                        {{ $equb->active ? 'Active' : 'Inactive' }}
                                                    </td>
                                                    <td>
                                                        <div class='dropdown'>
                                                            <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                                            <ul class='dropdown-menu p-4'>
                                                                @can('update main_equb')
                                                                    <li>
                                                                        <button class="text-secondary btn btn-flat" onclick="openEditModal({{ $equb->id }})">
                                                                            <span class="fa fa-edit"></span> Edit
                                                                        </button>
                                                                    </li>
                                                                @endcan
                                                                @can('delete main_equb')
                                                                    <a href="javascript:void(0);"
                                                                        class="btn-sm btn btn-flat"
                                                                        onclick="openDeleteModal({{ $equb }})"><i class="fas fa-trash-alt"></i>
                                                                        Delete
                                                                    </a>
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="table-responsive">
    <div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="exampleModalLabel">Delete Main Equb</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="deleteMainEqub">
                        @csrf
                        @method('DELETE')
                        <input id="id" name="id" hidden value="">
                        <p class="text-center">Are you sure you want to delete this Equb ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include the Add Main Equb Modal -->
@include('admin.mainEqub.addMainEqub')
@include('admin.mainEqub.editMainEqub')

@endsection

@section('scripts')
<script>
    // Setup CSRF token for all AJAX requests
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    $("#deleteMainEqub").submit(function() {
        $.LoadingOverlay("show");
    });

    function openDeleteModal(item) {
        $('#id').val(item.id);
        $('#deleteModal').modal('show');
        $('#deleteMainEqub').attr('action', 'main-equbs/' + $('#id').val())
    }

    // Clear Search
    $('#clearSearch').click(function() {
        $('#equbSearchText').val('');
        // Optionally refresh the table or apply a filter reset
    });

    // Open Edit Modal
    function openEditModal(equbId) {
        $.ajax({
            type: 'GET',
            url: '/main-equbs/' + equbId,
            success: function(data) {
                $('#editMainEqubId').val(data.id);
                $('#editMainEqubName').val(data.name);
                $('#editMainEqubStatus').val(data.active ? 1 : 0); // Set status based on active

                // Display current image if it exists
                if (data.image) {
                    $('#currentImage').attr('src', '{{ asset("storage/") }}/' + data.image).show();
                } else {
                    $('#currentImage').hide(); // Hide if no image
                }

                $('#editMainEqubModal').modal('show'); // Open the modal
            },
            error: function(xhr) {
                console.error('Error fetching data:', xhr);
                alert('Error fetching data: ' + xhr.responseText); // Dynamic error message
            }
        });
    }

    // Save Changes
    $(document).ready(function() {
        // Save Changes
        $('#saveEditMainEqub').click(function() {
            const id = $('#editMainEqubId').val();
            const name = $('#editMainEqubName').val();
            const status = $('#editMainEqubStatus').val();

            console.log('ID:', id);
            console.log('Name:', name);
            console.log('Status:', status); // Log the status

            // Create data object to send
            const data = {
                name: name,
                active: status 
            };

            $.ajax({
                type: 'PUT',
                url: '/main-equbs/' + id, // Ensure this URL matches your route
                data: data,
                success: function(result) {
                    console.log(data);
                    alert(result.message || 'Main Equb updated successfully!');
                    $('#editMainEqubModal').modal('hide'); // Hide the modal after saving
                    location.reload(); // Refresh the page to see changes
                },
                error: function(xhr) {
                    console.error('Error updating Main Equb:', xhr);
                    alert('Error updating Main Equb: ' + xhr.responseText);
                }
            });
        });
    });

    $(function() {
        $('#mainEqubs').addClass('active');
        $('#nav-u').addClass('active');
        $("#equbTable").DataTable({
            "responsive": false,
            "lengthChange": false,
            "searching": true,
            "autoWidth": false,
            language: {
                search: "",
                searchPlaceholder: "Search",
            },
            "buttons": [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':visible',
                        columns: function (index, data, node) {
                            return index !== 4;
                        }
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: ':visible',
                        columns: function (index, data, node) {
                            return index !== 4;
                        }
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible',
                        columns: function (index, data, node) {
                            return index !== 4;
                        }
                    }
                },
                'colvis'
            ]
        }).buttons().container().appendTo('#equb_table_data .col-md-6:eq(0)');
    });
</script>
@endsection
@endcan