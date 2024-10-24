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
        .addEqub, .equbTypeTab {
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
                                                <th>Main Equb Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mainEqubs as $key => $equb)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $equb->name }}</td>
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

@include('admin.mainEqub.addMainEqub')
@include('admin.mainEqub.editMainEqub') 

@endsection

@section('scripts')
<script>
    $(document).on('click', '.delete-equb', function() {
        const equbId = $(this).data('id');
        if (confirm('Are you sure you want to remove this main equb?')) {
            $.ajax({
                url: '/equbs/' + equbId,
                type: 'DELETE',
                success: function() {
                    location.reload(); // Refresh the equb table
                },
                error: function(xhr) {
                    alert('Error deleting equb: ' + xhr.responseText);
                }
            });
        }
    });

    $('#clearSearch').click(function() {
        $('#equbSearchText').val('');
        // Optionally refresh the table or apply a filter reset
    });

    // Show loading overlay on form submission
    $('#addMainEqubModal form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        $.LoadingOverlay("show"); // Show loading spinner

        // Perform AJAX submission
        $.ajax({
            url: $(this).attr('action'), // Use form action URL
            type: 'POST',
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                // Handle success (e.g., refresh table, close modal)
                location.reload(); // Refresh the page or table
            },
            error: function(xhr) {
                // Handle error (e.g., show error message)
                alert('Error saving equb: ' + xhr.responseText);
            },
            complete: function() {
                $.LoadingOverlay("hide"); // Hide loading spinner after completion
            }
        });
    });

    function openEditModal(equbId) {
        $.ajax({
            url: '/equbs/' + equbId + '/edit',
            type: 'GET',
            success: function(data) {
                $('#edit_equb_id').val(data.id);
                $('#edit_name').val(data.name);
                $('#edit_created_by').val(data.created_by);
                $('#edit_remark').val(data.remark);
                $('#editMainEqubModal').modal('show'); // Open the modal
            },
            error: function(xhr) {
                console.error('Error fetching data:', xhr);
            }
        });
    }

    $(document).ready(function() {
        $(document).on('click', '.view-icon', function(e) {
            e.preventDefault();
            var image = $(this).attr('equb-type-image');
            $("#viewImage").attr("src", "/storage/" + image);
            $('#modaloff6').modal('show');
        });

        $('.textareaa').summernote();

        $("#type").on("change", function() {
            const lotteryDate = document.getElementById("lottery_date_div");
            const startDate = document.getElementById("start_date_div");
            const endDate = document.getElementById("end_date_div");
            const quota = document.getElementById("quota_div");

            if ($(this).find("option:selected").val() === "Automatic") {
                lotteryDate.classList.remove("d-none");
                startDate.classList.remove("d-none");
                endDate.classList.remove("d-none");
                quota.classList.remove("d-none");
                lotteryDate.required = true;
                startDate.required = true;
                endDate.required = true;
                quota.required = true;
            } else {
                lotteryDate.classList.add("d-none");
                startDate.classList.add("d-none");
                endDate.classList.add("d-none");
                quota.classList.add("d-none");
                lotteryDate.required = false;
                startDate.required = false;
                endDate.required = false;
                quota.required = false;
            }
        });
    });
</script>
@endsection