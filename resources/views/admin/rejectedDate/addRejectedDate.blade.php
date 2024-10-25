{{-- @if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'it') --}}
    <div class="modal fade" id="addOffDateModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group"
                    action="{{ route('registerRejectedDate') }}" enctype="multipart/form-data" id="addOffDate">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h4 class="modal-title">Add Off Date</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <!-- text input -->
                            <div class="form-group required">
                                <label class="control-label">Date</label>
                                <input type="" class="form-control" id="rejected_date"
                                    name="rejected_date"placeholder="rejected date" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                <textarea id="description" name="description" class="form-control" placeholder="Description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
{{-- @endif --}}
