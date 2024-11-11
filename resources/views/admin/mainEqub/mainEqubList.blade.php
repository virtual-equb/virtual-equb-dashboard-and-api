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
</style>
@endsection

@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-pills" id="custom-tabs-two-tab" role="tablist">
                                    <li class="nav-item nav-blue memberTab">
                                        <a class="nav-link active" id="custom-tabs-two-member-tab" data-toggle="pill" href="#custom-tabs-two-member" role="tab" aria-controls="custom-tabs-two-member" aria-selected="true">
                                            <span class="fa fa-list"></span> Main Equb
                                        </a>
                                    </li>
                                </ul>
                                <div class="float-right">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMainEqubModal" style="margin-right: 30px;">
                                            <span class="fa fa-plus-circle"></span> Add Main Equb
                                        </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <input class="form-control responsive-input" type="text" id="equbSearchText" placeholder="Search Main Equb">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-default" id="clearSearch">Clear</button>
                                    </div>
                                </div>
                                <div id="equb_table_data" class="table-responsive">
                                    <table class="table table-bordered" id="equbTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Main Equbs</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mainEqubs as $key => $equb)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <img src="{{ asset('storage/' . $equb->image) }}" alt="{{ $equb->name }}" style="width: 50px; height: auto;">
                                                    </td>
                                                    <td>{{ $equb->name }}</td>
                                                    <td>
                                                        <span class="badge {{ $equb->active ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $equb->active ? 'Active' : 'Inactive' }}
                                                        </span>
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
                                                                    <li>
                                                                        <button class="text-secondary btn btn-flat delete-equb" data-id="{{ $equb->id }}">
                                                                            <i class="fas fa-trash-alt"></i> Delete
                                                                        </button>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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

    // Delete Equb
    $(document).on('click', '.delete-equb', function() {
        const equbId = $(this).data('id');
        if (confirm('Are you sure you want to remove this main equb?')) {
            $.ajax({
                url: '/main-equbs/' + equbId,
                type: 'DELETE',
                success: function(result) {
                    alert(result.message || 'Equb deleted successfully!'); // Dynamic success message
                    location.reload(); // Refresh the equb table
                },
                error: function(xhr) {
                    console.error('Error deleting equb:', xhr);
                    alert('Error deleting equb: ' + xhr.responseText); // Dynamic error message
                }
            });
        }
    });

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
// Save Changes
$(document).ready(function() {
    // Save Changes
    $('#saveEditMainEqub').click(function() {
        const id = $('#editMainEqubId').val();
        const name = $('#editMainEqubName').val();
        const status = $('#editMainEqubStatus').val();

        // Create data object to send
        const data = {
            name: name,
            active: status === "1" // Convert status to boolean
        };

        $.ajax({
            type: 'PUT',
            url: '/main-equbs/' + id, // Ensure this URL matches your route
            data: data,
            success: function(result) {
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

   
</script>
@endsection