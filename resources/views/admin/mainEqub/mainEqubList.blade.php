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
                                    @if (!in_array(Auth::user()->role, ['assistant', 'finance']))
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMainEqubModal" style="margin-right: 30px;">
                                            <span class="fa fa-plus-circle"></span> Add Main Equb
                                        </button>
                                    @endif
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
                                                    @if (Auth::user()->role != 'assistant')
                                                        <td>
                                                            <div class='dropdown'>
                                                                <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                                                <ul class='dropdown-menu p-4'>
                                                                    @if (Auth::user()->role != 'finance')
                                                                        <li>
                                                                            <button class="text-secondary btn btn-flat" onclick="openEditModal({{ $equb->id }})">
                                                                                <span class="fa fa-edit"></span> Edit
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <button class="text-secondary btn btn-flat delete-equb" data-id="{{ $equb->id }}">
                                                                                <i class="fas fa-trash-alt"></i> Delete
                                                                            </button>
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    @endif
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
    $(document).on('click', '.delete-equb', function() {
        const equbId = $(this).data('id');
        if (confirm('Are you sure you want to remove this main equb?')) {
            $.ajax({
                url: '/main-equbs/' + equbId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                success: function(result) {
                    location.reload(); // Refresh the equb table
                },
                error: function(xhr) {
                    console.error('Error deleting equb:', xhr); // Log error details to console
                    alert('Error deleting equb: ' + xhr.responseText);
                }
            });
        }
    });

    $('#clearSearch').click(function() {
        $('#equbSearchText').val('');
        // Optionally refresh the table or apply a filter reset
    });

    function openEditModal(equbId) {
        $.ajax({
            type: 'GET',
            url: '/main-equbs/' + equbId, // Fetch specific equb data
            success: function(data) {
                $('#edit_equb_id').val(data.id);
                $('#edit_name').val(data.name);
                $('#edit_remark').val(data.remark);
                $('#edit_status').val(data.active); // Set the status dropdown

                // Display current image if it exists
                if (data.image) {
                    $('#currentImage').attr('src', '{{ asset("storage/") }}/' + data.image).show();
                } else {
                    $('#currentImage').hide(); // Hide if no image
                }

                $('#editMainEqubModal').modal('show'); // Open the modal
            },
            error: function(xhr) {
                console.error('Error fetching data:', xhr); // Log error details to console
            }
        });
    }

    $(document).ready(function() {
        $('#saveChanges').click(function() {
            const id = $('#edit_equb_id').val();
            const name = $('#edit_name').val();
            const remark = $('#edit_remark').val();
            const status = $('#edit_status').val();
            const imageFile = $('#image')[0].files[0]; // Get the selected file

            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content')); // Include CSRF token
            formData.append('name', name);
            formData.append('remark', remark);
            formData.append('status', status);
            if (imageFile) {
                formData.append('image', imageFile); // Include image file if selected
            }

            $.ajax({
                type: 'PUT',
                url: '/main-equbs/' + id,
                data: formData,
                processData: false, // Important for FormData
                contentType: false, // Important for FormData
                success: function(result) {
                    location.reload(); // Refresh the equb table after saving
                },
                error: function(xhr) {
                    console.error('Error updating equb:', xhr); // Log error details to console
                    alert('Error updating equb: ' + xhr.responseText);
                }
            });
        });
    });
</script>
@endsection
