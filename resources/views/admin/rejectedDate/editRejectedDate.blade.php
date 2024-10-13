@if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'it')
    <div class="modal fade" id="editRejectedDateModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group" action="" id="updateOffDate">
                    {{ csrf_field() }}
                    @method('put')
                    <input type="hidden" id='off_date_id' name="off_date_id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Off Date</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="form-group required col-12">
                                    <label class="control-label">Date</label>
                                    <input type="text" class="form-control" id="update_rejected_date"
                                        name="rejected_date"placeholder="Rejected Date" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>Description</label>
                                    <textarea id="update_description" name="description" class="form-control" placeholder="Description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr -2">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
