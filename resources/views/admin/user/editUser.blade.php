<section class="content-header">
    <h1>
        <small>Update User</small><br>
    </h1>
</section>

<form method="post" class="form-horizontal form-group" id="editUserForm">
    {{ csrf_field() }}
    @method('put')  <!-- This line specifies that the form should be treated as a PUT request -->
    <div class="card-body">
        <input type="hidden" id='user_id' name="user_id" value="{{ $user['id'] }}">
        
        <div class="form-group row">
            <label for="name" class="col-md-2 control-label">Full Name <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
            <div class="col-md-10">
                <input type="text" class="form-control" id="name" name="name" value="{{ $user['name'] }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-md-2 control-label">Email <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
            <div class="col-md-10">
                <input type="email" class="form-control" id="email" name="email" value="{{ $user['email'] }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="phone" class="col-md-2 control-label">Phone <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
            <div class="col-md-10">
                <input type="text" class="form-control" id="phone" name="phone_number" value="{{ $user['phone_number'] }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="gender" class="col-md-2 control-label">Gender <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
            <div class="col-md-10">
                <select class="custom-select form-control" id="gender" name="gender" required>
                    <option value="Male" {{ $user['gender'] == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $user['gender'] == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="role" class="col-md-2 control-label">Role <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
            <div class="col-md-10">
                <select class="custom-select form-control" id="editRole" multiple name="role[]" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ in_array($role->name, $userRoles) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
               
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mr-5">
        <button type="submit" class="btn btn-outline-primary mr-3">Submit</button>
        <button type="reset" class="btn btn-warning text-white">Clear</button>
    </div>
</form>

<!-- Modal for Updating User Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ url('user/update-status/' . $user['id']) }}" method="post" id="updateStatus">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update User Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" {{ $user['status'] == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user['status'] == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </div>
        </form>
    </div>
</div>

